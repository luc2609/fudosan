<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

class UserContractEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $companyId;
    public $userContract;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($userContract, $companyId)
    {
        $this->userContract = $userContract;
        $this->companyId = $companyId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('contract-user-' . $this->companyId);
    }

    public function broadcastWith()
    {
        return [
            'userContract' => $this->userContract,
            'companyId' => $this->companyId,
            'time' => Carbon::now()
        ];
    }
}
