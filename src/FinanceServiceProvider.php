<?php

namespace Spork\Finance;

use App\Spork;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Spork\Finance\Contracts\Services\PlaidServiceContract;
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
        Spork::addFeature('finance', 'LibraryIcon', '/finance/dashboard', 'tool');
        if (config('spork.finance.enabled')) {
            Route::middleware('web')
                ->prefix('finance')
                ->group(__DIR__ . '/../routes/api.php');
        }
    }
}