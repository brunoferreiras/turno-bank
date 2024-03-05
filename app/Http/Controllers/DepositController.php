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

    public function newDeposit(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric',
            'description' => 'required|string',
            'image' => 'required|image',
        ]);
        $savedImage = $validated['image']->store('deposits', 'public');
        $user = auth('api')->user();
        $created = $this->depositService->register($user->id, [
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
}
