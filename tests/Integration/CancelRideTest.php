<?php

use App\Repositories\RideModelRepository;
use App\Ride;
use App\Services\MessageBroker\MessageBroker;
use App\Services\UnitOfWork\UnitOfWork;
use Core\Application\UseCases\CancelRide\CancelRideInput;
use Core\Application\UseCases\CancelRide\CancelRideUseCase;
use Core\Domain\Enums\RideStatusEnum;
use Core\Domain\Exceptions\RideNotFoundException;
use Core\Domain\ValueObjects\Uuid;

describe('CancelRide', function () {
    test('Deve falhar ao não encontrar a ride', function () {
        $messageBroker = Mockery::mock(MessageBroker::class);
        $messageBroker->shouldNotReceive('publish');
        $unitOfWork = Mockery::mock(UnitOfWork::class);
        $unitOfWork->shouldNotReceive('commit');
        $unitOfWork->shouldNotReceive('rollback');
        $cancelRideInput = new CancelRideInput(
            rideId: (string) Uuid::create(),
        );
        $rideRepository = new RideModelRepository(new Ride);
        $cancelRide = new CancelRideUseCase(
            rideRepository: $rideRepository,
            messageBroker: $messageBroker,
            unitOfWork: $unitOfWork
        );

        expect(fn () => $cancelRide->execute($cancelRideInput))->toThrow(RideNotFoundException::class);
    });

    test('Deve cancelar uma ride', function () {
        $rideModel = Ride::factory()->requested()->create();

        $messageBroker = Mockery::mock(MessageBroker::class);
        $messageBroker->shouldReceive('publish')->once();
        $unitOfWork = Mockery::mock(UnitOfWork::class);
        $unitOfWork->shouldReceive('commit')->once();
        $unitOfWork->shouldNotReceive('rollback');
        $cancelRideInput = new CancelRideInput(
            rideId: $rideModel->ride_id,
        );
        $rideRepository = new RideModelRepository(new Ride);
        $cancelRide = new CancelRideUseCase(
            rideRepository: $rideRepository,
            messageBroker: $messageBroker,
            unitOfWork: $unitOfWork
        );

        $cancelRide->execute($cancelRideInput);

        $rideModel->refresh();

        expect($rideModel->status)->toBe(RideStatusEnum::CANCELED->value);
    });
});
