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

class AccountControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function it_should_return_account_summary_empty(): void
    {
        $user = User::factory()->create([
            'type' => UserTypes::CUSTOMER->value,
        ]);
        $this->actingAs($user, 'api');
        $response = $this->getJson('/api/accounts/balance');
        $response->assertOk()
            ->assertJson([
                'balance' => 0,
                'total_incomes' => 0,
                'total_expenses' => 0,
            ]);
    }

    /** @test */
    public function it_should_return_account_summary_with_pending_deposits(): void
    {
        $user = User::factory()->create([
            'type' => UserTypes::CUSTOMER->value,
        ]);
        $this->actingAs($user, 'api');
        Deposit::factory()->create([
            'user_id' => $user->id,
            'status' => DepositStatus::PENDING->value,
            'amount' => 100,
        ]);
        $response = $this->getJson('/api/accounts/balance');
        $response->assertOk()
            ->assertJson([
                'balance' => 0,
                'total_incomes' => 0,
                'total_expenses' => 0,
            ]);
    }

    /** @test */
    public function it_should_return_account_summary_with_accepted_deposits(): void
    {
        $user = User::factory()->create([
            'type' => UserTypes::CUSTOMER->value,
        ]);
        $this->actingAs($user, 'api');
        Deposit::factory()->create([
            'user_id' => $user->id,
            'status' => DepositStatus::ACCEPTED->value,
            'amount' => 1000,
        ]);
        $account = $user->account;
        $account->update(['balance' => 1000]);
        $response = $this->getJson('/api/accounts/balance');
        $response->assertOk()
            ->assertJson([
                'balance' => 1000,
                'total_incomes' => 1000,
                'total_expenses' => 0,
            ]);
    }

    /** @test */
    public function it_should_return_account_summary_with_rejected_deposits(): void
    {
        $user = User::factory()->create([
            'type' => UserTypes::CUSTOMER->value,
        ]);
        $this->actingAs($user, 'api');
        Deposit::factory()->create([
            'user_id' => $user->id,
            'status' => DepositStatus::REJECTED->value,
            'amount' => 1000,
        ]);
        $response = $this->getJson('/api/accounts/balance');
        $response->assertOk()
            ->assertJson([
                'balance' => 0,
                'total_incomes' => 0,
                'total_expenses' => 0,
            ]);
    }

    /** @test */
    public function it_should_return_account_summary_with_purchases(): void
    {
        $user = User::factory()->create([
            'type' => UserTypes::CUSTOMER->value,
        ]);
        $this->actingAs($user, 'api');
        Deposit::factory()->create([
            'user_id' => $user->id,
            'status' => DepositStatus::ACCEPTED->value,
            'amount' => 10000,
        ]);
        Purchase::factory()->create([
            'user_id' => $user->id,
            'amount' => 5000,
        ]);
        $account = $user->account;
        $account->update(['balance' => 5000]);
        $response = $this->getJson('/api/accounts/balance');
        $response->assertOk()
            ->assertJson([
                'balance' => 5000,
                'total_incomes' => 10000,
                'total_expenses' => 5000,
            ]);
    }

    /** @test */
    public function it_should_return_all_transactions(): void
    {
        $user = User::factory()->create([
            'type' => UserTypes::CUSTOMER->value,
        ]);
        $this->actingAs($user, 'api');
        $deposit = Deposit::factory()->create([
            'user_id' => $user->id,
            'status' => DepositStatus::ACCEPTED->value,
            'amount' => 10000,
        ]);
        $purchase = Purchase::factory()->create([
            'user_id' => $user->id,
            'amount' => 5000,
        ]);
        $response = $this->getJson('/api/accounts/transactions');
        $response->assertOk()
            ->assertJsonCount(2)
            ->assertSee([
                'id' => $deposit->id,
                'amount' => 10000,
                'type' => 'income',
            ])
            ->assertSee([
                'id' => $purchase->id,
                'amount' => 5000,
                'type' => 'expense',
            ]);
    }
}
