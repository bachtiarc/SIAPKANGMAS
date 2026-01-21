@extends('layouts.dashboard')

@section('title', 'Detail Konsultasi - ' . $consultation->ticket_number)

@section('content')
<div class="p-6 bg-gray-50 min-h-screen">
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center space-x-4">
            <a href="{{ route('user.consultations.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali
            </a>
            <h1 class="text-2xl font-bold text-blue-700">Tiket {{ $consultation->ticket_number }}</h1>
        </div>
        
        <div class="flex items-center space-x-3">
            <a href="{{ route('user.consultations.download', $consultation) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
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
            Progres Konsultasi
        </h2>

        <div class="relative">
            <div class="absolute top-6 left-0 w-full h-0.5 bg-gray-200"></div>
            <div class="absolute top-6 left-0 h-0.5 bg-green-500 transition-all duration-500" 
                style="width: {{ $consultation->status == 'pending' ? '15%' : ($consultation->status == 'diproses' ? '50%' : '100%') }};"></div>

            <div class="relative flex justify-between">
                <div class="flex flex-col items-center" style="width: 33%;">
                    <div class="w-12 h-12 rounded-full flex items-center justify-center mb-3 bg-green-500 text-white shadow-lg z-10">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <div class="text-center">
                        <p class="font-semibold text-gray-900 text-sm">Konsultasi Terkirim</p>
                        <p class="text-xs text-gray-500 mt-1">{{ $consultation->created_at->format('d M Y, H:i') }}</p>
                    </div>
                </div>

                <div class="flex flex-col items-center" style="width: 33%;">
                    @php
                        $isProcessing = in_array($consultation->status, ['pending', 'diproses']);
                        $isFinished = in_array($consultation->status, ['selesai', 'ditolak']);
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
                        @if($consultation->status == 'pending')
                            <p class="text-xs text-yellow-600 mt-1 font-semibold italic">Tahap: Menunggu Respon...</p>
                        @elseif($consultation->status == 'diproses')
                            <p class="text-xs text-blue-600 mt-1 font-semibold italic">Tahap: Koordinasi Tim...</p>
                        @elseif($isFinished)
                            <p class="text-xs text-gray-400 mt-1">Selesai diproses</p>
                        @endif
                    </div>
                </div>

                <div class="flex flex-col items-center" style="width: 33%;">
                    @php
                        $finalBg = 'bg-gray-300';
                        if($consultation->status == 'selesai') $finalBg = 'bg-green-500';
                        if($consultation->status == 'ditolak') $finalBg = 'bg-red-500';
                    @endphp
                    <div class="w-12 h-12 rounded-full flex items-center justify-center mb-3 {{ $finalBg }} text-white shadow-lg z-10">
                        @if($consultation->status == 'selesai')
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        @elseif($consultation->status == 'ditolak')
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        @else
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                            </svg>
                        @endif
                    </div>
                    <div class="text-center">
                        <p class="font-semibold text-gray-900 text-sm">
                            {{ $consultation->status == 'ditolak' ? 'Ditolak' : 'Selesai' }}
                        </p>
                        @if($consultation->completed_at)
                            <p class="text-xs text-gray-500 mt-1">{{ $consultation->completed_at->format('d M Y, H:i') }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-8">
        <h2 class="flex items-center text-lg font-bold text-gray-900 mb-6">
            <svg class="w-6 h-6 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
            </svg>
            Rincian Konsultasi
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div>
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-600 mb-2">Subjek Konsultasi</label>
                    <p class="text-gray-900">{{ $consultation->subject }}</p>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-600 mb-2">Deskripsi Lengkap</label>
                    <p class="text-gray-900 whitespace-pre-line">{{ $consultation->description }}</p>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-600 mb-2">Kategori</label>
                    <p class="text-gray-900">{{ $consultation->category->name ?? '-' }}</p>
                </div>
            </div>

            <div>
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-600 mb-3">Dokumen Pendukung</label>
                    
                    {{-- PERBAIKAN: Membaca dari relasi documents (multiple) --}}
                    @if($consultation->documents && $consultation->documents->count() > 0)
                        <div class="space-y-2">
                            @foreach($consultation->documents as $doc)
                                @php
                                    $ext = strtolower(pathinfo($doc->file_path, PATHINFO_EXTENSION));
                                    $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                    $isPdf = $ext === 'pdf';
                                    $fileName = $doc->original_name;
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
                                        
                                        <div class="overflow-hidden">
                                            <p class="text-sm font-medium text-gray-900 truncate max-w-[150px] md:max-w-[200px]">{{ $fileName }}</p>
                                            <p class="text-xs text-gray-500">{{ number_format($doc->file_size / 1024, 2) }} KB</p>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-center space-x-2">
                                        <span class="text-xs text-gray-500 group-hover:text-blue-600">.{{ strtoupper($ext) }}</span>
                                        
                                        <a href="{{ asset('storage/' . $doc->file_path) }}" download="{{ $fileName }}" class="text-blue-600 hover:text-blue-800" title="Download">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                            </svg>
                                        </a>
                                        
                                        <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank" class="text-blue-600 hover:text-blue-800" title="Lihat">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-500 italic">Tidak ada dokumen pendukung</p>
                    @endif
                </div>

                @if($consultation->admin_response)
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-600 mb-2">Respon dari Admin</label>
                    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-lg">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-blue-600 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                            </svg>
                            <div class="flex-1">
                                <p class="text-gray-900 whitespace-pre-line">{{ $consultation->admin_response }}</p>
                                @if($consultation->handler)
                                    <p class="text-xs text-blue-700 mt-3 font-semibold">Ditangani oleh: {{ $consultation->handler->name }}</p>
                                @endif
                                @if($consultation->completed_at)
                                    <p class="text-xs text-gray-500 mt-1">{{ $consultation->completed_at->format('d M Y, H:i') }} WIB</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                @if($consultation->admin_notes && $consultation->admin_notes != $consultation->admin_response)
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-600 mb-2">Catatan Admin</label>
                    <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded-lg">
                        <p class="text-gray-900">{{ $consultation->admin_notes }}</p>
                    </div>
                </div>
                @endif

                @if($consultation->handler && !$consultation->admin_response)
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-600 mb-2">Ditangani Oleh</label>
                    <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
                        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-900">{{ $consultation->handler->name }}</p>
                            <p class="text-xs text-gray-500">{{ $consultation->handler->email ?? 'Admin' }}</p>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    @if(isset($consultation->statusHistories) && $consultation->statusHistories->count() > 0)
    <div class="bg-white rounded-lg shadow-sm p-8 mt-6">
        <h2 class="flex items-center text-lg font-bold text-gray-900 mb-6">
            <svg class="w-6 h-6 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            Riwayat Aktivitas
        </h2>

        <div class="space-y-4">
            @foreach($consultation->statusHistories as $history)
            <div class="flex items-start space-x-4">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="flex-1 bg-gray-50 rounded-lg p-4">
                    <div class="flex items-center justify-between mb-1">
                        <p class="text-sm font-bold text-gray-900">
                            {{-- Menggunakan kolom new_status sesuai tabel status_histories --}}
                            {{ strtoupper($history->new_status ?? $history->status) }}
                        </p>
                        <p class="text-xs text-gray-500">{{ $history->created_at->format('d/m/Y H:i') }} WIB</p>
                    </div>
                    @if($history->notes || $history->description)
                        <p class="text-sm text-gray-700">{{ $history->notes ?? $history->description }}</p>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection