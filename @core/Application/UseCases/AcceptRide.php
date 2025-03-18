<?php

namespace Core\Application\UseCases;

use App\Services\MessageBroker\MessageBroker;
use Core\Application\Repositories\AccountRepository;
use Core\Application\Repositories\RideRepository;
use Core\Application\UseCases\DTOs\AcceptRideInput;
use Core\Domain\Events\RideAcceptedEvent;
use Core\Domain\Exceptions\AccountCannotBeAcceptRideException;
use Core\Domain\Exceptions\AccountNotFoundException;
use Core\Domain\Exceptions\RideNotFoundException;

class AcceptRide
{
    public function __construct(
        private readonly RideRepository $rideRepository,
        private readonly AccountRepository $accountRepository,
        private readonly MessageBroker $messageBroker,
    ) {}

    public function execute(AcceptRideInput $input): void
    {
        $account = $this->accountRepository->getById($input->driverId);
        if (! $account) {
            throw new AccountNotFoundException;
        }

        if (! $account->canAcceptRide()) {
            throw new AccountCannotBeAcceptRideException;
        }

        $ride = $this->rideRepository->getById($input->rideId);
        if (! $ride) {
            throw new RideNotFoundException;
        }

        $ride->register(RideAcceptedEvent::name(), function (RideAcceptedEvent $event) use ($ride) {
            $this->rideRepository->update($ride);
            $this->messageBroker->publish($event->getName(), $event->getData());
        });

        $ride->accept($account->getId());
    }
}
