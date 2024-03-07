<?php

namespace App\Services;

use App\Enums\DepositStatus;
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

    public function register(int $accountId, array $data): bool
    {
        try {
            DB::beginTransaction();
            $deposit = $this->depositRepository->create([
                'account_id' => $accountId,
                'description' => $data['description'],
                'image' => $data['image'],
                'amount' => $data['amount'],
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

    public function getByAccount(int $accountId, int $status, int $perPage)
    {
        return $this->depositRepository->getByAccount($accountId, $status, $perPage);
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
            $this->depositRepository->update($deposit->id, [
                'status' => $depositStatus->value,
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
}
