<?php

use Core\Domain\ValueObjects\Email;

describe('Email Unit Test', function () {
    test('Should receives Error when creating email invalid', function (string $email) {
        new Email($email);
    })
        ->throws(Exception::class)
        ->with([
            'email',
            'email@',
            'email@email',
        ]);

    test('Should creata a valid email', function () {
        $email = new Email('john.doe@email.com');
        expect($email)->toBeInstanceOf(Email::class);
        expect($email->getValue())->toBe('john.doe@email.com');
    });
});
