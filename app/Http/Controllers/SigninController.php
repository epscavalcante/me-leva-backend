<?php

namespace App\Http\Controllers;

use App\Http\Requests\SigninRequest;
use Core\Application\UseCases\DTOs\SigninInput;
use Core\Application\UseCases\Signin;
use Illuminate\Http\Request;

class SigninController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(SigninRequest $request, Signin $signin)
    {
        $signinInput = new SigninInput(
            email: $request->validated('email'),
            password: $request->validated('password'),
        );
        $signinOutput = $signin->execute($signinInput);
        return response()->json([
            'access_token' => $signinOutput->accessToken
        ]);
    }
}
