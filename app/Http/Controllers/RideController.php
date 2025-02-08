<?php

namespace App\Http\Controllers;

use Core\Application\UseCases\AcceptRide;
use Core\Application\UseCases\DTOs\AcceptRideInput;
use Core\Application\UseCases\DTOs\GetRideInput;
use Core\Application\UseCases\DTOs\RequestRideInput;
use Core\Application\UseCases\GetRide;
use Core\Application\UseCases\RequestRide;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RideController extends Controller
{
    public function requestRide(Request $request, RequestRide $requesRide)
    {
        $requestRideInput = new RequestRideInput(
            passengerId: $request->input('passenger_id'),
            fromLatitude: $request->input('from_latitude'),
            fromLongitude: $request->input('from_longitude'),
            toLatitude: $request->input('to_latitude'),
            toLongitude: $request->input('to_longitude'),
        );
        $requestRideOutput = $requesRide->execute($requestRideInput);

        return response()->json($requestRideOutput, Response::HTTP_CREATED);
    }

    public function acceptRide(Request $request, string $rideId, AcceptRide $acceptRide)
    {
        $driverId = $request->input('driver_id');
        $acceptRideinput = new AcceptRideInput($rideId, $driverId);
        $acceptRide->execute($acceptRideinput);

        return response()->noContent();
    }

    public function getRide(string $rideId, GetRide $getRide)
    {
        $getRideInput = new GetRideInput($rideId);
        $getRideOutput = $getRide->execute($getRideInput);

        return response()->json($getRideOutput, Response::HTTP_OK);
    }
}
