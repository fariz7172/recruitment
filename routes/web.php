<?php

use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\PsychometricController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\AuthController;

// Public Routes
Route::get('/', function () {
    $jobs = \App\Models\Job::active()
        ->where(function ($query) {
            $query->whereNull('deadline')
                  ->orWhere('deadline', '>=', now());
        })
        ->latest()
        ->take(6)
        ->get();
    
    return view('landing', compact('jobs'));
})->name('landing');

// Login Route
Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::post('/login', [AuthController::class, 'authenticate'])->name('login.authenticate');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/lowongan', [JobController::class, 'publicIndex'])->name('jobs.public');
Route::get('/lowongan/{job}/apply', [ApplicationController::class, 'publicCreate'])->name('apply.create');
Route::post('/lowongan/{job}/apply', [ApplicationController::class, 'publicStore'])->name('apply.store');

// Admin Routes
Route::middleware(['auth'])->group(function () {
    // Basic Admin Protection (Assuming admin role check is done inController or via Gate later, simplified for now)
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    });

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Jobs Management
    Route::resource('jobs', JobController::class);

    // Applications Management
    Route::resource('applications', ApplicationController::class)->except(['edit', 'update']);
    Route::patch('/applications/{application}/status', [ApplicationController::class, 'updateStatus'])->name('applications.updateStatus');
    Route::post('/applications/{application}/screen', [ApplicationController::class, 'screen'])->name('applications.screen');

    // Psychometric Tests (protected by auth, further ownership check needed in controller)
    Route::prefix('applications/{application}/psychometric')->name('psychometric.')->group(function () {
        Route::post('/summary', [PsychometricController::class, 'generateFinalSummary'])->name('summary');
        Route::get('/', [PsychometricController::class, 'index'])->name('index');
        Route::get('/{test}/start', [PsychometricController::class, 'start'])->name('start');
        Route::post('/{test}/submit', [PsychometricController::class, 'submit'])->name('submit');
        Route::get('/{test}/result', [PsychometricController::class, 'result'])->name('result');
    });
});

