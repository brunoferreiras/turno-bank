<?php

namespace App\Observers;

use App\Enums\DepositStatus;
use App\Helpers\CurrencyHelper;
use App\Models\Deposit;
use App\Services\AccountService;

class DepositObserver
{
    public function updated(Deposit $deposit)
    {
        $type = DepositStatus::tryFrom($deposit->status);
        if ($type === DepositStatus::ACCEPTED) {
            $service = resolve(AccountService::class);
            $service->updateBalance($deposit->account_id, CurrencyHelper::formatToDatabase($deposit->amount));
        }
    }
}
