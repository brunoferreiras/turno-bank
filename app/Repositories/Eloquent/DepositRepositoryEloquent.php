<?php

namespace App\Repositories\Eloquent;

use App\Enums\DepositStatus;
use App\Models\Deposit;
use App\Repositories\DepositRepository;

class DepositRepositoryEloquent extends BaseRepositoryEloquent implements DepositRepository
{
    public function model()
    {
        return Deposit::class;
    }

    public function create(array $data): Deposit
    {
        return $this->makeModel()->create($data);
    }

    public function getPendings()
    {
        return $this->makeModel()
            ->with('user')
            ->where('status', DepositStatus::PENDING->value)
            ->orderBy('created_at', 'desc')
            ->paginate();
    }
}
