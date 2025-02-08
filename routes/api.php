<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\SignupController;
use Illuminate\Support\Facades\Route;

Route::post('/signup', SignupController::class);
Route::get('/accounts/{account_id}', [AccountController::class, 'getAccount']);
