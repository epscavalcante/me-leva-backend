<?php

use App\Account as AccountModel;
use App\Position as PositionModel;
use App\Repositories\AccountModelRepository;
use App\Repositories\PositionModelRepository;
use App\Repositories\RideModelRepository;
use App\Ride as RideModel;
use Core\Application\UseCases\DTOs\GetRideInput;
use Core\Application\UseCases\DTOs\GetRideOutput;
use Core\Application\UseCases\DTOs\StartRideInput;
use Core\Application\UseCases\GetRide;
use Core\Application\UseCases\Signup;
use Core\Application\UseCases\StartRide;
use Core\Domain\Events\EventDispatcher;
use Core\Domain\Exceptions\RideCannotBeStartedException;
use Core\Domain\Exceptions\RideNotFoundException;
use Core\Domain\ValueObjects\Uuid;

beforeEach(function () {
    $accountRepository = new AccountModelRepository(new AccountModel);
    $this->signup = new Signup(accountRepository: $accountRepository);

    $eventDispatcher = new EventDispatcher;
    $rideRepository = new RideModelRepository(new RideModel);
    $this->startRide = new StartRide(
        rideRepository: $rideRepository,
        eventDispatcher: $eventDispatcher
    );

    $positionRepository = new PositionModelRepository(new PositionModel);
    $this->getRide = new GetRide(
        rideRepository: $rideRepository,
        positionRepository: $positionRepository
    );
});

describe('StartRide', function () {
    test('Deve falhar não encontrar a corrida', function () {
        $startRideInput = new StartRideInput(
            rideId: Uuid::create(),
        );

        expect(fn () => $this->startRide->execute($startRideInput))->toThrow(RideNotFoundException::class);
    });

    test('Deve falhar ao iniciar uma corrida que não foi aceita', function () {
        $passengerModel = AccountModel::factory()->passenger()->create();
        $rideModel = RideModel::factory()->requested()
            ->create(['passenger_id' => $passengerModel->account_id]);

        $startRideInput = new StartRideInput($rideModel->ride_id);
        expect(fn () => $this->startRide->execute($startRideInput))->toThrow(RideCannotBeStartedException::class);
    });

    test('Deve iniciar uma corrida', function () {
        $passengerModel = AccountModel::factory()->passenger()->create();
        $driverModel = AccountModel::factory()->driver()->create();
        $rideModel = RideModel::factory()->accepted($driverModel->account_id)
            ->create(['passenger_id' => $passengerModel->account_id]);

        $startRideInput = new StartRideInput($rideModel->ride_id);
        $this->startRide->execute($startRideInput);

        $getRideInput = new GetRideInput($rideModel->ride_id);
        $getRideOutput = $this->getRide->execute($getRideInput);

        expect($getRideOutput)->toBeInstanceOf(GetRideOutput::class);
        expect($getRideOutput->status)->toBe('in_progress');
    });
});
