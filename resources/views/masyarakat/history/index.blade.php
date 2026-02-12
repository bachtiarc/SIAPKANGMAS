@extends('layouts.dashboard')

@section('title', 'Riwayat Pengajuan')

@section('content')
@php
    use Illuminate\Support\Str;

    $search = $q ?? request('q', '');

    $status = request('status'); // pending | diproses | selesai | ditolak | null

    $filteredHistories = $histories ?? collect([]);

    if ($status) {
        $filteredHistories = $filteredHistories->filter(function ($item) use ($status) {
            $st = strtolower((string) ($item->status ?? ''));

            if ($status === 'pending') {
                return in_array($st, ['pending', 'belum diproses']);
            }

            if ($status === 'diproses') {
                return in_array($st, ['in_progress', 'on_progress', 'diproses', 'sedang diproses']);
            }

            if ($status === 'selesai') {
                return in_array($st, ['completed', 'selesai']);
            }

            if ($status === 'ditolak') {
                return in_array($st, ['rejected', 'ditolak']);
            }

            return true;
        })->values();
    }

    // Statistik "Selesai" & "Ditolak" dihitung dari histories (overall)
    $allHistories = $histories ?? collect([]);

    $totalAll = $allHistories->count();

    $totalCompleted = $allHistories->filter(function ($item) {
        $st = strtolower((string) ($item->status ?? ''));
        return in_array($st, ['completed', 'selesai']);
    })->count();

    $totalRejected = $allHistories->filter(function ($item) {
        $st = strtolower((string) ($item->status ?? ''));
        return in_array($st, ['rejected', 'ditolak']);
    })->count();
@endphp

<div class="p-6">
    <!-- Breadcrumb -->
    <nav class="mb-6 text-sm">
        <ol class="flex items-center space-x-2">
            <li>
                <a href="{{ route('masyarakat.dashboard') }}" class="text-blue-600 hover:text-blue-800">Beranda</a>
            </li>
            <li class="text-gray-400">/</li>
            <li class="text-gray-600">Riwayat Pengajuan</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <h1 class="font-montserrat text-3xl font-bold text-gray-900 mb-2">Riwayat Pengajuan</h1>
        <p class="text-gray-600">Riwayat lengkap permohonan informasi, konsultasi, dan pengaduan Anda</p>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
        <!-- Total Semua -->
        <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Total Semua</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $totalAll }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Menunggu Proses -->
        <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-yellow-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Menunggu Proses</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $totalPending ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Diproses -->
        <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Diproses</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $totalProcessing ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Selesai -->
        <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Selesai</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $totalCompleted }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Ditolak -->
        <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-red-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Ditolak</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $totalRejected }}</p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters & Search (TANPA KATEGORI) -->
    <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
        <form method="GET" action="{{ route('masyarakat.history.index') }}" class="space-y-4">
            <!-- Search Bar -->
            <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0 md:space-x-4">
                <div class="flex-1 flex space-x-2">
                    <input
                        type="text"
                        name="q"
                        value="{{ $search }}"
                        placeholder="Cari nomor tiket atau judul pengajuan..."
                        class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    >

                    <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </button>

                    @if(request('q') || request('status'))
                        <a href="{{ route('masyarakat.history.index') }}"
                           class="px-4 py-2 bg-red-100 hover:bg-red-200 text-red-700 rounded-lg transition flex items-center"
                           title="Reset">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </a>
                    @endif
                </div>
            </div>

            <!-- Filter Status -->
            <div>
                <span class="text-sm text-gray-600 font-semibold mb-2 block">Status:</span>
                <div class="flex flex-wrap items-center gap-2">
                    @php
                        $baseParams = array_filter([
                            'q' => request('q'),
                        ]);
                    @endphp

                    <a href="{{ route('masyarakat.history.index', $baseParams) }}"
                       class="px-4 py-2 rounded-lg text-sm font-medium transition {{ !request('status') ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        Semua
                    </a>

                    <a href="{{ route('masyarakat.history.index', array_merge($baseParams, ['status' => 'pending'])) }}"
                       class="px-4 py-2 rounded-lg text-sm font-medium transition {{ request('status') === 'pending' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        Menunggu Proses
                    </a>

                    <a href="{{ route('masyarakat.history.index', array_merge($baseParams, ['status' => 'diproses'])) }}"
                       class="px-4 py-2 rounded-lg text-sm font-medium transition {{ request('status') === 'diproses' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        Diproses
                    </a>

                    <a href="{{ route('masyarakat.history.index', array_merge($baseParams, ['status' => 'selesai'])) }}"
                       class="px-4 py-2 rounded-lg text-sm font-medium transition {{ request('status') === 'selesai' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        Selesai
                    </a>

                    <a href="{{ route('masyarakat.history.index', array_merge($baseParams, ['status' => 'ditolak'])) }}"
                       class="px-4 py-2 rounded-lg text-sm font-medium transition {{ request('status') === 'ditolak' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        Ditolak
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        @if($filteredHistories->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Layanan</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Nomor Tiket</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Judul</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($filteredHistories as $item)
                            @php
                                $st = strtolower((string) ($item->status ?? ''));
                                $statusBadge = 'bg-gray-100 text-gray-800';
                                $statusLabel = ucfirst($st ?: '-');

                                if (in_array($st, ['pending', 'belum diproses'])) {
                                    $statusBadge = 'bg-yellow-100 text-yellow-800';
                                    $statusLabel = 'Menunggu Proses';
                                } elseif (in_array($st, ['in_progress', 'on_progress', 'diproses', 'sedang diproses'])) {
                                    $statusBadge = 'bg-blue-100 text-blue-800';
                                    $statusLabel = 'Diproses';
                                } elseif (in_array($st, ['completed', 'selesai'])) {
                                    $statusBadge = 'bg-green-100 text-green-800';
                                    $statusLabel = 'Selesai';
                                } elseif (in_array($st, ['rejected', 'ditolak'])) {
                                    $statusBadge = 'bg-red-100 text-red-800';
                                    $statusLabel = 'Ditolak';
                                }

                                // Tentukan route detail berdasarkan service_type
                                $service = (string) ($item->service_type ?? '');
                                if ($service === 'Pengaduan') {
                                    $detailUrl = route('masyarakat.complaints.show', $item->id);
                                } elseif ($service === 'Konsultasi') {
                                    $detailUrl = route('masyarakat.consultations.show', $item->id);
                                } else {
                                    $detailUrl = route('masyarakat.submissions.show', $item->id);
                                }

                                $ticketShow = $item->ticket_show ?? ($item->ticket_number ?? $item->ticket_id ?? '-');
                                $titleShow  = $item->subject ?? $item->title ?? '-';
                            @endphp

                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm font-semibold text-blue-700">{{ $service ?: '-' }}</span>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-gray-900">{{ $ticketShow }}</div>
                                </td>

                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">{{ Str::limit($titleShow, 55) }}</div>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ optional($item->created_at)->format('d M Y') }}</div>
                                    <div class="text-xs text-gray-500">{{ optional($item->created_at)->format('H:i') }} WIB</div>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $statusBadge }}">
                                        {{ $statusLabel }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <a href="{{ $detailUrl }}" class="text-blue-600 hover:text-blue-800 inline-flex items-center gap-2" title="Lihat Detail">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        <span class="font-semibold">Lihat</span>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>

                </table>
            </div>
        @else
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada riwayat</h3>
                <p class="mt-1 text-sm text-gray-500">
                    @if(request('q') || request('status'))
                        Tidak ada hasil yang sesuai dengan filter Anda.
                    @else
                        Anda belum memiliki riwayat pengajuan.
                    @endif
                </p>

                @if(request('q') || request('status'))
                    <div class="mt-6">
                        <a href="{{ route('masyarakat.history.index') }}"
                           class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            Reset Filter
                        </a>
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection