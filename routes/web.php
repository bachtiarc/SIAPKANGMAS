<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\User\DashboardController as UserDashboardController;
use App\Http\Controllers\User\SubmissionController as UserSubmissionController;
use App\Http\Controllers\User\ConsultationController;
use App\Http\Controllers\User\SubmissionPdfController as UserSubmissionPdfController;
use App\Http\Controllers\Masyarakat\SubmissionController as MasyarakatSubmissionController;
use App\Http\Controllers\User\ProfileController as UserProfileController;
use App\Http\Controllers\Masyarakat\DashboardController as MasyarakatDashboardController;
use App\Http\Controllers\Masyarakat\ProfileController as MasyarakatProfileController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;

// Public Routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/tentang-kami', [HomeController::class, 'about'])->name('about');
Route::get('/kontak', [HomeController::class, 'contact'])->name('contact');

// Authentication Routes (Guest Only)
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    
    Route::get('/register/pegawai', [RegisterController::class, 'showPegawaiForm'])->name('register.pegawai');
    // Jika method di controller adalah 'register', sesuaikan namanya
    Route::post('/register/pegawai', [RegisterController::class, 'registerPegawai']);
    
    Route::get('/register/masyarakat', [RegisterController::class, 'showMasyarakatForm'])->name('register.masyarakat');
    Route::post('/register/masyarakat', [RegisterController::class, 'registerMasyarakat']);
    
    Route::get('/password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');
});

// Logout (Authenticated Users Only)
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Email Verification Routes
Route::middleware('auth')->group(function () {
    Route::get('/email/verify', [VerificationController::class, 'show'])->name('verification.notice');
    Route::post('/email/resend', [VerificationController::class, 'resend'])
        ->middleware('throttle:6,1')
        ->name('verification.resend');
});

Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])
    ->name('verification.verify');


// User Pegawai Dashboard 
Route::middleware(['auth', 'verified', 'role:user,pegawai'])->prefix('pegawai')->name('user.')->group(function () {
    // DASHBOARD & PROFILE
    Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [UserProfileController::class, 'index'])->name('profile');
    Route::put('/password', [UserProfileController::class, 'updatePassword'])->name('password.update');
    Route::put('/profile/photo', [UserProfileController::class, 'updatePhoto'])->name('profile.photo.update');
    
    // PERMOHONAN INFORMASI (SUBMISSIONS)
    Route::get('/permohonan-informasi', [UserSubmissionController::class, 'index'])->name('submissions.index');
    Route::get('/permohonan-informasi/create', [UserSubmissionController::class, 'create'])->name('submissions.create');
    Route::post('/permohonan-informasi', [UserSubmissionController::class, 'store'])->name('submissions.store');
    Route::get('/permohonan-informasi/{submission}', [UserSubmissionController::class, 'show'])->name('submissions.show');
    
    // PDF & VIEW DOCUMENT (FITUR TETAP ADA)
    Route::get('/permohonan-informasi/{submission}/pdf', [UserSubmissionPdfController::class, 'download'])->name('submissions.download');
    Route::get('/permohonan-informasi/document/{document}', [UserSubmissionController::class, 'viewDocument'])->name('submissions.view-document');

    // KONSULTASI (DITAMBAHKAN DISINI)
    Route::resource('consultations', ConsultationController::class);
});

// User Masyarakat Dashboard 
Route::middleware(['auth', 'verified', 'role:user,masyarakat_umum'])->prefix('masyarakat')->name('masyarakat.')->group(function () {
    Route::get('/dashboard', [MasyarakatDashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [MasyarakatProfileController::class, 'index'])->name('profile');
    Route::put('/password', [MasyarakatProfileController::class, 'updatePassword'])->name('password.update');
    Route::put('/profile/photo', [MasyarakatProfileController::class, 'updatePhoto'])->name('profile.photo.update');
    // PERMOHONAN INFORMASI (SUBMISSIONS)
    Route::get('/permohonan-informasi', [MasyarakatSubmissionController::class, 'index'])->name('submissions.index');
    Route::get('/permohonan-informasi/create', [MasyarakatSubmissionController::class, 'create'])->name('submissions.create');
    Route::post('/permohonan-informasi', [MasyarakatSubmissionController::class, 'store'])->name('submissions.store');
    Route::get('/permohonan-informasi/{submission}', [MasyarakatSubmissionController::class, 'show'])->name('submissions.show');
});

// Admin Routes (Role: admin)
Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Route Manajemen Pengajuan
    Route::get('/manajemen-pengajuan/permohonan', [App\Http\Controllers\Admin\SubmissionController::class, 'index'])
        ->name('submissions.permohonan');

    // Route Detail & Update
    Route::get('/manajemen-pengajuan/permohonan/{id}', [App\Http\Controllers\Admin\SubmissionController::class, 'show'])
        ->name('submissions.show');
        
    Route::put('/manajemen-pengajuan/permohonan/{id}', [App\Http\Controllers\Admin\SubmissionController::class, 'update'])
        ->name('submissions.update');

    Route::get('/manajemen-pengajuan/dokumen/{id}', [App\Http\Controllers\Admin\SubmissionController::class, 'downloadDocument'])
        ->name('submissions.document');
});
