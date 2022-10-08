<?php

namespace Spork\Finance;

use Spork\Core\Spork;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Spork\Core\Models\FeatureList;
use Spork\Finance\Contracts\Services\PlaidServiceContract;
use Spork\Finance\Models\Account;
use Spork\Finance\Services\PlaidService;

class FinanceServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->publishes([
            __DIR__ . '/../database/migrations/' => database_path('migrations'),
        ], 'migrations');
        $this->app->bind(PlaidServiceContract::class, PlaidService::class);
        Spork::addFeature('finance', 'LibraryIcon', '/finance/dashboard', 'tool', ['finance', 'financeGroup', 'bill']);

        FeatureList::extend('accounts', fn () => $this->hasMany(Account::class));
        Spork::loadWith(['accounts']);

        if (config('spork.finance.enabled')) {
            Route::middleware('web')
                ->prefix('finance')
                ->group(__DIR__ . '/../routes/api.php');
        }
    }
}