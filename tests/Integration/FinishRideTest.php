<?php

use App\Account as AccountModel;
use App\Position as PositionModel;
use App\Repositories\PositionModelRepository;
use App\Repositories\RideModelRepository;
use App\Ride as RideModel;
use App\Services\MessageBroker\MessageBroker;
use Core\Application\UseCases\DTOs\FinishRideInput;
use Core\Application\UseCases\DTOs\GetRideInput;
use Core\Application\UseCases\DTOs\GetRideOutput;
use Core\Application\UseCases\DTOs\UpdatePositionInput;
use Core\Application\UseCases\FinishRide;
use Core\Application\UseCases\GetRide;
use Core\Application\UseCases\UpdatePosition;
use Core\Domain\Events\EventDispatcher;
use Core\Domain\Exceptions\RideCannotBeFinishedException;
use Core\Domain\Exceptions\RideNotFoundException;
use Core\Domain\ValueObjects\Uuid;

beforeEach(function () {
    $this->rideRepository = new RideModelRepository(new RideModel);
    $this->positionRepository = new PositionModelRepository(new PositionModel);
    $eventDispatcher = new EventDispatcher;
    $this->finishRide = new FinishRide(
        rideRepository: $this->rideRepository,
        positionRepository: $this->positionRepository,
        eventDispatcher: $eventDispatcher
    );
});

describe('FinishRide', function () {
    test('Deve falhar não encontrar a corrida', function () {
        $finishRideInput = new FinishRideInput(
            rideId: Uuid::create(),
        );

        expect(fn () => $this->finishRide->execute($finishRideInput))->toThrow(RideNotFoundException::class);
    });

    test('Deve falhar ao finalizar uma corrida que não foi iniciada', function () {
        $passengerModel = AccountModel::factory()->passenger()->create();
        $driverModel = AccountModel::factory()->driver()->create();
        $rideModel = RideModel::factory()->accepted($driverModel->account_id)
            ->create(['passenger_id' => $passengerModel->account_id]);

        $finishRideInput = new FinishRideInput($rideModel->ride_id);
        expect(fn () => $this->finishRide->execute($finishRideInput))->toThrow(RideCannotBeFinishedException::class);
    });

    test('Deve finailizar uma corrida', function () {
        $passengerModel = AccountModel::factory()->passenger()->create();
        $driverModel = AccountModel::factory()->driver()->create();
        $rideModel = RideModel::factory()->started()
            ->create([
                'driver_id' => $driverModel->account_id,
                'passenger_id' => $passengerModel->account_id,
            ]);

        $messageBroker = Mockery::mock(MessageBroker::class);
        $messageBroker->shouldReceive('publish')->times(4)->andReturn();
        $updatePosition = new UpdatePosition(
            rideRepository: $this->rideRepository,
            positionRepository: $this->positionRepository,
            messageBroker: $messageBroker
        );

        $updatePositionInput1 = new UpdatePositionInput(
            rideId: $rideModel->ride_id,
            latitude: '-27.584905257808835',
            longitude: '-48.545022195325124'
        );
        $updatePosition->execute($updatePositionInput1);

        $updatePositionInput2 = new UpdatePositionInput(
            rideId: $rideModel->ride_id,
            latitude: '-27.496887588317275',
            longitude: '-48.522234807851476'
        );
        $updatePosition->execute($updatePositionInput2);

        $updatePositionInput4 = new UpdatePositionInput(
            rideId: $rideModel->ride_id,
            latitude: '-27.584905257808835',
            longitude: '-48.545022195325124'
        );
        $updatePosition->execute($updatePositionInput4);

        $updatePositionInput3 = new UpdatePositionInput(
            rideId: $rideModel->ride_id,
            latitude: '-27.496887588317275',
            longitude: '-48.522234807851476'
        );
        $updatePosition->execute($updatePositionInput3);

        $finishRideInput = new FinishRideInput($rideModel->ride_id);
        $this->finishRide->execute($finishRideInput);

        $getRide = new GetRide(
            rideRepository: $this->rideRepository,
            positionRepository: $this->positionRepository
        );
        $getRideInput = new GetRideInput($rideModel->ride_id);
        $getRideOutput = $getRide->execute($getRideInput);

        expect($getRideOutput)->toBeInstanceOf(GetRideOutput::class);
        expect($getRideOutput->status)->toBe('completed');
        expect($getRideOutput->distance)->toBe(30.12504735293619);
        expect($getRideOutput->fare)->toBe(63.262599441166);
    });
});
