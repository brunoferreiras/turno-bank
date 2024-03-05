<?php

namespace App\Services;

use App\Helpers\CurrencyHelper;
use App\Repositories\DepositRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class DepositService
{
    public function __construct(
        protected DepositRepository $depositRepository
    ) {
    }

    public function register(int $userId, array $data): bool
    {
        try {
            DB::beginTransaction();
            $deposit = $this->depositRepository->create([
                'user_id' => $userId,
                'description' => $data['description'],
                'image' => $data['image'],
                'amount' => CurrencyHelper::formatToDatabase($data['amount']),
            ]);
            Log::info('Deposit created successfully', [
                'deposit' => $deposit
            ]);
            DB::commit();
            return !is_null($deposit);
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('Error creating deposit', [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
}
