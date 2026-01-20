<!-- resources/views/user/consultations/show.blade.php -->

@extends('layouts.dashboard')

@section('title', 'Detail Konsultasi - ' . $consultation->ticket_number)

@section('content')
<div class="p-6">
    <!-- Breadcrumb -->
    <nav class="mb-6 text-sm">
        <ol class="flex items-center space-x-2">
            <li><a href="{{ route('user.dashboard') }}" class="text-blue-600 hover:text-blue-800">Beranda</a></li>
            <li class="text-gray-400">/</li>
            <li><a href="{{ route('user.consultations.index') }}" class="text-blue-600 hover:text-blue-800">Konsultasi</a></li>
            <li class="text-gray-400">/</li>
            <li class="text-gray-600">{{ $consultation->ticket_number }}</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-blue-700">Tiket {{ $consultation->ticket_number }}</h1>
        <span class="px-4 py-1 rounded-full text-sm font-bold {{ $consultation->status_badge }}">
            {{ strtoupper($consultation->status_label) }}
        </span>
    </div>

    <!-- Progress Tracker -->
    <div class="bg-white rounded-lg shadow-sm p-8 mb-6">
        <div class="relative flex justify-between">
            <div class="absolute top-5 left-0 w-full h-1 bg-gray-200"></div>
            <div class="absolute top-5 left-0 h-1 bg-blue-500 transition-all" 
                 style="width: {{ $consultation->status == 'pending' ? '0%' : ($consultation->status == 'diproses' ? '50%' : '100%') }}">
            </div>
                        
            @foreach(['pending' => 'Diajukan', 'diproses' => 'Diproses', 'selesai' => 'Selesai'] as $key => $label)
                <div class="relative z-10 flex flex-col items-center">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center 
                        {{ $consultation->status == $key || ($key == 'pending') ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-400' }}">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <p class="text-xs font-bold mt-2">{{ $label }}</p>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Informasi Konsultasi -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="font-bold text-gray-900 border-b pb-2 mb-4">Informasi Konsultasi</h3>
                <div class="space-y-4">
                    <div>
                        <label class="text-xs font-bold text-gray-400 uppercase">Subjek</label>
                        <p class="text-gray-900 font-medium">{{ $consultation->subject }}</p>
                    </div>
                    <div>
                        <label class="text-xs font-bold text-gray-400 uppercase">Kategori</label>
                        <p class="text-gray-900">{{ $consultation->category->name ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="text-xs font-bold text-gray-400 uppercase">Deskripsi</label>
                        <p class="text-gray-800 italic">"{{ $consultation->description }}"</p>
                    </div>
                    
                    @if($consultation->attachment)
                    <div>
                        <label class="text-xs font-bold text-gray-400 uppercase">Lampiran</label>
                        <a href="{{ asset('storage/' . $consultation->attachment) }}" 
                           target="_blank" 
                           class="flex items-center text-blue-600 hover:text-blue-800 mt-2">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                            </svg>
                            <span class="text-sm">Lihat Dokumen</span>
                        </a>
                    </div>
                    @endif

                    @if(isset($consultation->documents) && $consultation->documents->count() > 0)
                    <div>
                        <label class="text-xs font-bold text-gray-400 uppercase">Dokumen Pendukung</label>
                        <div class="mt-2 space-y-2">
                            @foreach($consultation->documents as $document)
                            <a href="{{ route('user.consultations.documents.view', $document->id) }}" 
                               target="_blank" 
                               class="flex items-center text-blue-600 hover:text-blue-800">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                </svg>
                                <span class="text-sm">{{ $document->original_name }}</span>
                                <span class="text-xs text-gray-500 ml-2">({{ number_format($document->file_size / 1024 / 1024, 2) }} MB)</span>
                            </a>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Jawaban Admin -->
            @if($consultation->admin_notes)
            <div class="bg-blue-50 border-l-4 border-blue-500 p-6 rounded-lg">
                <h3 class="font-bold text-blue-800 mb-2 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                    </svg>
                    Respon dari Admin:
                </h3>
                <p class="text-blue-900">{{ $consultation->admin_notes }}</p>
                @if($consultation->handler)
                    <p class="text-xs text-blue-700 mt-3">Ditangani oleh: {{ $consultation->handler->name }}</p>
                @endif
            </div>
            @endif
        </div>

        <!-- Sidebar - Activity Log -->
        <div class="bg-white rounded-lg shadow-sm p-6 h-fit">
            <h3 class="font-bold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Log Aktivitas
            </h3>
            <div class="space-y-4">
                @if(isset($consultation->statusHistories))
                    @forelse($consultation->statusHistories as $history)
                    <div class="border-l-2 border-blue-200 pl-4 relative">
                        <div class="absolute -left-[5px] top-0 w-2 h-2 rounded-full bg-blue-500"></div>
                        <p class="text-xs font-bold text-gray-900">{{ strtoupper($history->status) }}</p>
                        <p class="text-xs text-gray-600">{{ $history->description }}</p>
                        <p class="text-[10px] text-gray-400 mt-1">{{ $history->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    @empty
                    <div class="text-center py-8">
                        <svg class="mx-auto h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-sm text-gray-500 mt-2">Belum ada riwayat status</p>
                    </div>
                    @endforelse
                @else
                    <div class="text-center py-8">
                        <svg class="mx-auto h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-sm text-gray-500 mt-2">Belum ada riwayat status</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Back Button -->
    <div class="mt-6">
        <a href="{{ route('user.consultations.index') }}" class="inline-flex items-center px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold rounded-lg transition">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali ke Daftar Konsultasi
        </a>
    </div>
</div>
@endsection