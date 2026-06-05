<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;

Route::get('/', [DashboardController::class, 'index']);
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