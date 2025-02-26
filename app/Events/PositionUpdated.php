<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;

class PositionUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets;

    /**
     * Create a new event instance.
     */
    public function __construct(
        private readonly array $data,
    ) {
    }

    public function broadcastWith(): array
    {
        return $this->data;
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'rides.position_updated';
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): Channel
    {
        return new Channel('rides');
    }
}
