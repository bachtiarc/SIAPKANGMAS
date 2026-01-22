<!-- resources/views/user/history/index.blade.php -->

@extends('layouts.dashboard')

@section('title', 'Riwayat Pengajuan')

@section('content')
<div class="p-6">
    <!-- Breadcrumb -->
    <nav class="mb-6 text-sm">
        <ol class="flex items-center space-x-2">
            <li><a href="{{ route('user.dashboard') }}" class="text-blue-600 hover:text-blue-800">Beranda</a></li>
            <li class="text-gray-400">/</li>
            <li class="text-gray-600">Riwayat Pengajuan</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <h1 class="font-montserrat text-3xl font-bold text-gray-900 mb-2">Riwayat Pengajuan Anda</h1>
        <p class="font-lato text-gray-600">Pantau status dan tindak lanjut permohonan informasi, konsultasi, dan pengaduan Anda.</p>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <!-- Total Pengajuan -->
        <div class="bg-white rounded-lg shadow-sm p-5 border-l-4 border-blue-500">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="font-lato text-sm text-gray-600">Total Pengajuan</p>
                    <p class="font-montserrat text-3xl font-bold text-gray-900">{{ $totalSubmissions }}</p>
                </div>
            </div>
        </div>

        <!-- Sedang Diproses -->
        <div class="bg-white rounded-lg shadow-sm p-5 border-l-4 border-yellow-500">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="w-8 h-8 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="font-lato text-sm text-gray-600">Sedang Diproses</p>
                    <p class="font-montserrat text-3xl font-bold text-gray-900">{{ $totalPending }}</p>
                </div>
            </div>
        </div>

        <!-- Pengajuan Selesai -->
        <div class="bg-white rounded-lg shadow-sm p-5 border-l-4 border-green-500">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="font-lato text-sm text-gray-600">Selesai</p>
                    <p class="font-montserrat text-3xl font-bold text-gray-900">{{ $totalCompleted }}</p>
                </div>
            </div>
        </div>

        <!-- Pengajuan Ditolak -->
        <div class="bg-white rounded-lg shadow-sm p-5 border-l-4 border-red-500">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="font-lato text-sm text-gray-600">Ditolak</p>
                    <p class="font-montserrat text-3xl font-bold text-gray-900">{{ $totalRejected }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
        <form method="GET" action="{{ route('user.history.index') }}" class="space-y-4">
            <!-- Search Bar -->
            <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
                <div class="flex-1 max-w-md">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari ID tiket atau judul..." 
                            class="font-lato block w-full pl-10 pr-10 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @if(request('search'))
                            <a href="{{ route('user.history.index', array_filter(['category' => request('category'), 'status' => request('status')])) }}" 
                               class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-red-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </a>
                        @endif
                    </div>
                </div>
                <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-montserrat font-semibold rounded-lg transition">
                    Cari
                </button>
            </div>

            <!-- Filter Chips - Kategori dan Status dalam 1 baris -->
            <div class="flex flex-wrap items-center gap-4">
                <!-- Kategori -->
                <div class="flex items-center gap-2">
                    <span class="text-sm text-gray-600 font-montserrat font-medium">Kategori:</span>
                    <label class="inline-flex items-center">
                        <input type="radio" name="category" value="" {{ !request('category') ? 'checked' : '' }} onchange="this.form.submit()" class="sr-only peer">
                        <span class="px-4 py-2 rounded-lg text-sm font-montserrat font-medium cursor-pointer transition peer-checked:bg-blue-600 peer-checked:text-white bg-gray-100 text-gray-700 hover:bg-gray-200">
                            Semua
                        </span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="radio" name="category" value="submission" {{ request('category') == 'submission' ? 'checked' : '' }} onchange="this.form.submit()" class="sr-only peer">
                        <span class="px-4 py-2 rounded-lg text-sm font-montserrat font-medium cursor-pointer transition peer-checked:bg-blue-600 peer-checked:text-white bg-gray-100 text-gray-700 hover:bg-gray-200">
                            Permohonan
                        </span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="radio" name="category" value="consultation" {{ request('category') == 'consultation' ? 'checked' : '' }} onchange="this.form.submit()" class="sr-only peer">
                        <span class="px-4 py-2 rounded-lg text-sm font-montserrat font-medium cursor-pointer transition peer-checked:bg-blue-600 peer-checked:text-white bg-gray-100 text-gray-700 hover:bg-gray-200">
                            Konsultasi
                        </span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="radio" name="category" value="complaint" {{ request('category') == 'complaint' ? 'checked' : '' }} onchange="this.form.submit()" class="sr-only peer">
                        <span class="px-4 py-2 rounded-lg text-sm font-montserrat font-medium cursor-pointer transition peer-checked:bg-blue-600 peer-checked:text-white bg-gray-100 text-gray-700 hover:bg-gray-200">
                            Pengaduan
                        </span>
                    </label>
                </div>

                <!-- Status -->
                <div class="flex items-center gap-2">
                    <span class="text-sm text-gray-600 font-montserrat font-medium">Status:</span>
                    <label class="inline-flex items-center">
                        <input type="radio" name="status" value="" {{ !request('status') ? 'checked' : '' }} onchange="this.form.submit()" class="sr-only peer">
                        <span class="px-4 py-2 rounded-lg text-sm font-montserrat font-medium cursor-pointer transition peer-checked:bg-blue-600 peer-checked:text-white bg-gray-100 text-gray-700 hover:bg-gray-200">
                            Semua
                        </span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="radio" name="status" value="pending" {{ request('status') == 'pending' ? 'checked' : '' }} onchange="this.form.submit()" class="sr-only peer">
                        <span class="px-4 py-2 rounded-lg text-sm font-montserrat font-medium cursor-pointer transition peer-checked:bg-blue-600 peer-checked:text-white bg-gray-100 text-gray-700 hover:bg-gray-200">
                            Diproses
                        </span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="radio" name="status" value="completed" {{ request('status') == 'completed' ? 'checked' : '' }} onchange="this.form.submit()" class="sr-only peer">
                        <span class="px-4 py-2 rounded-lg text-sm font-montserrat font-medium cursor-pointer transition peer-checked:bg-blue-600 peer-checked:text-white bg-gray-100 text-gray-700 hover:bg-gray-200">
                            Selesai
                        </span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="radio" name="status" value="rejected" {{ request('status') == 'rejected' ? 'checked' : '' }} onchange="this.form.submit()" class="sr-only peer">
                        <span class="px-4 py-2 rounded-lg text-sm font-montserrat font-medium cursor-pointer transition peer-checked:bg-blue-600 peer-checked:text-white bg-gray-100 text-gray-700 hover:bg-gray-200">
                            Ditolak
                        </span>
                    </label>
                </div>
            </div>

            @if(request('search'))
                <input type="hidden" name="search" value="{{ request('search') }}">
            @endif
        </form>
    </div>

    <!-- Submissions Table -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        @if($submissions->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-montserrat font-semibold text-gray-700 uppercase tracking-wider">ID Tiket</th>
                            <th class="px-6 py-3 text-left text-xs font-montserrat font-semibold text-gray-700 uppercase tracking-wider">Jenis Layanan</th>
                            <th class="px-6 py-3 text-left text-xs font-montserrat font-semibold text-gray-700 uppercase tracking-wider">Kategori</th>
                            <th class="px-6 py-3 text-left text-xs font-montserrat font-semibold text-gray-700 uppercase tracking-wider">Judul/Subjek</th>
                            <th class="px-6 py-3 text-left text-xs font-montserrat font-semibold text-gray-700 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-montserrat font-semibold text-gray-700 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-montserrat font-semibold text-gray-700 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($submissions as $submission)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-lato font-semibold text-gray-900">{{ $submission['ticket_id'] }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($submission['type'] == 'submission')
                                    <span class="px-3 py-1 text-xs font-montserrat font-semibold rounded-full bg-blue-100 text-blue-800">
                                        Permohonan
                                    </span>
                                @elseif($submission['type'] == 'consultation')
                                    <span class="px-3 py-1 text-xs font-montserrat font-semibold rounded-full bg-green-100 text-green-800">
                                        Konsultasi
                                    </span>
                                @else
                                    <span class="px-3 py-1 text-xs font-montserrat font-semibold rounded-full bg-purple-100 text-purple-800">
                                        Pengaduan
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-lato text-gray-600">{{ $submission['category'] }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-lato text-gray-900">{{ Str::limit($submission['title'], 50) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-lato text-gray-900">{{ $submission['date']->format('d M Y') }}</div>
                                <div class="text-xs font-lato text-gray-500">{{ $submission['date']->format('H:i') }} WIB</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php 
                                    $status = strtolower($submission['status']); 
                                @endphp
                                @if(in_array($status, ['completed', 'selesai']))
                                    <span class="px-3 py-1 text-xs font-montserrat font-semibold rounded-full bg-green-100 text-green-800">Selesai</span>
                                @elseif(in_array($status, ['pending', 'diproses', 'on_progress', 'in_progress']))
                                    <span class="px-3 py-1 text-xs font-montserrat font-semibold rounded-full bg-yellow-100 text-yellow-800">Diproses</span>
                                @elseif(in_array($status, ['rejected', 'ditolak']))
                                    <span class="px-3 py-1 text-xs font-montserrat font-semibold rounded-full bg-red-100 text-red-800">Ditolak</span>
                                @else
                                    <span class="px-3 py-1 text-xs font-montserrat font-semibold rounded-full bg-gray-100 text-gray-800">{{ ucfirst($status) }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <a href="{{ $submission['route'] }}?from=history" class="text-blue-600 hover:text-blue-800" title="Lihat Detail">
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
                <div class="flex items-center justify-between">
                    <div class="text-sm font-lato text-gray-600">
                        Menampilkan <span class="font-semibold">{{ $submissions->firstItem() }}</span> sampai <span class="font-semibold">{{ $submissions->lastItem() }}</span> dari <span class="font-semibold">{{ $submissions->total() }}</span> hasil
                    </div>
                    {{ $submissions->links() }}
                </div>
            </div>
        @else
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-montserrat font-medium text-gray-900">Belum ada riwayat pengajuan</h3>
                <p class="mt-1 text-sm font-lato text-gray-500">
                    @if(request('search') || request('category') || request('status'))
                        Tidak ditemukan hasil yang sesuai dengan filter Anda.
                    @else
                        Mulai dengan membuat pengajuan baru.
                    @endif
                </p>
                @if(!request('search') && !request('category') && !request('status'))
                <div class="mt-6">
                    <a href="{{ route('user.submissions.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-montserrat font-semibold">
                        Buat Pengajuan Baru
                    </a>
                </div>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection