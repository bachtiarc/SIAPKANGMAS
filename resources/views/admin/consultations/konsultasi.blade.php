@extends('layouts.admin')

@section('header_title', 'Manajemen Pengajuan')
@section('title', 'Manajemen Pengajuan')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-2">
        <p class="font-lato text-gray-600">Kelola dan unduh laporan pengajuan layanan bantuan Dinas Perindustrian dan Perdagangan Jawa Tengah.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center mb-4">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            </div>
            <p class="font-lato text-gray-600 text-sm mb-1">Total Tiket Masuk</p>
            <h3 class="font-montserrat text-3xl font-bold text-gray-900">{{ number_format($stats['total']) }}</h3>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <div class="w-10 h-10 bg-yellow-50 rounded-lg flex items-center justify-center mb-4">
                <svg class="w-6 h-6 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
            </div>
            <p class="font-lato text-gray-600 text-sm mb-1">Sedang Diproses</p>
            <h3 class="font-montserrat text-3xl font-bold text-gray-900">{{ number_format($stats['proses']) }}</h3>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <div class="w-10 h-10 bg-green-50 rounded-lg flex items-center justify-center mb-4">
                <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            </div>
            <p class="font-lato text-gray-600 text-sm mb-1">Selesai</p>
            <h3 class="font-montserrat text-3xl font-bold text-gray-900">{{ number_format($stats['selesai']) }}</h3>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <div class="w-10 h-10 bg-red-50 rounded-lg flex items-center justify-center mb-4">
                <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            </div>
            <p class="font-lato text-gray-600 text-sm mb-1">Belum Diproses</p>
            <h3 class="font-montserrat text-3xl font-bold text-gray-900">{{ number_format($stats['belum']) }}</h3>
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

        <form action="{{ route('admin.consultations.konsultasi') }}" method="GET" class="w-full">
            {{-- filter kamu biarin ya (nggak aku ubah) --}}
            <div class="p-4 border-b border-gray-200">
                <div class="overflow-x-auto">
                    <div class="min-w-max flex items-end gap-4">
                        {{-- (isi filter sama seperti punyamu) --}}
                        {{-- Rentang tanggal --}}
                        <div class="shrink-0">
                            <label class="block text-xs font-semibold text-gray-600 mb-2">Tgl Pengajuan :</label>
                            <div class="flex items-center gap-2">
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <input type="date" name="start_date" value="{{ request('start_date') }}" class="pl-10 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 text-gray-600 w-40">
                                </div>
                                <span class="text-gray-400 font-medium">-</span>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <input type="date" name="end_date" value="{{ request('end_date') }}" class="pl-10 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 text-gray-600 w-40">
                                </div>
                            </div>
                        </div>

                        {{-- Pelapor --}}
                        <div class="shrink-0 w-44">
                            <label class="block text-xs font-semibold text-gray-600 mb-2">Pelapor :</label>
                            <div class="relative">
                                <select name="type" class="w-full appearance-none px-3 py-2 pr-8 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 bg-white text-gray-600">
                                    <option value="Semua">Semua</option>
                                    <option value="pegawai" {{ request('type') == 'pegawai' ? 'selected' : '' }}>Pegawai</option>
                                    <option value="masyarakat_umum" {{ request('type') == 'masyarakat_umum' ? 'selected' : '' }}>Masyarakat Umum</option>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-500">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                </div>
                            </div>
                        </div>

                        {{-- Kategori --}}
                        <div class="shrink-0 w-64">
                            <label class="block text-xs font-semibold text-gray-600 mb-2">Kategori :</label>
                            <div class="relative">
                                <select name="category" class="w-full appearance-none px-3 py-2 pr-8 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 bg-white text-gray-600 truncate">
                                    <option value="Semua">Semua</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>
                                            {{ $cat->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-500">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                </div>
                            </div>
                        </div>

                        {{-- Status --}}
                        <div class="shrink-0 w-44">
                            <label class="block text-xs font-semibold text-gray-600 mb-2">Status :</label>
                            <div class="relative">
                                <select name="status" class="w-full appearance-none px-3 py-2 pr-8 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 bg-white text-gray-600">
                                    <option value="Semua">Semua</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Belum Diproses</option>
                                    <option value="on_progress" {{ request('status') == 'on_progress' ? 'selected' : '' }}>Sedang diproses</option>
                                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-500">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                </div>
                            </div>
                        </div>

                        <div class="shrink-0 flex items-center gap-2 ml-2">
                            <button type="submit" class="px-4 py-2 bg-blue-700 text-white text-sm font-medium rounded-lg hover:bg-blue-800 transition shadow-sm whitespace-nowrap">Terapkan</button>
                            <a href="{{ route('admin.consultations.konsultasi') }}" class="px-4 py-2 border border-blue-600 text-blue-600 text-sm font-medium rounded-lg hover:bg-blue-50 transition shadow-sm bg-white whitespace-nowrap">Reset</a>
                            <a href="{{ route('admin.management.export', ['tab' => 'konsultasi'] + request()->query()) }}"
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
            <div class="min-w-[1200px]">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50 sticky top-0 z-10">
                        <tr>
                            <th class="px-4 py-4 text-xs font-bold text-gray-600 uppercase tracking-wider border-b text-center whitespace-nowrap min-w-[160px]">ID Tiket</th>
                            <th class="px-4 py-4 text-xs font-bold text-gray-600 uppercase tracking-wider border-b text-center whitespace-nowrap min-w-[160px]">Tanggal Pengajuan</th>
                            <th class="px-4 py-4 text-xs font-bold text-gray-600 uppercase tracking-wider border-b text-center whitespace-nowrap min-w-[190px]">Nama Pelapor</th>
                            <th class="px-4 py-4 text-xs font-bold text-gray-600 uppercase tracking-wider border-b text-center whitespace-nowrap min-w-[240px]">Email Pelapor</th>
                            <th class="px-4 py-4 text-xs font-bold text-gray-600 uppercase tracking-wider border-b text-center whitespace-nowrap min-w-[140px]">Jenis Pelapor</th>
                            <th class="px-4 py-4 text-xs font-bold text-gray-600 uppercase tracking-wider border-b text-center whitespace-nowrap min-w-[190px]">Kategori</th>
                            <th class="px-4 py-4 text-xs font-bold text-gray-600 uppercase tracking-wider border-b text-center whitespace-nowrap min-w-[300px]">Subjek</th>
                            <th class="px-4 py-4 text-xs font-bold text-gray-600 uppercase tracking-wider border-b text-center whitespace-nowrap min-w-[150px]">Status</th>
                            <th class="px-4 py-4 text-xs font-bold text-gray-600 uppercase tracking-wider border-b text-center whitespace-nowrap min-w-[90px]">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100">
                        @forelse($consultations as $item)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-4 py-4 text-sm font-semibold text-gray-900 whitespace-nowrap">{{ $item->ticket_number }}</td>

                            <td class="px-4 py-4 text-sm text-gray-600 whitespace-nowrap text-center">{{ $item->created_at->format('d F Y') }}</td>

                            <td class="px-4 py-4 text-sm font-medium text-gray-900">
                                <div class="max-w-[220px] truncate">{{ $item->user->name ?? '-' }}</div>
                            </td>

                            <td class="px-4 py-4 text-sm text-gray-600">
                                <div class="max-w-[260px] truncate">{{ $item->user->email ?? '-' }}</div>
                            </td>

                            <td class="px-4 py-4 text-sm text-gray-600 whitespace-nowrap text-center">
                                {{ match(optional($item->user)->user_type) {
                                    'masyarakat_umum' => 'Masyarakat Umum',
                                    'pegawai'         => 'Pegawai',
                                    default           => '-',
                                } }}
                            </td>

                            <td class="px-4 py-4 text-sm text-gray-600">
                                <div class="max-w-[240px] truncate">{{ $item->category->name ?? '-' }}</div>
                            </td>

                            <td class="px-4 py-4 text-sm text-gray-900 font-medium">
                                <div class="max-w-[360px] truncate">{{ $item->subject ?? '-' }}</div>
                            </td>

                            <td class="px-4 py-4 text-center whitespace-nowrap">
                                @php
                                    $statusClass = match($item->status) {
                                        'completed'   => 'bg-green-100 text-green-700',
                                        'on_progress' => 'bg-yellow-100 text-yellow-700',
                                        'rejected'    => 'bg-red-100 text-red-700',
                                        default       => 'bg-gray-100 text-gray-700',
                                    };
                                    $statusLabel = match($item->status) {
                                        'completed'   => 'Selesai',
                                        'on_progress' => 'Sedang diproses',
                                        'rejected'    => 'Ditolak',
                                        default       => 'Belum Diproses',
                                    };
                                @endphp
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $statusClass }}">{{ $statusLabel }}</span>
                            </td>

                            <td class="px-4 py-4 text-center whitespace-nowrap">
                                <a href="{{ route('admin.consultations.show', $item->id) }}"
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
                            <td colspan="9" class="p-10 text-center text-gray-500">Data konsultasi tidak ditemukan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="p-4 border-t border-gray-200">
            {{ $consultations->links() }}
        </div>
    </div>
</div>
@endsection