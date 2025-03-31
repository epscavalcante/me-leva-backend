<?php

use Core\Domain\Entities\Ride;
use Core\Domain\Enums\RideStatusEnum;
use Core\Domain\Exceptions\RideCannotBeCanceledException;
use Core\Domain\ValueObjects\Email;
use Core\Domain\ValueObjects\RideStatus;
use Core\Domain\ValueObjects\Uuid;

describe('Ride Entity Test', function () {
    describe('Cancel Ride', function () {
        test('Deve falhar ao cancelar uma ride', function (string $status) {
            $ride = new Ride(
                rideId: (string) Uuid::create(),
                passengerId: (string) Uuid::create(),
                status: $status,
                fromLatitude: -15,
                fromLongitude: 56,
                toLatitude: -16,
                toLongitude: 57,
            );

            $ride->cancel();
        })
            ->throws(RideCannotBeCanceledException::class)
            ->with([
                RideStatusEnum::CANCELED->value,
                RideStatusEnum::COMPLETED->value,
            ]);

        test('Deve cancelar uma ride', function (string $status) {
            $ride = new Ride(
                rideId: (string) Uuid::create(),
                passengerId: (string) Uuid::create(),
                status: $status,
                fromLatitude: -15,
                fromLongitude: 56,
                toLatitude: -16,
                toLongitude: 57,
            );

            $ride->cancel();

            expect($ride->isCanceled())->toBeTrue();
        })
            ->with([
                RideStatusEnum::REQUESTED->value,
                RideStatusEnum::ACCEPTED->value,
                RideStatusEnum::STARTED->value,
            ]);
    });
});
