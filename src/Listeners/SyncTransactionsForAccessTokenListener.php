<?php

namespace Spork\Finance\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Spork\Finance\Contracts\Services\PlaidServiceContract;

class SyncTransactionsForAccessTokenListener implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle($event)
    {
        $featureList = $event->getFeatureList();

        if ($featureList->feature !== 'finance') {
            return false;
        }

        app(PlaidServiceContract::class)->getTransactions($featureList, now()->subMonth(), now());
    }
}
