<?php

namespace Core\Application\UseCases\CancelRide;

use App\Services\MessageBroker\MessageBroker;
use App\Services\UnitOfWork\UnitOfWork;
use Core\Application\Repositories\RideRepository;
use Core\Domain\Events\RideCanceledEvent;
use Core\Domain\Exceptions\RideNotFoundException;

class CancelRideUseCase
{
    public function __construct(
        private readonly RideRepository $rideRepository,
        private readonly MessageBroker $messageBroker,
        private readonly UnitOfWork $unitOfWork,
    ) {}

    public function execute(CancelRideInput $input): void
    {
        $ride = $this->rideRepository->getById($input->rideId);
        if (! $ride) {
            throw new RideNotFoundException;
        }

        $ride->register(RideCanceledEvent::name(), function (RideCanceledEvent $event) use ($ride) {
            try {
                $this->rideRepository->update($ride);

                $this->messageBroker->publish($event->getName(), $event->getData());
                $this->unitOfWork->commit();
            } catch (\Throwable $th) {
                $this->unitOfWork->rollback();
                throw $th;
            }
        });

        $ride->cancel();
    }
}
