<?php

use App\Http\Controllers\AdminApprovalController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BorrowingController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ScanController;
use Illuminate\Support\Facades\Route;

// Authentication & Registration Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Public Mobile QR Scan Interface
Route::prefix('scan')->name('scan.')->group(function () {
    Route::get('/camera', [ScanController::class, 'scanner'])->name('camera');
    Route::get('/{uuid}', [ScanController::class, 'show'])->name('show');
});

// Borrow & Return action endpoints
Route::prefix('borrowings')->name('borrowings.')->group(function () {
    Route::post('/borrow/{uuid}', [BorrowingController::class, 'borrow'])->name('borrow');
    Route::post('/return/{uuid}', [BorrowingController::class, 'returnItem'])->name('return');
});

// Admin Protected Routes
Route::middleware('auth')->group(function () {
    
    // Redirect root to assets
    Route::get('/', function () {
        return redirect()->route('assets.index');
    });

    // Assets CRUD & Print
    Route::prefix('assets')->name('assets.')->group(function () {
        Route::get('/', [AssetController::class, 'index'])->name('index');
        Route::get('/create', [AssetController::class, 'create'])->name('create');
        Route::post('/', [AssetController::class, 'store'])->name('store');
        Route::get('/{asset}', [AssetController::class, 'show'])->name('show');
        Route::get('/{asset}/edit', [AssetController::class, 'edit'])->name('edit');
        Route::put('/{asset}', [AssetController::class, 'update'])->name('update');
        Route::get('/{asset}/print-label', [AssetController::class, 'printLabel'])->name('print-label');
        Route::delete('/{asset}', [AssetController::class, 'destroy'])->name('destroy');
        Route::post('/{asset}/restore', [AssetController::class, 'restore'])->name('restore');
    });

    // Active Borrowings Dashboard
    Route::get('/borrowings', [BorrowingController::class, 'index'])->name('borrowings.index');

    // Year-end Financial Audit Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

    // Super Admin: Admin Accounts & Proposal Approvals
    Route::prefix('admin/users')->name('admin.users.')->group(function () {
        Route::get('/', [AdminApprovalController::class, 'index'])->name('index');
        Route::post('/{user}/approve', [AdminApprovalController::class, 'approve'])->name('approve');
        Route::post('/{user}/reject', [AdminApprovalController::class, 'reject'])->name('reject');
        Route::delete('/{user}', [AdminApprovalController::class, 'destroy'])->name('destroy');
    });
});
