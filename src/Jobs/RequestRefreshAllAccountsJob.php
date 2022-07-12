<?php

namespace Spork\Finance\Jobs;

use App\Models\FeatureList;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Spork\Finance\Events\AccountUpdateRequested;

class RequestRefreshAllAccountsJob
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct()
    {
        $features = FeatureList::forFeature(FeatureList::FEATURE_FINANCE)->get();

        $features->each(function ($feature) {
            event(new AccountUpdateRequested($feature));
        });
    }
}