<?php

namespace Core\Domain\Entities;

use Core\Domain\Exceptions\RideCannotBeAcceptedException;
use Core\Domain\ValueObjects\Position;
use Core\Domain\ValueObjects\Uuid;

class Ride
{
    private Uuid $rideId;

    private Uuid $passengerId;

    private ?Uuid $driverId;

    private Position $from;

    private Position $to;

    private string $status;

    public function __construct(
        string $rideId,
        string $passengerId,
        string $status,
        string $fromLatitude,
        string $fromLongitude,
        string $toLatitude,
        string $toLongitude,
        ?string $driverId = null,
    ) {
        $this->rideId = new Uuid($rideId);
        $this->driverId = $driverId ? new Uuid($driverId) : null;
        $this->passengerId = new Uuid($passengerId);
        $this->from = new Position($fromLatitude, $fromLongitude);
        $this->to = new Position($toLatitude, $toLongitude);
        $this->status = $status;
    }

    public static function create(
        string $passengerId,
        string $fromLatitude,
        string $fromLongitude,
        string $toLatitude,
        string $toLongitude,
    ) {
        $rideId = Uuid::create();
        $status = 'requested';

        return new Ride(
            rideId: $rideId,
            passengerId: $passengerId,
            status: $status,
            fromLatitude: $fromLatitude,
            fromLongitude: $fromLongitude,
            toLatitude: $toLatitude,
            toLongitude: $toLongitude,
        );
    }

    public function getId(): string
    {
        return $this->rideId->getValue();
    }

    public function getPassengerId(): string
    {
        return $this->passengerId->getValue();
    }

    public function getDriverId(): ?string
    {
        return $this->driverId?->getValue();
    }

    public function getFromLatitude(): string
    {
        return $this->from->getLatitude();
    }

    public function getFromLongitude(): string
    {
        return $this->from->getLongitude();
    }

    public function getToLatitude(): string
    {
        return $this->to->getLatitude();
    }

    public function getToLongitude(): string
    {
        return $this->to->getLongitude();
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function isRequested(): bool
    {
        return $this->status === 'requested';
    }

    public function canBeAccepted(): bool
    {
        return $this->isRequested();
    }

    public function accept(string $driverId)
    {
        if (! $this->canBeAccepted()) {
            throw new RideCannotBeAcceptedException();
        }

        $this->status = 'accepted';
        $this->driverId = new Uuid($driverId);
    }
}
