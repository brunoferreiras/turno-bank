<?php

namespace App\Repositories\Eloquent;

use App\Models\Purchase;
use App\Repositories\PurchaseRepository;

class PurchaseRepositoryEloquent extends BaseRepositoryEloquent implements PurchaseRepository
{
    public function model()
    {
        return Purchase::class;
    }

    public function register(array $data): Purchase
    {
        return $this->create($data);
    }

    public function getByAccount(int $accountId, int $perPage)
    {
        return $this->makeModel()
            ->where('account_id', $accountId)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }
}
