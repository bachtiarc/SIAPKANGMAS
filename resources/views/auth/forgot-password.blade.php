@extends('layouts.app')

@section('title', 'Lupa Password')

@push('styles')
<style>
    @keyframes gradient-slow {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.8; }
    }

    .animate-gradient-slow {
        animation: gradient-slow 20s ease-in-out infinite;
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gradient-to-br from-white via-blue-50 to-white animate-gradient-slow flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    
    <div class="max-w-md w-full space-y-8">
        
        <!-- Title -->
        <div class="text-center">
            <h1 class="font-montserrat text-4xl font-bold text-blue-700 mb-2">
                Lupa Password?
            </h1>
            <p class="font-lato text-gray-600">
                Masukkan email Anda untuk menerima link reset password
            </p>
        </div>

        <!-- Card -->
        <div class="bg-white/80 backdrop-blur-sm rounded-3xl shadow-xl p-8">
            
            <!-- Success Message -->
            @if (session('status'))
                <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm">
                    {{ session('status') }}
                </div>
            @endif

            <!-- Error Messages -->
            @if ($errors->any())
                <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <!-- Icon -->
            <div class="flex justify-center mb-6">
                <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center">
                    <svg class="w-10 h-10 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                    </svg>
                </div>
            </div>

            <!-- Form -->
            <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
                @csrf

                <!-- Email -->
                <div>
                    <label for="email" class="font-montserrat block text-sm font-semibold text-gray-700 mb-2">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                        class="font-lato appearance-none relative block w-full px-3 py-2.5 border border-gray-300 placeholder-gray-400 text-gray-900 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-gray-50"
                        placeholder="nama@domain.com">
                </div>

                <!-- Submit Button -->
                <div>
                    <button type="submit" class="font-montserrat group relative w-full flex justify-center py-3 px-4 border border-transparent text-base font-bold rounded-lg text-white bg-blue-700 hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-200">
                        Kirim Link Reset Password
                    </button>
                </div>

                <!-- Back to Login -->
                <div class="text-center">
                    <a href="{{ route('login') }}" class="font-montserrat text-sm font-medium text-gray-600 hover:text-blue-600">
                        ← Kembali ke halaman login
                    </a>
                </div>
            </form>

        </div>
    </div>
</div>
@endsection