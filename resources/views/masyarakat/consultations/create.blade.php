@extends('layouts.dashboard')

@section('title', 'Formulir Pengajuan Konsultasi')

@section('content')
<div class="p-6">
    <!-- Breadcrumb -->
    <nav class="mb-6 text-sm">
        <ol class="flex items-center space-x-2">
            <li><a href="{{ route('masyarakat.dashboard') }}" class="text-blue-600 hover:text-blue-800">Beranda</a></li>
            <li class="text-gray-400">/</li>
            <li><a href="{{ route('masyarakat.consultations.index') }}" class="text-blue-600 hover:text-blue-800">Konsultasi</a></li>
            <li class="text-gray-400">/</li>
            <li class="text-gray-600">Formulir Pengajuan Konsultasi</li>
        </ol>
    </nav>

    @php
        $from = request()->query('from', 'index');
        $backUrl = $from === 'dashboard'
            ? route('masyarakat.dashboard')
            : route('masyarakat.consultations.index');
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
                    Formulir Pengajuan Konsultasi
                </h1>
                <p class="text-gray-600 mt-1">
                    Silakan lengkapi formulir di bawah ini untuk mengajukan konsultasi kepada Dinas Perindustrian dan Perdagangan Provinsi Jawa Tengah.
                    Estimasi respon waktu 1x24 jam.
                </p>
            </div>
        </div>
    </div>
    
    <!-- Important Info  -->
    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-6 rounded-lg mb-6">
        <div class="flex items-start">
            <svg class="w-6 h-6 text-yellow-600 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
            <div class="flex-1">
                <h3 class="text-sm font-semibold text-yellow-800 mb-2">Perhatian:</h3>
                <ul class="list-disc list-inside text-sm text-yellow-700 space-y-1">
                    <li>Pastikan semua data yang Anda masukkan sudah benar</li>
                    <li>Anda akan menerima notifikasi via email setelah konsultasi diproses</li>
                    <li>Simpan nomor tiket Anda untuk melacak status konsultasi</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Form -->
    <form
        action="{{ route('masyarakat.consultations.store', ['from' => request('from', 'index')]) }}"
        method="POST"
        enctype="multipart/form-data"
        id="consultationForm">
        @csrf

        <div class="bg-white rounded-lg shadow-sm p-8 mb-6">
            <div class="flex items-center mb-6">
                <svg class="w-6 h-6 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                </svg>
                <h2 class="font-montserrat text-xl font-bold text-gray-900">Detail Konsultasi</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Subjek Konsultasi -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-900 mb-2">
                        Subjek Konsultasi <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="subject" value="{{ old('subject') }}" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('subject') border-red-500 @enderror"
                        placeholder="Contoh: Konsultasi Layanan - Gangguan Sistem">
                    @error('subject')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Deskripsi Konsultasi -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-900 mb-2">
                        Deskripsi Konsultasi <span class="text-red-500">*</span>
                    </label>
                    <textarea name="description" rows="6" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('description') border-red-500 @enderror"
                        placeholder="Jelaskan secara detail konsultasi Anda...">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Document Upload (Multiple Upload) -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-900 mb-2">
                        Dokumen Pendukung <span class="text-gray-500">(Maksimal 3 file)</span>
                    </label>

                    <div class="space-y-4">
                        <!-- Upload Area 1 -->
                        <div id="docBox1" class="border-2 border-gray-300 border-dashed rounded-lg p-4 hover:border-blue-400 transition cursor-pointer"
                             onclick="document.getElementById('document1').click()">
                            <div class="flex items-center space-x-3">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                </svg>
                                <div class="flex-1">
                                    <p class="text-sm font-semibold text-gray-700">Dokumen 1 <span class="text-gray-400">(Opsional)</span></p>
                                    <p class="text-xs text-gray-500">PDF, JPG, PNG (Max 2MB)</p>
                                    <p id="fileName1" class="text-sm text-green-600 font-semibold mt-1 hidden"></p>
                                    <p id="fileErr1" class="text-sm text-red-600 font-semibold mt-1 hidden"></p>
                                </div>
                                <button type="button" onclick="event.stopPropagation(); clearFile(1)" id="clearBtn1" class="hidden text-red-500 hover:text-red-700">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                            <input id="document1" name="documents[]" type="file" class="hidden" accept=".pdf,.jpg,.jpeg,.png"
                                   onchange="displayFileName(1, this)">
                        </div>

                        <!-- Upload Area 2 -->
                        <div id="docBox2" class="border-2 border-gray-300 border-dashed rounded-lg p-4 hover:border-blue-400 transition cursor-pointer"
                             onclick="document.getElementById('document2').click()">
                            <div class="flex items-center space-x-3">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                </svg>
                                <div class="flex-1">
                                    <p class="text-sm font-semibold text-gray-700">Dokumen 2 <span class="text-gray-400">(Opsional)</span></p>
                                    <p class="text-xs text-gray-500">PDF, JPG, PNG (Max 2MB)</p>
                                    <p id="fileName2" class="text-sm text-green-600 font-semibold mt-1 hidden"></p>
                                    <p id="fileErr2" class="text-sm text-red-600 font-semibold mt-1 hidden"></p>
                                </div>
                                <button type="button" onclick="event.stopPropagation(); clearFile(2)" id="clearBtn2" class="hidden text-red-500 hover:text-red-700">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                            <input id="document2" name="documents[]" type="file" class="hidden" accept=".pdf,.jpg,.jpeg,.png"
                                   onchange="displayFileName(2, this)">
                        </div>

                        <!-- Upload Area 3 -->
                        <div id="docBox3" class="border-2 border-gray-300 border-dashed rounded-lg p-4 hover:border-blue-400 transition cursor-pointer"
                             onclick="document.getElementById('document3').click()">
                            <div class="flex items-center space-x-3">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                </svg>
                                <div class="flex-1">
                                    <p class="text-sm font-semibold text-gray-700">Dokumen 3 <span class="text-gray-400">(Opsional)</span></p>
                                    <p class="text-xs text-gray-500">PDF, JPG, PNG (Max 2MB)</p>
                                    <p id="fileName3" class="text-sm text-green-600 font-semibold mt-1 hidden"></p>
                                    <p id="fileErr3" class="text-sm text-red-600 font-semibold mt-1 hidden"></p>
                                </div>
                                <button type="button" onclick="event.stopPropagation(); clearFile(3)" id="clearBtn3" class="hidden text-red-500 hover:text-red-700">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                            <input id="document3" name="documents[]" type="file" class="hidden" accept=".pdf,.jpg,.jpeg,.png"
                                   onchange="displayFileName(3, this)">
                        </div>
                    </div>

                    @error('documents')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    @error('documents.*')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-between">
            @php
                $from = request()->query('from', 'index');
                $backUrl = $from === 'dashboard'
                    ? route('masyarakat.dashboard')
                    : route('masyarakat.consultations.index');
            @endphp

            <a href="{{ $backUrl }}"
                class="px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold rounded-lg transition">
                Kembali
            </a>

            <button type="submit"
                class="px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 9l3 3m0 0l-3 3m3-3H8m13 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Kirim Pengajuan
            </button>
        </div>
    </form>
</div>

<!-- SUCCESS MODAL -->
<div id="successModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-blue-100">
                <svg class="h-10 w-10 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <h3 class="text-xl leading-6 font-bold text-gray-900 mt-5">Formulir Anda Berhasil Terkirim!</h3>
            <div class="mt-4 px-7 py-3">
                <p class="text-sm text-gray-600 mb-4">
                    Terima kasih telah mengirimkan konsultasi. Pengajuan Anda akan segera ditinjau. Silakan cek Email untuk melihat bukti konfirmasi formulir telah terkirim.
                </p>
                <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded">
                    <p class="text-xs text-gray-600 mb-1">Nomor Tiket Anda</p>
                    <div class="flex items-center justify-between">
                        <p class="font-bold text-blue-900 text-lg" id="ticketNumber"></p>
                        <button type="button" onclick="copyTicket()" class="text-blue-600 hover:text-blue-800" title="Copy">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            <div class="items-center px-4 py-3 space-y-2">
                @if(session('consultation_id'))
                <button type="button" onclick="window.location.href='{{ route('masyarakat.consultations.show', session('consultation_id')) }}'"
                    class="px-4 py-2 bg-blue-600 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-blue-700 focus:outline-none">
                    Lihat Detail Konsultasi
                </button>
                @endif
                <button type="button" onclick="window.location.href='{{ route('masyarakat.consultations.index') }}'"
                    class="px-4 py-2 bg-white text-gray-700 text-base font-medium rounded-md w-full border border-gray-300 shadow-sm hover:bg-gray-50 focus:outline-none">
                    Lihat Daftar Konsultasi
                </button>
                <button type="button" onclick="window.location.href='{{ route('masyarakat.dashboard') }}'"
                    class="px-4 py-2 bg-white text-gray-700 text-base font-medium rounded-md w-full border border-gray-300 shadow-sm hover:bg-gray-50 focus:outline-none">
                    Kembali ke Dashboard
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ERROR MODAL -->
<div id="errorModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-yellow-100">
                <svg class="h-10 w-10 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
            </div>
            <h3 class="text-xl leading-6 font-bold text-gray-900 mt-5">Formulir Anda Kurang Lengkap</h3>
            <div class="mt-4 px-7 py-3">
                <p class="text-sm text-gray-600 mb-3">
                    Mohon lengkapi semua field yang wajib diisi sebelum mengirimkan formulir.
                </p>
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

<!-- TOAST CONTAINER  -->
<div id="toast-container" class="fixed top-5 right-5 z-[9999] space-y-3"></div>

<style>
  .toast-enter { transform: translateX(120%); opacity: 0; }
  .toast-enter-active { transform: translateX(0); opacity: 1; transition: all .25s ease; }
  .toast-exit { transform: translateX(120%); opacity: 0; transition: all .25s ease; }
</style>

<script>
function formatFileSize(bytes) {
    if (!bytes) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return (bytes / Math.pow(k, i)).toFixed(2) + ' ' + sizes[i];
}


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
    toast.className = `toast-enter bg-white shadow-lg rounded-lg p-4 flex items-center space-x-3 min-w-[320px] border-l-4 ${borderColor}`;

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

function displayFileName(idx, input) {
    const nameEl = document.getElementById('fileName' + idx);
    const errEl  = document.getElementById('fileErr' + idx);
    const clearBtn = document.getElementById('clearBtn' + idx);

    if (errEl) {
        errEl.classList.add('hidden');
        errEl.textContent = '';
    }

    if (!input.files || !input.files[0]) {
        nameEl.classList.add('hidden');
        nameEl.textContent = '';
        clearBtn.classList.add('hidden');
        setDocBoxState(idx, null);
        return;
    }

    const f = input.files[0];
    const maxBytes = 2 * 1024 * 1024; // 2MB

    if (f.size > maxBytes) {
        input.value = '';
        nameEl.classList.add('hidden');
        nameEl.textContent = '';
        clearBtn.classList.add('hidden');
        setDocBoxState(idx, null);

        showToast(`Dokumen ${idx}: ukuran file melebihi 2MB.`, 'error');
        return;
    }

    nameEl.textContent = `✓ ${f.name} (${formatFileSize(f.size)})`;
    nameEl.classList.remove('hidden');
    clearBtn.classList.remove('hidden');
    setDocBoxState(idx, 'ok');
}

function clearFile(idx) {
    const input = document.getElementById('document' + idx);
    const nameEl = document.getElementById('fileName' + idx);
    const errEl  = document.getElementById('fileErr' + idx);
    const clearBtn = document.getElementById('clearBtn' + idx);

    if (input) input.value = '';

    if (nameEl) {
        nameEl.textContent = '';
        nameEl.classList.add('hidden');
    }

    if (errEl) {
        errEl.textContent = '';
        errEl.classList.add('hidden');
    }

    if (clearBtn) clearBtn.classList.add('hidden');

    setDocBoxState(idx, null);
}

function showSuccessModal(ticketNumber) {
    document.getElementById('ticketNumber').textContent = ticketNumber;
    document.getElementById('successModal').classList.remove('hidden');
}

function copyTicket() {
    const ticketNumber = document.getElementById('ticketNumber').textContent;
    navigator.clipboard.writeText(ticketNumber).then(() => {
        alert('Nomor tiket berhasil disalin!');
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
    } else if (typeof errors === 'object') {
        Object.values(errors).forEach(errorArray => {
            errorArray.forEach(error => {
                const li = document.createElement('li');
                li.textContent = error;
                errorList.appendChild(li);
            });
        });
    }

    document.getElementById('errorModal').classList.remove('hidden');
}

function closeErrorModal() {
    document.getElementById('errorModal').classList.add('hidden');
}

@if(session('success'))
    showSuccessModal('{{ session('ticket_id') ?? 'N/A' }}');
@endif

@if($errors->any())
    const errors = @json($errors->all());
    showErrorModal(errors);
@endif
</script>
@endsection