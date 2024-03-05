<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;

use App\Enums\UserTypes;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        Gate::define(UserTypes::ADMIN->getGate(), function ($user) {
            return $user->type === UserTypes::ADMIN->value;
        });
        Gate::define(UserTypes::CUSTOMER->getGate(), function ($user) {
            return $user->type === UserTypes::CUSTOMER->value;
        });
    }
}
