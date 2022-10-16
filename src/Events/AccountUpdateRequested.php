<?php

namespace Spork\Finance\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Spork\Core\Models\FeatureList;

class AccountUpdateRequested
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public FeatureList $featureList
    ) {
    }

    public function broadcastOn()
    {
        return new PrivateChannel($this->featuerList->feature.'.'.$this->featureList->id);
    }

    public function getFeatureList()
    {
        return $this->featureList;
    }
}
