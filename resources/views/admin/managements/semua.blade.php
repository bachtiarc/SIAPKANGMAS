@extends('layouts.admin')

@section('header_title', 'Manajemen Pengajuan')
@section('title', 'Manajemen Pengajuan')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-2">
        <p class="font-lato text-gray-600">
            Kelola dan unduh laporan pengajuan layanan bantuan Dinas Perindustrian dan Perdagangan Jawa Tengah.
        </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center mb-4">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
            <p class="font-lato text-gray-600 text-sm mb-1">Total Tiket Masuk</p>
            <h3 class="font-montserrat text-3xl font-bold text-gray-900">{{ number_format($stats['total'] ?? 0) }}</h3>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <div class="w-10 h-10 bg-yellow-50 rounded-lg flex items-center justify-center mb-4">
                <svg class="w-6 h-6 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
            </div>
            <p class="font-lato text-gray-600 text-sm mb-1">Sedang Diproses</p>
            <h3 class="font-montserrat text-3xl font-bold text-gray-900">{{ number_format($stats['proses'] ?? 0) }}</h3>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <div class="w-10 h-10 bg-green-50 rounded-lg flex items-center justify-center mb-4">
                <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <p class="font-lato text-gray-600 text-sm mb-1">Selesai</p>
            <h3 class="font-montserrat text-3xl font-bold text-gray-900">{{ number_format($stats['selesai'] ?? 0) }}</h3>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <div class="w-10 h-10 bg-red-50 rounded-lg flex items-center justify-center mb-4">
                <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <p class="font-lato text-gray-600 text-sm mb-1">Belum Diproses</p>
            <h3 class="font-montserrat text-3xl font-bold text-gray-900">{{ number_format($stats['belum'] ?? 0) }}</h3>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="p-4 pb-0 border-b border-gray-200">
            <div class="flex w-full gap-3">
                @php
                    $tabBase = 'flex-1 text-center px-6 py-3 font-montserrat font-semibold text-sm transition rounded-2xl';
                    $tabOff  = 'text-gray-600 bg-gray-50 hover:bg-gray-100 hover:text-blue-600';
                    $tabOn   = 'text-white bg-blue-700 shadow-sm';
                @endphp

                <a href="{{ route('admin.management.semua') }}"
                class="{{ $tabBase }} {{ request()->routeIs('admin.management.semua') ? $tabOn : $tabOff }}">
                    Semua
                </a>

                <a href="{{ route('admin.consultations.konsultasi') }}"
                class="{{ $tabBase }} {{ request()->routeIs('admin.consultations.konsultasi') ? $tabOn : $tabOff }}">
                    Konsultasi
                </a>

                <a href="{{ route('admin.complaints.pengaduan') }}"
                class="{{ $tabBase }} {{ request()->routeIs('admin.complaints.pengaduan') ? $tabOn : $tabOff }}">
                    Pengaduan
                </a>

                <a href="{{ route('admin.submissions.permohonan') }}"
                class="{{ $tabBase }} {{ request()->routeIs('admin.submissions.permohonan') ? $tabOn : $tabOff }}">
                    Permohonan Informasi
                </a>
            </div>
        </div>

        <form action="{{ route('admin.management.semua') }}" method="GET" class="w-full">
            {{-- filter kamu biarin (nggak aku ubah) --}}
            <div class="p-4 border-b border-gray-200">
                <div class="overflow-x-auto">
                    <div class="min-w-max flex items-end gap-4">
                        {{-- (isi filter sama seperti punyamu) --}}
                        <div class="shrink-0">
                            <label class="block text-xs font-semibold text-gray-600 mb-2">Rentang Tanggal :</label>
                            <div class="flex items-center gap-2">
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                    </div>
                                    <input type="date" name="start_date" value="{{ request('start_date') }}" class="pl-10 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 text-gray-600 w-40">
                                </div>
                                <span class="text-gray-400 font-medium">-</span>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                    </div>
                                    <input type="date" name="end_date" value="{{ request('end_date') }}" class="pl-10 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 text-gray-600 w-40">
                                </div>
                            </div>
                        </div>

                        <div class="shrink-0 w-44">
                            <label class="block text-xs font-semibold text-gray-600 mb-2">Pelapor :</label>
                            <div class="relative">
                                <select name="type" class="w-full appearance-none px-3 py-2 pr-8 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 bg-white text-gray-600">
                                    <option value="Semua">Semua</option>
                                    <option value="pegawai" {{ request('type') == 'pegawai' ? 'selected' : '' }}>Pegawai</option>
                                    <option value="masyarakat" {{ request('type') == 'masyarakat' ? 'selected' : '' }}>Masyarakat</option>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-500">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                </div>
                            </div>
                        </div>

                        <div class="shrink-0 w-72">
                            <label class="block text-xs font-semibold text-gray-600 mb-2">Kategori :</label>
                            <div class="relative">
                                <select name="category" class="w-full appearance-none px-3 py-2 pr-8 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 bg-white text-gray-600 truncate">
                                    <option value="Semua">Semua</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}" {{ (string)request('category') === (string)$cat->id ? 'selected' : '' }}>
                                            {{ strtoupper($cat->type) }} - {{ $cat->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-500">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                </div>
                            </div>
                        </div>

                        <div class="shrink-0 w-44">
                            <label class="block text-xs font-semibold text-gray-600 mb-2">Status :</label>
                            <div class="relative">
                                <select name="status" class="w-full appearance-none px-3 py-2 pr-8 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 bg-white text-gray-600">
                                    <option value="Semua">Semua</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Belum Diproses</option>
                                    <option value="proses" {{ request('status') == 'proses' ? 'selected' : '' }}>Sedang diproses</option>
                                    <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                                    <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-500">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                </div>
                            </div>
                        </div>

                        <div class="shrink-0 flex items-center gap-2 ml-2">
                            <button type="submit" class="px-4 py-2 bg-blue-700 text-white text-sm font-medium rounded-lg hover:bg-blue-800 transition shadow-sm whitespace-nowrap">Terapkan</button>
                            <a href="{{ route('admin.management.semua') }}" class="px-4 py-2 border border-blue-600 text-blue-600 text-sm font-medium rounded-lg hover:bg-blue-50 transition shadow-sm bg-white whitespace-nowrap">Reset</a>
                            <a href="{{ route('admin.management.export', ['tab' => 'semua'] + request()->query()) }}"
                                class="px-3 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition shadow-sm flex items-center justify-center"
                                title="Unduh Excel">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        {{-- TABLE (RAPI + SCROLL) --}}
        <div class="overflow-x-auto">
            <div class="min-w-[1350px]">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50 sticky top-0 z-10">
                        <tr>
                            <th class="px-4 py-4 text-xs font-bold text-gray-600 uppercase tracking-wider border-b text-center whitespace-nowrap min-w-[170px]">ID Tiket</th>
                            <th class="px-4 py-4 text-xs font-bold text-gray-600 uppercase tracking-wider border-b text-center whitespace-nowrap min-w-[160px]">Tanggal Pengajuan</th>
                            <th class="px-4 py-4 text-xs font-bold text-gray-600 uppercase tracking-wider border-b text-center whitespace-nowrap min-w-[190px]">Nama Pelapor</th>
                            <th class="px-4 py-4 text-xs font-bold text-gray-600 uppercase tracking-wider border-b text-center whitespace-nowrap min-w-[240px]">Email Pelapor</th>
                            <th class="px-4 py-4 text-xs font-bold text-gray-600 uppercase tracking-wider border-b text-center whitespace-nowrap min-w-[140px]">Jenis Pelapor</th>
                            <th class="px-4 py-4 text-xs font-bold text-gray-600 uppercase tracking-wider border-b text-center whitespace-nowrap min-w-[220px]">Kategori</th>
                            <th class="px-4 py-4 text-xs font-bold text-gray-600 uppercase tracking-wider border-b text-center whitespace-nowrap min-w-[320px]">Subjek</th>
                            <th class="px-4 py-4 text-xs font-bold text-gray-600 uppercase tracking-wider border-b text-center whitespace-nowrap min-w-[150px]">Status</th>
                            <th class="px-4 py-4 text-xs font-bold text-gray-600 uppercase tracking-wider border-b text-center whitespace-nowrap min-w-[90px]">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100">
                        @forelse($items as $row)
                            @php
                                $raw = strtolower($row['status'] ?? '');

                                $isSelesai = in_array($raw, ['completed','selesai']);
                                $isProses  = in_array($raw, ['on_progress','in_progress','diproses']);
                                $isTolak   = in_array($raw, ['rejected','ditolak']);

                                $statusClass = $isSelesai ? 'bg-green-100 text-green-700'
                                            : ($isProses ? 'bg-yellow-100 text-yellow-700'
                                            : ($isTolak ? 'bg-red-100 text-red-700'
                                            : 'bg-gray-100 text-gray-700'));

                                $statusLabel = $isSelesai ? 'Selesai'
                                            : ($isProses ? 'Sedang diproses'
                                            : ($isTolak ? 'Ditolak'
                                            : 'Belum Diproses'));
                            @endphp

                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-4 py-4 text-sm font-semibold text-gray-900 whitespace-nowrap">
                                    <div class="text-[11px] font-semibold text-gray-500 uppercase tracking-wide mb-1">
                                        {{ $row['service'] }}
                                    </div>
                                    {{ $row['ticket'] }}
                                </td>

                                <td class="px-4 py-4 text-sm text-gray-600 whitespace-nowrap text-center">
                                    {{ isset($row['created_at']) && $row['created_at'] ? \Carbon\Carbon::parse($row['created_at'])->format('d F Y') : '-' }}
                                </td>

                                <td class="px-4 py-4 text-sm font-medium text-gray-900">
                                    <div class="max-w-[220px] truncate">{{ $row['name'] ?? '-' }}</div>
                                </td>

                                <td class="px-4 py-4 text-sm text-gray-600">
                                    <div class="max-w-[260px] truncate">{{ $row['email'] ?? '-' }}</div>
                                </td>

                                <td class="px-4 py-4 text-sm text-gray-600 whitespace-nowrap text-center">
                                    {{ ucfirst($row['user_type'] ?? '-') }}
                                </td>

                                <td class="px-4 py-4 text-sm text-gray-600">
                                    <div class="max-w-[260px] truncate">{{ $row['category'] ?? '-' }}</div>
                                </td>

                                <td class="px-4 py-4 text-sm text-gray-900 font-medium">
                                    <div class="max-w-[380px] truncate">{{ $row['subject'] ?? '-' }}</div>
                                </td>

                                <td class="px-4 py-4 text-center whitespace-nowrap">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $statusClass }}">
                                        {{ $statusLabel }}
                                    </span>
                                </td>

                                <td class="px-4 py-4 text-center whitespace-nowrap">
                                    <a href="{{ $row['show_route'] }}"
                                       class="inline-flex p-2 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-full transition"
                                       title="Lihat Detail">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="p-10 text-center text-gray-500">Tidak ada data.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="p-4 border-t border-gray-200">
            {{ $items->links() }}
        </div>
    </div>
</div>
@endsection