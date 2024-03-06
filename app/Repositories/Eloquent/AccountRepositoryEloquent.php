<?php

namespace App\Repositories\Eloquent;

use App\Models\Account;
use App\Repositories\AccountRepository;

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
}
