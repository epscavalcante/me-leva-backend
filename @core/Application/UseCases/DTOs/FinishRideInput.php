<?php

namespace Core\Application\UseCases\DTOs;

class FinishRideInput
{
    public function __construct(
        public readonly string $rideId,
    ) {
    }
}
