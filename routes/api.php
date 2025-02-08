<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\RideController;
use App\Http\Controllers\SignupController;
use Illuminate\Support\Facades\Route;

Route::post('/signup', SignupController::class);
Route::get('/accounts/{account_id}', [AccountController::class, 'getAccount']);

Route::controller(RideController::class)
    ->prefix('rides')
    ->group(function () {
        Route::post('/', 'requestRide');
        Route::get('/{ride_id}', 'getRide');
    });
