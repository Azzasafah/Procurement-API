<?php

use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\DepartmentController;
use App\Http\Controllers\Web\ProcureController;
use App\Http\Controllers\Web\ReportController;
use App\Http\Controllers\Web\RequestController;
use App\Http\Controllers\Web\StockController;
use App\Http\Controllers\Web\UserController;
use App\Http\Controllers\Web\VendorController;
use Illuminate\Support\Facades\Route;

// Public & Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('web.login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('web.logout');
Route::match(['get', 'post'], '/auth/logout', [AuthController::class, 'logout'])->name('logout');
Route::post('/switch-role', [AuthController::class, 'switchRole'])->name('web.switch-role');

Route::get('/api-status', function () {
    return response()->json([
        'app' => config('app.name', 'Procurement API'),
        'version' => '1.0.0',
        'status' => 'operational',
        'api_docs' => '/api/v1'
    ]);
});

// Authenticated ERP Web Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/', function () {
        return redirect()->route('web.dashboard');
    });

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('web.dashboard');

    // Profile & Password
    Route::get('/profile', [AuthController::class, 'profile'])->name('web.profile');
    Route::put('/profile/password', [AuthController::class, 'updatePassword'])->name('web.profile.password');

    // Procurement Requests (Core FSM Lifecycle)
    Route::prefix('requests')->name('web.requests.')->group(function () {
        Route::get('/', [RequestController::class, 'index'])->name('index');
        Route::get('/create', [RequestController::class, 'create'])->name('create');
        Route::post('/', [RequestController::class, 'store'])->name('store');
        Route::get('/{id}', [RequestController::class, 'show'])->name('show');

        // FSM Transitions
        Route::match(['post', 'put'], '/{id}/submit', [RequestController::class, 'submit'])->name('submit');
        Route::match(['post', 'put'], '/{id}/approve', [RequestController::class, 'approve'])->name('approve');
        Route::match(['post', 'put'], '/{id}/reject', [RequestController::class, 'reject'])->name('reject');
        Route::match(['post', 'put'], '/{id}/procure', [RequestController::class, 'procure'])->name('procure');
        Route::match(['post', 'put'], '/{id}/complete', [RequestController::class, 'complete'])->name('complete');
    });

    // Purchase Orders & Logistics
    Route::prefix('procures')->name('web.procures.')->group(function () {
        Route::get('/', [ProcureController::class, 'index'])->name('index');
        Route::get('/{id}', [ProcureController::class, 'show'])->name('show');
        Route::match(['post', 'put'], '/{id}/deliver', [ProcureController::class, 'deliver'])->name('deliver');
    });

    // Warehouse & Stocks
    Route::prefix('stocks')->name('web.stocks.')->group(function () {
        Route::get('/', [StockController::class, 'index'])->name('index');
        Route::get('/create', [StockController::class, 'create'])->name('create');
        Route::post('/', [StockController::class, 'store'])->name('store');
        Route::post('/check', [StockController::class, 'check'])->name('check');
    });

    // Vendor Management
    Route::prefix('vendors')->name('web.vendors.')->group(function () {
        Route::get('/', [VendorController::class, 'index'])->name('index');
        Route::get('/create', [VendorController::class, 'create'])->name('create');
        Route::post('/', [VendorController::class, 'store'])->name('store');
        Route::get('/{id}', [VendorController::class, 'show'])->name('show');
    });

    // Master Data: Departments
    Route::prefix('departments')->name('web.departments.')->group(function () {
        Route::get('/', [DepartmentController::class, 'index'])->name('index');
        Route::post('/', [DepartmentController::class, 'store'])->name('store');
    });

    // Master Data: Users & Roles
    Route::prefix('users')->name('web.users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/create', [UserController::class, 'create'])->name('create');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::patch('/{id}/role', [UserController::class, 'updateRole'])->name('update-role');
    });

    // Executive Reports & BI
    Route::get('/reports', [ReportController::class, 'index'])->name('web.reports.index');
});
