<?php

use App\Account as AccountModel;
use App\Repositories\AccountModelRepository;
use App\Repositories\RideModelRepository;
use App\Ride as RideModel;
use Core\Application\UseCases\AcceptRide;
use Core\Application\UseCases\DTOs\AcceptRideInput;
use Core\Application\UseCases\DTOs\FinishRideInput;
use Core\Application\UseCases\DTOs\GetRideInput;
use Core\Application\UseCases\DTOs\GetRideOutput;
use Core\Application\UseCases\DTOs\RequestRideInput;
use Core\Application\UseCases\DTOs\SignupInput;
use Core\Application\UseCases\DTOs\StartRideInput;
use Core\Application\UseCases\FinishRide;
use Core\Application\UseCases\GetRide;
use Core\Application\UseCases\RequestRide;
use Core\Application\UseCases\Signup;
use Core\Application\UseCases\StartRide;
use Core\Domain\Exceptions\RideCannotBeFinishedException;
use Core\Domain\Exceptions\RideNotFoundException;
use Core\Domain\ValueObjects\Uuid;

beforeEach(function () {
    $accountRepository = new AccountModelRepository(new AccountModel());
    $this->signup = new Signup(accountRepository: $accountRepository);

    $rideRepository = new RideModelRepository(new RideModel());
    $this->requestRide = new RequestRide(
        accountRepository: $accountRepository,
        rideRepository: $rideRepository
    );

    $this->acceptRide = new AcceptRide(
        accountRepository: $accountRepository,
        rideRepository: $rideRepository
    );

    $this->startRide = new StartRide(
        rideRepository: $rideRepository
    );

    $this->finishRide = new FinishRide(
        rideRepository: $rideRepository
    );

    $this->getRide = new GetRide(
        rideRepository: $rideRepository
    );
});

describe('FinishRide', function () {
    test('Deve falhar não encontrar a corrida', function () {
        $startRideInput = new StartRideInput(
            rideId: Uuid::create(),
        );

        expect(fn () => $this->startRide->execute($startRideInput))->toThrow(RideNotFoundException::class);
    });

    test('Deve falhar ao finalizar uma corrida que não foi iniciada', function () {
        $signupPassengerInput = new SignupInput('John', 'Doe', 'john.doe@email.com', '00000000000', true, false);
        $signupPassengerOutput = $this->signup->execute($signupPassengerInput);

        $requestRideInput = new RequestRideInput(
            passengerId: $signupPassengerOutput->accountId,
            fromLatitude: '-27.584905257808835',
            fromLongitude: '-48.545022195325124',
            toLatitude: '-27.496887588317275',
            toLongitude: '-48.522234807851476'
        );
        $requestRideOutput = $this->requestRide->execute($requestRideInput);

        $signupDriverInput = new SignupInput('James', 'Brooks', 'james.brooks@email.com', '00000000000', false, true);
        $signupDriverOutput = $this->signup->execute($signupDriverInput);

        $acceptRideInput = new AcceptRideInput(
            rideId: $requestRideOutput->rideId,
            driverId: $signupDriverOutput->accountId
        );
        $this->acceptRide->execute($acceptRideInput);

        $finishRideInput = new FinishRideInput($requestRideOutput->rideId);
        expect(fn () => $this->finishRide->execute($finishRideInput))->toThrow(RideCannotBeFinishedException::class);
    });

    test('Deve iniciar uma corrida', function () {
        $signupPassengerInput = new SignupInput('John', 'Doe', 'john.doe@email.com', '00000000000', true, false);
        $signupPassengerOutput = $this->signup->execute($signupPassengerInput);

        $requestRideInput = new RequestRideInput(
            passengerId: $signupPassengerOutput->accountId,
            fromLatitude: '-27.584905257808835',
            fromLongitude: '-48.545022195325124',
            toLatitude: '-27.496887588317275',
            toLongitude: '-48.522234807851476'
        );
        $requestRideOutput = $this->requestRide->execute($requestRideInput);

        $signupDriverInput = new SignupInput('James', 'Brooks', 'james.brooks@email.com', '00000000000', false, true);
        $signupDriverOutput = $this->signup->execute($signupDriverInput);

        $acceptRideInput = new AcceptRideInput(
            rideId: $requestRideOutput->rideId,
            driverId: $signupDriverOutput->accountId
        );
        $this->acceptRide->execute($acceptRideInput);

        $startRideInput = new StartRideInput($requestRideOutput->rideId);
        $this->startRide->execute($startRideInput);

        $finishRideInput = new FinishRideInput($requestRideOutput->rideId);
        $this->finishRide->execute($finishRideInput);

        $getRideInput = new GetRideInput($requestRideOutput->rideId);
        $getRideOutput = $this->getRide->execute($getRideInput);

        expect($getRideOutput)->toBeInstanceOf(GetRideOutput::class);
        expect($getRideOutput->status)->toBe('completed');
    });
});
