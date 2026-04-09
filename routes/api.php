<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiAuthController;
use App\Http\Controllers\Api\ApiProductionController;
use App\Http\Controllers\Api\ApiOvertimeController;

// Public routes (no token needed)
Route::post('/login', [ApiAuthController::class, 'login']);

// Protected routes (need Bearer token)
Route::middleware('api.token')->group(function () {
    // Auth
    Route::post('/logout', [ApiAuthController::class, 'logout']);
    Route::get('/me', [ApiAuthController::class, 'me']);

    // Production
    Route::get('/employees', [ApiProductionController::class, 'employees']);
    Route::post('/input/{plant}', [ApiProductionController::class, 'store']);
    Route::get('/live-data/{plant?}', [ApiProductionController::class, 'liveData']);

    // Overtime
    Route::get('/overtimes', [ApiOvertimeController::class, 'index']);
    Route::post('/overtimes', [ApiOvertimeController::class, 'store']);
    Route::patch('/overtimes/{overtime}/approve', [ApiOvertimeController::class, 'approve']);
    Route::patch('/overtimes/{overtime}/reject', [ApiOvertimeController::class, 'reject']);
    Route::delete('/overtimes/{overtime}', [ApiOvertimeController::class, 'destroy']);
    Route::get('/employee-names', [ApiOvertimeController::class, 'employeeNames']);
});
