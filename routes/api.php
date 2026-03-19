<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\DataSourceController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\ScheduledReportController;
use App\Http\Controllers\Api\ExportController;
use Illuminate\Support\Facades\Route;

// Public auth routes
Route::prefix('v1/auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
});

// Protected routes with Sanctum authentication
Route::middleware('auth:sanctum')->prefix('v1')->group(function () {
    
    // Auth user routes
    Route::prefix('auth')->group(function () {
        Route::get('/user', [AuthController::class, 'user']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::put('/profile', [AuthController::class, 'updateProfile']);
        Route::put('/password', [AuthController::class, 'changePassword']);
    });
    
    // Users management
    Route::apiResource('users', UserController::class);
    Route::put('/users/{user}/status', [UserController::class, 'updateStatus']);
    Route::put('/users/{user}/role', [UserController::class, 'updateRole']);
    
    // Data Sources
    Route::apiResource('data-sources', DataSourceController::class);
    Route::post('/data-sources/{dataSource}/test-connection', [DataSourceController::class, 'testConnection']);
    Route::get('/data-sources/{dataSource}/tables', [DataSourceController::class, 'getTables']);
    Route::get('/data-sources/{dataSource}/columns', [DataSourceController::class, 'getColumns']);
    
    // Reports
    Route::apiResource('reports', ReportController::class);
    Route::get('/reports/{report}/run', [ReportController::class, 'run']);
    Route::post('/reports/{report}/export', [ReportController::class, 'export']);
    Route::post('/reports/{report}/duplicate', [ReportController::class, 'duplicate']);
    Route::get('/reports/{report}/preview', [ReportController::class, 'preview']);
    Route::put('/reports/{report}/publish', [ReportController::class, 'publish']);
    
    // Dashboards
    Route::apiResource('dashboards', DashboardController::class);
    Route::post('/dashboards/{dashboard}/widgets', [DashboardController::class, 'addWidget']);
    Route::put('/dashboards/{dashboard}/widgets/{widget}', [DashboardController::class, 'updateWidget']);
    Route::delete('/dashboards/{dashboard}/widgets/{widget}', [DashboardController::class, 'removeWidget']);
    Route::post('/dashboards/{dashboard}/duplicate', [DashboardController::class, 'duplicate']);
    Route::put('/dashboards/{dashboard}/layout', [DashboardController::class, 'updateLayout']);
    
    // Scheduled Reports
    Route::apiResource('scheduled-reports', ScheduledReportController::class);
    Route::put('/scheduled-reports/{scheduledReport}/status', [ScheduledReportController::class, 'updateStatus']);
    Route::post('/scheduled-reports/{scheduledReport}/run-now', [ScheduledReportController::class, 'runNow']);
    Route::get('/scheduled-reports/{scheduledReport}/executions', [ScheduledReportController::class, 'getExecutions']);
    
    // Exports
    Route::get('/exports', [ExportController::class, 'index']);
    Route::get('/exports/{export}', [ExportController::class, 'show']);
    Route::get('/exports/{export}/download', [ExportController::class, 'download']);
    Route::delete('/exports/{export}', [ExportController::class, 'destroy']);
    
    // Additional utility routes
    Route::get('/export-formats', [ExportController::class, 'getFormats']);
    Route::get('/chart-types', [ReportController::class, 'getChartTypes']);
    Route::get('/aggregation-functions', [ReportController::class, 'getAggregationFunctions']);
    Route::get('/filter-operators', [ReportController::class, 'getFilterOperators']);
    
    // System settings and statistics
    Route::get('/system/stats', [UserController::class, 'getSystemStats']);
    Route::get('/system/activity', [UserController::class, 'getActivityLog']);
    
    // Search and filtering
    Route::get('/search/reports', [ReportController::class, 'search']);
    Route::get('/search/dashboards', [DashboardController::class, 'search']);
    Route::get('/search/data-sources', [DataSourceController::class, 'search']);
    
    // Favorites
    Route::post('/reports/{report}/favorite', [ReportController::class, 'toggleFavorite']);
    Route::post('/dashboards/{dashboard}/favorite', [DashboardController::class, 'toggleFavorite']);
    Route::get('/favorites/reports', [ReportController::class, 'getFavorites']);
    Route::get('/favorites/dashboards', [DashboardController::class, 'getFavorites']);
    
    // Sharing
    Route::post('/reports/{report}/share', [ReportController::class, 'share']);
    Route::post('/dashboards/{dashboard}/share', [DashboardController::class, 'share']);
    Route::get('/shared/reports', [ReportController::class, 'getShared']);
    Route::get('/shared/dashboards', [DashboardController::class, 'getShared']);
    
    // Comments and collaboration
    Route::post('/reports/{report}/comments', [ReportController::class, 'addComment']);
    Route::put('/reports/{report}/comments/{comment}', [ReportController::class, 'updateComment']);
    Route::delete('/reports/{report}/comments/{comment}', [ReportController::class, 'deleteComment']);
    Route::get('/reports/{report}/comments', [ReportController::class, 'getComments']);
});

// Public routes for shared content (with token authentication)
Route::prefix('v1/public')->group(function () {
    Route::get('/reports/{report}/view', [ReportController::class, 'publicView'])->middleware('signed');
    Route::get('/dashboards/{dashboard}/view', [DashboardController::class, 'publicView'])->middleware('signed');
    Route::get('/exports/{export}/download', [ExportController::class, 'publicDownload'])->middleware('signed');
});

// Health check
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now()->toISOString(),
        'version' => config('app.version', '1.0.0')
    ]);
});