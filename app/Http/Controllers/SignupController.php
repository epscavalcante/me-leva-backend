<?php

namespace App\Http\Controllers;

use Core\Application\UseCases\DTOs\SignupInput;
use Core\Application\UseCases\Signup;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SignupController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, Signup $signup)
    {
        // validar usando o form_request
        $input = new SignupInput(
            firstName: $request->input('first_name'),
            lastName: $request->input('last_name'),
            email: $request->input('email'),
            phone: $request->input('phone'),
            isDriver: $request->input('is_driver'),
            isPassenger: $request->input('is_passenger'),
        );
        $output = $signup->execute($input);

        return response()->json($output, Response::HTTP_CREATED);
    }
}
