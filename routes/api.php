<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\DepartmentController;
use App\Http\Controllers\API\ProcurementController;
use App\Http\Controllers\API\ReportController;
use App\Http\Controllers\API\RequestController;
use App\Http\Controllers\API\StockController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\VendorController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - Procurement System v1
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {

    // Public Authentication Endpoints
    Route::prefix('auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);
    });

    // Authenticated Endpoints
    Route::middleware('auth:sanctum')->group(function () {

        // Auth Profile & Session
        Route::prefix('auth')->group(function () {
            Route::post('/logout', [AuthController::class, 'logout']);
            Route::get('/me', [AuthController::class, 'me']);
        });

        // User Management (Admin Only)
        Route::middleware('role:admin')->group(function () {
            Route::get('/users', [UserController::class, 'index']);
            Route::get('/users/{id}', [UserController::class, 'show']);
            Route::post('/users', [UserController::class, 'store']);
            Route::put('/users/{id}', [UserController::class, 'update']);
            Route::patch('/users/{id}/role', [UserController::class, 'updateRole']);
            Route::delete('/users/{id}', [UserController::class, 'destroy']);
        });

        // Department Management
        Route::get('/departments', [DepartmentController::class, 'index']);
        Route::get('/departments/{id}', [DepartmentController::class, 'show']);
        Route::middleware('role:admin')->group(function () {
            Route::post('/departments', [DepartmentController::class, 'store']);
            Route::put('/departments/{id}', [DepartmentController::class, 'update']);
            Route::delete('/departments/{id}', [DepartmentController::class, 'delete']);
        });

        // Vendor Management
        Route::get('/vendors', [VendorController::class, 'index']);
        Route::get('/vendors/{id}', [VendorController::class, 'show']);
        Route::middleware('role:admin,purchasing')->group(function () {
            Route::post('/vendors', [VendorController::class, 'store']);
            Route::put('/vendors/{id}', [VendorController::class, 'update']);
            Route::delete('/vendors/{id}', [VendorController::class, 'destroy']);
        });

        // Stock & Inventory Management
        Route::get('/stocks', [StockController::class, 'index']);
        Route::get('/stocks/{id}', [StockController::class, 'show']);
        Route::post('/stocks/check', [StockController::class, 'check']);
        Route::middleware('role:admin,warehouse')->group(function () {
            Route::post('/stocks', [StockController::class, 'store']);
            Route::put('/stocks/{id}', [StockController::class, 'update']);
        });

        // Procurement Requests Lifecycle
        Route::get('/requests', [RequestController::class, 'index']);
        Route::post('/requests', [RequestController::class, 'store']);
        Route::put('/requests/{id}', [RequestController::class, 'update']);
        Route::delete('/requests/{id}', [RequestController::class, 'destroy']);
        Route::put('/requests/{id}/submit', [RequestController::class, 'submit']);

        // Approvals (Manager & Admin)
        Route::middleware('role:manager,admin')->group(function () {
            Route::put('/requests/{id}/approve', [RequestController::class, 'approve']);
            Route::put('/requests/{id}/reject', [RequestController::class, 'reject']);
        });

        // Purchasing & Fulfillment (Purchasing & Admin)
        Route::middleware('role:purchasing,admin')->group(function () {
            Route::put('/requests/{id}/procure', [RequestController::class, 'procure']);
            Route::put('/requests/{id}/complete', [RequestController::class, 'complete']);
            Route::get('/procures', [ProcurementController::class, 'index']);
            Route::put('/procures/{id}/deliver', [ProcurementController::class, 'deliver']);
        });

        // Analytics & Reports (Admin & Manager)
        Route::middleware('role:admin,manager')->prefix('reports')->group(function () {
            Route::get('/summary', [ReportController::class, 'summary']);
            Route::get('/top-departments', [ReportController::class, 'topDepartments']);
            Route::get('/category-per-month', [ReportController::class, 'categoryPerMonth']);
            Route::get('/average-lead-time', [ReportController::class, 'averageLeadTime']);
        });
    });
});
