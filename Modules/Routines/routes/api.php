<?php

use Illuminate\Support\Facades\Route;
use Modules\Routines\Http\Controllers\RoutinesController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('routines', RoutinesController::class)->names('routines');
});
