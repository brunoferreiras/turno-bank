<?php

namespace App\Http\Controllers;

use App\Enums\DepositStatus;
use App\Services\DepositService;
use Illuminate\Http\Request;

class DepositController extends Controller
{
    public function __construct(
        protected DepositService $depositService
    ) {
    }

    public function index(Request $request)
    {
        $validated = $request->validate([
            'per_page' => 'nullable|integer',
            'status' => 'nullable|in:pending,accepted,rejected',
        ]);
        $user = auth('api')->user();
        $accountId = $user->account->id;
        $status = DepositStatus::fromStatus($validated['status'] ?? 0) ?? DepositStatus::PENDING;
        $deposits = $this->depositService->getByAccount($accountId, $status->value, $validated['per_page'] ?? 15);
        return response()->json($deposits);
    }

    public function newDeposit(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric',
            'description' => 'required|string',
            'image' => 'required|image',
        ]);
        $savedImage = $validated['image']->store('deposits', 's3');
        $user = auth('api')->user();
        $accountId = $user->account->id;
        $created = $this->depositService->register($accountId, [
            ...$validated,
            'status' => DepositStatus::PENDING,
            'image' => $savedImage,
        ]);
        if ($created) {
            return response()->json([
                'message' => 'Deposit created successfully',
            ], 201);
        }
        return response()->json([
            'message' => 'Deposit could not be created',
        ], 500);
    }

    public function pendings()
    {
        $deposits = $this->depositService->getPendings();
        return response()->json($deposits);
    }

    public function updateStatus(Request $request, int $deposit)
    {
        $validated = $request->validate([
            'status' => 'required|in:accepted,rejected',
        ]);
        $userId = auth('api')->id();
        $updated = $this->depositService->updateStatus($userId, $deposit, $validated['status']);
        if ($updated) {
            return response()->json([
                'message' => 'Deposit status updated successfully',
            ]);
        }
        return response()->json([
            'message' => 'Deposit status could not be updated',
        ], 500);
    }
}
