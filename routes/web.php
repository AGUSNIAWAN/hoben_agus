<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DailyActivityController;
use App\Http\Controllers\ExcelSubmissionController;
use App\Http\Controllers\SheetSyncController;
use App\Http\Controllers\TaskController;

// Rute Autentikasi
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rute yang Dilindungi (Wajib Login)
Route::middleware(['auth'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    
    // Sinkronisasi Live Sheet
    Route::get('/api/sync-area19', [SheetSyncController::class, 'syncArea19Sales'])->name('api.sync.area19');
    Route::get('/api/sync-serah-terima', [SheetSyncController::class, 'syncSerahTerima'])->name('api.sync.serah_terima');
    Route::get('/api/sync-kfm-helpdesk', [SheetSyncController::class, 'syncKfmHelpdesk'])->name('api.sync.kfm_helpdesk');

    // Manajemen Kegiatan Kanban
    Route::resource('tasks', TaskController::class)->except(['show', 'edit', 'update']);
    Route::patch('/tasks/{task}/status', [TaskController::class, 'updateStatus'])->name('tasks.update_status');

    // Input Checklist Harian Staf (Opsi A)
    Route::post('/daily-activities', [DailyActivityController::class, 'store'])->name('daily_activities.store');

    // Pengajuan & Upload Berkas Excel Staf (Opsi A)
    Route::post('/submissions', [ExcelSubmissionController::class, 'store'])->name('submissions.store');

    // Verifikasi Dokumen oleh Leader (Opsi B)
    Route::patch('/submissions/{submission}/verify', [ExcelSubmissionController::class, 'verify'])->name('submissions.verify');
    Route::get('/submissions/{submission}/download', [ExcelSubmissionController::class, 'download'])->name('submissions.download');
});
