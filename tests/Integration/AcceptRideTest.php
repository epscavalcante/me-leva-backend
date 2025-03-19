<?php

use App\Account as AccountModel;
use App\Position as PositionModel;
use App\Repositories\AccountModelRepository;
use App\Repositories\PositionModelRepository;
use App\Repositories\RideModelRepository;
use App\Ride as RideModel;
use App\Services\MessageBroker\MessageBroker;
use App\Services\UnitOfWork\UnitOfWork;
use Core\Application\UseCases\AcceptRide;
use Core\Application\UseCases\DTOs\AcceptRideInput;
use Core\Application\UseCases\DTOs\GetRideInput;
use Core\Application\UseCases\DTOs\GetRideOutput;
use Core\Application\UseCases\GetRide;
use Core\Domain\Exceptions\AccountCannotBeAcceptRideException;
use Core\Domain\Exceptions\AccountNotFoundException;
use Core\Domain\Exceptions\RideCannotBeAcceptedException;
use Core\Domain\Exceptions\RideNotFoundException;
use Core\Domain\ValueObjects\Uuid;

beforeEach(function () {
    /** @var RideModelRepository */
    $this->rideRepository = new RideModelRepository(new RideModel);
    $this->accountRepository = new AccountModelRepository(new AccountModel);
});

describe('AcceptRide', function () {
    test('Deve falhar encontrar o motorista', function () {
        $acceptRideInput = new AcceptRideInput(
            rideId: Uuid::create(),
            driverId: Uuid::create()
        );

        $messageBroker = Mockery::mock(MessageBroker::class);
        $unitOfWork = Mockery::mock(UnitOfWork::class);
        $unitOfWork->shouldNotReceive('publish');
        $unitOfWork->shouldNotReceive('commit');
        $unitOfWork->shouldNotReceive('rollback');
        $acceptRide = new AcceptRide(
            accountRepository: $this->accountRepository,
            rideRepository: $this->rideRepository,
            messageBroker: $messageBroker,
            unitOfWork: $unitOfWork
        );

        expect(fn () => $acceptRide->execute($acceptRideInput))->toThrow(AccountNotFoundException::class);
    });

    test('Deve falhar encontrar tentar aceitar uma corrida com a conta de passeiro', function () {
        $accountModel = AccountModel::factory()->passenger()->create();

        $acceptRideInput = new AcceptRideInput(
            rideId: Uuid::create(),
            driverId: $accountModel->account_id
        );

        $messageBroker = Mockery::mock(MessageBroker::class);
        $messageBroker->shouldNotReceive('publish');
        $unitOfWork = Mockery::mock(UnitOfWork::class);
        $unitOfWork->shouldNotReceive('publish');
        $unitOfWork->shouldNotReceive('commit');
        $unitOfWork->shouldNotReceive('rollback');
        $acceptRide = new AcceptRide(
            accountRepository: $this->accountRepository,
            rideRepository: $this->rideRepository,
            messageBroker: $messageBroker,
            unitOfWork: $unitOfWork
        );

        expect(fn () => $acceptRide->execute($acceptRideInput))->toThrow(AccountCannotBeAcceptRideException::class);
    });

    test('Deve falhar não encontrar a corrida', function () {
        $driverModel = AccountModel::factory()->driver()->create();

        $acceptRideInput = new AcceptRideInput(
            rideId: Uuid::create(),
            driverId: $driverModel->account_id
        );
        $messageBroker = Mockery::mock(MessageBroker::class);
        $messageBroker->shouldNotReceive('publish');
        $unitOfWork = Mockery::mock(UnitOfWork::class);
        $unitOfWork->shouldNotReceive('publish');
        $unitOfWork->shouldNotReceive('commit');
        $unitOfWork->shouldNotReceive('rollback');
        $acceptRide = new AcceptRide(
            accountRepository: $this->accountRepository,
            rideRepository: $this->rideRepository,
            messageBroker: $messageBroker,
            unitOfWork: $unitOfWork
        );
        expect(fn () => $acceptRide->execute($acceptRideInput))->toThrow(RideNotFoundException::class);
    });

    test('Deve falhar ao aceitar uma corrida que ja foi aceita', function () {
        $passenger = AccountModel::factory()->passenger()->create();
        $driver = AccountModel::factory()->driver()->create();
        $ride = RideModel::factory()->accepted($driver->account_id)
            ->create(['passenger_id' => $passenger->account_id]);

        $acceptRideInput = new AcceptRideInput(
            rideId: $ride->ride_id,
            driverId: $driver->account_id
        );

        $messageBroker = Mockery::mock(MessageBroker::class);
        $messageBroker->shouldNotReceive('publish');
        $unitOfWork = Mockery::mock(UnitOfWork::class);
        $unitOfWork->shouldNotReceive('publish');
        $unitOfWork->shouldNotReceive('commit');
        $unitOfWork->shouldNotReceive('rollback');
        $acceptRide = new AcceptRide(
            accountRepository: $this->accountRepository,
            rideRepository: $this->rideRepository,
            messageBroker: $messageBroker,
            unitOfWork: $unitOfWork
        );

        expect(fn () => $acceptRide->execute($acceptRideInput))->toThrow(RideCannotBeAcceptedException::class);
    });

    test('Deve aceitar uma corrida', function () {
        $passengerModel = AccountModel::factory()->passenger()->create();
        $rideModel = RideModel::factory()
            ->requested()
            ->create(['passenger_id' => $passengerModel->account_id]);
        $driverModel = AccountModel::factory()->driver()->create();

        $acceptRideInput = new AcceptRideInput(
            rideId: $rideModel->ride_id,
            driverId: $driverModel->account_id
        );
        $messageBroker = Mockery::mock(MessageBroker::class);
        $messageBroker->shouldReceive('publish')
            ->once()
            ->andReturn();
        $unitOfWork = Mockery::mock(UnitOfWork::class);
        $unitOfWork->shouldNotReceive('publish');
        $unitOfWork->shouldNotReceive('rollback');
        $unitOfWork->shouldReceive('commit')
            ->once()->andReturn();
        $acceptRide = new AcceptRide(
            accountRepository: $this->accountRepository,
            rideRepository: $this->rideRepository,
            messageBroker: $messageBroker,
            unitOfWork: $unitOfWork
        );
        $acceptRide->execute($acceptRideInput);

        $getRideInput = new GetRideInput($rideModel->ride_id);
        $positionRepository = new PositionModelRepository(new PositionModel);
        $getRide = new GetRide(
            rideRepository: $this->rideRepository,
            positionRepository: $positionRepository
        );
        $getRideOutput = $getRide->execute($getRideInput);
        expect($getRideOutput)->toBeInstanceOf(GetRideOutput::class);
        expect($getRideOutput->driverId)->toBe($driverModel->account_id);
        expect($getRideOutput->status)->toBe('accepted');
    });
});
