<?php

namespace Core\Domain\Factories;

use Core\Domain\Entities\Ride;
use Core\Domain\ValueObjects\AcceptedRideStatus;
use Core\Domain\ValueObjects\InProgressRideStatus;
use Core\Domain\ValueObjects\RequestedRideStatus;
use Core\Domain\ValueObjects\RideStatus;
use Exception;

class RideStatusFactory
{
    public static function create(string $status, Ride $ride): RideStatus
    {
        if ($status === 'requested') {
            return new RequestedRideStatus($ride);
        }

        if ($status === 'accepted') {
            return new AcceptedRideStatus($ride);
        }

        if ($status === 'in_progress') {
            return new InProgressRideStatus($ride);
        }

        throw new Exception('Invalid ride status');
    }
}
