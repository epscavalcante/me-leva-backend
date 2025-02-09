<?php

namespace Core\Domain\ValueObjects;

use Core\Domain\Entities\Ride;
use Core\Domain\Exceptions\RideCannotBeAcceptedException;
use Core\Domain\Exceptions\RideCannotBeFinishedException;
use Core\Domain\Exceptions\RideCannotBeRequestedException;
use Core\Domain\Exceptions\RideCannotBeStartedException;

class FinishedRideStatus extends RideStatus
{
    public function __construct(readonly Ride $ride)
    {
        parent::__construct('completed');
    }

    public function request(): void
    {
        throw new RideCannotBeRequestedException();
    }

    public function accept(): void
    {
        throw new RideCannotBeAcceptedException();
    }

    public function start(): void
    {
        throw new RideCannotBeStartedException();
    }

    public function finish(): void
    {
        throw new RideCannotBeFinishedException();
    }
}
