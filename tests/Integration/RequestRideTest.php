<?php

use App\Account as AccountModel;
use App\Position as PositionModel;
use App\Repositories\AccountModelRepository;
use App\Repositories\PositionModelRepository;
use App\Repositories\RideModelRepository;
use App\Ride as RideModel;
use Core\Application\UseCases\DTOs\GetRideInput;
use Core\Application\UseCases\DTOs\GetRideOutput;
use Core\Application\UseCases\DTOs\RequestRideInput;
use Core\Application\UseCases\DTOs\SignupInput;
use Core\Application\UseCases\GetRide;
use Core\Application\UseCases\RequestRide;
use Core\Application\UseCases\Signup;
use Core\Domain\Events\EventDispatcher;
use Core\Domain\Exceptions\AccountCannotRequestRideException;
use Core\Domain\Exceptions\AccountNotFoundException;
use Core\Domain\ValueObjects\Uuid;

beforeEach(function () {
    $this->eventDispatcher = new EventDispatcher();
});

describe('RequestRide', function () {

    test('Deve falhar ao não encontrar o passageiro', function () {
        $accountRepository = new AccountModelRepository(new AccountModel());
        $rideRepository = new RideModelRepository(new RideModel());
        $requestRide = new RequestRide(
            accountRepository: $accountRepository,
            rideRepository: $rideRepository,
            eventDispatcher: $this->eventDispatcher
        );
        $requestRideInput = new RequestRideInput(
            passengerId: Uuid::create(),
            fromLatitude: '-27.584905257808835',
            fromLongitude: '-48.545022195325124',
            toLatitude: '-27.496887588317275',
            toLongitude: '-48.522234807851476'
        );

        expect(fn () => $requestRide->execute($requestRideInput))->toThrow(AccountNotFoundException::class);
    });

    test('Deve falhar ao solicitar corrida de uma conta que não pode solicitar corridas (não é conta de passageiro)', function () {
        $accountRepository = new AccountModelRepository(new AccountModel());
        $signup = new Signup($accountRepository);
        $signupInput = new SignupInput('John', 'Doe', 'john.doe@email.com', '00000000000', false, true, 'password');
        $signupOutput = $signup->execute($signupInput);
        $rideRepository = new RideModelRepository(new RideModel());
        $requestRide = new RequestRide(
            accountRepository: $accountRepository,
            rideRepository: $rideRepository,
            eventDispatcher: $this->eventDispatcher
        );
        $requestRideInput = new RequestRideInput(
            passengerId: $signupOutput->accountId,
            fromLatitude: '-27.584905257808835',
            fromLongitude: '-48.545022195325124',
            toLatitude: '-27.496887588317275',
            toLongitude: '-48.522234807851476'
        );

        expect(fn () => $requestRide->execute($requestRideInput))->toThrow(AccountCannotRequestRideException::class);
    });

    test('Deve solicitar uma corrida', function () {
        $accountRepository = new AccountModelRepository(new AccountModel());
        $signup = new Signup($accountRepository);
        $signupInput = new SignupInput('John', 'Doe', 'john.doe@email.com', '00000000000', true, false, 'password');
        $signupOutput = $signup->execute($signupInput);
        $rideRepository = new RideModelRepository(new RideModel());

        $requestRide = new RequestRide(
            accountRepository: $accountRepository,
            rideRepository: $rideRepository,
            eventDispatcher: $this->eventDispatcher
        );
        $requestRideInput = new RequestRideInput(
            passengerId: $signupOutput->accountId,
            fromLatitude: '-27.584905257808835',
            fromLongitude: '-48.545022195325124',
            toLatitude: '-27.496887588317275',
            toLongitude: '-48.522234807851476'
        );
        $requestRideOutput = $requestRide->execute($requestRideInput);
        $positionRepository = new PositionModelRepository(new PositionModel());
        $this->getRide = new GetRide(
            rideRepository: $rideRepository,
            positionRepository: $positionRepository
        );
        $getRide = new GetRide($rideRepository, $positionRepository);
        $getRideInput = new GetRideInput($requestRideOutput->rideId);
        $getRideOutput = $getRide->execute($getRideInput);
        expect($getRideOutput)->toBeInstanceOf(GetRideOutput::class);
        expect($getRideOutput->passengerId)->toBe($signupOutput->accountId);
        expect($getRideOutput->status)->toBe('requested');
        expect($getRideOutput->fromLatitude)->toBe('-27.584905257808835');
        expect($getRideOutput->fromLongitude)->toBe('-48.545022195325124');
        expect($getRideOutput->toLatitude)->toBe('-27.496887588317275');
        expect($getRideOutput->toLongitude)->toBe('-48.522234807851476');
    });
});
