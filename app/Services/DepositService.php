<?php

namespace App\Services;

use App\Enums\DepositStatus;
use App\Helpers\CurrencyHelper;
use App\Repositories\AccountRepository;
use App\Repositories\DepositRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class DepositService
{
    public function __construct(
        protected DepositRepository $depositRepository,
        protected AccountRepository $accountRepository,
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

    public function getPendings()
    {
        return $this->depositRepository->getPendings();
    }

    public function updateStatus(int $userId, float $deposit, string $status): bool
    {
        try {
            DB::beginTransaction();
            $deposit = $this->depositRepository->findOne($deposit, $status);
            if (!$deposit) {
                throw new \Exception('Deposit not found');
            }
            $depositStatus = DepositStatus::fromStatus($status);
            $amount = CurrencyHelper::formatToDatabase($deposit->amount);
            $this->updateBalance($deposit->user_id, $amount, $depositStatus);
            Log::info('Deposit status updated successfully', [
                'deposit' => $deposit
            ]);
            $this->depositRepository->update($deposit->id, [
                'status' => $depositStatus,
                'approved_by' => $userId,
            ]);
            DB::commit();
            return !is_null($deposit);
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('Error updating deposit status', [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    private function updateBalance(int $userId, float $amount, DepositStatus $status): void
    {
        $account = $this->accountRepository->findWhere([
            'user_id' => $userId
        ], ['id'])->first();
        $this->accountRepository->updateBalance($account->id, CurrencyHelper::formatToDatabase($amount), $status);
    }
}
