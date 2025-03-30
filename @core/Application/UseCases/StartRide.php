<?php

namespace Core\Application\UseCases;

use App\Services\MessageBroker\MessageBroker;
use App\Services\UnitOfWork\UnitOfWork;
use Core\Application\Repositories\RideRepository;
use Core\Application\UseCases\DTOs\StartRideInput;
use Core\Domain\Events\RideStartedEvent;
use Core\Domain\Exceptions\RideNotFoundException;

class StartRide
{
    public function __construct(
        private readonly UnitOfWork $unitOfWork,
        private readonly MessageBroker $messageBroker,
        private readonly RideRepository $rideRepository,
    ) {}

    public function execute(StartRideInput $input): void
    {
        $ride = $this->rideRepository->getById($input->rideId);
        if (! $ride) {
            throw new RideNotFoundException;
        }

        $ride->register(RideStartedEvent::name(), function (RideStartedEvent $event) use ($ride) {
            try {
                $this->rideRepository->update($ride);

                $this->messageBroker->publish($event->getName(), $event->getData());
                $this->unitOfWork->commit();
            } catch (\Throwable $th) {
                $this->unitOfWork->rollback();
                throw $th;
            }
        });

        $ride->start();
    }
}
