<?php

use App\Account as AccountModel;
use App\Repositories\RideModelRepository;
use App\Ride as RideModel;
use App\Services\MessageBroker\MessageBroker;
use App\Services\UnitOfWork\DatabaseUnitOfWork;
use App\Services\UnitOfWork\UnitOfWork;
use Core\Application\Repositories\PositionRepository;
use Core\Application\Repositories\RideRepository;
use Core\Application\UseCases\DTOs\GetRideInput;
use Core\Application\UseCases\DTOs\GetRideOutput;
use Core\Application\UseCases\DTOs\StartRideInput;
use Core\Application\UseCases\GetRide;
use Core\Application\UseCases\StartRide;
use Core\Domain\Entities\Ride;
use Core\Domain\Exceptions\RideCannotBeStartedException;
use Core\Domain\Exceptions\RideNotFoundException;
use Core\Domain\ValueObjects\Uuid;

describe('StartRide', function () {
    test('Deve falhar não encontrar a corrida', function () {
        $unitOfWork = Mockery::mock(UnitOfWork::class);
        $unitOfWork->shouldReceive('commit')->times(0);
        $unitOfWork->shouldReceive('rollback')->times(0);
        $messageBroker = Mockery::mock(MessageBroker::class);
        $messageBroker->shouldReceive('publish')->times(0);
        $rideRepository = Mockery::mock(RideRepository::class);
        $rideRepository->shouldReceive('getById')->andReturnNull();
        $startRide = new StartRide(
            unitOfWork: $unitOfWork,
            messageBroker: $messageBroker,
            rideRepository: $rideRepository,
        );
        $startRideInput = new StartRideInput(
            rideId: Uuid::create(),
        );

        expect(fn () => $startRide->execute($startRideInput))->toThrow(RideNotFoundException::class);
    });

    test('Deve falhar ao iniciar uma corrida que não foi aceita', function () {
        $unitOfWork = Mockery::mock(UnitOfWork::class);
        $unitOfWork->shouldReceive('commit')->times(0);
        $unitOfWork->shouldReceive('rollback')->times(0);
        $messageBroker = Mockery::mock(MessageBroker::class);
        $messageBroker->shouldReceive('publish')->times(0);
        $rideRepository = Mockery::mock(RideRepository::class);

        $ride = new Ride(
            rideId: (string) Uuid::create(),
            passengerId: (string) Uuid::create(),
            status: 'requested',
            fromLatitude: -15,
            fromLongitude: 56,
            toLatitude: -15,
            toLongitude: 56,
        );

        $rideRepository->shouldReceive('getById')->andReturn($ride);
        $startRide = new StartRide(
            unitOfWork: $unitOfWork,
            messageBroker: $messageBroker,
            rideRepository: $rideRepository,
        );

        $startRideInput = new StartRideInput($ride->getId());
        expect(fn () => $startRide->execute($startRideInput))->toThrow(RideCannotBeStartedException::class);
    });

    test('Deve iniciar uma corrida', function () {

        $passengerModel = AccountModel::factory()->passenger()->create();
        $driverModel = AccountModel::factory()->driver()->create();
        $rideModel = RideModel::factory()->accepted($driverModel->account_id)
            ->create(['passenger_id' => $passengerModel->account_id]);

        $unitOfWork = new DatabaseUnitOfWork;
        $messageBroker = Mockery::mock(MessageBroker::class);
        $messageBroker->shouldReceive('publish')->times(1);
        $rideRepository = new RideModelRepository(new RideModel);
        $startRide = new StartRide(
            unitOfWork: $unitOfWork,
            messageBroker: $messageBroker,
            rideRepository: $rideRepository,
        );

        $startRideInput = new StartRideInput($rideModel->ride_id);
        $startRide->execute($startRideInput);

        $positionRepository = Mockery::mock(PositionRepository::class);
        $positionRepository->shouldReceive('getPositionsByRideId')->andReturn([]);
        $getRide = new GetRide(
            rideRepository: $rideRepository,
            positionRepository: $positionRepository
        );
        $getRideInput = new GetRideInput($rideModel->ride_id);
        $getRideOutput = $getRide->execute($getRideInput);

        expect($getRideOutput)->toBeInstanceOf(GetRideOutput::class);
        expect($getRideOutput->status)->toBe('in_progress');
    });
});
