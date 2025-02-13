<?php

namespace Core\Domain\Entities;

use Core\Domain\ValueObjects\Coordinate;
use Core\Domain\ValueObjects\Uuid;

class Position extends Entity
{
    private Uuid $positionId;

    private Uuid $rideId;

    private Coordinate $coordinate;

    public function __construct(
        string $positionId,
        string $rideId,
        string $latitude,
        string $longitude,
    ) {
        $this->positionId = new Uuid($positionId);
        $this->rideId = new Uuid($rideId);
        $this->coordinate = new Coordinate($latitude, $longitude);
    }

    public static function create(
        string $rideId,
        string $latitude,
        string $longitude,
    ) {
        $positionId = Uuid::create();

        return new Position(
            positionId: $positionId,
            rideId: $rideId,
            latitude: $latitude,
            longitude: $longitude,
        );
    }

    public function getId(): string
    {
        return $this->positionId->getValue();
    }

    public function getRideId(): string
    {
        return $this->rideId->getValue();
    }

    public function getCoordinate(): Coordinate
    {
        return $this->coordinate;
    }

    public function getLatitude(): string
    {
        return $this->coordinate->getLatitude();
    }

    public function getLongitude(): string
    {
        return $this->coordinate->getLongitude();
    }
}
