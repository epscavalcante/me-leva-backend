<?php

namespace Core\Domain\ValueObjects;

use Core\Domain\Entities\Ride;
use Core\Domain\Exceptions\RideCannotBeAcceptedException;
use Core\Domain\Exceptions\RideCannotBeRequestedException;
use Core\Domain\Exceptions\RideCannotBeStartedException;

class InProgressRideStatus extends RideStatus
{
    public function __construct(readonly Ride $ride)
    {
        parent::__construct('in_progress');
    }

    public function request(): void
    {
        throw new RideCannotBeRequestedException;
    }

    public function accept(): void
    {
        throw new RideCannotBeAcceptedException;
    }

    public function start(): void
    {
        throw new RideCannotBeStartedException;
    }

    public function finish(): void
    {
        $this->ride->setStatus(new FinishedRideStatus($this->ride));
    }
}
