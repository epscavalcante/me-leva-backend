<?php

namespace Core\Domain\Events;

use Core\Domain\Entities\Ride;

class RideFinishedEvent implements Event
{
    public function __construct(
        private readonly Ride $ride
    ) {
    }

    public static function name(): string
    {
        return 'RIDE.COMPLETED';
    }

    public function getName(): string
    {
        return RideFinishedEvent::name();
    }

    public function getData(): array
    {
        return [
            'ride_id' => $this->ride->getId(),
        ];
    }
}
