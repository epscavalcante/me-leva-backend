<?php

namespace Core\Application\UseCases;

use Core\Application\Repositories\AccountRepository;
use Core\Application\Repositories\RideRepository;
use Core\Application\UseCases\DTOs\GetRidesInput;
use Core\Application\UseCases\DTOs\GetRidesOutput;
use Core\Domain\Entities\Account;
use Core\Domain\Entities\Ride;

class GetRides
{
    public function __construct(
        private readonly RideRepository $rideRepository,
        private readonly AccountRepository $accountRepository,
    ) {}

    public function execute(GetRidesInput $input): GetRidesOutput
    {
        $ridesResult = $this->rideRepository->getRides(
            status: $input->status,
            page: $input->page,
            perPage: $input->perPage,
            sortBy: $input->sortBy,
            sortDir: $input->sortDir,
        );

        $passengerIds = [];
        $driverIds = [];

        foreach ($ridesResult->items() as $key => $ride) {
            $passengerId = $ride->getPassengerId();
            $driverId = $ride->getDriverId();

            if (! in_array($passengerId, $passengerIds))
                $passengerIds[] = $passengerId;

            if (! in_array($driverId, $driverIds))
                $driverIds[] = $driverId;
        }

        $accountIds = [
            ...$passengerIds,
            ...$driverIds,
        ];

        $accounts = $this->accountRepository->getByIds($accountIds);


        $items = [];

        foreach ($ridesResult->items() as $key => $ride) {
            $passengersFiltered = array_filter($accounts, fn (Account $account) => $ride->getPassengerId() === $account->getId());
            $driversFiltered = array_filter($accounts, fn (Account $account) => $ride->getDriverId() === $account->getId());

            $passenger = null;
            $driver = null;

            if (count($passengersFiltered))
                $passenger = reset($passengersFiltered);

            if (count($driversFiltered)) {
                $driver = reset($driversFiltered);
            }

            $items[] = [
                'rideId' => $ride->getId(),
                'status' => $ride->getStatus(),
                'passengerId' => $ride->getPassengerId(),
                'passengerName' => $passenger ? $passenger->getName() :  null,
                'driverId' => $ride->getDriverId(),
                'driverName' => $driver ? $driver->getName() :  null,
                'distance' => $ride->getDistance(),
                'fare' => $ride->getFare(),
                'fromLatitude' => $ride->getFromLatitude(),
                'fromLongitude' => $ride->getFromLongitude(),
                'toLatitude' => $ride->getToLatitude(),
                'toLongitude' => $ride->getToLongitude(),
            ];
        }

        return new GetRidesOutput(
            items: $items,
            total: $ridesResult->total()
        );
    }
}
