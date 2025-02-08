<?php

namespace Core\Application\UseCases\DTOs;

class GetAccountInput
{
    public function __construct(
        public readonly string $accountId,
    ) {
    }
}
