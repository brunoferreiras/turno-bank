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

    public function getTransactions(int $accountId)
    {
        return $this->makeModel()
            ->where('account_id', $accountId)
            ->orderBy('created_at', 'desc')
            ->paginate();
    }
}
