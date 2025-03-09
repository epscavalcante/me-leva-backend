<?php

namespace App\Services\TokenGenerator;

interface TokenGenerator
{
    public function encode(array $payload): string;

    public function decode(string $token): array;
}
