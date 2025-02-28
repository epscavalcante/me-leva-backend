<?php

namespace Core\Domain\Events;

use Core\Domain\Entities\Ride;

class RideStartedEvent implements Event
{
    public function __construct(
        private readonly Ride $ride
    ) {
    }

    public static function name(): string
    {
        return 'RIDE.STARTED';
    }

    public function getEntityId(): string
    {
        return $this->ride->getId();
    }

    public function getName(): string
    {
        return RideStartedEvent::name();
    }

    public function getData(): array
    {
        return [
            'ride_id' => $this->ride->getId(),
        ];
    }
}
