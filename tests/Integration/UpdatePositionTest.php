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
use Core\Application\UseCases\DTOs\RequestRideInput;
use Core\Application\UseCases\DTOs\SignupInput;
use Core\Application\UseCases\DTOs\StartRideInput;
use Core\Application\UseCases\DTOs\UpdatePositionInput;
use Core\Application\UseCases\GetRide;
use Core\Application\UseCases\RequestRide;
use Core\Application\UseCases\Signup;
use Core\Application\UseCases\StartRide;
use Core\Application\UseCases\UpdatePosition;
use Core\Domain\Events\EventDispatcher;
use Core\Domain\Events\RidePositionUpdatedEvent;
use Core\Domain\Exceptions\RideNotFoundException;
use Core\Domain\ValueObjects\Uuid;

beforeEach(function () {
    $accountRepository = new AccountModelRepository(new AccountModel);
    $this->signup = new Signup(accountRepository: $accountRepository);

    $eventDispatcher = new EventDispatcher;
    $rideRepository = new RideModelRepository(new RideModel);
    $this->requestRide = new RequestRide(
        accountRepository: $accountRepository,
        rideRepository: $rideRepository,
        eventDispatcher: $eventDispatcher
    );

    $this->acceptRide = new AcceptRide(
        accountRepository: $accountRepository,
        rideRepository: $rideRepository,
        eventDispatcher: $eventDispatcher
    );

    $this->startRide = new StartRide(
        rideRepository: $rideRepository,
        eventDispatcher: $eventDispatcher,
    );

    $eventDispatcher->register('RIDE.POSITION_UPDATED', function (RidePositionUpdatedEvent $event) {
        echo 'Ride has been position updated'.PHP_EOL;
    });

    $positionRepository = new PositionModelRepository(new PositionModel);
    $this->updatePosition = new UpdatePosition(
        rideRepository: $rideRepository,
        positionRepository: $positionRepository,
        eventDispatcher: $eventDispatcher
    );

    $this->getRide = new GetRide(
        rideRepository: $rideRepository,
        positionRepository: $positionRepository
    );
});

describe('UpdatePosition', function () {
    test('Deve falhar não encontrar a corrida', function () {
        $startRideInput = new StartRideInput(
            rideId: Uuid::create(),
        );

        expect(fn () => $this->startRide->execute($startRideInput))->toThrow(RideNotFoundException::class);
    });

    test('Deve atualizar as posicções de uma corrida', function () {
        $signupPassengerInput = new SignupInput('John', 'Doe', 'john.doe@email.com', '00000000000', true, false, 'password');
        $signupPassengerOutput = $this->signup->execute($signupPassengerInput);

        $requestRideInput = new RequestRideInput(
            passengerId: $signupPassengerOutput->accountId,
            fromLatitude: '-27.584905257808835',
            fromLongitude: '-48.545022195325124',
            toLatitude: '-27.496887588317275',
            toLongitude: '-48.522234807851476'
        );
        $requestRideOutput = $this->requestRide->execute($requestRideInput);

        $signupDriverInput = new SignupInput('James', 'Brooks', 'james.brooks@email.com', '00000000000', false, true, 'password');
        $signupDriverOutput = $this->signup->execute($signupDriverInput);

        $acceptRideInput = new AcceptRideInput(
            rideId: $requestRideOutput->rideId,
            driverId: $signupDriverOutput->accountId
        );
        $this->acceptRide->execute($acceptRideInput);

        $startRideInput = new StartRideInput($requestRideOutput->rideId);
        $this->startRide->execute($startRideInput);

        $updatePositionInput1 = new UpdatePositionInput(
            rideId: $requestRideOutput->rideId,
            latitude: '-27.584905257808835',
            longitude: '-48.545022195325124'
        );
        $this->updatePosition->execute($updatePositionInput1);

        $updatePositionInput2 = new UpdatePositionInput(
            rideId: $requestRideOutput->rideId,
            latitude: '-27.496887588317275',
            longitude: '-48.522234807851476'
        );
        $this->updatePosition->execute($updatePositionInput2);

        $updatePositionInput4 = new UpdatePositionInput(
            rideId: $requestRideOutput->rideId,
            latitude: '-27.584905257808835',
            longitude: '-48.545022195325124'
        );
        $this->updatePosition->execute($updatePositionInput4);

        $updatePositionInput3 = new UpdatePositionInput(
            rideId: $requestRideOutput->rideId,
            latitude: '-27.496887588317275',
            longitude: '-48.522234807851476'
        );
        $this->updatePosition->execute($updatePositionInput3);

        $getRideInput = new GetRideInput($requestRideOutput->rideId);
        $getRideOutput = $this->getRide->execute($getRideInput);

        expect($getRideOutput->distance)->toBe(30.12504735293619);
    });
});
