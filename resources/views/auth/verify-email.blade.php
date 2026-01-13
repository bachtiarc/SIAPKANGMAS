@extends('layouts.app')

@section('title', 'Verifikasi Email')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-white via-blue-50 to-white flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full">
        
        <!-- Card -->
        <div class="bg-white rounded-3xl shadow-xl p-10 text-center">
            
            <!-- Icon -->
            <div class="flex justify-center mb-6">
                <div class="w-24 h-24 bg-blue-100 rounded-full flex items-center justify-center">
                    <svg class="w-12 h-12 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                </div>
            </div>

            <!-- Title -->
            <h1 class="font-montserrat text-2xl font-bold text-gray-900 mb-4">
                Cek Email Anda untuk Verifikasi
            </h1>

            <!-- Message -->
            <p class="font-lato text-gray-600 mb-8 leading-relaxed">
                Kami telah mengirimkan email verifikasi ke alamat email Anda. Silakan klik tautan verifikasi tersebut untuk mengaktifkan akun Anda.
            </p>

            <!-- Success/Error Messages -->
            @if (session('success'))
                <div class="mb-6 bg-green-50 border-2 border-green-500 rounded-xl p-4 flex items-start space-x-3">
                    <div class="flex-shrink-0">
                        <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="font-montserrat text-sm font-semibold text-green-800">
                            {{ session('success') }}
                        </p>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-6 bg-red-50 border-2 border-red-500 rounded-xl p-4 flex items-start space-x-3">
                    <div class="flex-shrink-0">
                        <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="font-montserrat text-sm font-semibold text-red-800">
                            {{ session('error') }}
                        </p>
                    </div>
                </div>
            @endif

            <!-- Buttons -->
            <div class="space-y-3">
                <!-- Resend Email Button -->
                <form method="POST" action="{{ route('verification.resend') }}">
                    @csrf
                    <button type="submit" class="font-montserrat w-full py-3 px-6 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition duration-200">
                        Kirim Ulang Email Verifikasi
                    </button>
                </form>

                <!-- Back to Login Button -->
                <a href="{{ route('login') }}" class="font-montserrat block w-full py-3 px-6 bg-white border-2 border-gray-300 hover:bg-gray-50 text-gray-700 font-semibold rounded-lg transition duration-200">
                    Kembali ke Halaman Login
                </a>
            </div>

        </div>

    </div>
</div>

<!-- Success Modal (shows on registration) -->
<div id="successModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 hidden items-center justify-center z-50" style="display: none;">
    <div class="bg-white rounded-3xl shadow-2xl p-10 max-w-md w-full mx-4 text-center">
        
        <!-- Icon -->
        <div class="flex justify-center mb-6">
            <div class="w-24 h-24 bg-blue-100 rounded-full flex items-center justify-center">
                <svg class="w-12 h-12 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
            </div>
        </div>

        <!-- Title -->
        <h2 class="font-montserrat text-2xl font-bold text-gray-900 mb-4">
            Cek Email Anda untuk Verifikasi
        </h2>

        <!-- Message -->
        <p class="font-lato text-gray-600 mb-8 leading-relaxed">
            Kami telah mengirimkan email verifikasi ke alamat email Anda. Silakan klik tautan verifikasi tersebut untuk mengaktifkan akun Anda.
        </p>

        <!-- Button -->
        <button onclick="hideSuccessModal()" class="font-montserrat w-full py-3 px-6 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition duration-200">
            Mengerti
        </button>

    </div>
</div>

@push('scripts')
<script>
// Show modal if just registered
@if(session('registration_success'))
    document.addEventListener('DOMContentLoaded', function() {
        showSuccessModal();
    });
@endif

function showSuccessModal() {
    const modal = document.getElementById('successModal');
    modal.style.display = 'flex';
}

function hideSuccessModal() {
    const modal = document.getElementById('successModal');
    modal.style.display = 'none';
}

// Close modal when clicking outside
document.getElementById('successModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        hideSuccessModal();
    }
});

// Close modal with ESC key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        hideSuccessModal();
    }
});
</script>
@endpush
@endsection