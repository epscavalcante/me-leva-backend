<?php

namespace Core\Application\UseCases\DTOs;

class SigninOutput
{
    public function __construct(
        public readonly string $accessToken,
        //public readonly string $expiresIn,
    ) {
    }
}
