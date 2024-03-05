<?php

namespace App\Observers;

use App\Enums\UserTypes;
use App\Models\User;
use App\Services\AccountService;

class UserObserver
{
    public function created(User $user)
    {
        $type = UserTypes::tryFrom($user->type);
        if ($type === UserTypes::CUSTOMER) {
            $service = resolve(AccountService::class);
            $service->create($user->id);
        }
    }
}
