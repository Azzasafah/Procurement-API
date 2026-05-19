<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\DepartmentController;
use App\Http\Controllers\API\ProcurementController;
use App\Http\Controllers\API\ReportController;
use App\Http\Controllers\API\RequestController;
use App\Http\Controllers\API\StockController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\VendorController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    // health
    Route::get('/health', function () {
        return response()->json([
            'success' => true,
            'message' => 'Server is running'
        ]);
    });

    // public route
    Route::prefix('auth')->group(function () {
        Route::post('login',    [AuthController::class, 'login']);
        Route::post('register', [AuthController::class, 'register']);
    });

    //protected route
    Route::middleware('auth:sanctum')->group(function () {
        // Auth (self-service)
        Route::prefix('auth')->group(function () {
            Route::post('logout', [AuthController::class, 'logout']);
            Route::get('me',        [AuthController::class, 'me']);
        });

        // user management (admin only)
        Route::middleware('role:admin')->prefix('users')->group(function () {
            Route::get('/',             [UserController::class, 'index']);
            Route::get('/{id}',         [UserController::class, 'show']);
            Route::patch('/{id}/role',  [UserController::class, 'updateRole']);
            Route::post('/',            [UserController::class, 'store']);
            Route::put('/{id}',         [UserController::class, 'update']);
            Route::delete('/{id}',      [UserController::class, 'destroy']);
        });

        // department management
        Route::prefix('departments')->group(function () {
            Route::get('/',         [DepartmentController::class, 'index']);
            Route::get('/{id}',     [DepartmentController::class, 'show']);
            Route::middleware('role:admin, manager')->group(function () {
                Route::post('/',    [DepartmentController::class, 'store']);
                Route::put('/{id}', [DepartmentController::class, 'update']);
                Route::delete('/{id}',  [DepartmentController::class, 'delete']);
            });
        });

        // request management
        Route::prefix('requests')->group(function () {
            Route::get('/',        [RequestController::class, 'index']);
            Route::post('/',       [RequestController::class, 'store']);
            Route::put('/{id}',    [RequestController::class, 'update']);
            Route::delete('/{id}', [RequestController::class, 'destroy'])
                ->middleware('role:admin');
            // State transition
            Route::put('/{id}',    [RequestController::class, 'submit']);
            Route::middleware('role:manager, admin')->group(function () {
                Route::put('/{id}/approve', [RequestController::class, 'approve']);
                Route::put('/{id}/reject',  [RequestController::class, 'reject']);
            });

            Route::middleware('role:purchasing, admin')->group(function () {
                Route::put('/{id}/procure', [RequestController::class, 'procure']);
                Route::put('/{id}/complete', [RequestController::class, 'complete']);
            });
        });

        // procure management
        Route::prefix('procures')->group(function () {
            Route::get('/',        [ProcurementController::class, 'index']);
            // State transition
            Route::middleware('role:purchasing, admin')->group(function () {
                Route::put('/{id}/deliver', [ProcurementController::class, 'deliver']);
            });
        });

        // vendor management
        Route::prefix('vendors')->group(function () {
            Route::get('/',        [VendorController::class, 'index']);
            Route::get('/{id}',    [VendorController::class, 'show']);

            Route::middleware('role:purchasing,admin')->group(function () {
                Route::post('/',       [VendorController::class, 'store']);
                Route::put('/{id}',    [VendorController::class, 'update']);
                Route::delete('/{id}', [VendorController::class, 'destroy']);
            });
        });

        // stock management
        Route::prefix('stocks')->group(function () {
            Route::get('/',        [StockController::class, 'index']);
            Route::get('/{id}',    [StockController::class, 'show']);
            Route::post('/check',  [StockController::class, 'check']);

            Route::middleware('role:warehouse,admin')->group(function () {
                Route::post('/',    [StockController::class, 'store']);
                Route::put('/{id}', [StockController::class, 'update']);
            });
        });

        // report
        Route::middleware('role:manager,admin')->prefix('reports')->group(function () {
            Route::get('summary',            [ReportController::class, 'summary']);
            Route::get('top-departments',    [ReportController::class, 'topDepartments']);
            Route::get('category-per-month', [ReportController::class, 'categoryPerMonth']);
            Route::get('average-lead-time',  [ReportController::class, 'averageLeadTime']);
        });
    });
});
