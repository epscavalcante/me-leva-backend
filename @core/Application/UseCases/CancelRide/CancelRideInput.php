<?php

namespace Core\Application\UseCases\CancelRide;

class CancelRideInput
{
    public function __construct(
        public readonly string $rideId,
    ) {}
}
