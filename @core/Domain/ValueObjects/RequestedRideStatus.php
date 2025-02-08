<?php

namespace Core\Domain\ValueObjects;

use Core\Domain\Entities\Ride;
use Core\Domain\Exceptions\RideCannotBeRequestedException;
use Core\Domain\Exceptions\RideCannotBeStartedException;

class RequestedRideStatus extends RideStatus
{
    public function __construct(readonly Ride $ride)
    {
        parent::__construct('requested');
    }

    public function request(): void
    {
        throw new RideCannotBeRequestedException();
    }

    public function accept(): void
    {
        $this->ride->setStatus(new AcceptedRideStatus($this->ride));
    }

    public function start(): void
    {
        throw new RideCannotBeStartedException();
    }
}
