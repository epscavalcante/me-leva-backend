<?php

namespace Core\Domain\Events;

use Core\Domain\Entities\Position;

class RidePositionUpdatedEvent implements Event
{
    public function __construct(
        private readonly Position $position
    ) {
    }

    public static function name(): string
    {
        return 'RIDE.POSITION_UPDATED';
    }

    public function getEntityId(): string
    {
        return $this->position->getRideId();
    }

    public function getName(): string
    {
        return RidePositionUpdatedEvent::name();
    }

    public function getData(): array
    {
        return [
            'ride_id' => $this->position->getRideId(),
            'latitude' => $this->position->getLatitude(),
            'longitude' => $this->position->getLongitude(),
        ];
    }
}
