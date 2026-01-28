@extends('layouts.dashboard')

@section('title', 'Detail Permohonan - ' . $submission->ticket_id)

@section('content')
<div class="p-6 bg-gray-50 min-h-screen">
    <!-- Header -->
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
                    // Default: back to index
                    $backUrl = route('masyarakat.submissions.index');
                }

                // Status flags
                $statusLower = strtolower($submission->status);
                $isPending    = in_array($statusLower, ['pending','belum diproses']);
                $isProcessing = in_array($statusLower, ['in_progress','on_progress','diproses','sedang diproses']);
                $isCompleted  = in_array($statusLower, ['completed','selesai']);
                $isRejected   = in_array($statusLower, ['rejected','ditolak']);
                $isFinished   = $isCompleted || $isRejected;

                // Progress width
                if ($isPending) $progressWidth = '25%';
                elseif ($isProcessing) $progressWidth = '58%';
                elseif ($isFinished) $progressWidth = '100%';
                else $progressWidth = '0%';
            @endphp

            <a href="{{ $backUrl }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali
            </a>
            <h1 class="text-2xl font-bold text-blue-700">Tiket {{ $submission->ticket_id }}</h1>
        </div>

        <a href="{{ route('masyarakat.submissions.download', $submission) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
            </svg>
            Download PDF
        </a>
    </div>

    <!-- PROGRESS BAR -->
    <div class="bg-white rounded-lg shadow-sm p-8 mb-6">
        <h2 class="flex items-center text-lg font-bold text-gray-900 mb-6">
            <svg class="w-6 h-6 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h12M4 18h8"/>
            </svg>
            Progres Permohonan
        </h2>

        <div class="relative">
            <div class="absolute top-6 left-0 w-full h-0.5 bg-gray-200"></div>
            <div class="absolute top-6 left-0 h-0.5 bg-green-500 transition-all duration-500" style="width: {{ $progressWidth }};"></div>

            <div class="relative flex justify-between">
                <!-- STEP 1: Terkirim -->
                <div class="flex flex-col items-center w-1/4">
                    <div class="w-12 h-12 rounded-full bg-green-500 flex items-center justify-center text-white z-10">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <p class="font-semibold text-sm mt-2">Permohonan Terkirim</p>
                    <p class="text-xs text-gray-500">{{ $submission->created_at->format('d M Y, H:i') }}</p>
                </div>

                <!-- STEP 2: Menunggu Proses -->
                <div class="flex flex-col items-center w-1/4">
                    <div class="w-12 h-12 rounded-full {{ $isPending ? 'bg-yellow-400 ' : 'bg-green-500' }} flex items-center justify-center text-white z-10">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M6 2h12M6 22h12M8 2v6l4 4-4 4v6M16 2v6l-4 4 4 4v6"/>
                        </svg>
                    </div>
                    <p class="font-semibold text-sm mt-2">Menunggu Proses</p>
                    <p class="text-xs text-gray-500">
                        {{ $submission->waiting_at?->format('d M Y, H:i') ?? $submission->updated_at->format('d M Y, H:i') }}
                    </p>
                </div>

                <!-- STEP 3: Sedang Diproses -->
                <div class="flex flex-col items-center w-1/4">
                    <div class="w-12 h-12 rounded-full {{ $isProcessing ? 'bg-blue-500' : ($isFinished ? 'bg-green-500' : 'bg-gray-300') }} flex items-center justify-center text-white z-10">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6l4 2"/>
                        </svg>
                    </div>
                    <p class="font-semibold text-sm mt-2">Sedang Diproses</p>
                    <p class="text-xs text-gray-500">
                        {{ $submission->processing_at?->format('d M Y, H:i') ?? $submission->updated_at->format('d M Y, H:i') }}
                    </p>
                </div>

                <!-- STEP 4: Selesai/Ditolak -->
                <div class="flex flex-col items-center w-1/4">
                    <div class="w-12 h-12 rounded-full {{ $isCompleted ? 'bg-green-500' : ($isRejected ? 'bg-red-500' : 'bg-gray-300') }} flex items-center justify-center text-white z-10">
                        @if($isRejected)
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        @else
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        @endif
                    </div>
                    <p class="font-semibold text-sm mt-2">{{ $isCompleted ? 'Selesai' : ($isRejected ? 'Ditolak' : 'Menunggu') }}</p>
                    @if($submission->completed_at)
                        <p class="text-xs text-gray-500">{{ $submission->completed_at->format('d M Y, H:i') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- DETAIL PERMOHONAN -->
    <div class="bg-white rounded-lg shadow-sm p-8 mb-6">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Left -->
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-600">Kategori Permohonan</label>
                    <p class="text-gray-900">{{ $submission->category->name }}</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-600">Subjek Permohonan</label>
                    <p class="text-gray-900">{{ $submission->subject }}</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-600">Status</label>
                    <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full {{ $submission->status_badge }}">
                        {{ $submission->status_label }}
                    </span>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-600">Tanggal Pengajuan</label>
                    <p class="text-gray-900">{{ $submission->created_at->format('d F Y, H:i') }} WIB</p>
                </div>
            </div>

            <!-- Right -->
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-600">Deskripsi Lengkap</label>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-gray-900 whitespace-pre-line">{{ $submission->description }}</p>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-600">Dokumen Pendukung</label>
                    @if($submission->documents && $submission->documents->count() > 0)
                        <div class="space-y-2">
                            @foreach($submission->documents as $doc)
                                @php
                                    $fileName = $doc->original_name ?? basename($doc->file_path);
                                    $ext = pathinfo($fileName, PATHINFO_EXTENSION);
                                    $supabaseUrl = rtrim(env('SUPABASE_URL'), '/');
                                    $bucket = env('SUPABASE_SUBMISSIONS_BUCKET', 'submissions');
                                    $path = ltrim($doc->file_path, '/');
                                    if (Str::startsWith($path, 'submissions/')) $path = Str::after($path, 'submissions/');
                                    $documentUrl = "{$supabaseUrl}/storage/v1/object/public/{$bucket}/{$path}";
                                @endphp
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition group">
                                    <div class="flex items-center space-x-3 min-w-0 flex-1">
                                        @if(in_array(strtolower($ext), ['jpg','jpeg','png','gif','webp']))
                                            <svg class="w-8 h-8 text-blue-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/>
                                            </svg>
                                        @else
                                            <svg class="w-8 h-8 text-gray-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
                                            </svg>
                                        @endif
                                        <div class="overflow-hidden">
                                            <p class="text-sm font-medium text-gray-900 truncate max-w-[150px] md:max-w-[200px]">{{ $fileName }}</p>
                                            <p class="text-xs text-gray-500">{{ number_format($doc->file_size / 1024, 2) }} KB</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <span class="text-xs text-gray-500 group-hover:text-blue-600">.{{ strtoupper($ext) }}</span>
                                        <a href="{{ route('masyarakat.submissions.document.download', $doc) }}" class="text-blue-600 hover:text-blue-800" title="Download">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                            </svg>
                                        </a>
                                        <a href="{{ $documentUrl }}" target="_blank" class="text-blue-600 hover:text-blue-800" title="Lihat">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
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

                <!-- Admin Response -->
                @if($submission->admin_response || $submission->admin_notes)
                    <div>
                        <label class="block text-sm font-semibold text-gray-600 mb-2">Respon dari Admin</label>
                        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-lg">
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-blue-600 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                                </svg>
                                <div class="flex-1">
                                    <p class="text-gray-900 whitespace-pre-line">{{ $submission->admin_response ?? $submission->admin_notes }}</p>
                                    @if($submission->handler)
                                        <p class="text-xs text-blue-700 mt-3 font-semibold">Ditangani oleh: {{ $submission->handler->name }}</p>
                                    @endif
                                    @if($submission->completed_at)
                                        <p class="text-xs text-gray-500 mt-1">{{ $submission->completed_at->format('d M Y, H:i') }} WIB</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection