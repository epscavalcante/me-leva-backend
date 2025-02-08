<?php

namespace Core\Application\UseCases;

use Core\Application\Repositories\AccountRepository;
use Core\Application\Repositories\RideRepository;
use Core\Application\UseCases\DTOs\RequestRideInput;
use Core\Application\UseCases\DTOs\RequestRideOutput;
use Core\Domain\Entities\Ride;
use Core\Domain\Exceptions\AccountCannotRequestRideException;
use Core\Domain\Exceptions\AccountNotFoundException;

class RequestRide
{
    public function __construct(
        private readonly RideRepository $rideRepository,
        private readonly AccountRepository $accountRepository,
    ) {
    }

    public function execute(RequestRideInput $input): RequestRideOutput
    {
        $account = $this->accountRepository->getById($input->passengerId);
        if (! $account) {
            throw new AccountNotFoundException();
        }

        if (! $account->canRequestRide()) {
            throw new AccountCannotRequestRideException();
        }

        $ride = Ride::create(
            passengerId: $input->passengerId,
            fromLatitude: $input->fromLatitude,
            fromLongitude: $input->fromLongitude,
            toLatitude: $input->toLatitude,
            toLongitude: $input->toLongitude
        );

        $this->rideRepository->save($ride);

        return new RequestRideOutput(
            rideId: $ride->getId(),
        );
    }
}
