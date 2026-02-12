@extends('layouts.dashboard')

@section('title', 'Buat Permohonan Informasi (CO ADMIN)')

@section('content')
<div class="p-6">
    <!-- Breadcrumb -->
    <nav class="mb-6 text-sm">
        <ol class="flex items-center space-x-2">
            <li><a href="{{ route('user.dashboard') }}" class="text-blue-600 hover:text-blue-800">Beranda</a></li>
            <li class="text-gray-400">/</li>
            <li><a href="{{ route('user.submissions.index') }}" class="text-blue-600 hover:text-blue-800">Permohonan Informasi</a></li>
            <li class="text-gray-400">/</li>
            <li class="text-gray-600">Buat Baru</li>
        </ol>
    </nav>

    @if(session('toast_error'))
        <script>
            window.addEventListener('load', function () {
                const message = @json(session('toast_error'));
                const duration = Number(@json(session('toast_duration', 8000)));
                const toast = document.createElement('div');
                toast.className = 'fixed top-6 right-6 z-50 px-5 py-4 rounded-xl shadow-lg bg-red-600 text-white text-sm';
                toast.innerText = message;
                document.body.appendChild(toast);

                setTimeout(() => {
                    toast.style.opacity = '0';
                    toast.style.transition = 'opacity 300ms ease';
                    setTimeout(() => toast.remove(), 350);
                }, duration);
            });
        </script>
    @endif

    <!-- Success Modal -->
    @if(session('success') && session('ticket_id'))
    <div id="successModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-blue-100">
                    <svg class="h-10 w-10 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h3 class="text-xl leading-6 font-bold text-gray-900 mt-5">Formulir Berhasil Terkirim!</h3>
                <div class="mt-4 px-7 py-3">
                    <p class="text-sm text-gray-600 mb-4">
                        Pengajuan berhasil dibuat oleh CO ADMIN. Jika Email pemohon diisi, notifikasi akan dikirim ke email pemohon.
                    </p>
                    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded">
                        <p class="text-xs text-gray-600 mb-1">Nomor Tiket</p>
                        <div class="flex items-center justify-between">
                            <p class="font-bold text-blue-900 text-lg" id="ticketNumber">{{ session('ticket_id') }}</p>
                            <button type="button" onclick="copyTicket()" class="text-blue-600 hover:text-blue-800" title="Copy">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="items-center px-4 py-3 space-y-2">
                    @if(session('submission_id'))
                    <button type="button" onclick="window.location.href='{{ route('user.submissions.show', session('submission_id')) }}?from=create'"
                        class="px-4 py-2 bg-blue-600 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-blue-700 focus:outline-none">
                        Lihat Detail Permohonan
                    </button>
                    @endif
                    <button type="button" onclick="window.location.href='{{ route('user.submissions.index') }}'"
                        class="px-4 py-2 bg-white text-gray-700 text-base font-medium rounded-md w-full border border-gray-300 shadow-sm hover:bg-gray-50 focus:outline-none">
                        Lihat Daftar Permohonan
                    </button>
                    <button type="button" onclick="window.location.href='{{ route('user.dashboard') }}'"
                        class="px-4 py-2 bg-white text-gray-700 text-base font-medium rounded-md w-full border border-gray-300 shadow-sm hover:bg-gray-50 focus:outline-none">
                        Kembali ke Dashboard
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    @php
        $from = request()->query('from', 'index');
        $backUrl = $from === 'dashboard'
            ? route('user.dashboard')
            : route('user.submissions.index');
    @endphp

    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="flex items-center space-x-4">
            <a href="{{ $backUrl }}" class="text-gray-600 hover:text-gray-900">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h1 class="font-montserrat text-3xl font-bold text-gray-900">
                    Formulir Permohonan Informasi (CO ADMIN)
                </h1>
                <p class="text-gray-600 mt-1">
                    Silakan lengkapi formulir berikut untuk membuat permohonan informasi atas nama pemohon.
                    Estimasi respon waktu 2x24 jam.
                </p>
            </div>
        </div>
    </div>

    <!-- Important Info -->
    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-6 rounded-lg mb-6">
        <div class="flex items-start">
            <svg class="w-6 h-6 text-yellow-600 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
            <div class="flex-1">
                <h3 class="text-sm font-semibold text-yellow-800 mb-2">Perhatian:</h3>
                <ul class="list-disc list-inside text-sm text-yellow-700 space-y-1">
                    <li>Pastikan semua data pemohon yang dimasukkan sudah benar.</li>
                    <li>Jika Email pemohon diisi, sistem akan mengirim notifikasi ke Email pemohon.</li>
                    <li>Simpan ID tiket untuk pelacakan status.</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Form -->
    <form id="submissionForm" action="{{ route('user.submissions.store', ['from' => request('from', 'index')]) }}" method="POST" enctype="multipart/form-data">
        @csrf

        <!-- Data Pemohon Card -->
        <div class="bg-white rounded-lg shadow-sm p-8 mb-6">
            <div class="flex items-center mb-6">
                <svg class="w-6 h-6 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <h2 class="font-montserrat text-xl font-bold text-gray-900">Data Pemohon</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <!-- Nama -->
                <div class="md:col-span-2">
                    <label for="nama_lengkap" class="block text-sm font-semibold text-gray-900 mb-2">
                        Nama Lengkap <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="nama_lengkap" id="nama_lengkap" value="{{ old('nama_lengkap') }}" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('nama_lengkap') border-red-500 @enderror"
                        placeholder="Nama lengkap sesuai KTP">
                    @error('nama_lengkap')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- NIK -->
                <div>
                    <label for="nik" class="block text-sm font-semibold text-gray-900 mb-2">
                        NIK <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="nik" id="nik" value="{{ old('nik') }}" required maxlength="16"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('nik') border-red-500 @enderror"
                        placeholder="16 digit NIK">
                    @error('nik')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Telepon -->
                <div>
                    <label for="phone" class="block text-sm font-semibold text-gray-900 mb-2">
                        Nomor Telepon <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone') }}" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('phone') border-red-500 @enderror"
                        placeholder="Contoh: 62812xxxxxx">
                    @error('phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div class="md:col-span-2">
                    <label for="email" class="block text-sm font-semibold text-gray-900 mb-2">
                        Email Pemohon <span class="text-gray-400">(Opsional)</span>
                    </label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('email') border-red-500 @enderror"
                        placeholder="Jika diisi, notifikasi masuk ke email ini">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Kabupaten -->
                <div>
                    <label for="kabupaten_kode" class="block text-sm font-semibold text-gray-900 mb-2">
                        Kabupaten/Kota <span class="text-red-500">*</span>
                    </label>
                    <select name="kabupaten_kode" id="kabupaten_kode" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('kabupaten_kode') border-red-500 @enderror">
                        <option value="">Pilih Kabupaten/Kota</option>
                    </select>
                    @error('kabupaten_kode')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Kecamatan -->
                <div>
                    <label for="kecamatan_kode" class="block text-sm font-semibold text-gray-900 mb-2">
                        Kecamatan <span class="text-red-500">*</span>
                    </label>
                    <select name="kecamatan_kode" id="kecamatan_kode" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('kecamatan_kode') border-red-500 @enderror">
                        <option value="">Pilih Kecamatan</option>
                    </select>
                    @error('kecamatan_kode')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Desa/Kelurahan -->
                <div class="md:col-span-2">
                    <label for="desa_kode" class="block text-sm font-semibold text-gray-900 mb-2">
                        Desa/Kelurahan <span class="text-red-500">*</span>
                    </label>
                    <select name="desa_kode" id="desa_kode" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('desa_kode') border-red-500 @enderror">
                        <option value="">Pilih Desa/Kelurahan</option>
                    </select>
                    @error('desa_kode')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Alamat Detail -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-900 mb-2">
                        RT/RW / Nomor Jalan <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="alamat_detail" value="{{ old('alamat_detail') }}" required
                    <textarea name="alamat_detail" id="alamat_detail" rows="3"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('alamat_detail') border-red-500 @enderror"
                        placeholder="Contoh: RT 02/RW 01, Jl. Mawar No. 12">{{ old('alamat_detail') }}</textarea>
                    @error('alamat_detail')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Foto KTP -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-900 mb-2">
                        Upload Foto KTP <span class="text-red-500">*</span>
                    </label>

                    <div class="border-2 border-gray-300 border-dashed rounded-lg p-4 hover:border-blue-400 transition cursor-pointer"
                        onclick="document.getElementById('foto_ktp').click()">
                        <div class="flex items-center space-x-3">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                            </svg>
                            <div class="flex-1">
                                <p class="text-sm font-semibold text-gray-700">Foto KTP <span class="text-red-500">*</span></p>
                                <p class="text-xs text-gray-500">JPG, JPEG, PNG, WEBP (Max 2MB)</p>
                                <p id="ktpFileName" class="text-sm text-green-600 font-semibold mt-1 hidden"></p>
                            </div>
                            <button type="button" onclick="event.stopPropagation(); clearKtp()"
                                id="clearKtpBtn" class="hidden text-red-500 hover:text-red-700">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                        <input id="foto_ktp" name="foto_ktp" type="file" class="hidden" accept=".jpg,.jpeg,.png,.webp" required
                            onchange="displayKtpFileName(this)">
                    </div>

                    @error('foto_ktp')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

            </div>
        </div>

        <!-- Main Form Card -->
        <div class="bg-white rounded-lg shadow-sm p-8 mb-6">
            <div class="flex items-center mb-6">
                <svg class="w-6 h-6 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <h2 class="font-montserrat text-xl font-bold text-gray-900">Detail Permohonan</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <!-- Title -->
                <div class="md:col-span-2">
                    <label for="title" class="block text-sm font-semibold text-gray-900 mb-2">
                        Judul Permohonan <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="title" id="title" value="{{ old('title') }}" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('title') border-red-500 @enderror"
                        placeholder="Contoh: Permohonan Data Statistik Perdagangan Tahun 2023">
                    @error('title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-semibold text-gray-900 mb-2">
                        Deskripsi Lengkap <span class="text-red-500">*</span>
                    </label>
                    <textarea name="description" id="description" rows="6" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('description') border-red-500 @enderror"
                        placeholder="Jelaskan secara detail informasi yang diperlukan...">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tujuan -->
                <div class="md:col-span-2">
                    <label for="tujuan_permohonan" class="block text-sm font-semibold text-gray-900 mb-2">
                        Tujuan Permohonan <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="tujuan_permohonan" id="tujuan_permohonan" value="{{ old('tujuan_permohonan') }}" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('tujuan_permohonan') border-red-500 @enderror"
                        placeholder="Contoh: Untuk penelitian, laporan, data pendukung, dll.">
                    @error('tujuan_permohonan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Cara Penyampaian -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-900 mb-2">
                        Cara Penyampaian Feedback <span class="text-red-500">*</span>
                    </label>

                    <div class="space-y-3 text-[15px] leading-relaxed">
                        <label class="flex items-start gap-3 cursor-pointer">
                            <input type="radio" name="cara_penyampaian" value="online"
                                class="mt-1 text-blue-600 focus:ring-blue-500"
                                {{ old('cara_penyampaian', 'online') === 'online' ? 'checked' : '' }}>
                            <span class="text-gray-900">Secara Online</span>
                        </label>

                        <label class="flex items-start gap-3 cursor-pointer">
                            <input type="radio" name="cara_penyampaian" value="datang_langsung"
                                class="mt-1 text-blue-600 focus:ring-blue-500"
                                {{ old('cara_penyampaian') === 'datang_langsung' ? 'checked' : '' }}>
                            <span class="text-gray-900">Datang langsung di kantor Disperindag</span>
                        </label>
                    </div>

                    @error('cara_penyampaian')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror

                    <!-- Opsi Datang Langsung -->
                    <div id="opsiDatangLangsung"
                        class="mt-4 border border-gray-200 rounded-lg p-5 bg-gray-50 {{ old('cara_penyampaian') === 'datang_langsung' ? '' : 'hidden' }}">
                        <p class="text-sm font-semibold text-gray-700 mb-3">Opsi saat datang langsung:</p>

                        <div class="space-y-2 text-sm">
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="radio" name="datang_langsung_opsi" value="flashdisk"
                                    class="text-blue-600 focus:ring-blue-500"
                                    {{ old('datang_langsung_opsi') === 'flashdisk' ? 'checked' : '' }}>
                                <span class="text-gray-800">Membawa flashdisk / media penyimpanan</span>
                            </label>

                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="radio" name="datang_langsung_opsi" value="cetak"
                                    class="text-blue-600 focus:ring-blue-500"
                                    {{ old('datang_langsung_opsi') === 'cetak' ? 'checked' : '' }}>
                                <span class="text-gray-800">Cetak hasil permohonan dengan biaya sendiri</span>
                            </label>

                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="radio" name="datang_langsung_opsi" value="keduanya"
                                    class="text-blue-600 focus:ring-blue-500"
                                    {{ old('datang_langsung_opsi') === 'keduanya' ? 'checked' : '' }}>
                                <span class="text-gray-800">Keduanya</span>
                            </label>
                        </div>

                        @error('datang_langsung_opsi')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Documents -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-900 mb-2">
                        Dokumen Pendukung <span class="text-gray-400">(Opsional)</span>
                    </label>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                        <!-- Upload 1 -->
                        <div class="border-2 border-gray-300 border-dashed rounded-lg p-4 hover:border-blue-400 transition cursor-pointer"
                            onclick="document.getElementById('document1').click()">
                            <div class="flex items-center space-x-3">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                </svg>
                                <div class="flex-1">
                                    <p class="text-sm font-semibold text-gray-700">Dokumen 1 <span class="text-gray-400">(Opsional)</span></p>
                                    <p class="text-xs text-gray-500">PDF, JPG, PNG (Max 2MB)</p>
                                    <p id="fileName1" class="text-sm text-green-600 font-semibold mt-1 hidden"></p>
                                </div>
                                <button type="button" onclick="event.stopPropagation(); clearFile(1)" id="clearBtn1" class="hidden text-red-500 hover:text-red-700">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                            <input id="document1" name="documents[]" type="file" class="hidden" accept=".pdf,.jpg,.jpeg,.png"
                                onchange="displayFileName(1, this)">
                        </div>

                        <!-- Upload 2 -->
                        <div class="border-2 border-gray-300 border-dashed rounded-lg p-4 hover:border-blue-400 transition cursor-pointer"
                            onclick="document.getElementById('document2').click()">
                            <div class="flex items-center space-x-3">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                </svg>
                                <div class="flex-1">
                                    <p class="text-sm font-semibold text-gray-700">Dokumen 2 <span class="text-gray-400">(Opsional)</span></p>
                                    <p class="text-xs text-gray-500">PDF, JPG, PNG (Max 2MB)</p>
                                    <p id="fileName2" class="text-sm text-green-600 font-semibold mt-1 hidden"></p>
                                </div>
                                <button type="button" onclick="event.stopPropagation(); clearFile(2)" id="clearBtn2" class="hidden text-red-500 hover:text-red-700">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                            <input id="document2" name="documents[]" type="file" class="hidden" accept=".pdf,.jpg,.jpeg,.png"
                                onchange="displayFileName(2, this)">
                        </div>

                        <!-- Upload 3 -->
                        <div class="border-2 border-gray-300 border-dashed rounded-lg p-4 hover:border-blue-400 transition cursor-pointer"
                            onclick="document.getElementById('document3').click()">
                            <div class="flex items-center space-x-3">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                </svg>
                                <div class="flex-1">
                                    <p class="text-sm font-semibold text-gray-700">Dokumen 3 <span class="text-gray-400">(Opsional)</span></p>
                                    <p class="text-xs text-gray-500">PDF, JPG, PNG (Max 2MB)</p>
                                    <p id="fileName3" class="text-sm text-green-600 font-semibold mt-1 hidden"></p>
                                </div>
                                <button type="button" onclick="event.stopPropagation(); clearFile(3)" id="clearBtn3" class="hidden text-red-500 hover:text-red-700">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                            <input id="document3" name="documents[]" type="file" class="hidden" accept=".pdf,.jpg,.jpeg,.png"
                                onchange="displayFileName(3, this)">
                        </div>

                    </div>

                    @error('documents.*')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="flex items-center justify-between">
            <a href="{{ $backUrl }}"
                class="px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold rounded-lg transition">
                Kembali
            </a>

            <button type="submit"
                class="px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 9l3 3m0 0l-3 3m3-3H8m13 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Kirim Pengajuan
            </button>
        </div>
    </form>
</div>

<!-- ERROR MODAL -->
<div id="errorModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-yellow-100">
                <svg class="h-10 w-10 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
            </div>
            <h3 class="text-xl leading-6 font-bold text-gray-900 mt-5">Formulir Kurang Lengkap</h3>
            <div class="mt-4 px-7 py-3">
                <p class="text-sm text-gray-600 mb-3">Mohon lengkapi semua field wajib sebelum mengirim.</p>
                <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded text-left">
                    <p class="text-xs text-gray-700 font-semibold mb-2">Field yang perlu dilengkapi:</p>
                    <ul id="errorList" class="text-sm text-red-700 list-disc list-inside space-y-1"></ul>
                </div>
            </div>
            <div class="items-center px-4 py-3">
                <button type="button" onclick="closeErrorModal()"
                    class="px-4 py-2 bg-blue-600 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-blue-700 focus:outline-none">
                    Kembali & Lengkapi
                </button>
            </div>
        </div>
    </div>
</div>

<!-- TOAST CONTAINER -->
<div id="toast-container" class="fixed top-5 right-5 z-[9999] space-y-3"></div>

<style>
  .toast-enter { transform: translateX(120%); opacity: 0; }
  .toast-enter-active { transform: translateX(0); opacity: 1; transition: all .25s ease; }
  .toast-exit { transform: translateX(120%); opacity: 0; transition: all .25s ease; }
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const radios = document.querySelectorAll('input[name="cara_penyampaian"]');
    const box = document.getElementById('opsiDatangLangsung');

    function sync() {
        const checked = document.querySelector('input[name="cara_penyampaian"]:checked');
        if (checked && checked.value === 'datang_langsung') {
            box.classList.remove('hidden');
        } else {
            box.classList.add('hidden');
            const opsiRadios = document.querySelectorAll('input[name="datang_langsung_opsi"]');
            opsiRadios.forEach(r => r.checked = false);
        }
    }
    radios.forEach(r => r.addEventListener('change', sync));
    sync();

    loadKabupaten();

    const oldKab = @json(old('kabupaten_kode'));
    const oldKec = @json(old('kecamatan_kode'));
    const oldDesa = @json(old('desa_kode'));

    if (oldKab) {
        setTimeout(() => {
            document.getElementById('kabupaten_kode').value = oldKab;
            loadKecamatan(oldKab, oldKec, oldDesa);
        }, 300);
    }
});

// ========= TOAST =========
function showToast(message, type = 'success') {
    const container = document.getElementById('toast-container');
    if (!container) return;

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
        <button type="button" class="flex-shrink-0 text-gray-400 hover:text-gray-600">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    `;

    toast.querySelector('button').addEventListener('click', () => {
        toast.classList.add('toast-exit');
        setTimeout(() => toast.remove(), 250);
    });

    container.appendChild(toast);
    requestAnimationFrame(() => toast.classList.add('toast-enter-active'));

    setTimeout(() => {
        toast.classList.remove('toast-enter-active');
        toast.classList.add('toast-exit');
        setTimeout(() => toast.remove(), 250);
    }, 4000);
}

// ========= KTP =========
function displayKtpFileName(input) {
    const fileNameDisplay = document.getElementById('ktpFileName');
    const clearBtn = document.getElementById('clearKtpBtn');

    if (input.files && input.files[0]) {
        const file = input.files[0];
        const maxBytes = 2 * 1024 * 1024;

        if (file.size > maxBytes) {
            input.value = '';
            fileNameDisplay.classList.add('hidden');
            clearBtn.classList.add('hidden');
            showToast('Foto KTP: ukuran file melebihi 2MB.', 'error');
            return;
        }

        const fileSize = (file.size / 1024 / 1024).toFixed(2);
        fileNameDisplay.textContent = `✓ ${file.name} (${fileSize} MB)`;
        fileNameDisplay.classList.remove('hidden');
        clearBtn.classList.remove('hidden');
    }
}

function clearKtp() {
    const fileInput = document.getElementById('foto_ktp');
    const fileNameDisplay = document.getElementById('ktpFileName');
    const clearBtn = document.getElementById('clearKtpBtn');

    if (fileInput) fileInput.value = '';
    if (fileNameDisplay) fileNameDisplay.classList.add('hidden');
    if (clearBtn) clearBtn.classList.add('hidden');
}

// ========= DOKUMEN (urutan + max size) =========
function canUpload(idx) {
    if (idx === 1) return true;

    const prevInput = document.getElementById('document' + (idx - 1));
    const prevHasFile = prevInput && prevInput.files && prevInput.files.length > 0;

    if (!prevHasFile) {
        showToast(`Mohon upload Dokumen ${idx - 1} dulu sebelum Dokumen ${idx}.`, 'error');
        return false;
    }
    return true;
}

function displayFileName(index, input) {
    const fileNameDisplay = document.getElementById('fileName' + index);
    const clearBtn = document.getElementById('clearBtn' + index);

    if (!canUpload(index)) {
        input.value = '';
        if (fileNameDisplay) fileNameDisplay.classList.add('hidden');
        if (clearBtn) clearBtn.classList.add('hidden');
        return;
    }

    if (input.files && input.files[0]) {
        const file = input.files[0];
        const maxBytes = 2 * 1024 * 1024;

        if (file.size > maxBytes) {
            input.value = '';
            if (fileNameDisplay) fileNameDisplay.classList.add('hidden');
            if (clearBtn) clearBtn.classList.add('hidden');
            showToast(`Dokumen ${index}: ukuran file melebihi 2MB.`, 'error');
            return;
        }

        const fileSize = (file.size / 1024 / 1024).toFixed(2);
        fileNameDisplay.textContent = `✓ ${file.name} (${fileSize} MB)`;
        fileNameDisplay.classList.remove('hidden');
        clearBtn.classList.remove('hidden');
    }
}

function clearFile(index) {
    const fileInput = document.getElementById('document' + index);
    const fileNameDisplay = document.getElementById('fileName' + index);
    const clearBtn = document.getElementById('clearBtn' + index);

    if (fileInput) fileInput.value = '';
    if (fileNameDisplay) fileNameDisplay.classList.add('hidden');
    if (clearBtn) clearBtn.classList.add('hidden');
}

function copyTicket() {
    const ticketNumber = document.getElementById('ticketNumber').textContent;
    navigator.clipboard.writeText(ticketNumber).then(() => {
        showToast('Nomor tiket berhasil disalin!', 'success');
    });
}

function showErrorModal(errors) {
    const errorList = document.getElementById('errorList');
    errorList.innerHTML = '';

    if (Array.isArray(errors)) {
        errors.forEach(error => {
            const li = document.createElement('li');
            li.textContent = error;
            errorList.appendChild(li);
        });
    }

    document.getElementById('errorModal').classList.remove('hidden');
}

function closeErrorModal() {
    document.getElementById('errorModal').classList.add('hidden');
}

// ===== VALIDASI FRONTEND (biar modal error sama kayak masyarakat) =====
document.getElementById('submissionForm').addEventListener('submit', function(e) {
    const requiredFields = [
        { id: 'nama_lengkap', label: 'Nama Lengkap' },
        { id: 'nik', label: 'NIK' },
        { id: 'phone', label: 'Nomor Telepon' },
        { id: 'kabupaten_kode', label: 'Kabupaten/Kota' },
        { id: 'kecamatan_kode', label: 'Kecamatan' },
        { id: 'desa_kode', label: 'Desa/Kelurahan' },
        { id: 'foto_ktp', label: 'Foto KTP' },
        { id: 'title', label: 'Judul Permohonan' },
        { id: 'description', label: 'Deskripsi Lengkap' },
        { id: 'tujuan_permohonan', label: 'Tujuan Permohonan' },
    ];

    let errors = [];

    requiredFields.forEach(f => {
        const el = document.getElementById(f.id);
        if (!el) return;

        if (el.type === 'file') {
            if (!el.files || el.files.length === 0) errors.push(f.label);
        } else if (!el.value || String(el.value).trim() === '') {
            errors.push(f.label);
        }
    });

    const cara = document.querySelector('input[name="cara_penyampaian"]:checked');
    if (!cara) errors.push('Cara Penyampaian Feedback');

    if (cara && cara.value === 'datang_langsung') {
        const opsi = document.querySelector('input[name="datang_langsung_opsi"]:checked');
        if (!opsi) errors.push('Opsi Datang Langsung');
    }

    if (errors.length > 0) {
        e.preventDefault();
        showErrorModal(errors);
    }
});

function loadKabupaten() {
    const kabEl = document.getElementById('kabupaten_kode');
    kabEl.innerHTML = '<option value="">Pilih Kabupaten/Kota</option>';

    fetch('/api/kabupaten')
        .then(r => r.json())
        .then(items => {
            items.forEach(it => {
                const opt = document.createElement('option');
                opt.value = it.kode;
                opt.textContent = it.nama;
                kabEl.appendChild(opt);
            });
        })
        .catch(() => showToast('Gagal memuat data Kabupaten.', 'error'));
}

function loadKecamatan(kabKode, setSelected = null, desaSelected = null) {
    const kecEl = document.getElementById('kecamatan_kode');
    const desaEl = document.getElementById('desa_kode');

    kecEl.innerHTML = '<option value="">Pilih Kecamatan</option>';
    desaEl.innerHTML = '<option value="">Pilih Desa/Kelurahan</option>';

    if (!kabKode) return;

    fetch('/api/kecamatan/' + kabKode)
        .then(r => r.json())
        .then(items => {
            items.forEach(it => {
                const opt = document.createElement('option');
                opt.value = it.kode;
                opt.textContent = it.nama;
                kecEl.appendChild(opt);
            });

            if (setSelected) {
                kecEl.value = setSelected;
                loadDesa(setSelected, desaSelected);
            }
        })
        .catch(() => showToast('Gagal memuat data Kecamatan.', 'error'));
}

function loadDesa(kecKode, setSelected = null) {
    const desaEl = document.getElementById('desa_kode');
    desaEl.innerHTML = '<option value="">Pilih Desa/Kelurahan</option>';

    if (!kecKode) return;

    fetch('/api/desa/' + kecKode)
        .then(r => r.json())
        .then(items => {
            items.forEach(it => {
                const opt = document.createElement('option');
                opt.value = it.kode;
                opt.textContent = it.nama;
                desaEl.appendChild(opt);
            });

            if (setSelected) desaEl.value = setSelected;
        })
        .catch(() => showToast('Gagal memuat data Desa/Kelurahan.', 'error'));
}

document.getElementById('kabupaten_kode').addEventListener('change', function() {
    loadKecamatan(this.value);
});
document.getElementById('kecamatan_kode').addEventListener('change', function() {
    loadDesa(this.value);
});

</script>
@endsection