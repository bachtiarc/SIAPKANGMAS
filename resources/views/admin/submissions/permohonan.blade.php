{{-- resources/views/admin/submissions/permohonan.blade.php --}}
@extends('layouts.admin')

@section('header_title', 'Manajemen Pengajuan')
@section('title', 'Manajemen Pengajuan')

@section('content')
@php
    /**
     * BIDANG options (level 1)
     * Disamakan dengan yang ada di show.blade.php (optgroup-nya)
     */
    $bidangOptions = [
        'Sekretariat',
        'Bidang Pembangunan Sumber Daya Industri Dan Perwilayahan Industri',
        'Bidang Pemberdayaan Industri',
        'Bidang Pengembangan Sarana Prasarana, Pengawasan Dan Pengendalian Industri',
        'Bidang Perdagangan Dalam Negeri',
        'Bidang Perdagangan Luar Negeri',
        'Balai Industri Logam dan Kayu (BILK) Kelas A',
        'Balai Pengujian dan Sertifikasi Mutu Barang (BPSMB) Surakarta Kelas A',
        'Balai Pengujian dan Sertifikasi Mutu Barang (BPSMB) Semarang',
        'Balai Industri Produk Tekstil dan Alas Kaki (BIPTAK)',
        'Balai Industri Kreatif Digital dan Kemasan Kelas A (BIKDK)',
    ];

    $selectedBidang = request('diproses_bidang');
@endphp

<div class="space-y-6">
    <div class="flex flex-col gap-2">
        <p class="font-lato text-gray-600">Kelola dan unduh laporan pengajuan layanan bantuan Dinas Perindustrian dan Perdagangan Jawa Tengah.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-5 gap-6">
        <div class="bg-white/75 backdrop-blur-xl p-6 rounded-3xl shadow-sm border border-gray-200/70 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md">
            <div class="w-10 h-10 bg-blue-50/80 ring-1 ring-blue-200/60 rounded-2xl flex items-center justify-center mb-4">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
            <p class="font-lato text-gray-600 text-sm mb-1">Total Tiket Masuk</p>
            <h3 class="font-montserrat text-3xl font-extrabold tracking-tight text-gray-900">{{ number_format($stats['total']) }}</h3>
        </div>

        <div class="bg-white/75 backdrop-blur-xl p-6 rounded-3xl shadow-sm border border-gray-200/70 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md">
            <div class="w-10 h-10 bg-yellow-50/80 ring-1 ring-yellow-200/60 rounded-2xl flex items-center justify-center mb-4">
                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
            </div>
            <p class="font-lato text-gray-600 text-sm mb-1">Sedang Diproses</p>
            <h3 class="font-montserrat text-3xl font-extrabold tracking-tight text-gray-900">{{ number_format($stats['proses']) }}</h3>
        </div>

        <div class="bg-white/75 backdrop-blur-xl p-6 rounded-3xl shadow-sm border border-gray-200/70 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md">
            <div class="w-10 h-10 bg-green-50/80 ring-1 ring-green-200/60 rounded-2xl flex items-center justify-center mb-4">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <p class="font-lato text-gray-600 text-sm mb-1">Selesai</p>
            <h3 class="font-montserrat text-3xl font-extrabold tracking-tight text-gray-900">{{ number_format($stats['selesai']) }}</h3>
        </div>

        <div class="bg-white/75 backdrop-blur-xl p-6 rounded-3xl shadow-sm border border-gray-200/70 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md">
            <div class="w-10 h-10 bg-red-50/80 ring-1 ring-red-200/60 rounded-2xl flex items-center justify-center mb-4">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </div>
            <p class="font-lato text-gray-600 text-sm mb-1">Ditolak</p>
            <h3 class="font-montserrat text-3xl font-extrabold tracking-tight text-gray-900">{{ number_format($stats['ditolak']) }}</h3>
        </div>

        <div class="bg-white/75 backdrop-blur-xl p-6 rounded-3xl shadow-sm border border-gray-200/70 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md">
            <div class="w-10 h-10 bg-red-50/80 ring-1 ring-red-200/60 rounded-2xl flex items-center justify-center mb-4">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
            </div>
            <p class="font-lato text-gray-600 text-sm mb-1">Belum Diproses</p>
            <h3 class="font-montserrat text-3xl font-extrabold tracking-tight text-gray-900">{{ number_format($stats['belum']) }}</h3>
        </div>
    </div>

    <div class="bg-white/75 backdrop-blur-xl rounded-3xl shadow-sm border border-gray-200/70 overflow-hidden">
        <div class="p-4 border-b border-gray-200/70">
            @php
                $tabBase = 'flex-1 text-center px-6 py-3 font-montserrat font-semibold text-sm transition rounded-2xl ring-1 ring-transparent';
                $tabOff  = 'text-gray-600 bg-white/60 ring-gray-200/70 hover:bg-gray-50/70 hover:text-blue-600 hover:ring-gray-200/80';
                $tabOn   = 'text-blue-700 bg-blue-100/70 ring-blue-200/70 shadow-sm';
            @endphp

            <div class="bg-gray-50/70 p-2 rounded-3xl ring-1 ring-gray-200/60">
                <div class="flex w-full gap-3">
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
        </div>

        <form action="{{ route('admin.submissions.permohonan') }}" method="GET" class="w-full">
            <div class="p-4 border-b border-gray-200/70">
                <div class="overflow-x-auto">
                    <div class="min-w-max flex items-end gap-4">

                        {{-- Rentang Tanggal --}}
                        <div class="shrink-0">
                            <label class="block text-xs font-semibold text-gray-600 mb-2">Rentang Tanggal :</label>
                            <div class="flex items-center gap-2">
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <input type="date" name="start_date" value="{{ request('start_date') }}"
                                           class="pl-10 pr-3 py-2 border border-gray-300/80 rounded-2xl text-sm focus:ring-4 focus:ring-blue-500/15 focus:border-blue-500/60 text-gray-600 w-40 bg-white/70 shadow-sm">
                                </div>

                                <span class="text-gray-400 font-medium">-</span>

                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <input type="date" name="end_date" value="{{ request('end_date') }}"
                                           class="pl-10 pr-3 py-2 border border-gray-300/80 rounded-2xl text-sm focus:ring-4 focus:ring-blue-500/15 focus:border-blue-500/60 text-gray-600 w-40 bg-white/70 shadow-sm">
                                </div>
                            </div>
                        </div>

                        {{-- Pelapor --}}
                        <div class="shrink-0 w-44">
                            <label class="block text-xs font-semibold text-gray-600 mb-2">Pelapor :</label>
                            <div class="relative">
                                <select name="type"
                                        class="w-full appearance-none px-3 py-2 pr-8 border border-gray-300/80 rounded-2xl text-sm focus:ring-4 focus:ring-blue-500/15 focus:border-blue-500/60 bg-white/70 text-gray-600 shadow-sm">
                                    <option value="Semua">Semua</option>
                                    <option value="pegawai" {{ request('type') == 'pegawai' ? 'selected' : '' }}>Pegawai</option>
                                    <option value="masyarakat_umum" {{ request('type') == 'masyarakat_umum' ? 'selected' : '' }}>Masyarakat Umum</option>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-500">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </div>
                        </div>

                        {{-- ✅ Diproses oleh (Bidang) --}}
                        <div class="shrink-0 w-[360px]">
                            <label class="block text-xs font-semibold text-gray-600 mb-2">Diproses oleh (Bidang) :</label>
                            <div class="relative">
                                <select name="diproses_bidang"
                                        class="w-full appearance-none px-3 py-2 pr-8 border border-gray-300/80 rounded-2xl text-sm focus:ring-4 focus:ring-blue-500/15 focus:border-blue-500/60 bg-white/70 text-gray-600 truncate shadow-sm">
                                    <option value="">Semua</option>
                                    @foreach($bidangOptions as $b)
                                        <option value="{{ $b }}" {{ (string)$selectedBidang === (string)$b ? 'selected' : '' }}>
                                            {{ $b }}
                                        </option>
                                    @endforeach
                                </select>

                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-500">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </div>
                        </div>

                        {{-- Status --}}
                        <div class="shrink-0 w-44">
                            <label class="block text-xs font-semibold text-gray-600 mb-2">Status :</label>
                            <div class="relative">
                                <select name="status"
                                        class="w-full appearance-none px-3 py-2 pr-8 border border-gray-300/80 rounded-2xl text-sm focus:ring-4 focus:ring-blue-500/15 focus:border-blue-500/60 bg-white/70 text-gray-600 shadow-sm">
                                    <option value="Semua">Semua</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Belum Diproses</option>
                                    <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>Sedang diproses</option>
                                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                                    <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-500">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div class="shrink-0 flex items-center gap-2 ml-2">
                            <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 bg-blue-700 text-white text-sm font-semibold rounded-xl hover:bg-blue-800 active:scale-[.98] transition-all shadow-sm whitespace-nowrap">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z" /></svg>
                                Terapkan
                            </button>
                            <a href="{{ route('admin.submissions.permohonan') }}" class="inline-flex items-center gap-1.5 px-4 py-2 border border-gray-200 text-gray-600 text-sm font-semibold rounded-xl hover:bg-gray-50 active:scale-[.98] transition-all bg-white whitespace-nowrap">
                                Reset
                            </a>

                            <a href="{{ route('admin.management.export', ['tab' => 'permohonan'] + request()->query()) }}"
                               class="px-3 py-2 bg-orange-500 text-white rounded-2xl hover:bg-orange-600 transition shadow-sm flex items-center justify-center active:scale-[.99]"
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

        <div class="overflow-x-auto">
            <div class="min-w-[1200px]">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50/70 backdrop-blur sticky top-0 z-10">
                        <tr>
                            <th class="px-4 py-4 text-xs font-bold text-gray-600 uppercase tracking-wider border-b border-gray-200/70 text-center whitespace-nowrap min-w-[160px]">ID Tiket</th>
                            <th class="px-4 py-4 text-xs font-bold text-gray-600 uppercase tracking-wider border-b border-gray-200/70 text-center whitespace-nowrap min-w-[160px]">Tanggal Pengajuan</th>
                            <th class="px-4 py-4 text-xs font-bold text-gray-600 uppercase tracking-wider border-b border-gray-200/70 text-center whitespace-nowrap min-w-[190px]">Nama Pelapor</th>
                            <th class="px-4 py-4 text-xs font-bold text-gray-600 uppercase tracking-wider border-b border-gray-200/70 text-center whitespace-nowrap min-w-[240px]">Email Pelapor</th>
                            <th class="px-4 py-4 text-xs font-bold text-gray-600 uppercase tracking-wider border-b border-gray-200/70 text-center whitespace-nowrap min-w-[140px]">Jenis Pelapor</th>
                            <th class="px-4 py-4 text-xs font-bold text-gray-600 uppercase tracking-wider border-b border-gray-200/70 text-center whitespace-nowrap min-w-[240px]">Diteruskan Oleh</th>
                            <th class="px-4 py-4 text-xs font-bold text-gray-600 uppercase tracking-wider border-b border-gray-200/70 text-center whitespace-nowrap min-w-[260px]">Subjek</th>
                            <th class="px-4 py-4 text-xs font-bold text-gray-600 uppercase tracking-wider border-b border-gray-200/70 text-center whitespace-nowrap min-w-[150px]">Status</th>
                            <th class="px-4 py-4 text-xs font-bold text-gray-600 uppercase tracking-wider border-b border-gray-200/70 text-center whitespace-nowrap min-w-[90px]">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100/70">
                        @forelse($submissions as $item)
                            @php
                                $namaPelapor = '-';
                                $emailPelapor = '-';

                                // kalau creator pegawai -> ambil applicant
                                if ($item->user && $item->user->user_type === 'pegawai') {
                                    if ($item->applicant) {
                                        if (!empty($item->applicant->nama_lengkap)) {
                                            $namaPelapor = $item->applicant->nama_lengkap;
                                        } elseif (!empty($item->applicant->name)) {
                                            $namaPelapor = $item->applicant->name;
                                        }

                                        if (!empty($item->applicant->email)) {
                                            $emailPelapor = $item->applicant->email;
                                        }
                                    }
                                }
                                // kalau masyarakat -> ambil user
                                else {
                                    if ($item->user) {
                                        if (!empty($item->user->name)) {
                                            $namaPelapor = $item->user->name;
                                        }

                                        if (!empty($item->user->email)) {
                                            $emailPelapor = $item->user->email;
                                        }
                                    }
                                }
                            @endphp
                        <tr class="hover:bg-gray-50/70 transition">
                            <td class="px-4 py-4 text-sm font-semibold text-gray-900 whitespace-nowrap">
                                <a href="{{ route('admin.submissions.show', $item->id) }}"
                                   class="group inline-block rounded-lg focus:outline-none focus:ring-4 focus:ring-blue-500/15"
                                   title="Lihat Detail">
                                    <span class="underline-offset-4 group-hover:underline group-hover:text-blue-700">
                                        {{ $item->ticket_id }}
                                    </span>
                                </a>
                            </td>

                            <td class="px-4 py-4 text-sm text-gray-600 whitespace-nowrap text-center">
                                {{ $item->created_at->format('d F Y') }}
                            </td>

                            <td class="px-4 py-4 text-sm font-medium text-gray-900">
                                <div class="max-w-[220px] truncate">
                                    {{ $namaPelapor }}
                                </div>
                            </td>

                            <td class="px-4 py-4 text-sm text-gray-600">
                                <div class="max-w-[260px] truncate">
                                    {{ $emailPelapor }}
                                </div>
                            </td>

                            <td class="px-4 py-4 text-sm text-gray-600 whitespace-nowrap text-center">
                                {{ match(optional($item->user)->user_type) {
                                    'masyarakat_umum' => 'Masyarakat Umum',
                                    'pegawai'         => 'Pegawai',
                                    default           => '-',
                                } }}
                            </td>

                            @php
                                // Prioritas 1: kolom diproses_bidang
                                $bidangTampil = $item->diproses_bidang ?? null;

                                // Fallback: kalau hanya ada gabungan "Bidang - Kelompok"
                                if (!$bidangTampil && !empty($item->diproses_oleh)) {
                                    $tmp = explode(' - ', (string) $item->diproses_oleh, 2);
                                    $bidangTampil = trim($tmp[0] ?? '');
                                }

                                $bidangTampil = $bidangTampil ?: '-';
                            @endphp

                            <td class="px-4 py-4 text-sm text-gray-600 whitespace-nowrap text-center">
                                <div class="max-w-[260px] truncate mx-auto">
                                    {{ $bidangTampil }}
                                </div>
                            </td>

                            <td class="px-4 py-4 text-sm text-gray-900 font-medium">
                                <div class="max-w-[320px] truncate">
                                    {{ $item->title ?? '-' }}
                                </div>
                            </td>

                            <td class="px-4 py-4 text-center whitespace-nowrap">
                                @php
                                    $statusClass = match($item->status) {
                                        'completed'    => 'bg-green-100 text-green-700',
                                        'in_progress'  => 'bg-yellow-100 text-yellow-700',
                                        'rejected'     => 'bg-red-100 text-red-700',
                                        default        => 'bg-gray-100 text-gray-700',
                                    };

                                    $statusLabel = match($item->status) {
                                        'completed'    => 'Selesai',
                                        'in_progress'  => 'Sedang diproses',
                                        'rejected'     => 'Ditolak',
                                        default        => 'Belum Diproses',
                                    };
                                @endphp

                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $statusClass }} ring-1 ring-black/5">
                                    {{ $statusLabel }}
                                </span>
                            </td>

                            <td class="px-4 py-4 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('admin.submissions.show', $item->id) }}"
                                    class="inline-flex p-2 text-gray-500 hover:text-blue-600 hover:bg-blue-50/70 rounded-full transition active:scale-[.99]"
                                    title="Lihat Detail">
                                        {{-- icon mata --}}
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>

                                    @php
                                        // arsip hanya boleh kalau selesai / ditolak
                                        $canArchive = true;
                                    @endphp

                                    @if($canArchive)
                                        <form method="POST" action="{{ route('admin.submissions.archive', $item->id) }}" class="inline">
                                            @csrf
                                            <button type="button"
                                                    class="archive-btn inline-flex p-2 text-gray-500 hover:text-orange-600 hover:bg-orange-50/70 rounded-full transition active:scale-[.99]"
                                                    title="Arsipkan"
                                                    data-action="{{ route('admin.submissions.archive', $item->id) }}">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M20 7l-1 12a2 2 0 01-2 2H7a2 2 0 01-2-2L4 7m16 0H4m16 0l-1-3H5L4 7m6 4h4"/>
                                                </svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="p-10 text-center text-gray-500">
                                Data permohonan informasi tidak ditemukan.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="p-4 border-t border-gray-200/70">
            {{ $submissions->links() }}
        </div>
    </div>
</div>
@endsection