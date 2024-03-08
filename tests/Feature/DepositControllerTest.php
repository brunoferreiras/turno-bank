<?php

namespace Tests\Feature;

use App\Enums\DepositStatus;
use App\Enums\UserTypes;
use App\Models\Deposit;
use App\Models\User;
use App\Services\DepositService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DepositControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_should_create_a_new_deposit(): void
    {
        $user = User::factory()->create([
            'type' => UserTypes::CUSTOMER->value,
        ]);
        Storage::fake('public');
        $payload = Deposit::factory()->make([
            'amount' => 100,
        ])->toArray();
        $file = UploadedFile::fake()->image('any-check.jpg');
        $payload['image'] = $file;
        $this->actingAs($user, 'api');
        $response = $this->postJson('/api/deposits', $payload);
        Storage::disk('public')->assertExists('deposits/' . $file->hashName());
        $response->assertCreated();
        $this->assertDatabaseHas('deposits', [
            'account_id' => $user->account->id,
            'description' => $payload['description'],
            'amount' => 10000,
            'image' => 'deposits/' . $file->hashName(),
            'status' => DepositStatus::PENDING->value,
        ]);
    }

    /** @test */
    public function it_should_not_create_a_new_deposit_with_invalid_data(): void
    {
        $user = User::factory()->create([
            'type' => UserTypes::CUSTOMER->value,
        ]);
        $this->actingAs($user, 'api');
        $response = $this->postJson('/api/deposits', []);
        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'amount', 'description', 'image',
            ]);
    }

    /** @test */
    public function it_should_return_error_creating_a_new_deposit_if_upload_fails(): void
    {
        $user = User::factory()->create([
            'type' => UserTypes::CUSTOMER->value,
        ]);
        Storage::fake('public');
        $payload = Deposit::factory()->make([
            'amount' => 100,
        ])->toArray();
        $file = UploadedFile::fake()->image('any-check.jpg');
        $payload['image'] = $file;
        $this->actingAs($user, 'api');
        Storage::shouldReceive('disk->put')->andThrow(new \Exception('Error creating file'));
        $response = $this->postJson('/api/deposits', $payload);
        $response->assertStatus(500);
    }

    /** @test */
    public function it_should_return_error_if_create_deposit_fails(): void
    {
        $user = User::factory()->create([
            'type' => UserTypes::CUSTOMER->value,
        ]);
        Storage::fake('public');
        $payload = Deposit::factory()->make([
            'amount' => 100,
        ])->toArray();
        $file = UploadedFile::fake()->image('any-check.jpg');
        $payload['image'] = $file;
        $this->actingAs($user, 'api');
        $this->mock(DepositService::class, function ($mock) use ($user, $payload, $file) {
            $mock->shouldReceive('register')
                ->with($user->id, [
                    ...$payload,
                    'status' => DepositStatus::PENDING,
                    'image' => 'deposits/' . $file->hashName(),
                ])
                ->andReturn(false);
        });
        $response = $this->postJson('/api/deposits', $payload);
        $response->assertServerError();
        $this->assertDatabaseMissing('deposits', [
            'account_id' => $user->id,
            'description' => $payload['description'],
            'amount' => 10000,
            'image' => 'deposits/' . $file->hashName(),
            'status' => DepositStatus::PENDING->value,
        ]);
    }

    /** @test */
    public function it_should_return_pending_deposits(): void
    {
        $customer = User::factory()->create([
            'type' => UserTypes::CUSTOMER->value,
        ]);
        foreach (DepositStatus::cases() as $case) {
            Deposit::factory()->count(2)->create([
                'account_id' => $customer->account->id,
                'status' => $case->value,
            ]);
        }
        $admin = User::factory()->create([
            'type' => UserTypes::ADMIN->value,
        ]);
        $this->actingAs($admin, 'api');
        $response = $this->getJson('/api/deposits/pendings');
        $response->assertOk()
            ->assertJsonCount(2, 'data');
    }

    /** @test */
    public function it_should_forbidden_return_pending_deposits_if_user_is_not_admin(): void
    {
        $customer = User::factory()->create([
            'type' => UserTypes::CUSTOMER->value,
        ]);
        foreach (DepositStatus::cases() as $case) {
            Deposit::factory()->count(2)->create([
                'account_id' => $customer->account->id,
                'status' => $case->value,
            ]);
        }
        $user = User::factory()->create([
            'type' => UserTypes::CUSTOMER->value,
        ]);
        $this->actingAs($user, 'api');
        $response = $this->getJson('/api/deposits/pendings');
        $response->assertForbidden();
    }

    /** @test */
    public function it_should_update_deposit_status(): void
    {
        $customer = User::factory()->create([
            'type' => UserTypes::CUSTOMER->value,
        ]);
        $deposit = Deposit::factory()->create([
            'account_id' => $customer->account->id,
            'status' => DepositStatus::PENDING->value,
            'amount' => 1000,
        ]);
        $admin = User::factory()->create([
            'type' => UserTypes::ADMIN->value,
        ]);
        $this->actingAs($admin, 'api');
        $response = $this->patchJson("/api/deposits/{$deposit->id}/status", [
            'status' => 'accepted',
        ]);
        $response->assertOk();
        $this->assertDatabaseHas('deposits', [
            'id' => $deposit->id,
            'status' => DepositStatus::ACCEPTED->value,
            'approved_by' => $admin->id,
        ]);
        $this->assertDatabaseHas('accounts', [
            'id' => $customer->account->id,
            'amount' => 100000,
        ]);
    }

    /** @test */
    public function it_should_forbiden_update_deposit_status_if_user_is_not_admin(): void
    {
        $customer = User::factory()->create([
            'type' => UserTypes::CUSTOMER->value,
        ]);
        $deposit = Deposit::factory()->create([
            'account_id' => $customer->account->id,
            'status' => DepositStatus::PENDING->value,
        ]);
        $user = User::factory()->create([
            'type' => UserTypes::CUSTOMER->value,
        ]);
        $this->actingAs($user, 'api');
        $response = $this->patchJson("/api/deposits/{$deposit->id}/status", [
            'status' => 'accepted',
        ]);
        $response->assertForbidden();
        $this->assertDatabaseHas('deposits', [
            'id' => $deposit->id,
            'status' => DepositStatus::PENDING->value,
        ]);
    }

    /** @test */
    public function it_should_not_update_deposit_status_with_invalid_data(): void
    {
        $customer = User::factory()->create([
            'type' => UserTypes::CUSTOMER->value,
        ]);
        $deposit = Deposit::factory()->create([
            'account_id' => $customer->account->id,
            'status' => DepositStatus::PENDING->value,
        ]);
        $admin = User::factory()->create([
            'type' => UserTypes::ADMIN->value,
        ]);
        $this->actingAs($admin, 'api');
        $response = $this->patchJson("/api/deposits/{$deposit->id}/status", []);
        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'status',
            ]);
    }

    /** @test */
    public function it_should_return_error_if_update_deposit_status_fails(): void
    {
        $customer = User::factory()->create([
            'type' => UserTypes::CUSTOMER->value,
        ]);
        $deposit = Deposit::factory()->create([
            'account_id' => $customer->account->id,
            'status' => DepositStatus::PENDING->value,
        ]);
        $admin = User::factory()->create([
            'type' => UserTypes::ADMIN->value,
        ]);
        $this->actingAs($admin, 'api');
        $this->mock(DepositService::class, function ($mock) use ($admin, $deposit) {
            $mock->shouldReceive('updateStatus')
                ->with($admin->id, $deposit->id, 'accepted')
                ->andReturn(false);
        });
        $response = $this->patchJson("/api/deposits/{$deposit->id}/status", [
            'status' => 'accepted',
        ]);
        $response->assertServerError();
        $this->assertDatabaseHas('deposits', [
            'id' => $deposit->id,
            'status' => DepositStatus::PENDING->value,
        ]);
    }
}
