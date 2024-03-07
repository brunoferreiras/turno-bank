<?php

namespace Tests\Feature;

use App\Enums\DepositStatus;
use App\Enums\UserTypes;
use App\Models\Deposit;
use App\Models\Purchase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PurchaseControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function it_should_create_a_new_purchase(): void
    {
        $user = User::factory()->create([
            'type' => UserTypes::CUSTOMER->value,
        ]);
        $deposit = Deposit::factory()->create([
            'account_id' => $user->account->id,
            'amount' => 10000,
        ]);
        $deposit->update([
            'status' => DepositStatus::ACCEPTED->value,
        ]);
        $payload = Purchase::factory()->make([
            'amount' => 100,
        ])->toArray();
        $this->actingAs($user, 'api');
        $response = $this->post('/api/purchases', $payload);
        $response->assertCreated();
        $this->assertDatabaseCount('purchases', 1);
        $this->assertDatabaseHas('purchases', [
            'account_id' => $user->account->id,
            'description' => $payload['description'],
            'amount' => 10000,
        ]);
    }

    /** @test */
    public function it_should_not_create_a_new_purchase_with_invalid_data(): void
    {
        $user = User::factory()->create([
            'type' => UserTypes::CUSTOMER->value,
        ]);
        $this->actingAs($user, 'api');
        $response = $this->post('/api/purchases', []);
        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'amount', 'description',
            ]);
    }

    /** @test */
    public function it_should_not_create_if_user_has_no_funds(): void
    {
        $user = User::factory()->create([
            'type' => UserTypes::CUSTOMER->value,
        ]);
        $payload = Purchase::factory()->make([
            'amount' => 100,
        ])->toArray();
        $this->actingAs($user, 'api');
        $response = $this->post('/api/purchases', $payload);
        $response->assertStatus(500)
            ->assertJson([
                'error' => 'Insufficient funds',
            ]);
    }
}
