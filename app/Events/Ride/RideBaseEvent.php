<?php

namespace App\Events\Ride;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;

abstract class RideBaseEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets;

    public function __construct(
        private readonly string $rideId,
        private readonly string $eventName,
        private readonly array $eventData
    ) {}

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): Channel
    {
        $channelName = "rides.{$this->rideId}";

        return new Channel($channelName);
    }

    /*
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return $this->eventName;
    }

    public function broadcastWith(): array
    {
        return $this->eventData;
    }
}
