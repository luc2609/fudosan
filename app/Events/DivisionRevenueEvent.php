<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

class DivisionRevenueEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $companyId;
    public $divisionRevenue;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($divisionRevenue, $companyId)
    {
        $this->divisionRevenue = $divisionRevenue;
        $this->companyId = $companyId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('revenue-division-' . $this->companyId);
    }

    public function broadcastWith()
    {
        return [
            'divisionRevenue' => $this->divisionRevenue,
            'companyId' => $this->companyId,
            'time' => Carbon::now()
        ];
    }
}
