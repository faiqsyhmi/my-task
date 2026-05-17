<?php

use Illuminate\Support\Facades\Route;
use Modules\Routines\Http\Controllers\RoutinesController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('routines', RoutinesController::class)->names('routines');
});
