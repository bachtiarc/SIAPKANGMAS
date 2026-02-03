@extends('layouts.dashboard')

@section('title', 'Profil Pengguna')

@section('content')
<div id="toast-container" class="fixed top-6 right-6 z-50"></div>

<div class="p-6">
    <div class="mb-6">
        <h1 class="font-montserrat text-3xl font-bold text-blue-700">Profil Pengguna</h1>
        <p class="font-lato text-gray-600 mt-1">Kelola informasi data diri dan akun Anda.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-sm p-6 text-center">
                <div class="mb-4 relative">
                    @php
                        $user = auth()->user();
                    @endphp

                    @if($user->profile_photo_url)
                        <img src="{{ $user->profile_photo_url }}" alt="Profile"
                            class="w-32 h-32 mx-auto rounded-full object-cover border-4 border-blue-100">
                    @else
                        <div class="w-32 h-32 mx-auto bg-gradient-to-br from-blue-500 to-blue-700 rounded-full flex items-center justify-center text-white text-4xl font-bold border-4 border-blue-100">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                    @endif

                    <form action="{{ route('masyarakat.profile.photo.update') }}" method="POST" enctype="multipart/form-data" id="photoForm" class="absolute bottom-0 right-1/2 transform translate-x-1/2 translate-y-2">
                        @csrf
                        @method('PUT')
                        <label for="profile_photo" class="cursor-pointer bg-blue-600 hover:bg-blue-700 text-white p-2 rounded-full shadow-lg transition inline-block">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </label>
                        <input type="file" name="profile_photo" id="profile_photo" accept="image/jpeg,image/jpg,image/png" class="hidden" onchange="validateFileSize(this)">
                    </form>
                </div>

                <h2 class="font-montserrat text-xl font-bold text-gray-900 mb-1">
                    {{ auth()->user()->name }}
                </h2>
                <p class="font-lato text-sm text-gray-600 mb-1">Masyarakat Umum</p>
                <p class="font-lato text-sm text-gray-500">NIK: {{ auth()->user()->nik ?? '-' }}</p>

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

        <div class="lg:col-span-2 space-y-6">

            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center mb-6">
                    <svg class="w-6 h-6 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    <h3 class="font-montserrat text-xl font-bold text-gray-900">Informasi Pribadi</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="font-montserrat block text-sm font-semibold text-gray-700 mb-2">Nama Lengkap</label>
                        <input type="text" value="{{ auth()->user()->name }}" readonly
                            class="font-lato w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-700">
                    </div>

                    <div>
                        <label class="font-montserrat block text-sm font-semibold text-gray-700 mb-2">NIK (Nomor Induk Kewarganegaraan)</label>
                        <input type="text" value="{{ auth()->user()->nik ?? '-' }}" readonly
                            class="font-lato w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-700">
                    </div>

                    <div>
                        <label class="font-montserrat block text-sm font-semibold text-gray-700 mb-2">Email</label>
                        <input type="email" value="{{ auth()->user()->email }}" readonly
                            class="font-lato w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-700">
                    </div>

                    <div>
                        <label class="font-montserrat block text-sm font-semibold text-gray-700 mb-2">Nomor Telepon</label>
                        <input type="text" value="{{ auth()->user()->phone }}" readonly
                            class="font-lato w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-700">
                    </div>

                    <div class="md:col-span-2">
                        <label class="font-montserrat block text-sm font-semibold text-gray-700 mb-2">Alamat Lengkap</label>
                        <textarea readonly rows="3"
                            class="font-lato w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-700">{{ auth()->user()->address ?? '-' }}</textarea>
                    </div>

                    @if(auth()->user()->foto_ktp)
                        @php
                            // ====== FOTO KTP URL RESOLVER (LOCAL / SUPABASE / FULL URL) ======
                            $rawKtp = auth()->user()->foto_ktp;
                            $ktpUrl = null;

                            if (!empty($rawKtp)) {
                                if (\Illuminate\Support\Str::startsWith($rawKtp, ['http://', 'https://'])) {
                                    $ktpUrl = $rawKtp;
                                } elseif (\Illuminate\Support\Str::startsWith($rawKtp, ['ktp-photos/', 'profile-photos/', 'public/', 'storage/'])) {
                                    $normalized = $rawKtp;
                                    if (\Illuminate\Support\Str::startsWith($normalized, 'public/')) {
                                        $normalized = \Illuminate\Support\Str::after($normalized, 'public/');
                                    }
                                    if (\Illuminate\Support\Str::startsWith($normalized, 'storage/')) {
                                        $normalized = \Illuminate\Support\Str::after($normalized, 'storage/');
                                    }
                                    $ktpUrl = asset('storage/' . ltrim($normalized, '/'));
                                } else {
                                    $supabaseUrl = rtrim(env('SUPABASE_URL'), '/');
                                    $bucket = env('SUPABASE_KTP_BUCKET', 'ktp-photos');

                                    $filePath = ltrim($rawKtp, '/');
                                    if (\Illuminate\Support\Str::startsWith($filePath, $bucket . '/')) {
                                        $filePath = \Illuminate\Support\Str::after($filePath, $bucket . '/');
                                    }

                                    $ktpUrl = "{$supabaseUrl}/storage/v1/object/public/{$bucket}/{$filePath}";
                                }
                            }
                        @endphp

                        <div class="md:col-span-2">
                            <label class="font-montserrat block text-sm font-semibold text-gray-700 mb-2">Foto KTP</label>
                            <div class="border border-gray-300 rounded-lg p-4 bg-gray-50 text-center">
                                <img src="{{ $ktpUrl }}" alt="KTP" class="max-w-md mx-auto rounded-lg shadow-sm">
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center mb-6">
                    <svg class="w-6 h-6 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                    <h3 class="font-montserrat text-xl font-bold text-gray-900">Keamanan Akun</h3>
                </div>

                <form action="{{ route('masyarakat.password.update') }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="font-montserrat block text-sm font-semibold text-gray-700 mb-2">Password Saat Ini</label>
                        <input type="password" name="current_password" required
                            class="font-lato w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Masukkan password saat ini...">
                    </div>

                    <div>
                        <label class="font-montserrat block text-sm font-semibold text-gray-700 mb-2">Password Baru</label>
                        <input type="password" name="password" required
                            class="font-lato w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Minimal 8 karakter...">
                        <p class="font-lato text-xs text-gray-500 mt-1">* Buat password Anda 8 karakter atau lebih.</p>
                    </div>

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

@push('styles')
<style>
@keyframes slideIn { from { transform: translateX(400px); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
@keyframes slideOut { from { transform: translateX(0); opacity: 1; } to { transform: translateX(400px); opacity: 0; } }
.toast-enter { animation: slideIn 0.3s ease-out forwards; }
.toast-exit { animation: slideOut 0.3s ease-in forwards; }
</style>
@endpush

@push('scripts')
<script>
function showToast(message, type = 'success') {
    const container = document.getElementById('toast-container');
    let borderColor, iconColor, icon;

    if (type === 'success') {
        borderColor = 'border-green-500';
        iconColor = 'text-green-500';
        icon = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>';
    } else if (type === 'error') {
        borderColor = 'border-red-500';
        iconColor = 'text-red-500';
        icon = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>';
    } else {
        borderColor = 'border-blue-500';
        iconColor = 'text-blue-500';
        icon = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>';
    }

    const toast = document.createElement('div');
    toast.className = `toast-enter bg-white shadow-lg rounded-lg p-4 mb-3 flex items-center space-x-3 min-w-[320px] border-l-4 ${borderColor}`;

    toast.innerHTML = `
        <div class="flex-shrink-0">
            <svg class="w-6 h-6 ${iconColor}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                ${icon}
            </svg>
        </div>
        <div class="flex-1">
            <p class="font-montserrat text-sm font-semibold text-gray-900">${message}</p>
        </div>
        <button onclick="this.parentElement.remove()" class="flex-shrink-0 text-gray-400 hover:text-gray-600">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    `;

    container.appendChild(toast);

    setTimeout(() => {
        toast.classList.remove('toast-enter');
        toast.classList.add('toast-exit');
        setTimeout(() => toast.remove(), 300);
    }, 5000);
}

function validateFileSize(input) {
    const file = input.files[0];
    if (!file) return;

    const maxSize = 2 * 1024 * 1024;

    if (file.size > maxSize) {
        const fileSizeMB = (file.size / 1024 / 1024).toFixed(2);
        showToast(`Ukuran foto maksimal 2MB! File Anda: ${fileSizeMB}MB`, 'error');
        input.value = '';
        return false;
    }

    const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
    if (!allowedTypes.includes(file.type)) {
        showToast('Format foto harus JPG, JPEG, atau PNG!', 'error');
        input.value = '';
        return false;
    }

    document.getElementById('photoForm').submit();
    return true;
}

@if(session('photo_success'))
    showToast('{{ session("photo_success") }}', 'success');
@endif
@if(session('photo_error'))
    showToast('{{ session("photo_error") }}', 'error');
@endif
@if(session('password_success'))
    showToast('{{ session("password_success") }}', 'success');
@endif
@if(session('password_error'))
    showToast('{{ session("password_error") }}', 'error');
@endif
</script>
@endpush