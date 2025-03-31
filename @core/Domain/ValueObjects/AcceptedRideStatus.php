<?php

namespace Core\Domain\ValueObjects;

use Core\Domain\Entities\Ride;
use Core\Domain\Exceptions\RideCannotBeAcceptedException;
use Core\Domain\Exceptions\RideCannotBeCanceledException;
use Core\Domain\Exceptions\RideCannotBeFinishedException;
use Core\Domain\Exceptions\RideCannotBeRequestedException;

class AcceptedRideStatus extends RideStatus
{
    public function __construct(readonly Ride $ride)
    {
        parent::__construct('accepted');
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
        $this->ride->setStatus(new InProgressRideStatus($this->ride));
    }

    public function finish(): void
    {
        throw new RideCannotBeFinishedException;
    }

    public function cancel(): void
    {
        $this->ride->setStatus(new CanceledRideStatus($this->ride));
    }
}
