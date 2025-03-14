<?php

namespace Core\Application\UseCases\DTOs;

class UpdatePositionInput
{
    public function __construct(
        public readonly string $rideId,
        public readonly string $latitude,
        public readonly string $longitude,
    ) {}
}
