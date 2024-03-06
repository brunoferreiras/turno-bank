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
            ->where('status', DepositStatus::PENDING)
            ->orderBy('created_at', 'desc')
            ->paginate();
    }

    public function updateStatus(int $deposit, DepositStatus $status): bool
    {
        return $this->makeModel()
            ->where('id', $deposit)
            ->update(['status' => $status]);
    }
}
