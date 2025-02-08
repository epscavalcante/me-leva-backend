<?php

namespace Core\Application\UseCases\DTOs;

class SignupOutput
{
    public function __construct(
        public readonly string $accountId,
    ) {
    }
}
