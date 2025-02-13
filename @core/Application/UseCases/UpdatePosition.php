<?php

namespace Core\Application\UseCases;

use Core\Application\Repositories\PositionRepository;
use Core\Application\Repositories\RideRepository;
use Core\Application\UseCases\DTOs\UpdatePositionInput;
use Core\Domain\Entities\Position;
use Core\Domain\Exceptions\RideNotFoundException;

class UpdatePosition
{
    public function __construct(
        private readonly RideRepository $rideRepository,
        private readonly PositionRepository $positionRepository,
    ) {
    }

    public function execute(UpdatePositionInput $input): void
    {
        $ride = $this->rideRepository->getById($input->rideId);
        if (! $ride) {
            throw new RideNotFoundException();
        }

        $position = Position::create($ride->getId(), $input->latitude, $input->longitude);

        $this->positionRepository->save($position);
    }
}
