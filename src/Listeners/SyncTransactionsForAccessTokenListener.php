<?php

namespace Spork\Finance\Listeners;

use App\Models\FeatureList;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spork\Finance\Contracts\Services\PlaidServiceContract;
use Spork\Finance\Services\PlaidService;

class SyncTransactionsForAccessTokenListener implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle($event)
    {
        $featureList = $event->getFeatureList();

        if ($featureList->feature !== FeatureList::FEATURE_FINANCE) {
            return false;
        }

        app(PlaidServiceContract::class)->getTransactions($featureList, now()->subMonth(), now());
    }
}