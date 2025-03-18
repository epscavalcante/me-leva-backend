<?php

use App\Account as AccountModel;
use App\Position as PositionModel;
use App\Repositories\PositionModelRepository;
use App\Repositories\RideModelRepository;
use App\Ride as RideModel;
use App\Services\MessageBroker\MessageBroker;
use Core\Application\UseCases\DTOs\GetRideInput;
use Core\Application\UseCases\DTOs\UpdatePositionInput;
use Core\Application\UseCases\GetRide;
use Core\Application\UseCases\UpdatePosition;

describe('UpdatePosition', function () {
    test('Deve atualizar as posicções de uma corrida', function () {
        $passengerModel = AccountModel::factory()->passenger()->create();
        $driverModel = AccountModel::factory()->driver()->create();
        $rideModel = RideModel::factory()->started($driverModel->account_id)
            ->create(['passenger_id' => $passengerModel->account_id]);

        $rideRepository = new RideModelRepository(new RideModel);
        $messageBroker = Mockery::mock(MessageBroker::class);
        $messageBroker->shouldReceive('publish')
            ->times(4)
            ->andReturn();

        $positionRepository = new PositionModelRepository(new PositionModel);
        $updatePosition = new UpdatePosition(
            rideRepository: $rideRepository,
            positionRepository: $positionRepository,
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

        $getRide = new GetRide(
            rideRepository: $rideRepository,
            positionRepository: $positionRepository
        );
        $getRideInput = new GetRideInput($rideModel->ride_id);
        $getRideOutput = $getRide->execute($getRideInput);

        expect($getRideOutput->distance)->toBe(30.12504735293619);
    });
});
