<?php

namespace Core\Application\UseCases;

use App\Services\MessageBroker\MessageBroker;
use Core\Application\Repositories\PositionRepository;
use Core\Application\Repositories\RideRepository;
use Core\Application\UseCases\DTOs\UpdatePositionInput;
use Core\Domain\Entities\Position;
use Core\Domain\Events\RidePositionUpdatedEvent;
use Core\Domain\Exceptions\RideNotFoundException;

class UpdatePosition
{
    public function __construct(
        private readonly RideRepository $rideRepository,
        private readonly PositionRepository $positionRepository,
        private readonly MessageBroker $messageBroker
    ) {}

    public function execute(UpdatePositionInput $input): void
    {
        $ride = $this->rideRepository->getById($input->rideId);
        if (! $ride) {
            throw new RideNotFoundException;
        }

        $position = Position::create($ride->getId(), $input->latitude, $input->longitude);

        $ride->register(RidePositionUpdatedEvent::name(), function (RidePositionUpdatedEvent $event) use ($position) {
            $this->positionRepository->save($position);

            $this->messageBroker->publish($event->getName(), $event->getData());
        });

        $ride->updatePosition($position);
    }
}
