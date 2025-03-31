<?php

namespace Core\Domain\Factories;

use Core\Domain\Entities\Ride;
use Core\Domain\Enums\RideStatusEnum;
use Core\Domain\ValueObjects\AcceptedRideStatus;
use Core\Domain\ValueObjects\FinishedRideStatus;
use Core\Domain\ValueObjects\InProgressRideStatus;
use Core\Domain\ValueObjects\RequestedRideStatus;
use Core\Domain\ValueObjects\RideStatus;
use Exception;

class RideStatusFactory
{
    public static function create(string $status, Ride $ride): RideStatus
    {
        if ($status === RideStatusEnum::REQUESTED->value) {
            return new RequestedRideStatus($ride);
        }

        if ($status === RideStatusEnum::ACCEPTED->value) {
            return new AcceptedRideStatus($ride);
        }

        if ($status === RideStatusEnum::STARTED->value) {
            return new InProgressRideStatus($ride);
        }

        if ($status === RideStatusEnum::COMPLETED->value) {
            return new FinishedRideStatus($ride);
        }

        if ($status === RideStatusEnum::CANCELED->value) {
            return new FinishedRideStatus($ride);
        }

        throw new Exception('Invalid ride status');
    }
}
