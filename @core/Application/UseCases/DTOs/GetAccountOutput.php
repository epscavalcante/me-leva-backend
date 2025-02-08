<?php

namespace Core\Application\UseCases\DTOs;

class GetAccountOutput
{
    public function __construct(
        public readonly string $accountId,
        public readonly string $firstName,
        public readonly string $lastName,
        public readonly string $email,
        public readonly string $phone,
        public readonly bool $isPassenger,
        public readonly bool $isDriver,
        // public readonly ?string $carPlate,
    ) {
    }
}
