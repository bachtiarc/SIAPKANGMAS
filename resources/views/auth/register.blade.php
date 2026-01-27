@extends('layouts.app')

@section('title', 'Registrasi Akun Baru')

@push('styles')
<style>
    /* Gradient Animation */
    @keyframes gradient-slow {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.8; }
    }

    .animate-gradient-slow {
        animation: gradient-slow 20s ease-in-out infinite;
    }

    /* Blob Animation */
    @keyframes blob-slow {
        0%, 100% { transform: translate(0px, 0px) scale(1); }
        33% { transform: translate(20px, -30px) scale(1.05); }
        66% { transform: translate(-15px, 15px) scale(0.95); }
    }

    .animate-blob-slow {
        animation: blob-slow 15s ease-in-out infinite;
    }

    .animation-delay-2000 { animation-delay: 5s; }
    .animation-delay-4000 { animation-delay: 10s; }

    /* Toggle Button Active State */
    .toggle-btn-active {
        background-color: #FF8C00;
        color: white;
    }

    .toggle-btn-inactive {
        background-color: transparent;
        color: #FF8C00;
    }
</style>
@endpush

@section('content')
<!-- Background -->
<div class="min-h-screen bg-gradient-to-br from-white via-blue-50 to-white animate-gradient-slow relative overflow-hidden flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    
    <!-- Floating Blobs -->
    <div class="absolute top-0 -left-4 w-72 h-72 bg-purple-100 rounded-full mix-blend-multiply filter blur-3xl opacity-40 animate-blob-slow"></div>
    <div class="absolute top-0 -right-4 w-72 h-72 bg-blue-100 rounded-full mix-blend-multiply filter blur-3xl opacity-40 animate-blob-slow animation-delay-2000"></div>
    <div class="absolute -bottom-8 left-20 w-72 h-72 bg-indigo-100 rounded-full mix-blend-multiply filter blur-3xl opacity-40 animate-blob-slow animation-delay-4000"></div>

    <!-- Main Container -->
    <div class="max-w-6xl w-full relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-start">
            
            <!-- LEFT SIDE - Form Card -->
            <div class="bg-white/90 backdrop-blur-sm rounded-3xl shadow-xl p-8">
                
                <!-- Logo -->
                <div class="flex justify-center mb-6">
                    <img src="{{ asset('images/logo.png') }}" alt="SIAPKANGMAS" class="h-16">
                </div>

                <!-- Error Messages -->
                @if ($errors->any())
                    <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                        <ul class="list-disc list-inside text-sm font-lato">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Registration Form -->
                <form method="POST" action="{{ route('register') }}" id="registerForm" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    
                    <!-- Hidden user_type field -->
                    <input type="hidden" name="user_type" id="user_type" value="masyarakat_umum">

                    <!-- Nama Lengkap -->
                    <div>
                        <label class="font-montserrat block text-sm font-semibold text-gray-900 mb-2">Nama Lengkap</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            <input type="text" name="name" value="{{ old('name') }}" required
                                class="font-lato block w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-gray-50" 
                                placeholder="Masukkan nama lengkap">
                        </div>
                    </div>

                    <!-- NIK & Alamat (Masyarakat Only) -->
                    <div id="nik-field">
                        <label class="font-montserrat block text-sm font-semibold text-gray-900 mb-2">NIK (Nomor Induk Kewarganegaraan)</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <input type="text" name="nik" id="nik" value="{{ old('nik') }}"
                                class="font-lato block w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-gray-50" 
                                placeholder="Masukkan 16 digit NIK" maxlength="16">
                        </div>
                    </div>

                    <div id="address-field">
                        <label class="font-montserrat block text-sm font-semibold text-gray-900 mb-2">Alamat Lengkap</label>
                        <div class="relative">
                            <textarea name="address" id="address" rows="3"
                                class="font-lato block w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-gray-50" 
                                placeholder="Masukkan alamat lengkap sesuai KTP">{{ old('address') }}</textarea>
                        </div>
                    </div>

                    <!-- NIP (Pegawai Only) -->
                    <div id="nip-field" class="hidden">
                        <label class="font-montserrat block text-sm font-semibold text-gray-900 mb-2">NIP (Nomor Induk Pegawai)</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <input type="text" name="nip" id="nip" value="{{ old('nip') }}"
                                class="font-lato block w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-gray-50" 
                                placeholder="Contoh: 199003xxxxxx">
                        </div>
                    </div>

                    <!-- Email & Phone -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="font-montserrat block text-sm font-semibold text-gray-900 mb-2">Email</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <input type="email" name="email" value="{{ old('email') }}" required
                                    class="font-lato block w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-gray-50" 
                                    placeholder="Contoh: nama@domain.com">
                            </div>
                        </div>

                        <div>
                            <label class="font-montserrat block text-sm font-semibold text-gray-900 mb-2">Nomor Telepon</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                    </svg>
                                </div>
                                <input type="text" name="phone" value="{{ old('phone') }}" required
                                    class="font-lato block w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-gray-50" 
                                    placeholder="Contoh: 62xxxxxx">
                            </div>
                        </div>
                    </div>

                    <!-- Foto KTP (Masyarakat Only) -->
                    <div id="ktp-field">
                        <label class="font-montserrat block text-sm font-semibold text-gray-900 mb-2">Foto KTP</label>
                        <div id="drop-area" class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-blue-400 transition cursor-pointer bg-gray-50" onclick="document.getElementById('foto_ktp').click()">
                            <div class="space-y-1 text-center">
                                <div id="upload-icon-container">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </div>
                                
                                <div class="font-lato text-sm text-gray-600">
                                    <span id="upload-text" class="font-semibold text-blue-600 hover:text-blue-700">Klik untuk unggah</span> 
                                    <span id="upload-subtext">atau seret file ke sini</span>
                                </div>
                                
                                <div id="file-success-msg" class="hidden mt-2">
                                    <p class="text-green-600 font-bold flex items-center justify-center">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="Set10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                        KTP Berhasil diunggah!
                                    </p>
                                    <p id="file-name-display" class="text-xs text-gray-500 italic"></p>
                                </div>

                                <p class="font-lato text-xs text-gray-500" id="format-hint">Format: JPEG, JPG, PNG (Maksimal 2MB)</p>
                            </div>
                            <input id="foto_ktp" name="foto_ktp" type="file" class="hidden" accept="image/jpeg,image/jpg,image/png">
                        </div>
                    </div>

                    <!-- Bidang/Balai (Pegawai Only) -->
                    <div id="bidang-field" class="hidden">
                        <label class="font-montserrat block text-sm font-semibold text-gray-900 mb-2">Bidang / Balai</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            </div>
                            <select name="bidang" id="bidang"
                                class="font-lato block w-full pl-10 pr-10 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-gray-50 appearance-none">
                                <option value="">Pilih bidang atau balai Anda</option>
                                @if(isset($organizationStructure))
                                    @foreach($organizationStructure as $bidang => $jabatans)
                                        <option value="{{ $bidang }}" {{ old('bidang') == $bidang ? 'selected' : '' }}>{{ $bidang }}</option>
                                    @endforeach
                                @endif
                            </select>
                            <!-- Custom dropdown arrow -->
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Jabatan (Pegawai Only) -->
                    <div id="jabatan-field" class="hidden">
                        <label class="font-montserrat block text-sm font-semibold text-gray-900 mb-2">Jabatan</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <select name="jabatan" id="jabatan"
                                class="font-lato block w-full pl-10 pr-10 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-gray-50 appearance-none">
                                <option value="">Pilih jabatan, subbag, atau seksi Anda</option>
                            </select>
                            <!-- Custom dropdown arrow -->
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Password -->
                    <div>
                        <label class="font-montserrat block text-sm font-semibold text-gray-900 mb-2">Password</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </svg>
                            </div>
                            <input type="password" name="password" required
                                class="font-lato block w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-gray-50" 
                                placeholder="Masukkan password baru Anda">
                        </div>
                        <p class="font-lato text-xs text-gray-500 mt-1">Min. 8 karakter terdiri dari huruf dan angka.</p>
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label class="font-montserrat block text-sm font-semibold text-gray-900 mb-2">Konfirmasi Password</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </svg>
                            </div>
                            <input type="password" name="password_confirmation" required
                                class="font-lato block w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-gray-50" 
                                placeholder="Masukkan ulang password baru Anda">
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-4">
                        <button type="submit" class="font-montserrat w-full py-3 px-4 bg-blue-700 hover:bg-blue-800 text-white font-bold rounded-lg transition">
                            Daftar Sekarang
                        </button>
                    </div>

                    <!-- Login Link -->
                    <div class="text-center">
                        <p class="font-lato text-sm text-gray-700">
                            Sudah memiliki akun? 
                            <a href="{{ route('login') }}" class="font-montserrat text-blue-600 hover:text-blue-700 font-semibold underline">Masuk disini.</a>
                        </p>
                    </div>
                </form>

            </div>

            <!-- RIGHT SIDE - Info Card -->
            <div>
                <h1 class="font-montserrat text-4xl font-bold text-gray-900 mb-3">
                    Registrasi Akun Baru
                </h1>
                <p class="font-lato text-gray-600 text-lg mb-8">
                    Silahkan lengkapi data diri pegawai Anda untuk mengakses layanan Helpdesk Internal Dinas Perindustrian dan Perdagangan Provinsi Jawa Tengah. Pastikan data yang Anda masukkan telah valid.
                </p>

                <!-- Feature Cards -->
                <div class="space-y-4 mb-8">
                    <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg p-6 border border-blue-100">
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1">
                                <h3 class="font-montserrat text-lg font-bold text-gray-900 mb-2">Layanan Terpadu</h3>
                                <p class="font-lato text-sm text-gray-600">Akses bantuan lengkap bagi pegawai DISPERINDAG dalam satu pintu.</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg p-6 border border-blue-100">
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1">
                                <h3 class="font-montserrat text-lg font-bold text-gray-900 mb-2">Pelacakan Tiket</h3>
                                <p class="font-lato text-sm text-gray-600">Pantau status permohonan dan kendala pegawai DISPERINDAG secara real-time.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Toggle Buttons (Orange for active) -->
                <div class="flex space-x-4">
                    <button type="button" id="btn-pegawai" onclick="toggleForm('pegawai')" class="toggle-btn-inactive flex-1 py-3 px-6 font-montserrat font-bold rounded-lg border-2 border-orange-500 transition">
                        Pegawai
                    </button>
                    <button type="button" id="btn-masyarakat" onclick="toggleForm('masyarakat')" class="toggle-btn-active flex-1 py-3 px-6 font-montserrat font-bold rounded-lg border-2 border-orange-500 transition">
                        Masyarakat Umum
                    </button>
                </div>

            </div>

        </div>
    </div>
</div>

<!-- Success Modal -->
<div id="successModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 hidden items-center justify-center z-50" style="display: none;">
    <div class="bg-white rounded-3xl shadow-2xl p-10 max-w-md w-full mx-4 text-center">
        <div class="w-24 h-24 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-6">
            <svg class="w-12 h-12 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
            </svg>
        </div>
        
        <h2 class="font-montserrat text-2xl font-bold text-gray-900 mb-3">Cek Email Anda untuk Verifikasi</h2>
        <p class="font-lato text-gray-600 mb-8">
            Kami telah mengirimkan email verifikasi ke alamat email Anda. Silakan cek inbox atau folder spam Anda dan klik link verifikasi untuk mengaktifkan akun.
        </p>
        
        <a href="{{ route('login') }}" class="font-montserrat inline-block w-full py-3 px-6 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg transition">
            Kembali ke Halaman Login
        </a>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Organization structure data
const organizationStructure = @json($organizationStructure ?? []);

// Toggle form between Pegawai and Masyarakat
function toggleForm(type) {
    const userTypeInput = document.getElementById('user_type');
    const btnPegawai = document.getElementById('btn-pegawai');
    const btnMasyarakat = document.getElementById('btn-masyarakat');
    
    // Fields
    const nipField = document.getElementById('nip-field');
    const nikField = document.getElementById('nik-field');
    const ktpField = document.getElementById('ktp-field');
    const bidangField = document.getElementById('bidang-field');
    const jabatanField = document.getElementById('jabatan-field');
    
    // Inputs
    const nipInput = document.getElementById('nip');
    const nikInput = document.getElementById('nik');
    const ktpInput = document.getElementById('foto_ktp');
    const bidangInput = document.getElementById('bidang');
    const jabatanInput = document.getElementById('jabatan');
    
    if (type === 'pegawai') {
        // Update button styles
        btnPegawai.classList.remove('toggle-btn-inactive');
        btnPegawai.classList.add('toggle-btn-active');
        btnMasyarakat.classList.remove('toggle-btn-active');
        btnMasyarakat.classList.add('toggle-btn-inactive');
        
        // Show/hide fields
        nipField.classList.remove('hidden');
        bidangField.classList.remove('hidden');
        jabatanField.classList.remove('hidden');
        nikField.classList.add('hidden');
        ktpField.classList.add('hidden');
        
        // Update required attributes
        nipInput.required = true;
        bidangInput.required = true;
        jabatanInput.required = true;
        nikInput.required = false;
        ktpInput.required = false;
        
        // Update hidden field
        userTypeInput.value = 'pegawai';
        
        // Update form action
        document.getElementById('registerForm').action = '{{ route("register.pegawai") }}';
        document.getElementById('address-field').classList.add('hidden');
        document.getElementById('address').required = false;
        
    } else {
        // Update button styles
        btnMasyarakat.classList.remove('toggle-btn-inactive');
        btnMasyarakat.classList.add('toggle-btn-active');
        btnPegawai.classList.remove('toggle-btn-active');
        btnPegawai.classList.add('toggle-btn-inactive');
        
        // Show/hide fields
        nikField.classList.remove('hidden');
        ktpField.classList.remove('hidden');
        nipField.classList.add('hidden');
        bidangField.classList.add('hidden');
        jabatanField.classList.add('hidden');
        
        // Update required attributes
        nikInput.required = false;
        ktpInput.required = true;
        nipInput.required = false;
        bidangInput.required = false;
        jabatanInput.required = false;
        
        // Update hidden field
        userTypeInput.value = 'masyarakat_umum';
        
        // Update form action
        document.getElementById('registerForm').action = '{{ route("register.masyarakat") }}';
        document.getElementById('address-field').classList.remove('hidden');
        document.getElementById('address').required = true;
    }
}

// Bidang/Jabatan dynamic dropdown
document.getElementById('bidang')?.addEventListener('change', function() {
    const bidang = this.value;
    const jabatanSelect = document.getElementById('jabatan');
    
    // Clear previous options
    jabatanSelect.innerHTML = '<option value="">Pilih jabatan, subbag, atau seksi Anda</option>';
    
    if (bidang && organizationStructure[bidang]) {
        organizationStructure[bidang].forEach(function(jabatan) {
            const option = document.createElement('option');
            option.value = jabatan;
            option.textContent = jabatan;
            jabatanSelect.appendChild(option);
        });
    }
});

// Show modal if registration successful
@if(session('registration_success'))
    document.addEventListener('DOMContentLoaded', function() {
        showSuccessModal();
    });
@endif

function showSuccessModal() {
    document.getElementById('successModal').style.display = 'flex';
}

// Initialize form (default: masyarakat)
document.addEventListener('DOMContentLoaded', function() {
    toggleForm('masyarakat');
});

// Foto KTP upload 
document.getElementById('foto_ktp').addEventListener('change', function(e) {
    const file = e.target.files[0];
    const dropArea = document.getElementById('drop-area');
    const uploadText = document.getElementById('upload-text');
    const uploadSubtext = document.getElementById('upload-subtext');
    const uploadIcon = document.getElementById('upload-icon-container');
    const successMsg = document.getElementById('file-success-msg');
    const fileNameDisplay = document.getElementById('file-name-display');
    const formatHint = document.getElementById('format-hint');

    if (file) {
        // 1. Ubah tampilan border menjadi hijau
        dropArea.classList.remove('border-gray-300');
        dropArea.classList.add('border-green-500', 'bg-green-50');

        // 2. Sembunyikan instruksi awal
        uploadText.classList.add('hidden');
        uploadSubtext.classList.add('hidden');
        formatHint.classList.add('hidden');

        // 3. Tampilkan pesan sukses dan nama file
        successMsg.classList.remove('hidden');
        fileNameDisplay.textContent = "File: " + file.name;
        
        // 4. Ubah warna icon (opsional)
        uploadIcon.innerHTML = `
            <svg class="mx-auto h-12 w-12 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        `;
    }
});
</script>
@endpush