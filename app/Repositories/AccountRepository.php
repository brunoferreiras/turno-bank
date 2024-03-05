<?php

namespace App\Repositories;

use App\Models\Account;

interface AccountRepository
{
    public function create(array $data): Account;
}
