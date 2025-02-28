<?php

namespace Core\Domain\Events;

use Core\Domain\Entities\Ride;

class RideRequestedEvent implements Event
{
    public function __construct(
        private readonly Ride $ride
    ) {
    }

    public static function name(): string
    {
        return 'RIDE.REQUESTED';
    }

    public function getEntityId(): string
    {
        return $this->ride->getId();
    }

    public function getName(): string
    {
        return RideRequestedEvent::name();
    }

    public function getData(): array
    {
        return [
            'ride_id' => $this->ride->getId(),
        ];
    }
}
