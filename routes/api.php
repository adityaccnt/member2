<?php

use App\Http\Middleware\AuthJWT;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TokenController;

Route::get('token', [TokenController::class, 'token']);

Route::middleware([AuthJWT::class])->group(function () {
    Route::apiResource('user', UserController::class);
});
