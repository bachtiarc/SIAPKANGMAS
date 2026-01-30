@extends('layouts.dashboard')

@section('title', 'Hasil Pencarian')

@section('content')
<div class="p-6">
    <!-- Back Button and Header -->
    <div class="mb-6">
        <a href="{{ route('masyarakat.dashboard') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800 mb-4 font-lato">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali ke Dashboard
        </a>
        
        <h1 class="text-2xl font-bold text-gray-900 font-montserrat">
            Hasil pencarian: "{{ $q }}"
        </h1>
        <p class="text-gray-600 mt-1 font-lato">
            Ditemukan {{ $submissions->count() + $consultations->count() + $complaints->count() }} hasil
        </p>
    </div>

    @if(
        $submissions->isEmpty() &&
        $consultations->isEmpty() &&
        $complaints->isEmpty()
    )
        <div class="bg-white rounded-lg shadow-sm p-12 text-center">
            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            <p class="text-gray-500 font-lato text-lg">Tidak ada hasil ditemukan untuk "{{ $q }}"</p>
            <p class="text-gray-400 font-lato text-sm mt-2">Coba gunakan kata kunci yang berbeda</p>
        </div>
    @endif

    @if($submissions->count())
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <h2 class="font-semibold text-lg mb-4 font-montserrat text-gray-900 flex items-center">
            <span class="w-2 h-8 bg-blue-600 rounded-full mr-3"></span>
            Permohonan Informasi ({{ $submissions->count() }})
        </h2>
        <div class="space-y-3">
            @foreach($submissions as $i)
                <a href="{{ route('masyarakat.submissions.show', $i->id) }}?from=search&q={{ urlencode($q) }}" class="block border border-gray-200 p-4 rounded-lg hover:border-blue-500 hover:shadow-md transition">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="text-xs text-gray-500 font-mono font-lato">{{ $i->ticket_id }}</span>
                            </div>
                            <h3 class="font-medium text-gray-900 font-montserrat mb-1">
                                {{ $i->title }}
                            </h3>
                            <p class="text-sm text-gray-600 font-lato">
                                {{ Str::limit($i->description, 100) }}
                            </p>
                            <div class="flex items-center gap-4 mt-2 text-xs text-gray-500 font-lato">
                                <span>{{ $i->created_at->format('d/m/Y') }}</span>
                                @php 
                                    $status = strtolower($i->status);
                                @endphp
                                @if($status == 'completed')
                                    <span class="px-2 py-1 rounded-full bg-green-100 text-green-800 font-semibold">Selesai</span>
                                @elseif(in_array($status, ['pending', 'in_progress']))
                                    <span class="px-2 py-1 rounded-full bg-yellow-100 text-yellow-800 font-semibold">Diproses</span>
                                @elseif($status == 'rejected')
                                    <span class="px-2 py-1 rounded-full bg-red-100 text-red-800 font-semibold">Ditolak</span>
                                @endif
                            </div>
                        </div>
                        <svg class="w-5 h-5 text-gray-400 ml-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
    @endif

    @if($consultations->count())
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <h2 class="font-semibold text-lg mb-4 font-montserrat text-gray-900 flex items-center">
            <span class="w-2 h-8 bg-green-600 rounded-full mr-3"></span>
            Konsultasi ({{ $consultations->count() }})
        </h2>
        <div class="space-y-3">
            @foreach($consultations as $i)
                <a href="{{ route('masyarakat.consultations.show', $i->id) }}?from=search&q={{ urlencode($q) }}" class="block border border-gray-200 p-4 rounded-lg hover:border-green-500 hover:shadow-md transition">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="text-xs text-gray-500 font-mono font-lato">{{ $i->ticket_number }}</span>
                            </div>
                            <h3 class="font-medium text-gray-900 font-montserrat mb-1">
                                {{ $i->subject }}
                            </h3>
                            <p class="text-sm text-gray-600 font-lato">
                                {{ Str::limit($i->description, 100) }}
                            </p>
                            <div class="flex items-center gap-4 mt-2 text-xs text-gray-500 font-lato">
                                <span>{{ $i->created_at->format('d/m/Y') }}</span>
                                @php 
                                    $status = strtolower($i->status);
                                @endphp
                                @if($status == 'completed')
                                    <span class="px-2 py-1 rounded-full bg-green-100 text-green-800 font-semibold">Selesai</span>
                                @elseif(in_array($status, ['pending', 'on_progress']))
                                    <span class="px-2 py-1 rounded-full bg-yellow-100 text-yellow-800 font-semibold">Diproses</span>
                                @elseif($status == 'rejected')
                                    <span class="px-2 py-1 rounded-full bg-red-100 text-red-800 font-semibold">Ditolak</span>
                                @endif
                            </div>
                        </div>
                        <svg class="w-5 h-5 text-gray-400 ml-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
    @endif

    @if($complaints->count())
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <h2 class="font-semibold text-lg mb-4 font-montserrat text-gray-900 flex items-center">
            <span class="w-2 h-8 bg-red-600 rounded-full mr-3"></span>
            Pengaduan ({{ $complaints->count() }})
        </h2>
        <div class="space-y-3">
            @foreach($complaints as $i)
                <a href="{{ route('masyarakat.complaints.show', $i->id) }}?from=search&q={{ urlencode($q) }}" class="block border border-gray-200 p-4 rounded-lg hover:border-red-500 hover:shadow-md transition">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="text-xs text-gray-500 font-mono font-lato">{{ $i->ticket_number }}</span>
                            </div>
                            <h3 class="font-medium text-gray-900 font-montserrat mb-1">
                                {{ $i->subject }}
                            </h3>
                            <p class="text-sm text-gray-600 font-lato">
                                {{ Str::limit($i->description, 100) }}
                            </p>
                            <div class="flex items-center gap-4 mt-2 text-xs text-gray-500 font-lato">
                                <span>{{ $i->created_at->format('d/m/Y') }}</span>
                                @php 
                                    $status = strtolower($i->status);
                                @endphp
                                @if($status == 'selesai')
                                    <span class="px-2 py-1 rounded-full bg-green-100 text-green-800 font-semibold">Selesai</span>
                                @elseif(in_array($status, ['pending', 'diproses']))
                                    <span class="px-2 py-1 rounded-full bg-yellow-100 text-yellow-800 font-semibold">Diproses</span>
                                @elseif($status == 'ditolak')
                                    <span class="px-2 py-1 rounded-full bg-red-100 text-red-800 font-semibold">Ditolak</span>
                                @endif
                            </div>
                        </div>
                        <svg class="w-5 h-5 text-gray-400 ml-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection