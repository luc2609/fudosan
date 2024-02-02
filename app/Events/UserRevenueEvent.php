<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

class UserRevenueEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $companyId;
    public $userRevenue;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($userRevenue, $companyId)
    {
        $this->userRevenue = $userRevenue;
        $this->companyId = $companyId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('revenue-user-' . $this->companyId);
    }

    public function broadcastWith()
    {
        return [
            'userRevenue' => $this->userRevenue,
            'companyId' => $this->companyId,
            'time' => Carbon::now()
        ];
    }
}
