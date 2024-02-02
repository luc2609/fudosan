<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

class DivisionBrokeRageEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $companyId;
    public $divisionBrokerage;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($divisionBrokerage, $companyId)
    {
        $this->divisionBrokerage = $divisionBrokerage;
        $this->companyId = $companyId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('brokerage-division-' . $this->companyId);
    }

    public function broadcastWith()
    {
        return [
            'divisionBrokerage' => $this->divisionBrokerage,
            'companyId' => $this->companyId,
            'time' => Carbon::now()
        ];
    }
}
