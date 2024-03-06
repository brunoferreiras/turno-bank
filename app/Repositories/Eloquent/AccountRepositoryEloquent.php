<?php

namespace App\Repositories\Eloquent;

use App\Enums\DepositStatus;
use App\Models\Account;
use App\Repositories\AccountRepository;
use Illuminate\Support\Facades\DB;

class AccountRepositoryEloquent extends BaseRepositoryEloquent implements AccountRepository
{
    public function model()
    {
        return Account::class;
    }

    public function newAccount(array $data): Account
    {
        return $this->create($data);
    }

    public function updateBalance(int $account, int $balance): bool
    {
        return $this->makeModel()
            ->where('id', $account)
            ->update(['balance' => $balance]);
    }

    public function getSummaryAccount(int $userId): array
    {
        $result = $this->makeModel()
            ->select(
                'accounts.id',
                'accounts.balance',
                DB::raw('COALESCE(SUM(deposits.amount), 0) as total_incomes'),
                DB::raw('COALESCE(SUM(purchases.amount), 0) as total_expenses'),
            )
            ->leftJoin('deposits', 'accounts.user_id', '=', 'deposits.user_id')
            ->leftJoin('purchases', 'accounts.user_id', '=', 'purchases.user_id')
            ->where('accounts.user_id', $userId)
            ->where('deposits.status', DepositStatus::ACCEPTED->value)
            ->groupBy('accounts.id')
            ->get()
            ->first();
        return [
            'balance' => optional($result)->balance ?? 0,
            'total_incomes' => (int) optional($result)->total_incomes ?? 0,
            'total_expenses' => (int) optional($result)->total_expenses ?? 0,
        ];
    }
}
