<?php

namespace Core\Domain\Events;

use Core\Domain\Entities\Ride;

class RideCanceledEvent implements Event
{
    public function __construct(
        private readonly Ride $ride
    ) {}

    public static function name(): string
    {
        return 'RIDE.CANCELED';
    }

    public function getEntityId(): string
    {
        return $this->ride->getId();
    }

    public function getName(): string
    {
        return RideCanceledEvent::name();
    }

    public function getData(): array
    {
        return [
            'ride_id' => $this->ride->getId(),
        ];
    }
}
