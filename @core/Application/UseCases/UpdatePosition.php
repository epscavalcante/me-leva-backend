<?php

namespace Core\Application\UseCases;

use Core\Application\Repositories\PositionRepository;
use Core\Application\Repositories\RideRepository;
use Core\Application\UseCases\DTOs\UpdatePositionInput;
use Core\Domain\Entities\Position;
use Core\Domain\Events\Event;
use Core\Domain\Events\EventDispatcher;
use Core\Domain\Events\RidePositionUpdatedEvent;
use Core\Domain\Exceptions\RideNotFoundException;

class UpdatePosition
{
    public function __construct(
        private readonly RideRepository $rideRepository,
        private readonly PositionRepository $positionRepository,
        private readonly EventDispatcher $eventDispatcher
    ) {}

    public function execute(UpdatePositionInput $input): void
    {
        $ride = $this->rideRepository->getById($input->rideId);
        if (! $ride) {
            throw new RideNotFoundException;
        }

        $position = Position::create($ride->getId(), $input->latitude, $input->longitude);

        $ride->register(RidePositionUpdatedEvent::name(), function (Event $event) use ($position) {
            $this->positionRepository->save($position);

            $this->eventDispatcher->dispatch($event);
        });

        $ride->updatePosition($position);
    }
}
