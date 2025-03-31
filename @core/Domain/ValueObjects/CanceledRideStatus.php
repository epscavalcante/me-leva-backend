<?php

namespace Core\Domain\ValueObjects;

use Core\Domain\Entities\Ride;
use Core\Domain\Enums\RideStatusEnum;
use Core\Domain\Exceptions\RideCannotBeAcceptedException;
use Core\Domain\Exceptions\RideCannotBeCanceledException;
use Core\Domain\Exceptions\RideCannotBeFinishedException;
use Core\Domain\Exceptions\RideCannotBeRequestedException;
use Core\Domain\Exceptions\RideCannotBeStartedException;

class CanceledRideStatus extends RideStatus
{
    public function __construct(readonly Ride $ride)
    {
        parent::__construct(RideStatusEnum::CANCELED->value);
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
        throw new RideCannotBeFinishedException;
    }

    public function cancel(): void
    {
        throw new RideCannotBeCanceledException;
    }
}
