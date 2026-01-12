@extends('layouts.app')

@section('title', 'Masuk')

@push('styles')
<style>
    /* Subtle Slow Gradient Animation */
    @keyframes gradient-slow {
        0%, 100% {
            opacity: 1;
        }
        50% {
            opacity: 0.8;
        }
    }

    .animate-gradient-slow {
        animation: gradient-slow 20s ease-in-out infinite;
    }

    /* Very Slow Blob Animation */
    @keyframes blob-slow {
        0%, 100% {
            transform: translate(0px, 0px) scale(1);
        }
        33% {
            transform: translate(20px, -30px) scale(1.05);
        }
        66% {
            transform: translate(-15px, 15px) scale(0.95);
        }
    }

    .animate-blob-slow {
        animation: blob-slow 15s ease-in-out infinite;
    }

    .animation-delay-2000 {
        animation-delay: 5s;
    }

    .animation-delay-4000 {
        animation-delay: 10s;
    }
</style>
@endpush

@section('content')
<!-- Background with Gradient Animation -->
<div class="min-h-screen bg-gradient-to-br from-white via-blue-50 to-white animate-gradient-slow relative overflow-hidden flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    
    <!-- Floating Blobs -->
    <div class="absolute top-0 -left-4 w-72 h-72 bg-purple-100 rounded-full mix-blend-multiply filter blur-3xl opacity-40 animate-blob-slow"></div>
    <div class="absolute top-0 -right-4 w-72 h-72 bg-blue-100 rounded-full mix-blend-multiply filter blur-3xl opacity-40 animate-blob-slow animation-delay-2000"></div>
    <div class="absolute -bottom-8 left-20 w-72 h-72 bg-indigo-100 rounded-full mix-blend-multiply filter blur-3xl opacity-40 animate-blob-slow animation-delay-4000"></div>

    <!-- Login Card -->
    <div class="max-w-md w-full space-y-8 relative z-10">
        
        <!-- Title & Logo (Outside Card) -->
        <div class="text-center">
            <h1 class="font-montserrat text-5xl font-bold text-blue-700 mb-4">
                Selamat Datang!
            </h1>
            <img src="{{ asset('images/logo.png') }}" alt="SIAPKANGMAS" class="h-16 mx-auto">
        </div>

        <!-- Card -->
        <div class="bg-white/80 backdrop-blur-sm rounded-3xl shadow-xl p-8">
            
            <!-- Tab Navigation -->
            <div class="relative mb-6">
                <!-- Tab Buttons -->
                <div class="flex">
                    <button type="button" id="tab-user" onclick="switchTab('user')" 
                        class="tab-button flex-1 py-3 text-center font-montserrat font-semibold text-base transition-all duration-200 text-blue-600 relative z-10">
                        Pengguna
                    </button>
                    <button type="button" id="tab-admin" onclick="switchTab('admin')" 
                        class="tab-button flex-1 py-3 text-center font-montserrat font-semibold text-base transition-all duration-200 text-gray-500 hover:text-gray-700 relative z-10">
                        Admin
                    </button>
                </div>
                
                <!-- Bottom Border (Gray) -->
                <div class="absolute bottom-0 left-0 right-0 h-0.5 bg-gray-200"></div>
                
                <!-- Sliding Blue Indicator -->
                <div id="tab-indicator" class="absolute bottom-0 left-0 h-0.5 bg-blue-600 transition-all duration-300 ease-out" style="width: 50%;"></div>
            </div>

            <!-- Error Messages -->
            @if ($errors->any())
                <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
                    {{ session('error') }}
                </div>
            @endif

            @if (session('success'))
                <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('status'))
                <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm">
                    {{ session('status') }}
                </div>
            @endif

            <!-- Login Form -->
            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                <!-- Hidden User Type -->
                <input type="hidden" name="user_type" id="user_type" value="user">

                <!-- Email -->
                <div>
                    <label for="email" class="font-montserrat block text-sm font-semibold text-gray-700 mb-2">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required 
                        class="font-lato appearance-none relative block w-full px-3 py-2.5 border border-gray-300 placeholder-gray-400 text-gray-900 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-gray-50"
                        placeholder="nama@domain.com">
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="font-montserrat block text-sm font-semibold text-gray-700 mb-2">Password</label>
                    <div class="relative">
                        <input id="password" name="password" type="password" required 
                            class="font-lato appearance-none relative block w-full px-3 py-2.5 pr-10 border border-gray-300 placeholder-gray-400 text-gray-900 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-gray-50"
                            placeholder="Masukkan password Anda...">
                        <button type="button" onclick="togglePassword()" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <svg id="eye-icon" class="h-5 w-5 text-gray-400 hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Remember Me & Forgot Password -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember" name="remember" type="checkbox" 
                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="remember" class="ml-2 block text-sm font-lato text-gray-700">
                            Ingat saya
                        </label>
                    </div>

                    <div class="text-sm">
                        <a href="{{ route('password.request') }}" class="font-montserrat font-medium text-blue-600 hover:text-blue-700">
                            Lupa password?
                        </a>
                    </div>
                </div>

                <!-- reCAPTCHA -->

                <div class="mb-6 flex justify-center">
                    {!! app('captcha')->display() !!}
                </div>

                <!-- Submit Button -->
                <div>
                    <button type="submit" class="font-montserrat group relative w-full flex justify-center py-3 px-4 border border-transparent text-base font-bold rounded-lg text-white bg-blue-700 hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-200">
                        Masuk
                    </button>
                </div>
            </form>

            <!-- Register Link -->
            <div class="text-center mt-6">
                <p class="font-lato text-sm text-gray-700">
                    Belum memiliki akun? 
                    <a href="{{ route('register') }}" class="font-montserrat text-blue-600 hover:text-blue-700 font-semibold underline">Daftar disini.</a>
                </p>
            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Tab switching functionality with sliding indicator
function switchTab(tab) {
    const userTab = document.getElementById('tab-user');
    const adminTab = document.getElementById('tab-admin');
    const indicator = document.getElementById('tab-indicator');
    const userTypeInput = document.getElementById('user_type');

    if (tab === 'user') {
        // Update tab colors
        userTab.classList.add('text-blue-600');
        userTab.classList.remove('text-gray-500');
        adminTab.classList.remove('text-blue-600');
        adminTab.classList.add('text-gray-500');
        
        // Move indicator to left (Pengguna tab)
        indicator.style.left = '0%';
        indicator.style.width = '50%';
        
        userTypeInput.value = 'user';
    } else {
        // Update tab colors
        adminTab.classList.add('text-blue-600');
        adminTab.classList.remove('text-gray-500');
        userTab.classList.remove('text-blue-600');
        userTab.classList.add('text-gray-500');
        
        // Move indicator to right (Admin tab)
        indicator.style.left = '50%';
        indicator.style.width = '50%';
        
        userTypeInput.value = 'admin';
    }
}

// Toggle password visibility
function togglePassword() {
    const passwordInput = document.getElementById('password');
    const eyeIcon = document.getElementById('eye-icon');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        eyeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>';
    } else {
        passwordInput.type = 'password';
        eyeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>';
    }
}
</script>
@endpush