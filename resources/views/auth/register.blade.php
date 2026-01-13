@extends('layouts.app')

@section('title', 'Registrasi Akun Baru')

@section('content')
<!-- Background with Subtle Animated Gradient -->
<div class="min-h-screen relative overflow-hidden bg-gradient-to-br from-gray-50 via-white to-gray-50">
    <!-- Subtle Moving Gradient Overlay -->
    <div class="absolute inset-0 bg-gradient-to-br from-blue-50/30 via-transparent to-purple-50/30 animate-gradient-slow"></div>
    
    <!-- Very Subtle Floating Shapes -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none opacity-40">
        <div class="absolute top-20 -left-20 w-96 h-96 bg-blue-100 rounded-full mix-blend-multiply filter blur-3xl animate-blob-slow"></div>
        <div class="absolute top-40 -right-20 w-96 h-96 bg-purple-100 rounded-full mix-blend-multiply filter blur-3xl animate-blob-slow animation-delay-2000"></div>
        <div class="absolute -bottom-32 left-1/3 w-96 h-96 bg-indigo-100 rounded-full mix-blend-multiply filter blur-3xl animate-blob-slow animation-delay-4000"></div>
    </div>

    <!-- Main Content -->
    <div class="relative z-10 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-6xl mx-auto">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-start">
                
                <!-- LEFT SIDE - Form Card -->
                <div class="bg-white rounded-2xl shadow-md p-8 lg:p-10">
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
                    <form method="POST" action="{{ route('register') }}" class="space-y-5">
                        @csrf

                        <!-- Nama Lengkap -->
                        <div>
                            <label for="name" class="font-montserrat block text-sm font-semibold text-gray-900 mb-2">Nama Lengkap</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                                <input type="text" name="name" id="name" value="{{ old('name') }}" 
                                    class="font-lato block w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-gray-50 placeholder-gray-400" 
                                    placeholder="Masukkan nama lengkap" required>
                            </div>
                        </div>

                        <!-- NIP -->
                        <div>
                            <label for="nip" class="font-montserrat block text-sm font-semibold text-gray-900 mb-2">NIP (Nomor Induk Pegawai)</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                                <input type="text" name="nip" id="nip" value="{{ old('nip') }}" 
                                    class="font-lato block w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-gray-50 placeholder-gray-400" 
                                    placeholder="Contoh: 199003xxxxxx" required>
                            </div>
                        </div>

                        <!-- Email & Phone -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Email -->
                            <div>
                                <label for="email" class="font-montserrat block text-sm font-semibold text-gray-900 mb-2">Email</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                    <input type="email" name="email" id="email" value="{{ old('email') }}" 
                                        class="font-lato block w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-gray-50 placeholder-gray-400" 
                                        placeholder="Contoh: nama@domain.com" required>
                                </div>
                            </div>

                            <!-- Phone -->
                            <div>
                                <label for="phone" class="font-montserrat block text-sm font-semibold text-gray-900 mb-2">Nomor Telepon</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                        </svg>
                                    </div>
                                    <input type="text" name="phone" id="phone" value="{{ old('phone') }}" 
                                        class="font-lato block w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-gray-50 placeholder-gray-400" 
                                        placeholder="Contoh: 08xxxxxx">
                                </div>
                            </div>
                        </div>

                        <!-- Bidang / Balai -->
                        <div>
                            <label for="bidang" class="font-montserrat block text-sm font-semibold text-gray-900 mb-2">Bidang / Balai</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                </div>
                                <select name="bidang" id="bidang" onchange="updateJabatanOptions()" 
                                    class="font-lato block w-full pl-10 pr-10 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-gray-50 appearance-none" required>
                                    <option value="">Pilih bidang atau balai Anda</option>
                                    @foreach($organizationStructure as $bidang => $jabatans)
                                        <option value="{{ $bidang }}" {{ old('bidang') == $bidang ? 'selected' : '' }}>{{ $bidang }}</option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <!-- Jabatan -->
                        <div>
                            <label for="jabatan" class="font-montserrat block text-sm font-semibold text-gray-900 mb-2">Jabatan</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <select name="jabatan" id="jabatan" 
                                    class="font-lato block w-full pl-10 pr-10 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-gray-50 appearance-none" required>
                                    <option value="">Pilih jabatan, subbag, atau seksi Anda</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <!-- Password -->
                        <div>
                            <label for="password" class="font-montserrat block text-sm font-semibold text-gray-900 mb-2">Password</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                    </svg>
                                </div>
                                <input type="password" name="password" id="password" 
                                    class="font-lato block w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-gray-50 placeholder-gray-400" 
                                    placeholder="Masukkan password baru Anda" required>
                            </div>
                            <p class="font-lato mt-1.5 text-xs text-blue-600">Min. 8 karakter terdiri dari huruf dan angka.</p>
                        </div>

                        <!-- Password Confirmation (Hidden in UI but needed for validation) -->
                        <input type="hidden" name="password_confirmation" id="password_confirmation" value="">

                        <!-- Submit Button -->
                        <button type="submit" class="font-montserrat w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3.5 px-4 rounded-lg transition duration-200 shadow-sm hover:shadow-md mt-6">
                            Daftar Sekarang
                        </button>

                        <!-- Login Link -->
                        <div class="text-center mt-4">
                            <p class="font-lato text-sm text-gray-700">
                                Sudah memiliki akun? 
                                <a href="{{ route('login') }}" class="font-montserrat text-blue-600 hover:text-blue-700 font-semibold underline">Masuk disini.</a>
                            </p>
                        </div>
                    </form>
                </div>

                <!-- RIGHT SIDE - Info Section -->
                <div class="space-y-8 lg:pt-8">
                    <!-- Title -->
                    <div>
                        <h1 class="font-montserrat text-5xl font-bold text-gray-900 mb-4 leading-tight">Registrasi Akun Baru</h1>
                        <p class="font-lato text-lg text-blue-700 leading-relaxed">
                            Silahkan lengkapi data diri pegawai Anda untuk mengakses layanan Helpdesk Internal Dinas Perindustrian dan Perdagangan Provinsi Jawa Tengah. Pastikan data yang Anda masukkan telah valid.
                        </p>
                    </div>

                    <!-- Feature Cards -->
                    <div class="space-y-4">
                        <!-- Card 1: Layanan Terpadu -->
                        <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm hover:shadow-md transition-shadow">
                            <div class="flex items-start space-x-4">
                                <div class="flex-shrink-0">
                                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <h3 class="font-montserrat text-lg font-bold text-gray-900 mb-1">Layanan Terpadu</h3>
                                    <p class="font-lato text-sm text-gray-600">Akses bantuan lengkap bagi pegawai DISPERINDAG dalam satu pintu.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Card 2: Pelacakan Tiket -->
                        <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm hover:shadow-md transition-shadow">
                            <div class="flex items-start space-x-4">
                                <div class="flex-shrink-0">
                                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <h3 class="font-montserrat text-lg font-bold text-gray-900 mb-1">Pelacakan Tiket</h3>
                                    <p class="font-lato text-sm text-gray-600">Pantau status permohonan dan kendala pegawai DISPERINDAG secara real-time.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- Success Modal (matching screenshot) -->
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

        <!-- Buttons -->
        <div class="space-y-3">
            <a href="{{ route('login') }}" class="font-montserrat block w-full py-3 px-6 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition duration-200">
                Kembali ke Halaman Login
            </a>
        </div>

    </div>
</div>

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

@push('scripts')
<script>
// Organization structure data
const organizationStructure = @json($organizationStructure);

// Update jabatan options based on selected bidang
function updateJabatanOptions() {
    const bidangSelect = document.getElementById('bidang');
    const jabatanSelect = document.getElementById('jabatan');
    const selectedBidang = bidangSelect.value;
    
    jabatanSelect.innerHTML = '<option value="">Pilih jabatan, subbag, atau seksi Anda</option>';
    
    if (selectedBidang && organizationStructure[selectedBidang]) {
        organizationStructure[selectedBidang].forEach(function(jabatan) {
            const option = document.createElement('option');
            option.value = jabatan;
            option.textContent = jabatan;
            jabatanSelect.appendChild(option);
        });
    }
}

// Auto-fill password confirmation (since it's hidden)
document.getElementById('password').addEventListener('input', function() {
    document.getElementById('password_confirmation').value = this.value;
});

// Restore jabatan selection on page load
document.addEventListener('DOMContentLoaded', function() {
    const oldJabatan = "{{ old('jabatan') }}";
    if (oldJabatan) {
        updateJabatanOptions();
        document.getElementById('jabatan').value = oldJabatan;
    }
    
    // Show success modal if registration successful
    @if(session('registration_success'))
        showSuccessModal();
    @endif
});

// Show success modal
function showSuccessModal() {
    const modal = document.getElementById('successModal');
    modal.style.display = 'flex';
}

// Hide success modal
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
</script>
@endpush
@endsection