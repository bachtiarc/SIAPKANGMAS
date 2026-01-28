@extends('layouts.dashboard')

@section('title', 'Daftar Pengaduan')

@section('content')
<div class="p-6">
    <!-- Breadcrumb -->
    <nav class="mb-6 text-sm">
        <ol class="flex items-center space-x-2">
            <li><a href="{{ route('masyarakat.dashboard') }}" class="text-blue-600 hover:text-blue-800">Beranda</a></li>
            <li class="text-gray-400">/</li>
            <li class="text-gray-600">Pengaduan</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <h1 class="font-montserrat text-3xl font-bold text-gray-900 mb-2">Daftar Formulir Pengaduan</h1>
    </div>

    <!-- Actions Bar -->
    <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
            <!-- Create Button -->
            <a href="{{ route('masyarakat.complaints.create', ['from' => 'index']) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                </svg>
                Buat Pengajuan Pengaduan Baru
            </a>

            <!-- Search -->
            <form method="GET" action="{{ route('masyarakat.complaints.index') }}" class="flex space-x-2">
                @if(request('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif
                
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari ID tiket atau subjek..." 
                    class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition w-64">
                
                <button type="submit" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-semibold rounded-lg transition">
                    Cari
                </button>
            </form>
        </div>
    </div>

    <!-- Status Filter Tabs -->
    <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
        <div class="flex flex-wrap gap-2">
            @php
                $currentStatus = request('status', 'semua');
            @endphp
            
            <a href="{{ route('masyarakat.complaints.index', array_merge(request()->except('status'), ['status' => 'semua'])) }}"
                class="px-4 py-2 rounded-lg font-medium transition {{ $currentStatus === 'semua' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                Semua
            </a>
            <a href="{{ route('masyarakat.complaints.index', array_merge(request()->except('status'), ['status' => 'pending'])) }}"
                class="px-4 py-2 rounded-lg font-medium transition {{ $currentStatus === 'pending' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                Menunggu Proses
            </a>
            <a href="{{ route('masyarakat.complaints.index', array_merge(request()->except('status'), ['status' => 'diproses'])) }}"
                class="px-4 py-2 rounded-lg font-medium transition {{ $currentStatus === 'diproses' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                Diproses
            </a>
            <a href="{{ route('masyarakat.complaints.index', array_merge(request()->except('status'), ['status' => 'selesai'])) }}"
                class="px-4 py-2 rounded-lg font-medium transition {{ $currentStatus === 'selesai' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                Selesai
            </a>
        </div>
    </div>

    <!-- Complaints List -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        @if($complaints->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">No. Tiket</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Subjek</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Kategori</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($complaints as $complaint)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="font-semibold text-blue-600">{{ $complaint->ticket_number }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ Str::limit($complaint->subject, 50) }}</div>
                                <div class="text-sm text-gray-500">{{ Str::limit($complaint->description, 80) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm text-gray-900">{{ $complaint->category->name ?? '-' }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $complaint->created_at->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $status = strtolower($complaint->status);
                                    $statusClass = 'bg-gray-100 text-gray-800';
                                    $statusText = 'Unknown';
                                    
                                    if (in_array($status, ['pending', 'belum diproses'])) {
                                        $statusClass = 'bg-yellow-100 text-yellow-800';
                                        $statusText = 'Menunggu Proses';
                                    } elseif (in_array($status, ['in_progress', 'on_progress', 'diproses', 'sedang diproses'])) {
                                        $statusClass = 'bg-blue-100 text-blue-800';
                                        $statusText = 'Diproses';
                                    } elseif (in_array($status, ['completed', 'selesai'])) {
                                        $statusClass = 'bg-green-100 text-green-800';
                                        $statusText = 'Selesai';
                                    }
                                @endphp
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass }}">
                                    {{ $statusText }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('masyarakat.complaints.show', $complaint->id) }}" class="text-blue-600 hover:text-blue-900 transition">
                                    Lihat Detail
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $complaints->links() }}
            </div>
        @else
            <div class="p-12 text-center">
                <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 48 48">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Belum Ada Pengaduan</h3>
                <p class="text-gray-500 mb-6">Anda belum membuat pengaduan apapun. Mulai buat pengaduan sekarang.</p>
                <a href="{{ route('masyarakat.complaints.create', ['from' => 'index']) }}" class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition">
                    Buat Pengaduan Pertama
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
