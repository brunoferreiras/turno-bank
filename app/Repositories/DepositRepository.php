<?php

namespace App\Repositories;

use App\Models\Deposit;

interface DepositRepository extends BaseRepository
{
    public function create(array $data): Deposit;

    public function getPendings();
}
