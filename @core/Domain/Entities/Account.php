<?php

namespace Core\Domain\Entities;

use Core\Domain\ValueObjects\Email;
use Core\Domain\ValueObjects\Name;
use Core\Domain\ValueObjects\Phone;
use Core\Domain\ValueObjects\Uuid;

class Account extends Entity
{
    private Uuid $accountId;

    private Name $name;

    private Email $email;

    private Phone $phone;

    private bool $isDriver;

    private bool $isPassenger;

    public function __construct(
        string $accountId,
        string $firstName,
        string $lastName,
        string $email,
        string $phone,
        bool $isPassenger,
        bool $isDriver,
    ) {
        $this->name = new Name(firstName: $firstName, lastName: $lastName);
        $this->email = new Email($email);
        $this->phone = new Phone($phone);
        $this->accountId = new Uuid($accountId);
        $this->isPassenger = $isPassenger;
        $this->isDriver = $isDriver;
    }

    public static function create(
        string $firstName,
        string $lastName,
        string $email,
        string $phone,
        bool $isPassenger,
        bool $isDriver,
    ) {
        $accountId = Uuid::create();

        return new Account(
            accountId: $accountId,
            firstName: $firstName,
            lastName: $lastName,
            email: $email,
            phone: $phone,
            isPassenger: $isPassenger,
            isDriver: $isDriver
        );
    }

    public function getId(): string
    {
        return $this->accountId->getValue();
    }

    public function getName(): string
    {
        return $this->name->getFullName();
    }

    public function getFirstName(): string
    {
        return $this->name->getFirstName();
    }

    public function getLastName(): string
    {
        return $this->name->getLastName();
    }

    public function getEmail(): string
    {
        return $this->email->getValue();
    }

    public function getPhone(): string
    {
        return $this->phone->getValue();
    }

    public function isDriver(): bool
    {
        return $this->isDriver;
    }

    public function isPassenger(): bool
    {
        return $this->isPassenger;
    }

    public function canRequestRide(): bool
    {
        return $this->isPassenger();
    }

    public function canAcceptRide(): bool
    {
        return $this->isDriver();
    }
}
