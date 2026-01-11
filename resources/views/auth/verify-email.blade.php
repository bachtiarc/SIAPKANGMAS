@extends('layouts.app')

@section('title', 'Verifikasi Email')

@section('content')
<!-- Background Gray -->
<div class="min-h-screen bg-gray-200 py-12 px-4 sm:px-6 lg:px-8">
    
    <!-- Top Label -->
    <div class="max-w-3xl mx-auto mb-6">
        <p class="font-montserrat text-sm font-medium text-gray-600 tracking-wider uppercase">
            VERIFIKASI EMAIL
        </p>
    </div>

    <!-- Main Content Card -->
    <div class="max-w-3xl mx-auto">
        <div class="bg-white rounded-3xl shadow-lg p-12 sm:p-16 text-center">
            
            <!-- Icon Circle -->
            <div class="flex justify-center mb-8">
                <div class="w-40 h-40 bg-blue-100 rounded-full flex items-center justify-center">
                    <svg class="w-20 h-20 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                </div>
            </div>

            <!-- Title -->
            <h1 class="font-montserrat text-4xl font-bold text-gray-900 mb-6">
                Cek Email Anda untuk Verifikasi
            </h1>

            <!-- Description -->
            <p class="font-lato text-lg text-gray-600 mb-10 leading-relaxed max-w-2xl mx-auto">
                Kami telah mengirimkan email verifikasi ke alamat email Anda. Silakan klik tautan verifikasi tersebut untuk mengaktifkan akun Anda.
            </p>

            <!-- Success Message (if email resent) -->
            @if (session('resent'))
                <div class="mb-8 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
                    <p class="text-sm font-lato">✓ Email verifikasi baru telah dikirim ke alamat email Anda!</p>
                </div>
            @endif

            <!-- Buttons -->
            <div class="space-y-4 max-w-xl mx-auto">
                <!-- Resend Email Button (Blue) -->
                <form method="POST" action="{{ route('verification.resend') }}">
                    @csrf
                    <button type="submit" class="font-montserrat w-full bg-blue-700 hover:bg-blue-800 text-white font-bold py-4 px-8 rounded-xl transition duration-200 shadow-sm hover:shadow-md text-lg">
                        Kirim Ulang Email Verifikasi
                    </button>
                </form>

                <!-- Back to Login Button (White with Border) -->
                <a href="{{ route('login') }}" class="font-montserrat block w-full bg-white hover:bg-gray-50 text-gray-800 font-bold py-4 px-8 rounded-xl border-2 border-gray-300 transition duration-200 text-lg">
                    Kembali ke Halaman Login
                </a>
            </div>

        </div>
    </div>
</div>
@endsection