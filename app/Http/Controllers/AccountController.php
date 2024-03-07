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
        $user = auth('api')->user();
        $accountId = $user->account->id;
        $accounts = $this->accountService->getSummaryAccount($accountId);
        return response()->json($accounts);
    }

    public function transactions()
    {
        $user = auth('api')->user();
        $accountId = $user->account->id;
        $accounts = $this->accountService->getTransactions($accountId);
        return response()->json($accounts);
    }
}
