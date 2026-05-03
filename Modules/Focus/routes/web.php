<?php

use Illuminate\Support\Facades\Route;
use Modules\Focus\Http\Controllers\FocusController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('foci', FocusController::class)->names('focus');
});
