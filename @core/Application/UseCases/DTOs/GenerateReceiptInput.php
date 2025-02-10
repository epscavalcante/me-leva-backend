<?php

namespace Core\Application\UseCases\DTOs;

class GenerateReceiptInput
{
    public function __construct(
        public readonly string $rideId,
    ) {
    }
}
