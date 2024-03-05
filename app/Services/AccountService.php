<?php

namespace App\Services;

use App\Repositories\AccountRepository;
use Illuminate\Support\Facades\Log;

class AccountService
{
    public function __construct(
        protected AccountRepository $accountRepository
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
}
