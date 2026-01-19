<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\ClasController;
use App\Http\Controllers\TrainerController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\DivisionController;

// Public routes - Landing Page
Route::get('/', [LandingController::class, 'index'])->name('home');
Route::post('/submit-order', [LandingController::class, 'submit'])->name('landing.submit');

// Academy Registration Routes (Public - Anyone can register)
Route::get('/daftar/magang', [RegistrationController::class, 'magangForm'])->name('admin.registrations.magang');
Route::post('/daftar/magang', [RegistrationController::class, 'storeMagang'])->name('admin.registrations.magang.store');
Route::get('/daftar/sertifikasi', [RegistrationController::class, 'sertifikasiForm'])->name('admin.registrations.sertifikasi');
Route::post('/daftar/sertifikasi', [RegistrationController::class, 'storeSertifikasi'])->name('admin.registrations.sertifikasi.store');


// Authentication routes (Admin & Employee only)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    
    // Google OAuth Routes
    Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('auth.google');
    Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');
    
    // Forgot Password Routes
    Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPasswordForm'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
    
    // Register route removed - no more client self-registration
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// Authenticated routes
Route::middleware(['auth'])->group(function () {
    
    // Division switcher route
    Route::post('/set-division', [DivisionController::class, 'setDivision'])->name('division.set');
    
    // Dashboard routes - role based (Admin & Employee only)
    Route::get('/dashboard', function() {
        $user = auth()->user();
        
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        } else {
            return redirect()->route('employee.dashboard');
        }
    })->name('dashboard');

    // Admin routes
    Route::middleware(['admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'adminDashboard'])->name('dashboard');
        Route::get('/dashboard/filter-data', [DashboardController::class, 'getFilteredData'])->name('dashboard.filter-data');
        Route::post('/dashboard/save-target', [DashboardController::class, 'saveTarget'])->name('dashboard.save-target');
        Route::get('/dashboard/calendar-events', [DashboardController::class, 'getCalendarEvents'])->name('dashboard.calendar-events');
        
        // Orders management (now includes pending review from landing page)
        Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
        Route::post('/orders/{order}/confirm', [OrderController::class, 'confirmPayment'])->name('orders.confirm');
        Route::post('/orders/{order}/reject', [OrderController::class, 'rejectOrder'])->name('orders.reject');
        Route::get('/orders/{order}/installment-info', [OrderController::class, 'getInstallmentInfo'])->name('orders.installment-info');
        Route::post('/orders/{order}/update-installment', [OrderController::class, 'updateInstallment'])->name('orders.update-installment');
        
        // PKS Management
        Route::get('/orders/{order}/pks', [OrderController::class, 'showPksForm'])->name('orders.pks.form');
        Route::post('/orders/{order}/pks/generate', [OrderController::class, 'generatePks'])->name('orders.pks.generate');
        
        // Clients management
        Route::get('/clients', [ClientController::class, 'index'])->name('clients.index');
        Route::get('/clients/{client}', [ClientController::class, 'show'])->name('clients.show');
        Route::get('/clients/{client}/edit', [ClientController::class, 'edit'])->name('clients.edit');
        Route::put('/clients/{client}', [ClientController::class, 'update'])->name('clients.update');
        
        // Projects management
        Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
        Route::get('/projects/create', [ProjectController::class, 'create'])->name('projects.create');
        Route::post('/projects', [ProjectController::class, 'store'])->name('projects.store');
        Route::get('/projects/{project}', [ProjectController::class, 'show'])->name('projects.show');
        Route::patch('/projects/{project}/status', [ProjectController::class, 'updateStatus'])->name('projects.updateStatus');
        Route::patch('/projects/{project}/notes', [ProjectController::class, 'updateNotes'])->name('projects.updateNotes');
        Route::patch('/projects/{project}/mark-completed', [ProjectController::class, 'markCompleted'])->name('projects.markCompleted');
        
        // Project Expenses
        Route::post('/projects/{project}/expenses', [ProjectController::class, 'storeExpense'])->name('projects.expenses.store');
        Route::patch('/projects/{project}/expenses/{expense}', [ProjectController::class, 'updateExpense'])->name('projects.expenses.update');
        Route::delete('/projects/{project}/expenses/{expense}', [ProjectController::class, 'deleteExpense'])->name('projects.expenses.delete');
        
        // Project Chat (Admin)
        Route::post('/projects/{project}/chat', [ProjectController::class, 'storeChat'])->name('projects.chat.store');
        Route::get('/projects/{project}/chats', [ProjectController::class, 'getChats'])->name('projects.chat.get');
        
        // Team member management
        Route::post('/projects/{project}/team-members', [ProjectController::class, 'assignTeamMember'])->name('projects.assignTeamMember');
        Route::delete('/projects/{project}/team-members/{member}', [ProjectController::class, 'removeTeamMember'])->name('projects.removeTeamMember');
        
        // Karyawan (Employee) management
        Route::get('/karyawan', [KaryawanController::class, 'index'])->name('karyawan.index');
        Route::get('/karyawan/create', [KaryawanController::class, 'create'])->name('karyawan.create');
        Route::post('/karyawan', [KaryawanController::class, 'store'])->name('karyawan.store');
        Route::get('/karyawan/{karyawan}', [KaryawanController::class, 'show'])->name('karyawan.show');
        Route::get('/karyawan/{karyawan}/edit', [KaryawanController::class, 'edit'])->name('karyawan.edit');
        Route::put('/karyawan/{karyawan}', [KaryawanController::class, 'update'])->name('karyawan.update');
        Route::delete('/karyawan/{karyawan}', [KaryawanController::class, 'destroy'])->name('karyawan.destroy');
        
        // Laporan (Reports) management
        Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
        Route::get('/laporan/export', [LaporanController::class, 'exportCsv'])->name('laporan.export');
        
        // Teams management
        Route::get('/projects/{project}/teams/create', [TeamController::class, 'create'])->name('teams.create');
        Route::post('/projects/{project}/teams', [TeamController::class, 'store'])->name('teams.store');
        Route::delete('/teams/{team}/members/{member}', [TeamController::class, 'removeMember'])->name('teams.removeMember');

        // Classes management (Academy only)
        Route::get('/classes', [ClasController::class, 'index'])->name('classes.index');
        Route::get('/classes/create', [ClasController::class, 'create'])->name('classes.create');
        Route::get('/classes/approved', [ClasController::class, 'showclas'])->name('classes.showclas');
        Route::post('/classes', [ClasController::class, 'store'])->name('classes.store');
        Route::get('/classes/{clas}', [ClasController::class, 'show'])->name('classes.show');
        Route::get('/classes/{clas}/edit', [ClasController::class, 'edit'])->name('classes.edit');
        Route::put('/classes/{clas}', [ClasController::class, 'update'])->name('classes.update');
        Route::delete('/classes/{clas}', [ClasController::class, 'destroy'])->name('classes.destroy');
        Route::post('/classes/{clas}/approve', [ClasController::class, 'approve'])->name('classes.approve');
        Route::post('/classes/{clas}/reject', [ClasController::class, 'reject'])->name('classes.reject');

        // Tracking Kelas
        Route::get('/tracking', [ClasController::class, 'track'])->name('tracking.index');
        
        // Trainer Management

        // Settings
        Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
        Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');

        // Payment Requests (Admin)
        Route::resource('payment-requests', \App\Http\Controllers\Admin\PaymentRequestController::class)
            ->only(['index', 'show', 'update']);
        
        // Payment Request Actions
        Route::post('/payment-requests/{paymentRequest}/mark-as-paid', [\App\Http\Controllers\Admin\PaymentRequestController::class, 'markAsPaid'])
            ->name('payment-requests.mark-as-paid');
        Route::post('/payment-requests/{paymentRequest}/mark-as-processing', [\App\Http\Controllers\Admin\PaymentRequestController::class, 'markAsProcessing'])
            ->name('payment-requests.mark-as-processing');
    });

    // Employee routes
    Route::middleware(['employee'])->prefix('employee')->name('employee.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'employeeDashboard'])->name('dashboard');
        
        // View assigned projects
        Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
        Route::get('/projects/{project}', [ProjectController::class, 'show'])->name('projects.show');

        // Payment Requests
        Route::resource('payment-requests', \App\Http\Controllers\Employee\PaymentRequestController::class)
            ->only(['index', 'create', 'store', 'show']);

        // Task Attachments
        Route::post('/tasks/{task}/attachments', [\App\Http\Controllers\Employee\TaskAttachmentController::class, 'store'])
            ->name('tasks.attachments.store');
        Route::get('/attachments/{attachment}/download', [\App\Http\Controllers\Employee\TaskAttachmentController::class, 'download'])
            ->name('tasks.download-attachment');
        Route::delete('/attachments/{attachment}', [\App\Http\Controllers\Employee\TaskAttachmentController::class, 'destroy'])
            ->name('tasks.attachments.destroy');

        // Project Chat
        Route::get('/projects/{project}/chat', [\App\Http\Controllers\Employee\ProjectChatController::class, 'index'])
            ->name('projects.chat');
        Route::post('/projects/{project}/chat', [\App\Http\Controllers\Employee\ProjectChatController::class, 'store'])
            ->name('projects.chat.store');
    });

    // Client routes
    Route::middleware(['client'])->prefix('client')->name('client.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'clientDashboard'])->name('dashboard');
        
        // Client Projects - View and Monitor
        Route::get('/projects', [\App\Http\Controllers\Client\ClientProjectController::class, 'index'])->name('projects.index');
        Route::get('/projects/{project}', [\App\Http\Controllers\Client\ClientProjectController::class, 'show'])->name('projects.show');
        
        // Project Chat (Global - Client, Team & Admin)
        Route::post('/projects/{project}/chat', [\App\Http\Controllers\Client\ClientProjectController::class, 'storeChat'])->name('projects.chat');
        Route::get('/projects/{project}/chat/messages', [\App\Http\Controllers\Client\ClientProjectController::class, 'getChats'])->name('projects.chat.messages');
    });
});

