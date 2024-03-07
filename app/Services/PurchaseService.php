<?php

namespace App\Services;

use App\Repositories\AccountRepository;
use App\Repositories\PurchaseRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class PurchaseService
{
    public function __construct(
        protected PurchaseRepository $purchaseRepository,
        protected AccountRepository $accountRepository,
    ) {
    }

    public function create(int $accountId, array $data): array
    {
        try {
            DB::beginTransaction();
            $account = $this->accountRepository->findOne($accountId);
            $amount = $data['amount'];
            if ($account->amount < $amount) {
                Log::error('Insufficient funds', [
                    'accountBalance' => $account->amount,
                    'amount' => $amount
                ]);
                throw new \Exception('Insufficient funds');
            }
            $purchase = $this->purchaseRepository->register([
                'account_id' => $accountId,
                'amount' => $amount,
                'description' => $data['description']
            ]);
            DB::commit();
            Log::info('Purchase created successfully', [
                'purchase' => $purchase
            ]);
            return [
                'success' => true,
                'purchase' => $purchase
            ];
        } catch (Throwable $th) {
            DB::rollBack();
            Log::error('Error during create a new purchase: ', [
                'error' => $th->getMessage()
            ]);
            return [
                'success' => false,
                'error' => $th->getMessage()
            ];
        }
    }
}
