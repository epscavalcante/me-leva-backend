<?php

namespace Core\Domain\Events;

use Core\Domain\Entities\Ride;

class RideAcceptedEvent implements Event
{
    public function __construct(
        private readonly Ride $ride
    ) {}

    public static function name(): string
    {
        return 'RIDE.ACCEPTED';
    }

    public function getEntityId(): string
    {
        return $this->ride->getId();
    }

    public function getName(): string
    {
        return RideAcceptedEvent::name();
    }

    public function getData(): array
    {
        return [
            'ride_id' => $this->ride->getId(),
            'driver_id' => $this->ride->getDriverId(),
        ];
    }
}
