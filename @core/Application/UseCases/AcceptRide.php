<?php

namespace Core\Application\UseCases;

use Core\Application\Repositories\AccountRepository;
use Core\Application\Repositories\RideRepository;
use Core\Application\UseCases\DTOs\AcceptRideInput;
use Core\Domain\Exceptions\AccountCannotBeAcceptRideException;
use Core\Domain\Exceptions\AccountNotFoundException;
use Core\Domain\Exceptions\RideNotFoundException;

class AcceptRide
{
    public function __construct(
        private readonly RideRepository $rideRepository,
        private readonly AccountRepository $accountRepository,
    ) {
    }

    public function execute(AcceptRideInput $input): void
    {
        $account = $this->accountRepository->getById($input->driverId);
        if (! $account) {
            throw new AccountNotFoundException();
        }

        if (! $account->canAcceptRide()) {
            throw new AccountCannotBeAcceptRideException();
        }

        $ride = $this->rideRepository->getById($input->rideId);
        if (! $ride) {
            throw new RideNotFoundException();
        }

        $ride->accept($account->getId());

        $this->rideRepository->update($ride);
    }
}
