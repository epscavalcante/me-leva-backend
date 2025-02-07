<?php

namespace Core\Application\UseCases\DTOs;

class SignupInput
{
    public function __construct(
        public readonly string $firstName,
        public readonly string $lastName,
        public readonly string $email,
        public readonly string $phone,
        public readonly bool $isPassenger,
        public readonly bool $isDriver,
        // public readonly ?string $carPlate,
    ) {}
}
