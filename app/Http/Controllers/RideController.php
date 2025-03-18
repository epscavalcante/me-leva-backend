<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdatePositionRequest;
use Core\Application\UseCases\AcceptRide;
use Core\Application\UseCases\DTOs\AcceptRideInput;
use Core\Application\UseCases\DTOs\FinishRideInput;
use Core\Application\UseCases\DTOs\GetRideInput;
use Core\Application\UseCases\DTOs\GetRidesInput;
use Core\Application\UseCases\DTOs\RequestRideInput;
use Core\Application\UseCases\DTOs\StartRideInput;
use Core\Application\UseCases\DTOs\UpdatePositionInput;
use Core\Application\UseCases\FinishRide;
use Core\Application\UseCases\GetRide;
use Core\Application\UseCases\GetRides;
use Core\Application\UseCases\RequestRide;
use Core\Application\UseCases\StartRide;
use Core\Application\UseCases\UpdatePosition;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RideController extends Controller
{
    public function requestRide(Request $request, RequestRide $requesRide)
    {
        $requestRideInput = new RequestRideInput(
            passengerId: auth()->id(),
            fromLatitude: $request->input('from_latitude'),
            fromLongitude: $request->input('from_longitude'),
            toLatitude: $request->input('to_latitude'),
            toLongitude: $request->input('to_longitude'),
        );
        $requestRideOutput = $requesRide->execute($requestRideInput);

        return response()->json(
            data: [
                'ride_id' => $requestRideOutput->rideId,
            ],
            status: Response::HTTP_CREATED
        );
    }

    public function acceptRide(string $rideId, AcceptRide $acceptRide)
    {
        $driverId = auth()->id();
        $acceptRideinput = new AcceptRideInput($rideId, $driverId);
        $acceptRide->execute($acceptRideinput);

        return response()->noContent();
    }

    public function startRide(string $rideId, StartRide $startRide)
    {
        $startRideinput = new StartRideInput($rideId);
        $startRide->execute($startRideinput);

        return response()->noContent();
    }

    public function finishRide(string $rideId, FinishRide $finishRide)
    {
        $finishRideinput = new FinishRideInput($rideId);
        $finishRide->execute($finishRideinput);

        return response()->noContent();
    }

    public function getRide(string $rideId, GetRide $getRide)
    {
        $getRideInput = new GetRideInput($rideId);
        $getRideOutput = $getRide->execute($getRideInput);

        return response()->json($getRideOutput, Response::HTTP_OK);
    }

    public function getRides(Request $request, GetRides $getRides)
    {
        $getRidesInput = new GetRidesInput(
            status: $request->input('status', null),
            page: $request->input('page', null),
            perPage: $request->input('perPage', null),
            sortBy: $request->input('sortBy', null),
            sortDir: $request->input('sortDir', null),
        );
        $getRidesOutput = $getRides->execute($getRidesInput);

        return response()->json($getRidesOutput, Response::HTTP_OK);
    }

    public function updatePosition(UpdatePositionRequest $request, string $rideId, UpdatePosition $updatePosition)
    {
        $updatePositionInput = new UpdatePositionInput(
            rideId: $rideId,
            latitude: $request->validated('latitude'),
            longitude: $request->validated('longitude'),
        );

        $updatePosition->execute($updatePositionInput);

        return response()->noContent();
    }
}
