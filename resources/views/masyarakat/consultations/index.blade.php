@extends('layouts.dashboard')

@section('title', 'Daftar Konsultasi')

@section('content')
<div class="p-6">
    <!-- Breadcrumb -->
    <nav class="mb-6 text-sm">
        <ol class="flex items-center space-x-2">
            <li><a href="{{ route('masyarakat.dashboard') }}" class="text-blue-600 hover:text-blue-800">Beranda</a></li>
            <li class="text-gray-400">/</li>
            <li class="text-gray-600">Konsultasi</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <h1 class="font-montserrat text-3xl font-bold text-gray-900 mb-2">Daftar Formulir Konsultasi</h1>
    </div>

    <!-- Actions Bar -->
    <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
            <!-- Create Button -->
            <a href="{{ route('masyarakat.consultations.create', ['from' => 'index']) }}"
               class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                </svg>
                Buat Pengajuan Konsultasi Baru
            </a>

            <!-- Search -->
            <form method="GET" action="{{ route('masyarakat.consultations.index') }}" class="flex space-x-2">
                @if(request('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif

                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari ID tiket atau subjek.."
                    class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 w-64">

                <button type="submit" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </button>

                @if(request('search'))
                    <a href="{{ route('masyarakat.consultations.index', request('status') ? ['status' => request('status')] : []) }}"
                       class="px-4 py-2 bg-red-100 hover:bg-red-200 text-red-700 rounded-lg transition flex items-center"
                       title="Hapus pencarian">
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

            <a href="{{ route('masyarakat.consultations.index', request('search') ? ['search' => request('search')] : []) }}"
               class="px-3 py-1 rounded-lg text-sm {{ !request('status') || request('status') == 'semua' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                Semua
            </a>

            <a href="{{ route('masyarakat.consultations.index', array_filter(['status' => 'pending', 'search' => request('search')])) }}"
               class="px-3 py-1 rounded-lg text-sm {{ request('status') == 'pending' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                Menunggu Diproses
            </a>

            <a href="{{ route('masyarakat.consultations.index', array_filter(['status' => 'diproses', 'search' => request('search')])) }}"
               class="px-3 py-1 rounded-lg text-sm {{ request('status') == 'diproses' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                Diproses
            </a>

            <a href="{{ route('masyarakat.consultations.index', array_filter(['status' => 'selesai', 'search' => request('search')])) }}"
               class="px-3 py-1 rounded-lg text-sm {{ request('status') == 'selesai' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                Selesai
            </a>

            <a href="{{ route('masyarakat.consultations.index', array_filter(['status' => 'ditolak', 'search' => request('search')])) }}"
            class="px-3 py-1 rounded-lg text-sm {{ request('status') == 'ditolak' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                Ditolak
            </a>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        @if($consultations->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Nomor Tiket</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Judul Pengajuan</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($consultations as $consultation)
                        <tr class="hover:bg-gray-50">
                            <!-- Nomor Tiket -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $consultation->ticket_number }}</div>
                            </td>

                            <!-- Subjek -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $consultation->subject }}</div>
                            </td>

                            <!-- Tanggal -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $consultation->created_at->format('d M Y') }}</div>
                                <div class="text-xs text-gray-500">{{ $consultation->created_at->format('H:i') }} WIB</div>
                            </td>

                            <!-- Status -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $status = strtolower($consultation->status ?? '');

                                    if (in_array($status, ['pending', 'belum diproses'], true)) {
                                        $statusClass = 'bg-yellow-100 text-yellow-800';
                                        $statusText  = 'Menunggu Diproses';
                                    } elseif (in_array($status, ['in_progress', 'on_progress', 'diproses', 'sedang diproses'], true)) {
                                        $statusClass = 'bg-blue-100 text-blue-800';
                                        $statusText  = 'Diproses';
                                    } elseif (in_array($status, ['completed', 'selesai'], true)) {
                                        $statusClass = 'bg-green-100 text-green-800';
                                        $statusText  = 'Selesai';
                                    } elseif (in_array($status, ['rejected', 'ditolak'], true)) {
                                        $statusClass = 'bg-red-100 text-red-800';
                                        $statusText  = 'Ditolak';
                                    } else {
                                        // fallback biar gak muncul Unknown lagi
                                        $statusClass = 'bg-yellow-100 text-yellow-800';
                                        $statusText  = 'Menunggu Diproses';
                                    }
                                @endphp
                                <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $statusClass }}">
                                    {{ $statusText }}
                                </span>
                            </td>

                            <!-- Aksi -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <a href="{{ route('masyarakat.consultations.show', $consultation->id) }}?from=index"
                                   class="text-blue-600 hover:text-blue-800"
                                   title="Lihat Detail">
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
                {{ $consultations->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada konsultasi</h3>
                <p class="mt-1 text-sm text-gray-500">Mulai dengan membuat pengajuan konsultasi baru.</p>
                <div class="mt-6">
                    <a href="{{ route('masyarakat.consultations.create', ['from' => 'index']) }}"
                       class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Buat Konsultasi Baru
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection