@extends('layouts.dashboard')

@section('title', 'Detail Tiket - ' . $submission->ticket_id)

@section('content')
<div class="p-6 bg-gray-50 min-h-screen">
    <nav class="mb-6 text-sm">
        <ol class="flex items-center space-x-2">
            <li><a href="{{ route('user.dashboard') }}" class="text-blue-600 hover:text-blue-800">Beranda</a></li>
            <li class="text-gray-400">></li>
            <li><a href="{{ route('user.submissions.index') }}" class="text-blue-600 hover:text-blue-800">Riwayat Pengajuan</a></li>
            <li class="text-gray-400">></li>
            <li class="text-gray-600">Detail Tiket {{ $submission->ticket_id }}</li>
        </ol>
    </nav>

    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center space-x-4">
            <a href="{{ route('user.submissions.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali
            </a>
            <h1 class="text-2xl font-bold text-blue-700">Tiket {{ $submission->ticket_id }}</h1>
        </div>
        
        <div class="flex items-center space-x-3">
            <a href="{{ route('user.submissions.download', $submission) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                </svg>
                Download PDF
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-8 mb-6">
        <h2 class="flex items-center text-lg font-bold text-gray-900 mb-6">
            <svg class="w-6 h-6 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
            </svg>
            Progres Tiket
        </h2>

        <div class="relative">
            <div class="absolute top-6 left-0 w-full h-0.5 bg-gray-200"></div>
            <div class="absolute top-6 left-0 h-0.5 bg-green-500 transition-all duration-500" 
                style="width: {{ $submission->status == 'pending' ? '15%' : ($submission->status == 'in_progress' ? '50%' : '100%') }};"></div>

            <div class="relative flex justify-between">
                <div class="flex flex-col items-center" style="width: 33%;">
                    <div class="w-12 h-12 rounded-full flex items-center justify-center mb-3 bg-green-500 text-white shadow-lg z-10">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <div class="text-center">
                        <p class="font-semibold text-gray-900 text-sm">Pengajuan Terkirim</p>
                        <p class="text-xs text-gray-500 mt-1">{{ $submission->created_at->format('d M Y, H:i') }}</p>
                    </div>
                </div>

                <div class="flex flex-col items-center" style="width: 33%;">
                    @php
                        $isProcessing = in_array($submission->status, ['pending', 'in_progress']);
                        $isFinished = in_array($submission->status, ['completed', 'rejected']);
                    @endphp
                    <div class="w-12 h-12 rounded-full flex items-center justify-center mb-3 {{ $isFinished ? 'bg-green-500' : ($isProcessing ? 'bg-yellow-400' : 'bg-gray-300') }} text-white shadow-lg z-10">
                        @if($isFinished)
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        @else
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                        @endif
                    </div>
                    <div class="text-center">
                        <p class="font-semibold text-gray-900 text-sm">Sedang Diproses</p>
                        @if($submission->status == 'pending')
                            <p class="text-xs text-yellow-600 mt-1 font-semibold italic">Tahap: Verifikasi Berkas...</p>
                        @elseif($submission->status == 'in_progress')
                            <p class="text-xs text-blue-600 mt-1 font-semibold italic">Tahap: Koordinasi Bidang...</p>
                        @elseif($isFinished)
                            <p class="text-xs text-gray-400 mt-1">Selesai diproses</p>
                        @endif
                    </div>
                </div>

                <div class="flex flex-col items-center" style="width: 33%;">
                    @php
                        $finalBg = 'bg-gray-300';
                        if($submission->status == 'completed') $finalBg = 'bg-green-500';
                        if($submission->status == 'rejected') $finalBg = 'bg-red-500';
                    @endphp
                    <div class="w-12 h-12 rounded-full flex items-center justify-center mb-3 {{ $finalBg }} text-white shadow-lg z-10">
                        @if($submission->status == 'completed')
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        @elseif($submission->status == 'rejected')
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        @else
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        @endif
                    </div>
                    <div class="text-center">
                        <p class="font-semibold text-gray-900 text-sm">
                            {{ $submission->status == 'rejected' ? 'Ditolak' : 'Selesai' }}
                        </p>
                        @if($submission->completed_at)
                            <p class="text-xs text-gray-500 mt-1">{{ $submission->completed_at->format('d M Y, H:i') }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-8">
        <h2 class="flex items-center text-lg font-bold text-gray-900 mb-6">
            <svg class="w-6 h-6 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            Rincian Formulir Pengajuan
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div>
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-600 mb-2">Judul Permohonan</label>
                    <p class="text-gray-900">{{ $submission->title }}</p>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-600 mb-2">Deskripsi Lengkap</label>
                    <p class="text-gray-900 whitespace-pre-line">{{ $submission->description }}</p>
                </div>
            </div>

            <div>
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-600 mb-2">Kategori Informasi</label>
                    <p class="text-gray-900">{{ $submission->category->name }}</p>
                </div>

                @if($submission->documents && count($submission->documents) > 0)
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-600 mb-3">Dokumen Pendukung ({{ count($submission->documents) }})</label>
                    <div class="space-y-2">
                        @foreach($submission->documents as $index => $document)
                        @php
                            $ext = strtolower(pathinfo($document->file_path, PATHINFO_EXTENSION));
                            $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif']);
                            $isPdf = $ext === 'pdf';
                        @endphp
                        
                        <div class="flex items-center justify-between px-4 py-3 bg-gray-50 hover:bg-gray-100 border border-gray-200 rounded-lg transition group">
                            <div class="flex items-center space-x-3">
                                @if($isPdf)
                                    <svg class="w-8 h-8 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"></path>
                                    </svg>
                                @elseif($isImage)
                                    <svg class="w-8 h-8 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"></path>
                                    </svg>
                                @else
                                    <svg class="w-8 h-8 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"></path>
                                    </svg>
                                @endif
                                
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Dokumen {{ $index + 1 }}</p>
                                    <p class="text-xs text-gray-500">{{ $document->original_name }}</p>
                                    <p class="text-xs text-gray-400">{{ number_format($document->file_size / 1024, 2) }} KB</p>
                                </div>
                            </div>
                            
                            <div class="flex items-center space-x-2">
                                <span class="text-xs text-gray-500 group-hover:text-blue-600">.{{ strtoupper($ext) }}</span>
                                
                                @if($isImage)
                                    <a href="{{ asset('storage/' . $document->file_path) }}" download="{{ $document->original_name }}" class="text-blue-600 hover:text-blue-800">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                        </svg>
                                    </a>
                                    <a href="{{ asset('storage/' . $document->file_path) }}" target="_blank" class="text-blue-600 hover:text-blue-800">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                        </svg>
                                    </a>
                                @elseif($isPdf)
                                    <a href="{{ asset('storage/' . $document->file_path) }}" download="{{ $document->original_name }}" class="text-blue-600 hover:text-blue-800">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                        </svg>
                                    </a>
                                    <a href="{{ route('user.submissions.view-document', $document) }}" target="_blank" class="text-blue-600 hover:text-blue-800">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                        </svg>
                                    </a>
                                @else
                                    <a href="{{ asset('storage/' . $document->file_path) }}" download="{{ $document->original_name }}" class="text-blue-600 hover:text-blue-800">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                        </svg>
                                    </a>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @else
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-600 mb-2">Dokumen Pendukung</label>
                    <p class="text-sm text-gray-500 italic">Tidak ada dokumen pendukung</p>
                </div>
                @endif

                @if($submission->admin_notes)
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-600 mb-2">Catatan Admin</label>
                    <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded">
                        <p class="text-gray-900">{{ $submission->admin_notes }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection