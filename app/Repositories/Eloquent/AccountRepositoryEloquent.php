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
            ->update(['amount' => $balance]);
    }

    public function getSummaryAccount(int $accountId): array
    {
        $result = $this->makeModel()
            ->select(
                'accounts.id',
                'accounts.amount',
                DB::raw('ROUND(COALESCE(SUM(deposits.amount), 0) / 100, 2) as total_incomes'),
                DB::raw('ROUND(COALESCE(SUM(purchases.amount), 0) / 100, 2) as total_expenses'),
            )
            ->leftJoin('deposits', 'accounts.id', '=', 'deposits.account_id')
            ->leftJoin('purchases', 'accounts.id', '=', 'purchases.account_id')
            ->where('accounts.id', $accountId)
            ->where('deposits.status', DepositStatus::ACCEPTED->value)
            ->groupBy('accounts.id')
            ->first();
        return [
            'balance' => optional($result)->amount ?? 0,
            'total_incomes' => (int) optional($result)->total_incomes ?? 0,
            'total_expenses' => (int) optional($result)->total_expenses ?? 0,
        ];
    }
}
