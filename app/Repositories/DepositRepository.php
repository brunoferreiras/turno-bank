<?php

namespace App\Repositories;

use App\Models\Deposit;

interface DepositRepository
{
    public function create(array $data): Deposit;
}
