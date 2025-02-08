<?php

use Core\Domain\ValueObjects\Name;

describe('Name Unit Test', function () {
    test('Should receive Exception', function (string $firstName, $lastName) {
        new Name(
            firstName: $firstName,
            lastName: $lastName
        );
    })
        ->throws(Exception::class)
        ->with([
            ['a', 'aa'],
            ['aa', 'a'],
            ['', 'a'],
            [str_repeat('a', 156), str_repeat('a', 100)],
            [str_repeat('a', 100), str_repeat('a', 156)],
        ]);

    test('Should create a name', function () {
        $name = new Name('User', 'Test');

        expect($name->getFirstName())->toBe('User');
        expect($name->getLastName())->toBe('Test');
        expect($name->getFullName())->toBe('User Test');
    });
});
