@extends('layouts.dashboard')

@section('content')
<div class="p-6 bg-gray-50 min-h-screen">
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center space-x-4">
            @php
                $from = request()->get('from', 'index');
                
                if ($from === 'dashboard') {
                    $backUrl = route('masyarakat.dashboard');
                } elseif ($from === 'history') {
                    $backUrl = route('masyarakat.history.index');
                } elseif ($from === 'search') {
                    $searchQuery = request()->get('q', '');
                    $backUrl = route('search.result', ['q' => $searchQuery]);
                } else {
                    $backUrl = route('masyarakat.complaints.index'); 
                }
            @endphp
            
            <a href="{{ $backUrl }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali
            </a>
            <h1 class="text-2xl font-bold text-blue-700">Tiket {{ $complaint->ticket_number }}</h1>
        </div>

        <a href="{{ route('masyarakat.complaints.download', $complaint) }}"
           class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
            </svg>
            Download PDF
        </a>
    </div>

    <!-- PROGRES PENGADUAN -->
    <div class="bg-white rounded-lg shadow-sm p-8 mb-6">
        <h2 class="flex items-center text-lg font-bold text-gray-900 mb-6">
            <svg class="w-6 h-6 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 6h16
                    M4 12h12
                    M4 18h8"/>
            </svg>
            Progres Pengaduan
        </h2>

        @php
            $statusLower = strtolower($complaint->status);

            if (in_array($statusLower, ['pending', 'belum diproses'])) {
                $progressWidth = '25%';
            } elseif (in_array($statusLower, ['in_progress', 'on_progress', 'diproses', 'sedang diproses'])) {
                $progressWidth = '58%';
            } elseif (in_array($statusLower, ['completed', 'selesai', 'rejected', 'ditolak'])) {
                $progressWidth = '100%';
            } else {
                $progressWidth = '0%';
            }

            $isPending = in_array($statusLower, ['pending', 'belum diproses']);
            $isProcessing = in_array($statusLower, ['in_progress', 'on_progress', 'diproses', 'sedang diproses']);
            $isCompleted = in_array($statusLower, ['completed', 'selesai']);
            $isRejected = in_array($statusLower, ['rejected', 'ditolak']);
            $isFinished = $isCompleted || $isRejected;
        @endphp

        <div class="relative">
            <div class="absolute top-6 left-0 w-full h-0.5 bg-gray-200"></div>
            <div class="absolute top-6 left-0 h-0.5 bg-green-500 transition-all duration-500"
                 style="width: {{ $progressWidth }};"></div>

            <div class="relative flex justify-between">
                <!-- STEP 1 -->
                <div class="flex flex-col items-center w-1/4">
                    <div class="w-12 h-12 rounded-full bg-green-500 flex items-center justify-center text-white z-10">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <p class="font-semibold text-sm">Pengaduan Terkirim</p>
                    <p class="text-xs text-gray-500">
                        {{ $complaint->created_at->format('d M Y, H:i') }}
                    </p>
                </div>

                <!-- STEP 2 : MENUNGGU PROSES  -->
                <div class="flex flex-col items-center w-1/4">
                    <div class="w-12 h-12 rounded-full {{ !$isPending ? 'bg-green-500' : 'bg-yellow-400' }}
                        flex items-center justify-center text-white z-10">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 2h12M6 22h12M8 2v6l4 4-4 4v6M16 2v6l-4 4 4 4v6"/>
                        </svg>
                    </div>
                    <p class="font-semibold text-sm">Menunggu Diproses</p>
                    <p class="text-xs text-gray-500">
                        {{ $complaint->updated_at->format('d M Y, H:i') }}
                    </p>
                </div>

                <!-- STEP 3 -->
                <div class="flex flex-col items-center w-1/4">
                    <div class="w-12 h-12 rounded-full
                        {{ $isProcessing ? 'bg-blue-500' : ($isFinished ? 'bg-green-500' : 'bg-gray-300') }}
                        flex items-center justify-center text-white z-10">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v6l4 2"/>
                        </svg>
                    </div>
                    <p class="font-semibold text-sm">Sedang Diproses</p>
                    <p class="text-xs text-gray-500">
                        {{ $complaint->updated_at->format('d M Y, H:i') }}
                    </p>
                </div>

                <!-- STEP 4 -->
                <div class="flex flex-col items-center w-1/4">
                    <div class="w-12 h-12 rounded-full
                        {{ $isCompleted ? 'bg-green-500' : ($isRejected ? 'bg-red-500' : 'bg-gray-300') }}
                        flex items-center justify-center text-white z-10">
                        @if($isRejected)
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        @else
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7"/>
                            </svg>
                        @endif
                    </div>
                    <p class="font-semibold text-sm">
                        {{ $isCompleted ? 'Selesai' : ($isRejected ? 'Ditolak' : 'Menunggu') }}
                    </p>
                    @if($complaint->completed_at)
                        <p class="text-xs text-gray-500">
                            {{ $complaint->completed_at->format('d M Y, H:i') }}
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Detail Pengaduan -->
    <div class="bg-white rounded-lg shadow-sm p-8 mb-6">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Left Column -->
            <div class="space-y-6">
                <div>
                    <h2 class="flex items-center text-lg font-bold text-gray-900 mb-6">
                        <svg class="w-6 h-6 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        Informasi Pengaduan
                    </h2>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-600 mb-2">Subjek Pengaduan</label>
                    <p class="text-gray-900">{{ $complaint->subject }}</p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-600 mb-2">Status Pengaduan</label>
                    <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full {{ $complaint->status_badge }}">
                        {{ $complaint->status_label }}
                    </span>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-600 mb-2">Tanggal Pengajuan</label>
                    <p class="text-gray-900">{{ $complaint->created_at->format('d F Y, H:i') }} WIB</p>
                </div>
            </div>

            <!-- Right Column -->
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-600 mb-2">Deskripsi Lengkap</label>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-gray-900 whitespace-pre-line">{{ $complaint->description }}</p>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-600 mb-2">Dokumen Pendukung</label>
                    @if($complaint->documents && $complaint->documents->count() > 0)
                        <div class="space-y-2">
                            @foreach($complaint->documents as $doc)
                                @php
                                    $fileName = $doc->original_name ?? basename($doc->file_path);
                                    $ext = pathinfo($fileName, PATHINFO_EXTENSION);
                                    
                                    // Build Supabase URL
                                    $supabaseUrl = rtrim(env('SUPABASE_URL'), '/');
                                    $bucket = env('SUPABASE_COMPLAINTS_BUCKET', 'complaints');
                                    $path = ltrim($doc->file_path, '/');
                                    
                                    // Remove bucket prefix if present
                                    if (Str::startsWith($path, 'complaints/')) {
                                        $path = Str::after($path, 'complaints/');
                                    }
                                    
                                    $documentUrl = "{$supabaseUrl}/storage/v1/object/public/{$bucket}/{$path}";
                                @endphp
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition group">
                                    <div class="flex items-center space-x-3 min-w-0 flex-1">
                                        @if(in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                                            <svg class="w-8 h-8 text-blue-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"></path>
                                            </svg>
                                        @else
                                            <svg class="w-8 h-8 text-gray-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
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
                                        
                                        <!-- Download Button -->
                                        <a href="{{ route('masyarakat.complaints.document.download', $doc) }}" class="text-blue-600 hover:text-blue-800" title="Download">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                            </svg>
                                        </a>
                                        
                                        <!-- View Button -->
                                        <a href="{{ route('masyarakat.complaints.documents.view', $doc) }}" target="_blank" class="text-blue-600 hover:text-blue-800" title="Lihat">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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

                @if($complaint->admin_response || $complaint->admin_notes)
                <div>
                    <label class="block text-sm font-semibold text-gray-600 mb-2">Respon dari Admin</label>
                    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-lg">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-blue-600 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                            </svg>
                            <div class="flex-1">
                                <p class="text-gray-900 whitespace-pre-line">{{ $complaint->admin_response ?? $complaint->admin_notes }}</p>
                                @if($complaint->handler)
                                    <p class="text-xs text-blue-700 mt-3 font-semibold">Ditangani oleh: {{ $complaint->handler->name }}</p>
                                @endif
                                @if($complaint->completed_at)
                                    <p class="text-xs text-gray-500 mt-1">{{ $complaint->completed_at->format('d M Y, H:i') }} WIB</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                @if($complaint->handler && !$complaint->admin_response && !$complaint->admin_notes)
                <div>
                    <label class="block text-sm font-semibold text-gray-600 mb-2">Ditangani Oleh</label>
                    <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
                        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-900">{{ $complaint->handler->name }}</p>
                            <p class="text-xs text-gray-500">{{ $complaint->handler->email ?? 'Admin' }}</p>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
@endsection