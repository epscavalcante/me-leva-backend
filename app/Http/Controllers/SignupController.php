<?php

namespace App\Http\Controllers;

use App\Http\Requests\SignupRequest;
use Core\Application\UseCases\DTOs\SignupInput;
use Core\Application\UseCases\Signup;
use Illuminate\Http\Response;

class SignupController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(SignupRequest $request, Signup $signup)
    {
        // validar usando o form_request
        $input = new SignupInput(
            firstName: $request->validated('first_name'),
            lastName: $request->validated('last_name'),
            email: $request->validated('email'),
            phone: $request->validated('phone'),
            isDriver: $request->validated('is_driver'),
            isPassenger: $request->validated('is_passenger'),
            password: $request->validated('password')
        );
        $output = $signup->execute($input);

        return response()->json($output, Response::HTTP_CREATED);
    }
}
