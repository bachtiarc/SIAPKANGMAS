@extends('layouts.dashboard')

@section('title', 'Daftar Pengaduan')

@section('content')
<div class="p-6">
    <!-- Breadcrumb -->
    <nav class="mb-6 text-sm">
        <ol class="flex items-center space-x-2">
            <li><a href="{{ route('user.dashboard') }}" class="text-blue-600 hover:text-blue-800">Beranda</a></li>
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
            <a href="{{ route('user.complaints.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                </svg>
                Buat Pengajuan Pengaduan Baru
            </a>

            <!-- Search -->
            <form method="GET" action="{{ route('user.complaints.index') }}" class="flex space-x-2">
                @if(request('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif
                
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari ID tiket atau subjek..." 
                    class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 w-64">
                <button type="submit" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </button>
                
                @if(request('search'))
                    <a href="{{ route('user.complaints.index', request('status') ? ['status' => request('status')] : []) }}" 
                       class="px-4 py-2 bg-red-100 hover:bg-red-200 text-red-700 rounded-lg transition flex items-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </a>
                @endif
            </form>
        </div>

        <!-- Filters -->
        <div class="mt-4 flex items-center space-x-2">
            <span class="text-sm text-gray-600">Filter:</span>
            <a href="{{ route('user.complaints.index', request('search') ? ['search' => request('search')] : []) }}" 
               class="px-3 py-1 rounded-lg text-sm {{ !request('status') || request('status') == 'semua' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                Semua
            </a>
            <a href="{{ route('user.complaints.index', array_filter(['status' => 'pending', 'search' => request('search')])) }}" 
               class="px-3 py-1 rounded-lg text-sm {{ request('status') == 'pending' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                Diproses
            </a>
            <a href="{{ route('user.complaints.index', array_filter(['status' => 'selesai', 'search' => request('search')])) }}" 
               class="px-3 py-1 rounded-lg text-sm {{ request('status') == 'selesai' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                Selesai
            </a>
            <a href="{{ route('user.complaints.index', array_filter(['status' => 'ditolak', 'search' => request('search')])) }}" 
               class="px-3 py-1 rounded-lg text-sm {{ request('status') == 'ditolak' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                Ditolak
            </a>
        </div>
    </div>

    <!-- Complaints Table -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        @if($complaints->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">ID Tiket</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Subjek Pengaduan</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($complaints as $complaint)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-semibold text-gray-900">{{ $complaint->ticket_number }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ Str::limit($complaint->subject, 50) }}</div>
                                <div class="text-xs text-gray-500">{{ $complaint->category->name ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $complaint->created_at->format('d M Y') }}</div>
                                <div class="text-xs text-gray-500">{{ $complaint->created_at->format('H:i') }} WIB</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $complaint->status_badge }}">
                                    {{ $complaint->status_label }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <a href="{{ route('user.complaints.show', $complaint->id) }}" class="text-blue-600 hover:text-blue-800">
                                    <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
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
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada pengaduan</h3>
                <p class="mt-1 text-sm text-gray-500">Mulai dengan membuat pengajuan pengaduan baru.</p>
                <div class="mt-6">
                    <a href="{{ route('user.complaints.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Buat Pengaduan Baru
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection