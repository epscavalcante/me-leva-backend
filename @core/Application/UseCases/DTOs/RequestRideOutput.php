<?php

namespace Core\Application\UseCases\DTOs;

class RequestRideOutput
{
    public function __construct(
        public readonly string $rideId,
    ) {
    }
}
