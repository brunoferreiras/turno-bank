<?php

namespace App\Repositories;

use App\Models\Account;

interface AccountRepository
{
    public function newAccount(array $data): Account;
}
