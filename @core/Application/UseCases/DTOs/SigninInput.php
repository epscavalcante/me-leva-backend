<?php

namespace Core\Application\UseCases\DTOs;

class SigninInput
{
    public function __construct(
        public readonly string $email,
        public readonly string $password,
    ) {
    }
}
