<?php

namespace App\Repositories;

interface TransactionRepository extends BaseRepository
{
    public function getTransactions(int $userId);
}
