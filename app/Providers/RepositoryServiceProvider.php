<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->bind(
            \App\Repositories\UserRepository::class,
            \App\Repositories\Eloquent\UserRepositoryEloquent::class
        );
        $this->app->bind(
            \App\Repositories\AccountRepository::class,
            \App\Repositories\Eloquent\AccountRepositoryEloquent::class
        );
        $this->app->bind(
            \App\Repositories\DepositRepository::class,
            \App\Repositories\Eloquent\DepositRepositoryEloquent::class
        );
        $this->app->bind(
            \App\Repositories\PurchaseRepository::class,
            \App\Repositories\Eloquent\PurchaseRepositoryEloquent::class
        );
        $this->app->bind(
            \App\Repositories\TransactionRepository::class,
            \App\Repositories\Eloquent\TransactionRepositoryEloquent::class
        );
    }
}
