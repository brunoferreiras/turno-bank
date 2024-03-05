<?php

namespace App\Services;

use App\Enums\UserTypes;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class UserService
{
    public function __construct(
        protected UserRepository $userRepository
    ) {
    }

    public function create(array $data): ?User
    {
        try {
            DB::beginTransaction();
            $user = $this->userRepository->register([
                ...$data,
                'type' => UserTypes::CUSTOMER->value,
            ]);
            Log::info('User created successfully', [
                'user' => $user
            ]);
            DB::commit();
            return $user;
        } catch (Throwable $th) {
            DB::rollBack();
            Log::error('Error during create a new user: ', [
                'error' => $th->getMessage()
            ]);
            return null;
        }
    }
}
