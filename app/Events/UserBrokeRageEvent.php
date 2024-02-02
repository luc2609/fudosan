<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

class UserBrokeRageEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $companyId;
    public $userBrokerage;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($userBrokerage, $companyId)
    {
        $this->userBrokerage = $userBrokerage;
        $this->companyId = $companyId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('brokerage-user-' . $this->companyId);
    }

    public function broadcastWith()
    {
        return [
            'userBrokerage' => $this->userBrokerage,
            'companyId' => $this->companyId,
            'time' => Carbon::now()
        ];
    }
}
