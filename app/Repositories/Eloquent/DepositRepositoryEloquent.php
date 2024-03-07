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
            ->with('account')
            ->where('status', DepositStatus::PENDING->value)
            ->orderBy('created_at', 'desc')
            ->paginate();
    }

    public function getByAccount(int $accountId, int $status, int $perPage)
    {
        return $this->makeModel()
            ->where('account_id', $accountId)
            ->where('status', $status)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }
}
