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
}
