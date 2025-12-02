<?php

use App\Modules\JobTypes\Http\Controllers\JobTypeController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'admin'])->group(function (): void {
    Route::get('/Job-type/index', [JobTypeController::class, 'index'])->name('job-type-index');
    Route::get('/Job-type/new-view', [JobTypeController::class, 'returnCreate'])->name('new-job-type-view');
    Route::post('/Job-type/new-post', [JobTypeController::class, 'create'])->name('new-job-type');
    Route::get('/Job-type/edit-view/{id}', [JobTypeController::class, 'returnUpdate'])->name('edit-job-type-view');
    Route::post('/Job-type/edit-post', [JobTypeController::class, 'update'])->name('edit-job-type');
});
