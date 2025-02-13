<?php

namespace Core\Domain\Entities;

use Core\Domain\Events\EventDispatcher;
use Core\Domain\Events\RideFinishedEvent;
use Core\Domain\Factories\RideStatusFactory;
use Core\Domain\Services\DistanceCalculator;
use Core\Domain\ValueObjects\Coordinate;
use Core\Domain\ValueObjects\RideStatus;
use Core\Domain\ValueObjects\Uuid;

class Ride extends EventDispatcher
{
    private Uuid $rideId;

    private Uuid $passengerId;

    private ?Uuid $driverId;

    private Coordinate $from;

    private int $fare;

    private int $distance;

    private Coordinate $to;

    private RideStatus $status;

    public function __construct(
        string $rideId,
        string $passengerId,
        string $status,
        string $fromLatitude,
        string $fromLongitude,
        string $toLatitude,
        string $toLongitude,
        ?string $driverId = null,
        int $fare = 0,
        int $distance = 0,
    ) {
        parent::__construct();

        $this->rideId = new Uuid($rideId);
        $this->driverId = $driverId ? new Uuid($driverId) : null;
        $this->passengerId = new Uuid($passengerId);
        $this->from = new Coordinate($fromLatitude, $fromLongitude);
        $this->to = new Coordinate($toLatitude, $toLongitude);
        $this->status = RideStatusFactory::create($status, $this);
        $this->distance = $distance;
        $this->fare = $fare;
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
        $driverId = null;
        $distance = 0;
        $fare = 0;

        return new Ride(
            rideId: $rideId,
            passengerId: $passengerId,
            driverId: $driverId,
            status: $status,
            fromLatitude: $fromLatitude,
            fromLongitude: $fromLongitude,
            toLatitude: $toLatitude,
            toLongitude: $toLongitude,
            distance: $distance,
            fare: $fare
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
        return $this->status->getValue();
    }

    public function getFare(): int
    {
        return $this->fare;
    }

    public function getDistance(): int
    {
        return $this->distance;
    }

    public function setStatus(RideStatus $status): void
    {
        $this->status = $status;
    }

    public function accept(string $driverId)
    {
        $this->status->accept();
        $this->driverId = new Uuid($driverId);
    }

    public function start()
    {
        $this->status->start();
    }

    /**
     * @param  Position[]  $positions
     */
    public function finish(array $positions)
    {

        $this->status->finish();
        $distance = DistanceCalculator::calculateByPositions($positions);
        $this->distance = $distance;
        $this->fare = $distance * 2.1;
        $eventRideCompleted = new RideFinishedEvent($this);
        $this->dispatch($eventRideCompleted);
    }

    public function isCompleted(): bool
    {
        return $this->getStatus() === 'completed';
    }
}
