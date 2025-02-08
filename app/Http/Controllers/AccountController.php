<?php

namespace App\Http\Controllers;

use Core\Application\UseCases\DTOs\GetAccountInput;
use Core\Application\UseCases\GetAccount;
use Illuminate\Http\Response;

class AccountController extends Controller
{
    public function getAccount(string $accountId, GetAccount $getAccount)
    {
        $getAccountInput = new GetAccountInput($accountId);
        $getAccountOutput = $getAccount->execute($getAccountInput);

        return response()->json($getAccountOutput, Response::HTTP_OK);
    }
}
