<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

class DivisionContractEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $contractDivision;
    public $companyId;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($contractDivision, $companyId)
    {
        $this->contractDivision = $contractDivision;
        $this->companyId = $companyId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('contract-division-' . $this->companyId);
    }

    public function broadcastWith()
    {
        return [
            'contractDivision' => $this->contractDivision,
            'companyId' => $this->companyId,
            'time' => Carbon::now()
        ];
    }
}
