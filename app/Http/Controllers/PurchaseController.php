<?php

namespace App\Http\Controllers;

use App\Services\PurchaseService;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    public function __construct(
        protected PurchaseService $purchaseService
    ) {
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric',
            'description' => 'required|string',
        ]);
        $user = auth('api')->user();
        $accountId = $user->account->id;
        $response = $this->purchaseService->create($accountId, $validated);
        if (optional($response)['success'] === false) {
            return response()->json([
                'error' => $response['error']
            ], 500);
        }
        return response()->json($response['purchase'], 201);
    }
}
