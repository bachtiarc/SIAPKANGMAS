@extends('layouts.dashboard')

@section('title', 'Formulir Permohonan Informasi')

@section('content')
<div class="p-6">
    <!-- Breadcrumb -->
    <nav class="mb-6 text-sm">
        <ol class="flex items-center space-x-2">
            <li><a href="{{ route('masyarakat.dashboard') }}" class="text-blue-600 hover:text-blue-800">Beranda</a></li>
            <li class="text-gray-400">/</li>
            <li><a href="{{ route('masyarakat.submissions.index') }}" class="text-blue-600 hover:text-blue-800">Permohonan Informasi</a></li>
            <li class="text-gray-400">/</li>
            <li class="text-gray-600">Formulir Permohonan Informasi</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <h1 class="font-montserrat text-3xl font-bold text-gray-900 mb-2">Formulir Permohonan Informasi</h1>
        <p class="text-gray-600">Silahkan lengkapi formulir di bawah ini untuk mengajukan permohonan informasi publik terkait perindustrian dan perdagangan. Estimasi respon waktu ada 1 kali 24 jam.</p>
    </div>

    <!-- Form -->
    <form action="{{ route('masyarakat.submissions.store') }}" method="POST" enctype="multipart/form-data" id="submissionForm">
        @csrf
        
        <div class="bg-white rounded-lg shadow-sm p-8 mb-6">
            <div class="flex items-center mb-6">
                <svg class="w-6 h-6 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <h2 class="font-montserrat text-xl font-bold text-gray-900">Detail Permohonan</h2>
            </div>

            <!-- Error Messages -->
            @if ($errors->any())
                <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                    <ul class="list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Judul Permohonan -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-900 mb-2">
                        Judul Permohonan <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="title" value="{{ old('title') }}" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                        placeholder="Contoh: Data Ekspor Rempah Semarang Tahun xxxx">
                    @error('title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Kategori Informasi -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-900 mb-2">
                        Kategori Informasi <span class="text-red-500">*</span>
                    </label>
                    <select name="category_id" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Pilih Kategori Informasi</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Deskripsi Lengkap -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-900 mb-2">
                        Deskripsi Lengkap <span class="text-red-500">*</span>
                    </label>
                    <textarea name="description" rows="6" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                        placeholder="Jelaskan secara spesifik informasi yang Anda butuhkan. Sertakan detail waktu, lokasi, atau konteks relevan lainnya.">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Dokumen Pendukung -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-900 mb-2">
                        Dokumen Pendukung
                    </label>
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-blue-400 transition cursor-pointer" 
                        onclick="document.getElementById('document').click()">
                        <div class="space-y-1 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="text-sm text-gray-600">
                                <span class="font-semibold text-blue-600 hover:text-blue-700">Klik untuk unggah</span> atau seret file ke sini
                            </div>
                            <p class="text-xs text-gray-500">Format: PDF, JPG, PNG (Maksimal 5MB)</p>
                            <p id="fileName" class="text-sm text-green-600 font-semibold mt-2 hidden"></p>
                        </div>
                        <input id="document" name="document" type="file" class="hidden" accept=".pdf,.jpg,.jpeg,.png" 
                            onchange="displayFileName(this)">
                    </div>
                    @error('document')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-between">
            <a href="{{ route('masyarakat.submissions.index') }}" 
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

<script>
function displayFileName(input) {
    const fileNameDisplay = document.getElementById('fileName');
    if (input.files && input.files[0]) {
        const file = input.files[0];
        const fileSize = (file.size / 1024 / 1024).toFixed(2); // Convert to MB
        
        if (fileSize > 5) {
            alert('Ukuran file maksimal 5MB!');
            input.value = '';
            fileNameDisplay.classList.add('hidden');
            return;
        }
        
        fileNameDisplay.textContent = `✓ ${file.name} (${fileSize} MB)`;
        fileNameDisplay.classList.remove('hidden');
    }
}
</script>
@endsection