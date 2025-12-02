<?php

use App\Modules\Materials\Http\Controllers\MaterialController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'admin'])->group(function (): void {
    Route::get('/material/new-view', [MaterialController::class, 'returnCreate'])->name('material-add');
    Route::post('/material/new', [MaterialController::class, 'create'])->name('material-add-post');
    Route::get('/material/edit/{id}', [MaterialController::class, 'returnUpdate'])->name('edit-material-view');
    Route::post('/material/edit', [MaterialController::class, 'update'])->name('edit-material');
    Route::get('/materials', [MaterialController::class, 'index'])->name('material-index');
    Route::get('/materials/delete', [MaterialController::class, 'delete'])->name('material-delete');
    Route::post('/materials/disable', [MaterialController::class, 'disable'])->name('material-disable');
    Route::post('/materials/enable', [MaterialController::class, 'enable'])->name('material-enable');
});
