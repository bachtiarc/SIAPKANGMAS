@extends('layouts.dashboard')

@section('title', 'Formulir Pengajuan Pengaduan')

@section('content')
<div class="p-6">
    <!-- Breadcrumb -->
    <nav class="mb-6 text-sm">
        <ol class="flex items-center space-x-2">
            <li><a href="{{ route('masyarakat.dashboard') }}" class="text-blue-600 hover:text-blue-800">Beranda</a></li>
            <li class="text-gray-400">/</li>
            <li><a href="{{ route('masyarakat.complaints.index') }}" class="text-blue-600 hover:text-blue-800">Pengaduan</a></li>
            <li class="text-gray-400">/</li>
            <li class="text-gray-600">Formulir Pengajuan Pengaduan</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <h1 class="font-montserrat text-3xl font-bold text-gray-900 mb-2">Formulir Pengajuan Pengaduan</h1>
        <p class="text-gray-600">Silakan lengkapi formulir di bawah ini untuk menyampaikan aspirasi dan pengaduan Anda kepada Dinas Perindustrian dan Perdagangan Provinsi Jawa Tengah. Estimasi respon waktu 1x24 jam.</p>
    </div>

    <!-- Form -->
    @php
        // Parameter asal halaman untuk tombol "Kembali" dan redirect setelah submit
        $from = request()->query('from', 'index');
        $backUrl = $from === 'dashboard' ? route('masyarakat.dashboard') : route('masyarakat.complaints.index');
        // Sertakan parameter 'from' di action POST agar Controller bisa mempertahankan asal halaman
        $storeUrl = route('masyarakat.complaints.store', ['from' => $from]);
    @endphp

    <form action="{{ $storeUrl }}" method="POST" enctype="multipart/form-data" id="complaintForm">
        @csrf
        <input type="hidden" name="from" value="{{ $from }}">
        
        <div class="bg-white rounded-lg shadow-sm p-8 mb-6">
            <div class="flex items-center mb-6">
                <svg class="w-6 h-6 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                </svg>
                <h2 class="font-montserrat text-xl font-bold text-gray-900">Informasi Pengaduan</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <!-- Judul Pengaduan -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Judul/Subjek Pengaduan <span class="text-red-500">*</span></label>
                    <input type="text" name="subject" value="{{ old('subject') }}" 
                        class="w-full px-4 py-3 border @error('subject') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                        placeholder="Masukkan judul pengaduan Anda">
                    @error('subject')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Kategori -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Kategori Pengaduan <span class="text-red-500">*</span></label>
                    <select name="category_id" 
                        class="w-full px-4 py-3 border @error('category_id') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                        <option value="">Pilih kategori</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Lokasi -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Lokasi Kejadian <span class="text-red-500">*</span></label>
                    <input type="text" name="location" value="{{ old('location') }}" 
                        class="w-full px-4 py-3 border @error('location') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                        placeholder="Masukkan lokasi kejadian">
                    @error('location')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tanggal Kejadian -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Kejadian <span class="text-red-500">*</span></label>
                    <input type="date" name="incident_date" value="{{ old('incident_date') }}" 
                        class="w-full px-4 py-3 border @error('incident_date') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                    @error('incident_date')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Waktu Kejadian -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Waktu Kejadian (Opsional)</label>
                    <input type="time" name="incident_time" value="{{ old('incident_time') }}" 
                        class="w-full px-4 py-3 border @error('incident_time') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                    @error('incident_time')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Deskripsi -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Deskripsi Pengaduan <span class="text-red-500">*</span></label>
                    <textarea name="description" rows="6"
                        class="w-full px-4 py-3 border @error('description') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition resize-none"
                        placeholder="Jelaskan secara detail pengaduan Anda...">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Lampiran -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Lampiran Bukti (Opsional)</label>
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-500 transition">
                        <input type="file" name="documents[]" id="documents" multiple accept=".pdf,.jpg,.jpeg,.png" class="hidden">
                        <label for="documents" class="cursor-pointer">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v12a4 4 0 01-4 4H12a4 4 0 01-4-4V20m32-12l-8 8m8-8v12m-8-8h12" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <p class="mt-1 text-sm text-gray-600">
                                <span class="font-medium text-blue-600 hover:text-blue-500">
                                    Klik untuk upload
                                </span>
                                atau drag and drop
                            </p>
                            <p class="mt-1 text-xs text-gray-500">PDF, PNG, JPG up to 10MB</p>
                        </label>
                    </div>
                    <div id="fileList" class="mt-3 space-y-2"></div>
                    @error('documents.*')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

            </div>
        </div>

        <!-- Info Box -->
        <div class="bg-blue-50 border-l-4 border-blue-500 p-6 rounded-lg mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-semibold text-blue-800">Informasi Penting</h3>
                    <div class="mt-2 text-sm text-blue-700">
                        <ul class="list-disc pl-5 space-y-1">
                            <li>Pastikan informasi yang Anda berikan akurat dan jelas</li>
                            <li>Lampiran bukti akan membantu proses penanganan pengaduan</li>
                            <li>Setelah mengirim pengaduan, Anda akan mendapatkan nomor tiket</li>
                            <li>Simpan nomor tiket Anda untuk melacak status permohonan</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-between">
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
                    Terima kasih telah mengirimkan pengaduan. Pengajuan Anda akan segera ditinjau. Silakan cek Email untuk melihat bukti konfirmasi formulir telah terkirim.
                </p>
                <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded">
                    <p class="text-xs text-gray-600 mb-1">Nomor Tiket Anda</p>
                    <div class="flex items-center justify-between">
                        <p class="font-bold text-blue-900 text-lg" id="ticketNumber"></p>
                        <button onclick="copyTicket()" class="text-blue-600 hover:text-blue-800" title="Copy">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            <div class="items-center px-4 py-3 space-y-2">
                @if(session('complaint_id'))
                <button onclick="window.location.href='{{ route('masyarakat.complaints.show', session('complaint_id')) }}'" 
                    class="px-4 py-2 bg-blue-600 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-blue-700 focus:outline-none">
                    Lihat Detail Pengaduan
                </button>
                @endif
                <button onclick="window.location.href='{{ route('masyarakat.complaints.index') }}'" 
                    class="px-4 py-2 bg-white text-gray-700 text-base font-medium rounded-md w-full border border-gray-300 shadow-sm hover:bg-gray-50 focus:outline-none">
                    Lihat Semua Pengaduan
                </button>
                <button onclick="window.location.href='{{ route('masyarakat.dashboard') }}'" 
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
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100">
                <svg class="h-10 w-10 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </div>
            <h3 class="text-xl leading-6 font-bold text-gray-900 mt-5">Terjadi Kesalahan!</h3>
            <div class="mt-4 px-7 py-3">
                <p class="text-sm text-gray-600" id="errorMessage">
                    Mohon periksa kembali form yang Anda isi.
                </p>
            </div>
            <div class="items-center px-4 py-3">
                <button onclick="closeErrorModal()" 
                    class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-red-700 focus:outline-none">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<!-- COPY SUCCESS TOAST -->
<div id="copyToast" class="hidden fixed bottom-5 right-5 bg-gray-800 text-white px-4 py-2 rounded-lg shadow-lg z-50">
    Nomor tiket berhasil disalin!
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // File upload preview
    const fileInput = document.getElementById('documents');
    const fileList = document.getElementById('fileList');

    if (fileInput) {
        fileInput.addEventListener('change', function() {
            fileList.innerHTML = '';
            
            Array.from(this.files).forEach((file, index) => {
                const fileItem = document.createElement('div');
                fileItem.className = 'flex items-center justify-between p-3 bg-gray-50 rounded-lg';
                
                fileItem.innerHTML = `
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-gray-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <div>
                            <p class="text-sm font-medium text-gray-900">${file.name}</p>
                            <p class="text-xs text-gray-500">${formatFileSize(file.size)}</p>
                        </div>
                    </div>
                    <button type="button" onclick="removeFile(${index})" class="text-red-600 hover:text-red-800">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                `;
                
                fileList.appendChild(fileItem);
            });
        });
    }

    // Show success modal if session has success
    @if(session('success'))
        document.getElementById('successModal').classList.remove('hidden');
        document.getElementById('ticketNumber').textContent = '{{ session('ticket_id') }}';
    @endif

    // Show error modal if there are validation errors
    @if($errors->any())
        document.getElementById('errorModal').classList.remove('hidden');
    @endif
});

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

function removeFile(index) {
    const fileInput = document.getElementById('documents');
    const dt = new DataTransfer();
    
    Array.from(fileInput.files).forEach((file, i) => {
        if (i !== index) dt.items.add(file);
    });
    
    fileInput.files = dt.files;
    fileInput.dispatchEvent(new Event('change'));
}

function closeErrorModal() {
    document.getElementById('errorModal').classList.add('hidden');
}

function copyTicket() {
    const ticketNumber = document.getElementById('ticketNumber').textContent;
    navigator.clipboard.writeText(ticketNumber).then(() => {
        const toast = document.getElementById('copyToast');
        toast.classList.remove('hidden');
        setTimeout(() => {
            toast.classList.add('hidden');
        }, 2000);
    });
}
</script>
@endsection