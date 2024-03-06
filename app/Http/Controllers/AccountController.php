<?php

namespace App\Http\Controllers;

use App\Services\AccountService;

class AccountController
{
    public function __construct(
        protected AccountService $accountService
    ) {
    }

    public function balance()
    {
        $userId = auth('api')->id();
        $accounts = $this->accountService->getSummaryAccount($userId);
        return response()->json($accounts);
    }

    public function transactions()
    {
        $userId = auth('api')->id();
        $accounts = $this->accountService->getTransactions($userId);
        return response()->json($accounts);
    }
}
