<?php

namespace Core\Domain\Factories;

use Core\Domain\ValueObjects\MD5Password;
use Core\Domain\ValueObjects\Password;
use Core\Domain\ValueObjects\PlainPassword;

class PasswordFactory
{
    public static function create(string $password, string $algorithm): Password
    {
        return match ($algorithm) {
            PlainPassword::ALGORITHM => new PlainPassword($password),
            MD5Password::ALGORITHM => new MD5Password($password)
        };
    }
}
