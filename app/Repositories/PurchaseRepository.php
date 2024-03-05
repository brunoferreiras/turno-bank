<?php

namespace App\Repositories;

use App\Models\Purchase;

interface PurchaseRepository
{
    public function create(array $data): Purchase;
}
