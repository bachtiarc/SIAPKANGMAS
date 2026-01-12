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
                <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
                    {{ session('error') }}
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
@endsection