<?php

namespace App\Repositories;

use App\Enums\DepositStatus;
use App\Models\Deposit;

interface DepositRepository extends BaseRepository
{
    public function create(array $data): Deposit;

    public function getPendings();

    public function updateStatus(int $deposit, DepositStatus $status): bool;
}
