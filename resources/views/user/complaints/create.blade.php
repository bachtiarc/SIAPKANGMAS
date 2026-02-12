@extends('layouts.dashboard')

@section('title', 'Buat Pengaduan (CO ADMIN)')

@section('content')
<div class="p-6">
    <!-- Breadcrumb -->
    <nav class="mb-6 text-sm">
        <ol class="flex items-center space-x-2">
            <li><a href="{{ route('user.dashboard') }}" class="text-blue-600 hover:text-blue-800">Beranda</a></li>
            <li class="text-gray-400">/</li>
            <li><a href="{{ route('user.complaints.index') }}" class="text-blue-600 hover:text-blue-800">Pengaduan</a></li>
            <li class="text-gray-400">/</li>
            <li class="text-gray-600">Buat Baru</li>
        </ol>
    </nav>

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
                        Pengaduan berhasil dibuat oleh CO ADMIN. Jika Email pemohon diisi, notifikasi dapat dikirim sesuai alur sistem kamu.
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
                    @if(session('complaint_id'))
                    <button type="button"
                        onclick="window.location.href='{{ route('user.complaints.show', session('complaint_id')) }}?from=create'"
                        class="px-4 py-2 bg-blue-600 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-blue-700 focus:outline-none">
                        Lihat Detail Pengaduan
                    </button>
                    @endif

                    <button type="button" onclick="window.location.href='{{ route('user.complaints.create') }}'"
                        class="px-4 py-2 bg-gray-100 text-gray-800 text-base font-medium rounded-md w-full shadow-sm hover:bg-gray-200 focus:outline-none">
                        Buat Pengaduan Baru
                    </button>

                    <button type="button" onclick="window.location.href='{{ route('user.complaints.index') }}'"
                        class="px-4 py-2 bg-white border border-gray-300 text-gray-800 text-base font-medium rounded-md w-full shadow-sm hover:bg-gray-50 focus:outline-none">
                        Lihat Daftar Pengaduan
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    @php
        $from = request()->query('from', 'index');
        $backUrl = $from === 'dashboard' ? route('user.dashboard') : route('user.complaints.index');
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
                <h1 class="font-montserrat text-3xl font-bold text-gray-900">Formulir Pengaduan (CO ADMIN)</h1>
                <p class="text-gray-600 mt-1">Silakan lengkapi formulir berikut untuk membuat pengaduan atas nama pemohon.</p>
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
                    <li>Jika Email pemohon diisi, sistem dapat mengirim notifikasi sesuai alur kamu.</li>
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

    <form action="{{ route('user.complaints.store') }}" method="POST" enctype="multipart/form-data" id="complaintForm">
        @csrf

        <!-- DATA PEMOHON -->
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
                    <label class="block text-sm font-semibold text-gray-900 mb-2">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap') }}" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-900 mb-2">NIK <span class="text-red-500">*</span></label>
                    <input type="text" name="nik" value="{{ old('nik') }}" required maxlength="16" placeholder="16 digit NIK"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-900 mb-2">Nomor Telepon/WA <span class="text-red-500">*</span></label>
                    <input type="text" name="phone" value="{{ old('phone') }}" required placeholder="contoh: 08xxxxxxxxxx"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-900 mb-2">Email (Opsional)</label>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="contoh: nama@email.com"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-900 mb-2">Kabupaten/Kota <span class="text-red-500">*</span></label>
                    <select name="kabupaten_kode" id="kabupaten" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Pilih Kabupaten/Kota</option>
                    </select>
                    <input type="hidden" name="kabupaten_nama" id="kabupaten_nama" value="{{ old('kabupaten_nama') }}">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-900 mb-2">Kecamatan <span class="text-red-500">*</span></label>
                    <select name="kecamatan_kode" id="kecamatan" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Pilih Kecamatan</option>
                    </select>
                    <input type="hidden" name="kecamatan_nama" id="kecamatan_nama" value="{{ old('kecamatan_nama') }}">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-900 mb-2">Desa/Kelurahan <span class="text-red-500">*</span></label>
                    <select name="desa_kode" id="desa" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Pilih Desa/Kelurahan</option>
                    </select>
                    <input type="hidden" name="desa_nama" id="desa_nama" value="{{ old('desa_nama') }}">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-900 mb-2">Alamat Lengkap (RT/RW, No Jalan, Dusun, dll) <span class="text-red-500">*</span></label>
                    <textarea name="alamat_detail" rows="3" required
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Tulis RT/RW, nomor jalan, dusun, dll...">{{ old('alamat_detail') }}</textarea>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-900 mb-2">Foto KTP <span class="text-red-500">*</span></label>
                    <input type="file" name="foto_ktp" accept="image/*" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-white">
                    <p class="text-xs text-gray-500 mt-2">Format: JPG/PNG/WEBP. Maks 2MB.</p>
                </div>
            </div>
        </div>

        <!-- DETAIL PENGADUAN -->
        <div class="bg-white rounded-lg shadow-sm p-8 mb-6">
            <div class="flex items-center mb-6">
                <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center mr-3">
                    <svg class="w-6 h-6 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="font-montserrat text-xl font-bold text-gray-900">Detail Pengaduan</h2>
                    <p class="text-sm text-gray-600">Tuliskan pengaduan dengan jelas.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6">
                <!-- Title -->
                <div class="md:col-span-2">
                    <label for="title" class="block text-sm font-semibold text-gray-900 mb-2">
                        Judul Pengajuan Pengaduan <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="subject" id="title" value="{{ old('subject') }}" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('title') border-red-500 @enderror"
                        placeholder="Contoh: Pengaduan Toilet Pasar Rusak">
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

        <!-- Buttons -->
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
                Kirim Pengaduan
            </button>
        </div>
    </form>
</div>

<script>
function displayFileName(i, input){
    const nameEl = document.getElementById('fileName'+i);
    const clearBtn = document.getElementById('clearBtn'+i);
    if(input.files && input.files[0]){
        nameEl.textContent = input.files[0].name;
        nameEl.classList.remove('hidden');
        clearBtn.classList.remove('hidden');
    }else{
        nameEl.textContent = '';
        nameEl.classList.add('hidden');
        clearBtn.classList.add('hidden');
    }
}
function clearFile(i){
    const input = document.getElementById('document'+i);
    input.value = '';
    displayFileName(i, input);
}
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

document.addEventListener('DOMContentLoaded', async () => {
    const kab = document.getElementById('kabupaten');
    const kec = document.getElementById('kecamatan');
    const desa = document.getElementById('desa');

    const kabNama = document.getElementById('kabupaten_nama');
    const kecNama = document.getElementById('kecamatan_nama');
    const desaNama = document.getElementById('desa_nama');

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
        if(kab.value){
            kabNama.value = kab.options[kab.selectedIndex].text;
        }
    }

    async function loadKecamatan(kabKode, setOld=false){
        if(!kabKode){
            kec.innerHTML = '<option value="">Pilih Kecamatan</option>';
            desa.innerHTML = '<option value="">Pilih Desa/Kelurahan</option>';
            kecNama.value = '';
            desaNama.value = '';
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
        if(kec.value){
            kecNama.value = kec.options[kec.selectedIndex].text;
        }
    }

    async function loadDesa(kecKode, setOld=false){
        if(!kecKode){
            desa.innerHTML = '<option value="">Pilih Desa/Kelurahan</option>';
            desaNama.value = '';
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
        if(desa.value){
            desaNama.value = desa.options[desa.selectedIndex].text;
        }
    }

    await loadKabupaten();
    if(oldKab) await loadKecamatan(oldKab, true);
    if(oldKec) await loadDesa(oldKec, true);

    kab.addEventListener('change', async (e) => {
        kabNama.value = kab.options[kab.selectedIndex]?.text || '';
        await loadKecamatan(e.target.value);
        desa.innerHTML = '<option value="">Pilih Desa/Kelurahan</option>';
        desaNama.value = '';
    });

    kec.addEventListener('change', async (e) => {
        kecNama.value = kec.options[kec.selectedIndex]?.text || '';
        await loadDesa(e.target.value);
    });

    desa.addEventListener('change', async () => {
        desaNama.value = desa.options[desa.selectedIndex]?.text || '';
    });
});
</script>
@endsection