<?php

use Illuminate\Support\Facades\Route;
use Modules\Focus\Http\Controllers\FocusController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('foci', FocusController::class)->names('focus');
});
