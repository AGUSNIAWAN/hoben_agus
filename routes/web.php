<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SheetSyncController;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/api/sync-area19', [SheetSyncController::class, 'syncArea19Sales'])->name('api.sync.area19');
