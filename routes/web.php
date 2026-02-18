<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Api\WilayahController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\User\DashboardController as UserDashboardController;
use App\Http\Controllers\User\SubmissionController as UserSubmissionController;
use App\Http\Controllers\User\ConsultationController;
use App\Http\Controllers\User\ConsultationPdfController;
use App\Http\Controllers\User\SubmissionPdfController as UserSubmissionPdfController;
use App\Http\Controllers\User\HistoryController;
use App\Http\Controllers\Masyarakat\SubmissionController as MasyarakatSubmissionController;
use App\Http\Controllers\User\ComplaintController;
use App\Http\Controllers\User\ComplaintPdfController;
use App\Http\Controllers\User\ProfileController as UserProfileController;
use App\Http\Controllers\Masyarakat\DashboardController as MasyarakatDashboardController;
use App\Http\Controllers\Masyarakat\ProfileController as MasyarakatProfileController;
use App\Http\Controllers\Masyarakat\SubmissionPdfController;
use App\Http\Controllers\Masyarakat\ConsultationController as MasyarakatConsultationController;
use App\Http\Controllers\Masyarakat\ConsultationPdfController as MasyarakatConsultationPdfController;
use App\Http\Controllers\Masyarakat\ComplaintController as MasyarakatComplaintController;
use App\Http\Controllers\Masyarakat\ComplaintPdfController as MasyarakatComplaintPdfController;
use App\Http\Controllers\Masyarakat\HistoryController as MasyarakatHistoryController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\SubmissionController as AdminSubmissionController;
use App\Http\Controllers\Admin\ConsultationController as AdminConsultationController;
use App\Http\Controllers\Admin\ComplaintController as AdminComplaintController;
use App\Http\Controllers\Admin\AllSubmissionsController;
use App\Http\Controllers\Admin\ReportExportController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\TicketSearchController;
use App\Http\Controllers\ContactController;
use App\Services\BrevoMailer;

// Public Routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/tentang-kami', [HomeController::class, 'about'])->name('about');
Route::get('/kontak', [HomeController::class, 'contact'])->name('contact');

// PENCARIAN TIKET TANPA LOGIN (PUBLIC)
Route::get('/lacak-tiket', [SearchController::class, 'publicSearch'])->name('ticket.search');
// Kirim pesan kontak (PUBLIC)
Route::post('/kontak/kirim', [ContactController::class, 'send'])->name('contact.send');

// PENCARIAN TIKET UNTUK YANG SUDAH LOGIN
Route::middleware(['auth'])->group(function () {
    Route::get('/search/preview', [SearchController::class, 'preview'])->name('search.preview');
    Route::get('/search/result', [SearchController::class, 'result'])->name('search.result');
});

// Authentication Routes (Guest Only)
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register'])->name('register.submit');
    
    Route::get('/password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Email Verification 
Route::middleware('auth')->group(function () {
    Route::get('/email/verify', [VerificationController::class, 'show'])->name('verification.notice');
    Route::post('/email/resend', [VerificationController::class, 'resend'])
        ->middleware('throttle:6,1')
        ->name('verification.resend');
});

// RESEND VERIFIKASI TANPA LOGIN
    Route::post('/register/resend', 
        [RegisterController::class, 'resendVerification']
    )->name('resend.verification');

Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])
    ->name('verification.verify');

// WILAYAH
Route::prefix('api')->group(function () {
    Route::get('/kabupaten', [WilayahController::class, 'kabupaten']);
    Route::get('/kecamatan/{kodeKab}', [WilayahController::class, 'kecamatan']);
    Route::get('/desa/{kodeKec}', [WilayahController::class, 'desa']);
});

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
    
    // PDF & VIEW/DOWNLOAD DOCUMENT - DITAMBAHKAN ROUTE DOWNLOAD
    Route::get('/permohonan-informasi/{submission}/pdf', [UserSubmissionPdfController::class, 'download'])->name('submissions.download');
    Route::get('/permohonan-informasi/document/{document}/view', [UserSubmissionController::class, 'viewDocument'])->name('submissions.view');
    Route::get('/permohonan-informasi/document/{document}/download', [UserSubmissionController::class, 'downloadDocument'])->name('submissions.document.download');

    // KONSULTASI 
    Route::resource('consultations', ConsultationController::class);
    Route::get('/consultations', [ConsultationController::class, 'index'])->name('consultations.index');
    Route::get('/consultations/create', [ConsultationController::class, 'create'])->name('consultations.create');
    Route::post('/consultations', [ConsultationController::class, 'store'])->name('consultations.store');
    Route::get('/consultations/{consultation}', [ConsultationController::class, 'show'])->name('consultations.show');
    Route::get('/consultations/{consultation}/pdf', [ConsultationPdfController::class, 'download'])->name('consultations.download');
    Route::get('/consultations/document/{document}/download', [ConsultationController::class, 'downloadDocument'])
    ->name('consultations.document.download');

    // PENGADUAN (COMPLAINTS)
    Route::get('/complaints', [ComplaintController::class, 'index'])->name('complaints.index');
    Route::get('/complaints/create', [ComplaintController::class, 'create'])->name('complaints.create');
    Route::post('/complaints', [ComplaintController::class, 'store'])->name('complaints.store');
    Route::get('/complaints/{complaint}', [ComplaintController::class, 'show'])->name('complaints.show');
    Route::get('/complaints/{complaint}/pdf', [ComplaintPdfController::class, 'download'])->name('complaints.download');
    Route::get('/complaints/document/{document}', [ComplaintController::class, 'viewDocument'])->name('complaints.documents.view');
    Route::get('/complaints/document/{document}/download', [ComplaintController::class, 'downloadDocument'])
    ->name('complaints.document.download');

    // RIWAYAT PENGAJUAN - PERBAIKAN: Menggunakan HistoryController yang sudah di-import di atas
    Route::get('/riwayat-pengajuan', [HistoryController::class, 'index'])->name('history.index');
});

// User Masyarakat Dashboard 
Route::middleware(['auth', 'verified'])->prefix('masyarakat')->name('masyarakat.')->group(function () {
    Route::get('/dashboard', [MasyarakatDashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [MasyarakatProfileController::class, 'index'])->name('profile');
    Route::put('/password', [MasyarakatProfileController::class, 'updatePassword'])->name('password.update');
    Route::put('/profile/photo', [MasyarakatProfileController::class, 'updatePhoto'])->name('profile.photo.update');

    // PERMOHONAN INFORMASI (SUBMISSIONS)
    Route::get('/permohonan-informasi', [MasyarakatSubmissionController::class, 'index'])->name('submissions.index');
    Route::get('/permohonan-informasi/create', [MasyarakatSubmissionController::class, 'create'])->name('submissions.create');
    Route::post('/permohonan-informasi', [MasyarakatSubmissionController::class, 'store'])->name('submissions.store');
    Route::get('/permohonan-informasi/{submission}', [MasyarakatSubmissionController::class, 'show'])->name('submissions.show');
    Route::get('/permohonan-informasi/{submission}/pdf', [SubmissionPdfController::class, 'download'])->name('submissions.download');
    Route::get('/permohonan-informasi/document/{document}/view', [MasyarakatSubmissionController::class, 'viewDocument'])->name('submissions.view');
    Route::get('/permohonan-informasi/document/{document}/download', [MasyarakatSubmissionController::class, 'downloadDocument'])->name('submissions.document.download');

    // KONSULTASI (CONSULTATIONS)
    Route::prefix('konsultasi')->name('consultations.')->group(function () {
        Route::get('/', [MasyarakatConsultationController::class, 'index'])->name('index');
        Route::get('/create', [MasyarakatConsultationController::class, 'create'])->name('create');
        Route::post('/store', [MasyarakatConsultationController::class, 'store'])->name('store');
        Route::get('/{consultation}', [MasyarakatConsultationController::class, 'show'])->name('show');
        Route::get('/{consultation}/download', [MasyarakatConsultationPdfController::class, 'download'])->name('download');
        Route::get('/document/{document}/download', [MasyarakatConsultationController::class, 'downloadDocument'])->name('document.download');
    });
    
    // PENGADUAN
    Route::get('/pengaduan', [MasyarakatComplaintController::class, 'index'])->name('complaints.index');
    Route::get('/pengaduan/create', [MasyarakatComplaintController::class, 'create'])->name('complaints.create');
    Route::post('/pengaduan', [MasyarakatComplaintController::class, 'store'])->name('complaints.store');
    Route::get('/pengaduan/{complaint}', [MasyarakatComplaintController::class, 'show'])->name('complaints.show');
    Route::get('/pengaduan/{complaint}/pdf', [MasyarakatComplaintPdfController::class, 'download'])->name('complaints.download');
    Route::get('/pengaduan/document/{document}', [MasyarakatComplaintController::class, 'viewDocument'])->name('complaints.documents.view');
    Route::get('/pengaduan/document/{document}/download', [MasyarakatComplaintController::class, 'downloadDocument'])->name('complaints.document.download');

    // RIWAYAT PENGAJUAN
    Route::get('/riwayat-pengajuan', [MasyarakatHistoryController::class, 'index'])->name('history.index');
});

// Admin Routes (Role: admin)
Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::get('/manajemen-pengajuan', [App\Http\Controllers\Admin\AllSubmissionsController::class, 'index'])
        ->name('management.semua');

    Route::get('/manajemen-pengajuan/export/{tab}', [App\Http\Controllers\Admin\ReportExportController::class, 'export'])
        ->name('management.export');
    
    Route::get('/tickets/search', [App\Http\Controllers\Admin\TicketSearchController::class, 'search'])
        ->name('tickets.search');

     Route::get('/management/arsip', [\App\Http\Controllers\Admin\ArchiveController::class, 'index'])
        ->name('management.arsip');

    // Route Manajemen Pengajuan
    Route::get('/manajemen-pengajuan/permohonan', [AdminSubmissionController::class, 'index'])
        ->name('submissions.permohonan');

    Route::get('/manajemen-pengajuan/permohonan/{id}', [AdminSubmissionController::class, 'show'])
        ->name('submissions.show');
        
    Route::put('/manajemen-pengajuan/permohonan/{id}', [AdminSubmissionController::class, 'update'])
        ->name('submissions.update');

    Route::get('/manajemen-pengajuan/dokumen/{id}', [AdminSubmissionController::class, 'downloadDocument'])
        ->name('submissions.document');

    Route::get('/manajemen-pengajuan/permohonan/{id}/pdf', [AdminSubmissionController::class, 'downloadPdf'])
        ->name('submissions.pdf');
    
    Route::get('/manajemen-pengajuan/permohonan/{id}/ktp/download', [AdminSubmissionController::class, 'downloadKtp'])
        ->name('submissions.ktp.download');


    // Route Manajemen Konsultasi
    Route::get('/manajemen-pengajuan/konsultasi', [AdminConsultationController::class, 'index'])
        ->name('consultations.konsultasi');

    Route::get('/manajemen-pengajuan/konsultasi/{id}', [AdminConsultationController::class, 'show'])
        ->name('consultations.show');

    Route::put('/manajemen-pengajuan/konsultasi/{id}', [AdminConsultationController::class, 'update'])
        ->name('consultations.update');

    Route::get('/manajemen-pengajuan/konsultasi/dokumen/{id}', [AdminConsultationController::class, 'downloadDocument'])
        ->name('consultations.document');
    
    Route::get('/manajemen-pengajuan/konsultasi/{id}/pdf', [AdminConsultationController::class, 'downloadPdf'])
        ->name('consultations.pdf');

    Route::get('/manajemen-pengajuan/konsultasi/{id}/ktp/download', [AdminConsultationController::class, 'downloadKtp'])
        ->name('consultations.ktp.download');

    // Route Manajemen Pengaduan
    Route::get('/manajemen-pengajuan/pengaduan', [AdminComplaintController::class, 'index'])
        ->name('complaints.pengaduan');
    
    Route::get('/manajemen-pengajuan/pengaduan/{id}', [AdminComplaintController::class, 'show'])
        ->name('complaints.show');

    Route::put('/manajemen-pengajuan/pengaduan/{id}', [AdminComplaintController::class, 'update'])
        ->name('complaints.update');

    Route::get('/manajemen-pengajuan/pengaduan/dokumen/{id}', [AdminComplaintController::class, 'downloadDocument'])
        ->name('complaints.document');

    Route::get('/manajemen-pengajuan/pengaduan/{id}/pdf', [AdminComplaintController::class, 'downloadPdf'])
        ->name('complaints.pdf');
    
    Route::get('/manajemen-pengajuan/pengaduan/{id}/ktp/download', [AdminComplaintController::class, 'downloadKtp'])
        ->name('complaints.ktp.download');

    // Manajemen Arsip
    Route::post('/consultations/{id}/archive', [\App\Http\Controllers\Admin\ArchiveController::class, 'archiveConsultation'])
        ->name('consultations.archive');
    Route::post('/complaints/{id}/archive', [\App\Http\Controllers\Admin\ArchiveController::class, 'archiveComplaint'])
        ->name('complaints.archive');
    Route::post('/submissions/{id}/archive', [\App\Http\Controllers\Admin\ArchiveController::class, 'archiveSubmission'])
        ->name('submissions.archive');

    Route::post('/consultations/{id}/unarchive', [\App\Http\Controllers\Admin\ArchiveController::class, 'unarchiveConsultation'])
        ->name('consultations.unarchive');
    Route::post('/complaints/{id}/unarchive', [\App\Http\Controllers\Admin\ArchiveController::class, 'unarchiveComplaint'])
        ->name('complaints.unarchive');
    Route::post('/submissions/{id}/unarchive', [\App\Http\Controllers\Admin\ArchiveController::class, 'unarchiveSubmission'])
        ->name('submissions.unarchive');
});