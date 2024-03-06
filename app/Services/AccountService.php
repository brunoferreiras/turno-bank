<?php

namespace App\Services;

use App\Models\Transaction;
use App\Repositories\AccountRepository;
use App\Repositories\TransactionRepository;
use Illuminate\Support\Facades\Log;

class AccountService
{
    public function __construct(
        protected AccountRepository $accountRepository,
        protected TransactionRepository $transactionRepository
    ) {
    }

    public function create(int $userId)
    {
        Log::info('Creating account for user', [
            'user_id' => $userId
        ]);
        return $this->accountRepository->newAccount([
            'user_id' => $userId,
            'balance' => 0,
        ]);
    }

    public function getSummaryAccount(int $userId)
    {
        return $this->accountRepository->getSummaryAccount($userId);
    }

    public function updateBalance(int $accountId, int $balance)
    {
        return $this->accountRepository->updateBalance($accountId, $balance);
    }

    public function getTransactions(int $userId)
    {
        return $this->transactionRepository->getTransactions($userId);
    }
}
