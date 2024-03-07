<?php

namespace App\Services;

use App\Repositories\AccountRepository;
use App\Repositories\TransactionRepository;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class AccountService
{
    const ONE_DAY_IN_SECONDS = 60 * 60 * 24;

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
            'amount' => 0,
        ]);
    }

    public function getSummaryAccount(int $accountId)
    {
        return Cache::remember("summary:account:{$accountId}", self::ONE_DAY_IN_SECONDS, fn () => $this->accountRepository->getSummaryAccount($accountId));
    }

    public function updateBalance(int $accountId, int $balance)
    {
        Cache::forget("summary:account:{$accountId}");
        return $this->accountRepository->updateBalance($accountId, $balance);
    }

    public function getTransactions(int $accountId)
    {
        return $this->transactionRepository->getTransactions($accountId);
    }
}
