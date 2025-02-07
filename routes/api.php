<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\SignupController;
use Illuminate\Support\Facades\Route;


Route::post('/signup', SignupController::class);

// Route::controller(AccountController::class)
//     ->prefix('v1')
//     ->group(fn () => {
//         Route::post('/signup')


//     });
