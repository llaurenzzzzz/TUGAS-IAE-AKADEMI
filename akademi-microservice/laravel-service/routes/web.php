<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TimetableController;

Route::get('/', [AuthController::class, 'showLogin']);
Route::get('/login', [AuthController::class, 'showLogin']);
Route::get('/dashboard', [DashboardController::class, 'showDashboard']);

Route::get('/health', function () {
    return response()->json([
        'service' => 'laravel-service',
        'status' => 'running'
    ]);
});

Route::get('/report', function () {
    return response()->json([
        'service' => 'laravel-service',
        'message' => 'Laravel Service digunakan untuk simulasi laporan transaksi',
        'data' => [
            'total_report' => 2,
            'report_type' => 'order summary',
            'generated_by' => 'Laravel Service'
        ]
    ]);
});

Route::get('/timetable', [TimetableController::class, 'index']);