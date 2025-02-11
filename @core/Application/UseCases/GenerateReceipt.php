<?php

namespace Core\Application\UseCases;

use Core\Application\Repositories\RideRepository;
use Core\Application\UseCases\DTOs\GenerateReceiptInput;
use Core\Domain\Exceptions\RideNotFoundException;

class GenerateReceipt
{
    public function __construct(
        private readonly RideRepository $rideRepository,
    ) {
    }

    public function execute(GenerateReceiptInput $input): void
    {
        $ride = $this->rideRepository->getById($input->rideId);
        if (! $ride) {
            throw new RideNotFoundException();
        }
        error_log("Generate receipt for ride {$ride->getId()}");
    }
}
