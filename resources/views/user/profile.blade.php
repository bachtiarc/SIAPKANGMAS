@extends('layouts.dashboard')

@section('title', 'Profil Pengguna')

@section('content')
<div class="p-6">
    <!-- Page Header -->
    <div class="mb-6">
        <h1 class="font-montserrat text-3xl font-bold text-blue-700">Profil Pengguna</h1>
        <p class="font-lato text-gray-600 mt-1">Kelola informasi data diri dan akun Anda.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Left Column: Profile Card -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-sm p-6 text-center">
                <!-- Profile Picture with Upload -->
                <div class="mb-4 relative">
                    @if(auth()->user()->profile_photo)
                        <img src="{{ asset('storage/' . auth()->user()->profile_photo) }}" alt="Profile" class="w-32 h-32 mx-auto rounded-full object-cover border-4 border-blue-100">
                    @else
                        <div class="w-32 h-32 mx-auto bg-gradient-to-br from-blue-500 to-blue-700 rounded-full flex items-center justify-center text-white text-4xl font-bold border-4 border-blue-100">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                    @endif
                    
                    <!-- Upload Button Overlay -->
                    <form action="{{ route('user.profile.photo.update') }}" method="POST" enctype="multipart/form-data" id="photoForm" class="absolute bottom-0 right-1/2 transform translate-x-1/2 translate-y-2">
                        @csrf
                        @method('PUT')
                        <label for="profile_photo" class="cursor-pointer bg-blue-600 hover:bg-blue-700 text-white p-2 rounded-full shadow-lg transition inline-block">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </label>
                        <input type="file" name="profile_photo" id="profile_photo" accept="image/*" class="hidden" onchange="document.getElementById('photoForm').submit()">
                    </form>
                </div>

                @if(session('photo_success'))
                    <div class="mb-4 bg-green-50 border-2 border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm font-semibold">
                        ✓ {{ session('photo_success') }}
                    </div>
                @endif

                @if(session('photo_error'))
                    <div class="mb-4 bg-red-50 border-2 border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm font-semibold">
                        ✕ {{ session('photo_error') }}
                    </div>
                @endif

                <!-- User Info -->
                <h2 class="font-montserrat text-xl font-bold text-gray-900 mb-1">
                    {{ auth()->user()->name }}
                </h2>
                <p class="font-lato text-sm text-gray-600 mb-1">{{ auth()->user()->jabatan }}</p>
                <p class="font-lato text-sm text-gray-500">{{ auth()->user()->bidang }}</p>

                <!-- Stats -->
                <div class="flex justify-center space-x-8 mt-6 pt-6 border-t border-gray-200">
                    <div>
                        <p class="font-montserrat text-2xl font-bold text-gray-900">{{ $totalSubmissions ?? 0 }}</p>
                        <p class="font-lato text-xs text-gray-600">Total Tiket</p>
                    </div>
                    <div>
                        <p class="font-montserrat text-2xl font-bold text-blue-600">{{ $completedSubmissions ?? 0 }}</p>
                        <p class="font-lato text-xs text-gray-600">Selesai</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Information & Security -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Personal Information (Read-Only) -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center mb-6">
                    <svg class="w-6 h-6 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    <h3 class="font-montserrat text-xl font-bold text-gray-900">Informasi Pribadi</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Nama -->
                    <div>
                        <label class="font-montserrat block text-sm font-semibold text-gray-700 mb-2">Nama Lengkap</label>
                        <input type="text" value="{{ auth()->user()->name }}" readonly
                            class="font-lato w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-700">
                    </div>

                    <!-- NIP -->
                    <div>
                        <label class="font-montserrat block text-sm font-semibold text-gray-700 mb-2">NIP (Nomor Induk Pegawai)</label>
                        <input type="text" value="{{ auth()->user()->nip }}" readonly
                            class="font-lato w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-700">
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="font-montserrat block text-sm font-semibold text-gray-700 mb-2">Email</label>
                        <input type="email" value="{{ auth()->user()->email }}" readonly
                            class="font-lato w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-700">
                    </div>

                    <!-- Phone -->
                    <div>
                        <label class="font-montserrat block text-sm font-semibold text-gray-700 mb-2">Nomor Telepon</label>
                        <input type="text" value="{{ auth()->user()->phone }}" readonly
                            class="font-lato w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-700">
                    </div>

                    <!-- Bidang -->
                    <div>
                        <label class="font-montserrat block text-sm font-semibold text-gray-700 mb-2">Bidang atau Balai</label>
                        <input type="text" value="{{ auth()->user()->bidang }}" readonly
                            class="font-lato w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-700">
                    </div>

                    <!-- Jabatan -->
                    <div>
                        <label class="font-montserrat block text-sm font-semibold text-gray-700 mb-2">Jabatan, Subbag, atau Seksi</label>
                        <input type="text" value="{{ auth()->user()->jabatan }}" readonly
                            class="font-lato w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-700">
                    </div>
                </div>
            </div>

            <!-- Account Security -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center mb-6">
                    <svg class="w-6 h-6 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                    <h3 class="font-montserrat text-xl font-bold text-gray-900">Keamanan Akun</h3>
                </div>

                <!-- Success/Error Messages -->
                @if (session('password_success'))
                    <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm">
                        {{ session('password_success') }}
                    </div>
                @endif

                @if (session('password_error'))
                    <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
                        {{ session('password_error') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <form action="{{ route('user.password.update') }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <!-- Current Password -->
                    <div>
                        <label class="font-montserrat block text-sm font-semibold text-gray-700 mb-2">Password Saat Ini</label>
                        <input type="password" name="current_password" required
                            class="font-lato w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Masukkan password saat ini...">
                    </div>

                    <!-- New Password -->
                    <div>
                        <label class="font-montserrat block text-sm font-semibold text-gray-700 mb-2">Password Baru</label>
                        <input type="password" name="password" required
                            class="font-lato w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Minimal 8 karakter...">
                        <p class="font-lato text-xs text-gray-500 mt-1">* Buat password Anda 8 karakter atau lebih.</p>
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label class="font-montserrat block text-sm font-semibold text-gray-700 mb-2">Konfirmasi Password Baru</label>
                        <input type="password" name="password_confirmation" required
                            class="font-lato w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Masukkan ulang password baru...">
                    </div>

                    <div class="pt-4">
                        <button type="submit" class="font-montserrat px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition">
                            Ganti Password
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>
@endsection