<?php

namespace Core\Application\UseCases\DTOs;

class GetRidesOutput
{
    public function __construct(
        public readonly ?array $items = [],
        public readonly ?int $total = 0,
    ) {
    }
}
