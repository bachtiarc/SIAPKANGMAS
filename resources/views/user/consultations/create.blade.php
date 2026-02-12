@extends('layouts.dashboard')

@section('title', 'Buat Konsultasi (CO ADMIN)')

@section('content')
<div class="p-6">
    <!-- Breadcrumb -->
    <nav class="mb-6 text-sm">
        <ol class="flex items-center space-x-2">
            <li><a href="{{ route('user.dashboard') }}" class="text-blue-600 hover:text-blue-800">Beranda</a></li>
            <li class="text-gray-400">/</li>
            <li><a href="{{ route('user.consultations.index') }}" class="text-blue-600 hover:text-blue-800">Konsultasi</a></li>
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
                        Pengajuan konsultasi berhasil dibuat oleh CO ADMIN. Jika Email pemohon diisi, notifikasi akan dikirim ke email pemohon.
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
                        <p class="text-[11px] text-gray-500 mt-2">Simpan ID tiket ini untuk pelacakan status.</p>
                    </div>
                </div>

                <div class="items-center px-4 py-3 space-y-2">
                    @if(session('consultation_id'))
                    <button type="button"
                        onclick="window.location.href='{{ route('user.consultations.show', session('consultation_id')) }}?from=create'"
                        class="px-4 py-2 bg-blue-600 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-blue-700 focus:outline-none">
                        Lihat Detail Pengajuan
                    </button>
                    @endif

                    <button type="button" onclick="window.location.href='{{ route('user.consultations.create') }}'"
                        class="px-4 py-2 bg-gray-100 text-gray-800 text-base font-medium rounded-md w-full shadow-sm hover:bg-gray-200 focus:outline-none">
                        Buat Pengajuan Baru
                    </button>

                    <button type="button" onclick="window.location.href='{{ route('user.consultations.index') }}'"
                        class="px-4 py-2 bg-white border border-gray-300 text-gray-800 text-base font-medium rounded-md w-full shadow-sm hover:bg-gray-50 focus:outline-none">
                        Lihat Daftar Konsultasi
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
            : route('user.consultations.index');
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
                    Formulir Konsultasi (CO ADMIN)
                </h1>
                <p class="text-gray-600 mt-1">
                    Silakan lengkapi formulir berikut untuk membuat permohonan konsultasi atas nama pemohon.
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

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-800 p-4 rounded-lg mb-6">
            <div class="font-semibold mb-2">Terdapat kesalahan pada input:</div>
            <ul class="list-disc list-inside text-sm space-y-1">
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('user.consultations.store') }}" method="POST" enctype="multipart/form-data" id="consultationForm">
        @csrf

        <div class="bg-white rounded-lg shadow-sm p-8 mb-6">
            <div class="flex items-center mb-6">
                <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center mr-3">
                    <svg class="w-6 h-6 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M16 7a4 4 0 10-8 0 4 4 0 008 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="font-montserrat text-xl font-bold text-gray-900">Data Pemohon</h2>
                    <p class="text-sm text-gray-600">Data ini digunakan untuk identitas pemohon.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-900 mb-2">
                        Nama Lengkap <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap') }}" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-900 mb-2">
                        NIK <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="nik" value="{{ old('nik') }}" required maxlength="16"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="16 digit NIK">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-900 mb-2">
                        Nomor Telepon/WA <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="phone" value="{{ old('phone') }}" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="contoh: 08xxxxxxxxxx">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-900 mb-2">
                        Email (Opsional)
                    </label>
                    <input type="email" name="email" value="{{ old('email') }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="contoh: nama@email.com">
                </div>

                <!-- Pekerjaan -->
                <div class="md:col-span-2">
                    <label for="pekerjaan" class="block text-sm font-semibold text-gray-900 mb-2">
                        Pekerjaan <span class="text-red-500">*</span>
                    </label>

                    <select name="pekerjaan" id="pekerjaan" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('pekerjaan') border-red-500 @enderror">
                        <option value="">Pilih Pekerjaan</option>
                        <option value="Pelajar/Mahasiswa" {{ old('pekerjaan')=='Pelajar/Mahasiswa' ? 'selected' : '' }}>Pelajar/Mahasiswa</option>
                        <option value="PNS" {{ old('pekerjaan')=='PNS' ? 'selected' : '' }}>PNS</option>
                        <option value="TNI/POLRI" {{ old('pekerjaan')=='TNI/POLRI' ? 'selected' : '' }}>TNI/POLRI</option>
                        <option value="Pegawai Swasta" {{ old('pekerjaan')=='Pegawai Swasta' ? 'selected' : '' }}>Pegawai Swasta</option>
                        <option value="Wiraswasta" {{ old('pekerjaan')=='Wiraswasta' ? 'selected' : '' }}>Wiraswasta</option>
                        <option value="Ibu Rumah Tangga" {{ old('pekerjaan')=='Ibu Rumah Tangga' ? 'selected' : '' }}>Ibu Rumah Tangga</option>
                        <option value="Tidak Bekerja" {{ old('pekerjaan')=='Tidak Bekerja' ? 'selected' : '' }}>Tidak Bekerja</option>
                        <option value="Lainnya" {{ old('pekerjaan')=='Lainnya' ? 'selected' : '' }}>Lainnya</option>
                    </select>

                    @error('pekerjaan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Pekerjaan (Lainnya) -->
                <div id="wrap_pekerjaan_lainnya" class="md:col-span-2 {{ old('pekerjaan')==='Lainnya' ? '' : 'hidden' }}">
                    <label for="pekerjaan_lainnya" class="block text-sm font-semibold text-gray-900 mb-2">
                        Pekerjaan (Lainnya) <span class="text-red-500">*</span>
                    </label>

                    <input type="text" name="pekerjaan_lainnya" id="pekerjaan_lainnya" value="{{ old('pekerjaan_lainnya') }}"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('pekerjaan_lainnya') border-red-500 @enderror"
                        placeholder="Tulis pekerjaan pemohon">

                    @error('pekerjaan_lainnya')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-900 mb-2">
                        Kabupaten/Kota <span class="text-red-500">*</span>
                    </label>
                    <select name="kabupaten_kode" id="kabupaten" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Pilih Kabupaten/Kota</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-900 mb-2">
                        Kecamatan <span class="text-red-500">*</span>
                    </label>
                    <select name="kecamatan_kode" id="kecamatan" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Pilih Kecamatan</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-900 mb-2">
                        Desa/Kelurahan <span class="text-red-500">*</span>
                    </label>
                    <select name="desa_kode" id="desa" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Pilih Desa/Kelurahan</option>
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-900 mb-2">
                        Alamat Lengkap (RT/RW, No Jalan, Dusun, dll) <span class="text-red-500">*</span>
                    </label>
                    <textarea name="alamat_detail" rows="3" required
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Tulis RT/RW, nomor jalan, dusun, dll...">{{ old('alamat_detail') }}</textarea>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-900 mb-2">
                        Foto KTP <span class="text-red-500">*</span>
                    </label>
                    <input type="file" name="foto_ktp" accept="image/*" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-white">
                    <p class="text-xs text-gray-500 mt-2">Format: JPG/PNG/WEBP. Maks 2MB.</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-8 mb-6">
            <div class="flex items-center mb-6">
                <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center mr-3">
                    <svg class="w-6 h-6 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="font-montserrat text-xl font-bold text-gray-900">Detail Konsultasi</h2>
                    <p class="text-sm text-gray-600">Jelaskan kebutuhan konsultasi dengan jelas.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6">
                <!-- Title -->
                <div class="md:col-span-2">
                    <label for="title" class="block text-sm font-semibold text-gray-900 mb-2">
                        Judul Pengajuan Konsultasi <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="subject" id="title" value="{{ old('subject') }}" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('title') border-red-500 @enderror"
                        placeholder="Contoh: Permohonan Konsultasi Mengenai...">
                    @error('subject')
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

<script>
function copyTicket() {
    const text = document.getElementById('ticketNumber')?.innerText || '';
    if(!text) return;

    navigator.clipboard.writeText(text).then(() => {
        const toast = document.createElement('div');
        toast.className = 'fixed top-6 right-6 z-50 px-5 py-4 rounded-xl shadow-lg bg-green-600 text-white text-sm';
        toast.innerText = 'Nomor tiket berhasil disalin!';
        document.body.appendChild(toast);
        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transition = 'opacity 300ms ease';
            setTimeout(() => toast.remove(), 350);
        }, 2500);
    });
}

function togglePekerjaanLainnya() {
    const pekerjaanEl = document.getElementById('pekerjaan');
    const wrap = document.getElementById('wrap_pekerjaan_lainnya');
    const input = document.getElementById('pekerjaan_lainnya');

    if (!pekerjaanEl || !wrap) return;

    if (pekerjaanEl.value === 'Lainnya') {
        wrap.classList.remove('hidden');
        if (input) input.required = true;
    } else {
        wrap.classList.add('hidden');
        if (input) {
            input.required = false;
            input.value = '';
        }
    }
}

document.getElementById('pekerjaan')?.addEventListener('change', togglePekerjaanLainnya);
togglePekerjaanLainnya();

function displayFileName(index, input) {
    const fileNameEl = document.getElementById(`fileName${index}`);
    const clearBtn = document.getElementById(`clearBtn${index}`);

    if (!input || !input.files || input.files.length === 0) {
        if (fileNameEl) {
            fileNameEl.textContent = '';
            fileNameEl.classList.add('hidden');
        }
        if (clearBtn) clearBtn.classList.add('hidden');
        return;
    }

    const file = input.files[0];
    if (fileNameEl) {
        fileNameEl.textContent = file.name;
        fileNameEl.classList.remove('hidden');
    }
    if (clearBtn) clearBtn.classList.remove('hidden');
}

function clearFile(index) {
    const input = document.getElementById(`document${index}`);
    const fileNameEl = document.getElementById(`fileName${index}`);
    const clearBtn = document.getElementById(`clearBtn${index}`);

    if (input) input.value = '';
    if (fileNameEl) {
        fileNameEl.textContent = '';
        fileNameEl.classList.add('hidden');
    }
    if (clearBtn) clearBtn.classList.add('hidden');
}

document.addEventListener('DOMContentLoaded', async () => {
    const kab = document.getElementById('kabupaten');
    const kec = document.getElementById('kecamatan');
    const desa = document.getElementById('desa');

    const oldKab = @json(old('kabupaten_kode'));
    const oldKec = @json(old('kecamatan_kode'));
    const oldDesa = @json(old('desa_kode'));

    async function fetchJson(url){
        const res = await fetch(url);
        if(!res.ok) return [];
        return await res.json();
    }

    async function loadKabupaten(){
        const data = await fetchJson('/api/kabupaten');
        kab.innerHTML = '<option value="">Pilih Kabupaten/Kota</option>';
        data.forEach(item => {
            const opt = document.createElement('option');
            opt.value = item.kode;
            opt.textContent = item.nama;
            if (oldKab && oldKab === item.kode) opt.selected = true;
            kab.appendChild(opt);
        });
    }

    async function loadKecamatan(kabKode, setOld = false){
        if(!kabKode){
            kec.innerHTML = '<option value="">Pilih Kecamatan</option>';
            desa.innerHTML = '<option value="">Pilih Desa/Kelurahan</option>';
            return;
        }

        const data = await fetchJson(`/api/kecamatan/${kabKode}`);
        kec.innerHTML = '<option value="">Pilih Kecamatan</option>';
        data.forEach(item => {
            const opt = document.createElement('option');
            opt.value = item.kode;
            opt.textContent = item.nama;
            if (setOld && oldKec && oldKec === item.kode) opt.selected = true;
            kec.appendChild(opt);
        });
    }

    async function loadDesa(kecKode, setOld = false){
        if(!kecKode){
            desa.innerHTML = '<option value="">Pilih Desa/Kelurahan</option>';
            return;
        }

        const data = await fetchJson(`/api/desa/${kecKode}`);
        desa.innerHTML = '<option value="">Pilih Desa/Kelurahan</option>';
        data.forEach(item => {
            const opt = document.createElement('option');
            opt.value = item.kode;
            opt.textContent = item.nama;
            if (setOld && oldDesa && oldDesa === item.kode) opt.selected = true;
            desa.appendChild(opt);
        });
    }

    await loadKabupaten();

    if(oldKab){
        await loadKecamatan(oldKab, true);
    }
    if(oldKec){
        await loadDesa(oldKec, true);
    }

    kab.addEventListener('change', async (e) => {
        await loadKecamatan(e.target.value);
        desa.innerHTML = '<option value="">Pilih Desa/Kelurahan</option>';
    });

    kec.addEventListener('change', async (e) => {
        await loadDesa(e.target.value);
    });
});
</script>
@endsection