<?php

namespace App\Repositories\Eloquent;

use App\Models\Transaction;
use App\Repositories\TransactionRepository;

class TransactionRepositoryEloquent extends BaseRepositoryEloquent implements TransactionRepository
{
    public function model()
    {
        return Transaction::class;
    }

    public function getTransactions(int $userId)
    {
        return $this->makeModel()
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
