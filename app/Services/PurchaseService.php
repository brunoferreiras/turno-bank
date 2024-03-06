<?php

namespace App\Services;

use App\Helpers\CurrencyHelper;
use App\Models\Purchase;
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

    public function create(int $userId, array $data): array
    {
        try {
            DB::beginTransaction();
            $amount = CurrencyHelper::formatToDatabase($data['amount']);
            $balance = $this->accountRepository->findOne($userId);
            if ($balance->balance < $amount) {
                Log::error('Insufficient funds', [
                    'balance' => $balance->balance,
                    'amount' => $amount
                ]);
                throw new \Exception('Insufficient funds');
            }
            $purchase = $this->purchaseRepository->register([
                'user_id' => $userId,
                'amount' => $amount,
                'description' => $data['description']
            ]);
            $this->accountRepository->updateBalance($balance->id, $balance->balance - $data['amount']);
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
