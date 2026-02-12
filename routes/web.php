<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public routes
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');

// View dashboards without login (for testing/demo)
Route::get('/user/guest', [DashboardController::class, 'user'])->name('user.guest');
Route::get('/approver/guest', [DashboardController::class, 'approver'])->name('approver.guest');
Route::get('/superadmin/guest', [DashboardController::class, 'superadmin'])->name('superadmin.guest');
// Approve/Reject for guest (redirect back to approver guest)
Route::post('/approver/guest/approve/{id}', [DashboardController::class, 'approveRequest'])->name('approver.guest.approve');
Route::post('/approver/guest/reject/{id}', [DashboardController::class, 'rejectRequest'])->name('approver.guest.reject');

// User panel pages (work for both guest and logged-in)
Route::get('/user/requests/create', [UserController::class, 'createRequest'])->name('user.requests.create');
Route::post('/user/requests', [UserController::class, 'storeRequest'])->name('user.requests.store');
Route::get('/user/requests', [UserController::class, 'viewRequests'])->name('user.requests.view');
Route::redirect('/user/track', '/user/requests', 301);
Route::get('/user/reports', [UserController::class, 'reports'])->name('user.reports');
Route::get('/user/support', [UserController::class, 'support'])->name('user.support');

Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');
Route::match(['get', 'post'], '/logout', [AuthController::class, 'logout'])->name('logout');

// Forgot password: user requests reset; superadmin approves from Admin Management
Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('forgot-password.form');
Route::post('/forgot-password', [AuthController::class, 'submitForgotPasswordRequest'])->name('forgot-password.submit');

/*
|--------------------------------------------------------------------------
| Dashboard redirect (authenticated)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [HomeController::class, 'dashboardRedirect'])->name('dashboard.redirect');

    /*
    |--------------------------------------------------------------------------
    | PRS Dashboards (authenticated)
    |--------------------------------------------------------------------------
    */
    Route::match(['get', 'post'], '/user', [DashboardController::class, 'user'])->name('user.dashboard');
    Route::match(['get', 'post'], '/superadmin', [DashboardController::class, 'superadmin'])->name('superadmin.dashboard');
    Route::get('/superadmin/admins', [SuperAdminController::class, 'admins'])->name('superadmin.admins');
    Route::post('/superadmin/send-otp', [SuperAdminController::class, 'sendOtp'])->name('superadmin.send-otp');
    Route::post('/superadmin/admins/add', [SuperAdminController::class, 'addUser'])->name('superadmin.admins.add');
    Route::put('/superadmin/admins/{user}/role', [SuperAdminController::class, 'updateRole'])->name('superadmin.admins.update-role');
    Route::post('/superadmin/admins/{user}/deactivate', [SuperAdminController::class, 'deactivate'])->name('superadmin.admins.deactivate');
    Route::post('/superadmin/admins/{user}/reactivate', [SuperAdminController::class, 'reactivate'])->name('superadmin.admins.reactivate');
    Route::get('/superadmin/approvers', [SuperAdminController::class, 'approvers'])->name('superadmin.approvers');
    Route::get('/superadmin/requests', [SuperAdminController::class, 'allRequests'])->name('superadmin.requests');
    Route::get('/superadmin/reports', [SuperAdminController::class, 'reports'])->name('superadmin.reports');
    Route::get('/superadmin/settings', [SuperAdminController::class, 'settings'])->name('superadmin.settings');
    Route::match(['get', 'post'], '/approver', [DashboardController::class, 'approver'])->name('approver.dashboard');
    Route::post('/approver/approve/{id}', [DashboardController::class, 'approveRequest'])->name('approver.approve');
    Route::post('/approver/reject/{id}', [DashboardController::class, 'rejectRequest'])->name('approver.reject');
});
