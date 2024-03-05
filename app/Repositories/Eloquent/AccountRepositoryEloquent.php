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

    public function create(array $data): Account
    {
        return $this->model->create($data);
    }
}
