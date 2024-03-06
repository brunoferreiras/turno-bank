<?php

namespace App\Repositories;

use App\Models\Account;

interface AccountRepository extends BaseRepository
{
    public function newAccount(array $data): Account;

    public function updateBalance(int $accountId, int $balance): bool;
}
