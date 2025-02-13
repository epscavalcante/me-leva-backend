<?php

use App\Account as AccountModel;
use App\Position as PositionModel;
use App\Repositories\AccountModelRepository;
use App\Repositories\PositionModelRepository;
use App\Repositories\RideModelRepository;
use App\Ride as RideModel;
use Core\Application\UseCases\AcceptRide;
use Core\Application\UseCases\DTOs\AcceptRideInput;
use Core\Application\UseCases\DTOs\GetRideInput;
use Core\Application\UseCases\DTOs\GetRideOutput;
use Core\Application\UseCases\DTOs\RequestRideInput;
use Core\Application\UseCases\DTOs\SignupInput;
use Core\Application\UseCases\GetRide;
use Core\Application\UseCases\RequestRide;
use Core\Application\UseCases\Signup;
use Core\Domain\Exceptions\AccountCannotBeAcceptRideException;
use Core\Domain\Exceptions\AccountNotFoundException;
use Core\Domain\Exceptions\RideCannotBeAcceptedException;
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

    $positionRepository = new PositionModelRepository(new PositionModel());
    $this->getRide = new GetRide(
        rideRepository: $rideRepository,
        positionRepository: $positionRepository
    );
});

describe('AcceptRide', function () {
    test('Deve falhar encontrar o motorista', function () {
        $acceptRideInput = new AcceptRideInput(
            rideId: Uuid::create(),
            driverId: Uuid::create()
        );

        expect(fn () => $this->acceptRide->execute($acceptRideInput))->toThrow(AccountNotFoundException::class);
    });

    test('Deve falhar encontrar tentar aceitar uma corrida com a conta de passeiro', function () {
        $signupDriverInput = new SignupInput('James', 'Brooks', 'james.brooks@email.com', '00000000000', true, false);
        $signupDriverOutput = $this->signup->execute($signupDriverInput);

        $acceptRideInput = new AcceptRideInput(
            rideId: Uuid::create(),
            driverId: $signupDriverOutput->accountId
        );

        expect(fn () => $this->acceptRide->execute($acceptRideInput))->toThrow(AccountCannotBeAcceptRideException::class);
    });

    test('Deve falhar não encontrar a corrida', function () {
        $signupDriverInput = new SignupInput('James', 'Brooks', 'james.brooks@email.com', '00000000000', false, true);
        $signupDriverOutput = $this->signup->execute($signupDriverInput);

        $acceptRideInput = new AcceptRideInput(
            rideId: Uuid::create(),
            driverId: $signupDriverOutput->accountId
        );

        expect(fn () => $this->acceptRide->execute($acceptRideInput))->toThrow(RideNotFoundException::class);
    });

    test('Deve falhar ao aceitar uma corrida que ja foi aceita', function () {
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

        expect(fn () => $this->acceptRide->execute($acceptRideInput))->toThrow(RideCannotBeAcceptedException::class);
    });

    test('Deve aceitar uma corrida', function () {
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

        $getRideInput = new GetRideInput($requestRideOutput->rideId);
        $getRideOutput = $this->getRide->execute($getRideInput);
        expect($getRideOutput)->toBeInstanceOf(GetRideOutput::class);
        expect($getRideOutput->driverId)->toBe($signupDriverOutput->accountId);
        expect($getRideOutput->status)->toBe('accepted');
    });
});
