<?php

use Illuminate\Support\Facades\Route;
use Modules\Tasks\Http\Controllers\TaskAttachmentController;
use Modules\Tasks\Http\Controllers\TasksController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('tasks/attachments/{attachment}/download', [TaskAttachmentController::class, 'download'])
        ->whereUuid('attachment')
        ->name('tasks.attachments.download');

    Route::resource('tasks', TasksController::class)->names('tasks');
});
