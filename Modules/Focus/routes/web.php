<?php

use Illuminate\Support\Facades\Route;
use Modules\Focus\Http\Controllers\FocusController;
use Modules\Focus\Livewire\FocusMode;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('focus', FocusMode::class)->name('focus.mode');
    Route::resource('foci', FocusController::class)->names('focus');
});
