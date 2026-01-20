@extends('layouts.dashboard')

@section('title', 'Detail Permohonan - ' . $submission->ticket_id)

@section('content')
<div class="p-6">
    <!-- Success Message -->
    @if(session('success'))
    <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg flex items-center">
        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
        </svg>
        {{ session('success') }}
    </div>
    @endif

    <!-- Breadcrumb -->
    <nav class="mb-6 text-sm">
        <ol class="flex items-center space-x-2">
            <li><a href="{{ route('masyarakat.dashboard') }}" class="text-blue-600 hover:text-blue-800">Beranda</a></li>
            <li class="text-gray-400">/</li>
            <li><a href="{{ route('masyarakat.submissions.index') }}" class="text-blue-600 hover:text-blue-800">Permohonan Informasi</a></li>
            <li class="text-gray-400">/</li>
            <li class="text-gray-600">{{ $submission->ticket_id }}</li>
        </ol>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content (Left) -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Ticket Info Card -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h1 class="font-montserrat text-2xl font-bold text-gray-900">{{ $submission->title }}</h1>
                        <p class="text-sm text-gray-500 mt-1">{{ $submission->category->name }}</p>
                    </div>
                    <span class="px-4 py-2 text-sm font-semibold rounded-full {{ $submission->status_badge }}">
                        {{ $submission->status_label }}
                    </span>
                </div>

                <!-- Ticket Number -->
                <div class="bg-blue-50 border-l-4 border-blue-600 p-4 mb-4">
                    <div class="text-sm text-gray-600">Nomor Tiket</div>
                    <div class="font-bold text-blue-900 text-lg">{{ $submission->ticket_id }}</div>
                    <div class="text-xs text-gray-500 mt-1">{{ $submission->full_ticket_number }}</div>
                </div>

                <!-- Description -->
                <div class="mb-6">
                    <h3 class="font-semibold text-gray-900 mb-2">Deskripsi Permohonan</h3>
                    <p class="text-gray-700 whitespace-pre-line">{{ $submission->description }}</p>
                </div>

                <!-- Document -->
                @if($submission->document_path)
                <div class="mb-6">
                    <h3 class="font-semibold text-gray-900 mb-2">Dokumen Pendukung</h3>
                    <a href="{{ Storage::url($submission->document_path) }}" target="_blank" 
                        class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                        Lihat Dokumen
                    </a>
                </div>
                @endif

                <!-- Admin Notes (if any) -->
                @if($submission->admin_notes)
                <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4">
                    <h3 class="font-semibold text-gray-900 mb-2">Catatan Admin</h3>
                    <p class="text-gray-700">{{ $submission->admin_notes }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Sidebar (Right) -->
        <div class="space-y-6">
            <!-- Status Timeline -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="font-semibold text-gray-900 mb-4">Informasi Pengajuan</h3>
                
                <div class="space-y-3">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-gray-400 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <div>
                            <div class="text-sm text-gray-500">Tanggal Pengajuan</div>
                            <div class="font-medium text-gray-900">{{ $submission->created_at->format('d F Y') }}</div>
                            <div class="text-xs text-gray-500">{{ $submission->created_at->format('H:i') }} WIB</div>
                        </div>
                    </div>

                    @if($submission->handled_by)
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-gray-400 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <div>
                            <div class="text-sm text-gray-500">Ditangani Oleh</div>
                            <div class="font-medium text-gray-900">{{ $submission->handler->name }}</div>
                        </div>
                    </div>
                    @endif

                    @if($submission->completed_at)
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-green-500 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <div class="text-sm text-gray-500">Diselesaikan Pada</div>
                            <div class="font-medium text-gray-900">{{ $submission->completed_at->format('d F Y') }}</div>
                            <div class="text-xs text-gray-500">{{ $submission->completed_at->format('H:i') }} WIB</div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Actions -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="font-semibold text-gray-900 mb-4">Aksi</h3>
                <div class="space-y-2">
                    <a href="{{ route('masyarakat.submissions.index') }}" 
                        class="block w-full px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-center rounded-lg transition">
                        Kembali ke Daftar
                    </a>
                    <button onclick="window.print()" 
                        class="block w-full px-4 py-2 bg-blue-100 hover:bg-blue-200 text-blue-700 text-center rounded-lg transition">
                        Cetak
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection