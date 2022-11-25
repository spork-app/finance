<?php

namespace Spork\Finance;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Spork\Core\Models\FeatureList;
use Spork\Core\Spork;
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
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->app->bind(PlaidServiceContract::class, PlaidService::class);
        Spork::addFeature('finance', 'LibraryIcon', '/finance/dashboard', 'tool', ['finance', 'financeGroup', 'bill']);

        FeatureList::extend('accounts', fn () => $this->hasMany(Account::class));
        Spork::loadWith(['accounts']);
        $this->mergeConfigFrom(__DIR__ . '/../config/spork.php', 'spork.finance');

        if (config('spork.finance.enabled')) {
            Route::middleware('web')
                ->prefix('finance')
                ->group(__DIR__.'/../routes/api.php');
        }
    }
}
