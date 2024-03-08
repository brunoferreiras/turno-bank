<?php

namespace App\Repositories;

use App\Models\Purchase;

interface PurchaseRepository extends BaseRepository
{
    public function register(array $data): Purchase;

    public function getByAccount(int $accountId, int $perPage);
}
