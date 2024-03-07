<?php

namespace App\Observers;

use App\Helpers\CurrencyHelper;
use App\Models\Purchase;
use App\Services\AccountService;

class PurchaseObserver
{
    public function created(Purchase $purchase)
    {
        $service = resolve(AccountService::class);
        $accountAmount = $purchase->account->amount;
        $service->updateBalance($purchase->account_id, CurrencyHelper::formatToDatabase($accountAmount - $purchase->amount));
    }
}
