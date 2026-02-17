{{-- resources/views/admin/consultations/show.blade.php --}}
@extends('layouts.admin')

@section('header_title', 'Detail Konsultasi')
@section('title', 'Detail Konsultasi ' . ($consultation->ticket_id ?? $consultation->ticket_number ?? '-'))

@section('content')
@php
    // =========================
    // Tentukan pemohon (admin)
    // =========================
    $creator  = $consultation->user;
    $userType = $creator->user_type ?? null;

    $pemohon = ($userType === 'pegawai')
        ? ($consultation->applicant ?? null)
        : $creator;

    // =========================
    // Ticket id
    // =========================
    $ticketId = $consultation->ticket_id ?? $consultation->ticket_number ?? '-';

    // =========================
    // WhatsApp link generator
    // =========================
    $rawPhone = $pemohon->phone ?? ($pemohon->phone_number ?? '') ?? '';
    $phoneDigits = preg_replace('/\D+/', '', (string) $rawPhone);

    if (str_starts_with($phoneDigits, '0')) {
        $waPhone = '62' . substr($phoneDigits, 1);
    } elseif (str_starts_with($phoneDigits, '62')) {
        $waPhone = $phoneDigits;
    } elseif (str_starts_with($phoneDigits, '8')) {
        $waPhone = '62' . $phoneDigits;
    } else {
        $waPhone = $phoneDigits;
    }

    $waPhone = (strlen($waPhone) >= 10) ? $waPhone : null;

    $pemohonNameForWa = $pemohon->nama_lengkap ?? $pemohon->name ?? 'Bapak/Ibu';
    $waText = rawurlencode("Halo {$pemohonNameForWa}, kami dari Admin SIAPKANGMAS terkait Pengajuan {$ticketId}.");
    $waLink = $waPhone ? "https://wa.me/{$waPhone}?text={$waText}" : null;

    // KTP public URL dikirim dari controller show()
    $ktpPublicUrl = $ktpPublicUrl ?? null;

    // =========================
    // Status badge (samakan style)
    // =========================
    $statusRaw = strtolower((string)($consultation->status ?? 'pending'));

    $badgeColor = match(true) {
        in_array($statusRaw, ['pending', 'belum diproses']) => 'bg-gray-100 text-gray-700',
        in_array($statusRaw, ['on_progress','in_progress','diproses','sedang diproses']) => 'bg-yellow-100 text-yellow-800',
        in_array($statusRaw, ['completed','selesai']) => 'bg-green-100 text-green-800',
        in_array($statusRaw, ['rejected','ditolak']) => 'bg-red-100 text-red-800',
        default => 'bg-gray-100 text-gray-800',
    };

    $statusLabel = match(true) {
        in_array($statusRaw, ['pending', 'belum diproses']) => 'Belum Diproses',
        in_array($statusRaw, ['on_progress','in_progress','diproses','sedang diproses']) => 'Sedang Diproses',
        in_array($statusRaw, ['completed','selesai']) => 'Selesai',
        in_array($statusRaw, ['rejected','ditolak']) => 'Ditolak',
        default => ucfirst($statusRaw ?: '-'),
    };

    $statusText = fn($st) => match((string)$st) {
        'pending' => 'Pending',
        'on_progress', 'in_progress' => 'Sedang Diproses',
        'completed', 'selesai' => 'Selesai',
        'rejected', 'ditolak' => 'Ditolak',
        default => ucfirst((string)$st),
    };

    // =========================
    // Dropdown 2 tingkat (Bidang -> Kelompok)
    // =========================
    $bidangKelompok = [
        'Sekretariat' => [
            'Sekretaris',
            'Sub Bagian Program',
            'Sub Bagian Keuangan',
            'Sub Bagian Umum dan Kepegawaian',
        ],
        'Bidang Pembangunan Sumber Daya Industri Dan Perwilayahan Industri' => [
            'Kelompok Kerja Pengembangan Perwilayahan Industri',
            'Kelompok Kerja pengembangan Teknologi Industri',
            'Kelompok Kerja Pengembangan SDM Industri',
        ],
        'Bidang Pemberdayaan Industri' => [
            'Kelompok Kerja Pengembangan Industri',
            'Kelompok Kerja Promosi dan Kerja Sama Industri',
            'Kelompok Kerja Promosi dan Kerja Sama Industri',
        ],
        'Bidang Pengembangan Sarana Prasarana, Pengawasan Dan Pengendalian Industri' => [
            'Kelompok Kerja Pengembangan Sarana Prasarana Industri',
            'Kelompok Kerja Pengawasan dan Pengendalian Industri',
            'Kelompok Kerja Data dan Informasi Industri',
        ],
        'Bidang Perdagangan Dalam Negeri' => [
            'Kelompok Kerja Pengendalian Bapokting, Pengembangan Informasi dan Sarana Perdagangan',
            'Kelompok Kerja Promosi dan Kerjasama',
            'Kelompok Kerja Perlindungan Konsumen dan Tertib Niaga',
        ],
        'Bidang Perdagangan Luar Negeri' => [
            'Kelompok Kerja Ekspor dan Impor',
            'Kelompok Kerja Promosi dan Kerjasama Perdagangan Luar Negeri',
            'Kelompok Kerja Informasi Dan Analisis Pasar',
        ],
        'Balai Industri Logam dan Kayu (BILK) Kelas A' => [
            'Kelompok Kerja Pelayanan Jasa Keteknikan,',
            'Kelompok Kerja Penerapan dan Rekayasa',
            'Kelompok Jabatan Fungsional',
        ],
        'Balai Pengujian dan Sertifikasi Mutu Barang (BPSMB) Surakarta Kelas A' => [
            'Kelompok Kerja Pelayanan Teknis Pengujian dan Kalibrasi',
            'Kelompok Kerja Pengembangan Jasa Pengujian dan Kalibrasi',
            'Kelompok Jabatan Fungsional',
        ],
        'Balai Pengujian dan Sertifikasi Mutu Barang (BPSMB) Semarang' => [
            'Kelompok Kerja Pengembangan Produk Alas Kaki',
            'Kelompok Kerja Pengembangan Jasa Pengujian dan Kalibrasi',
            'Kelompok Jabatan Fungsional',
        ],
        'Balai Industri Produk Tekstil dan Alas Kaki (BIPTAK)' => [
            'Kelompok Kerja Pengembangan Produk Tekstil',
            'Kelompok Kerja Pengembangan Produk Alas Kaki',
            'Kelompok Jabatan Fungsional',
        ],
        'Balai Industri Kreatif Digital dan Kemasan Kelas A (BIKDK)' => [
            'Kelompok Kerja Industri Kreatif Digital',
            'Kelompok Kerja Pengembangan Kemasan',
            'Kelompok Jabatan Fungsional',
        ],
    ];

    $oldBidang   = old('diproses_bidang', $consultation->diproses_bidang ?? null);
    $oldKelompok = old('diproses_kelompok', $consultation->diproses_kelompok ?? null);

    if ((!$oldBidang || !$oldKelompok) && !empty($consultation->diproses_oleh) && str_contains($consultation->diproses_oleh, ' - ')) {
        [$bTmp, $kTmp] = array_pad(explode(' - ', $consultation->diproses_oleh, 2), 2, null);
        $oldBidang   = $oldBidang ?: $bTmp;
        $oldKelompok = $oldKelompok ?: $kTmp;
    }

    $diprosesOlehCombined = old('diproses_oleh', $consultation->diproses_oleh ?? null);

    // =========================
    // Timeline items (persis submissions)
    // =========================
    $histories = collect($consultation->statusHistories ?? [])->sortBy('created_at')->values();

    $timelineItems = collect();

    $timelineItems->push([
        'title' => 'Tiket Dibuat oleh Pemohon',
        'time'  => $consultation->created_at,
        'note'  => null,
    ]);

    $timelineItems->push([
        'title' => 'Tiket Diterima Sistem',
        'time'  => $consultation->created_at,
        'note'  => null,
    ]);

    foreach ($histories as $h) {
        $timelineItems->push([
            'title' => "Status diubah menjadi '" . $statusText($h->new_status) . "'",
            'time'  => $h->created_at,
            'note'  => $h->notes ?? null,
        ]);
    }

    $activeIndex = max(0, $timelineItems->count() - 1);

    // isi
    $judul = $consultation->subject ?? $consultation->title ?? '-';
    $deskripsi = $consultation->description ?? '-';

    // docs
    $docs = $consultation->documents ?? collect();

    $progressPct = $timelineItems->count() > 1
        ? round(($activeIndex / ($timelineItems->count() - 1)) * 100)
        : 0;
@endphp

<style>
    .with-scrollbar::-webkit-scrollbar { width: 10px; }
    .with-scrollbar::-webkit-scrollbar-track { background: #f3f4f6; border-radius: 999px; }
    .with-scrollbar::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 999px; border: 2px solid #f3f4f6; }
    .with-scrollbar::-webkit-scrollbar-thumb:hover { background: #9ca3af; }
    .with-scrollbar { scrollbar-color: #d1d5db #f3f4f6; scrollbar-width: thin; }
</style>

<div class="space-y-6">
    {{-- TOP BAR --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="p-5 md:p-6">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div class="flex items-start gap-4">
                    <a href="{{ url()->previous() }}"
                       class="shrink-0 p-2.5 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition"
                       title="Kembali">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        <span class="sr-only">Kembali</span>
                    </a>

                    <div class="min-w-0">
                        <div class="flex items-center gap-3 flex-wrap">
                            <h1 class="font-montserrat text-2xl font-bold text-gray-900 truncate">
                                Detail Konsultasi {{ $ticketId }}
                            </h1>
                            <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $badgeColor }}">
                                {{ $statusLabel }}
                            </span>
                        </div>

                        <div class="flex items-center gap-3 mt-1.5 text-sm text-gray-500 font-lato flex-wrap">
                            <div class="inline-flex items-center gap-2">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10m-13 9h16a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v11a2 2 0 002 2z"/>
                                </svg>
                                <span>Diajukan pada {{ optional($consultation->created_at)->format('d F Y') }}</span>
                            </div>

                            <span class="text-gray-300">|</span>

                            <div class="inline-flex items-center gap-2">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6M7 4h10a2 2 0 012 2v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6a2 2 0 012-2z"/>
                                </svg>
                                <span>Layanan : Konsultasi</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-2 justify-start lg:justify-end">
                    @if($waLink)
                        <a href="{{ $waLink }}"
                           target="_blank"
                           rel="noopener"
                           class="inline-flex items-center justify-center w-11 h-11 rounded-xl bg-green-600 text-white hover:bg-green-700 transition shadow-sm"
                           title="Chat WhatsApp">
                            <svg class="w-5 h-5" viewBox="0 0 32 32" fill="currentColor">
                                <path d="M19.11 17.22c-.27-.14-1.6-.79-1.85-.88-.25-.09-.43-.14-.61.14-.18.27-.7.88-.86 1.06-.16.18-.32.2-.59.07-.27-.14-1.14-.42-2.17-1.34-.8-.71-1.34-1.59-1.5-1.86-.16-.27-.02-.42.12-.56.12-.12.27-.32.41-.48.14-.16.18-.27.27-.45.09-.18.05-.34-.02-.48-.07-.14-.61-1.47-.84-2.01-.22-.52-.45-.45-.61-.46h-.52c-.18 0-.48.07-.73.34-.25.27-.96.94-.96 2.29 0 1.35.99 2.66 1.12 2.84.14.18 1.95 2.98 4.73 4.18.66.29 1.18.46 1.58.59.66.21 1.26.18 1.74.11.53-.08 1.6-.65 1.83-1.28.23-.63.23-1.17.16-1.28-.07-.11-.25-.18-.52-.32z"/>
                                <path d="M16.02 3C8.86 3 3.05 8.81 3.05 15.97c0 2.28.6 4.51 1.75 6.48L3 29l6.73-1.76a12.9 12.9 0 0 0 6.29 1.61h.01c7.16 0 12.97-5.81 12.97-12.97C28.99 8.81 23.18 3 16.02 3zm0 23.33h-.01c-2.02 0-4-.54-5.74-1.55l-.41-.24-3.99 1.04 1.07-3.89-.26-.4a10.77 10.77 0 0 1-1.67-5.75c0-5.96 4.85-10.81 10.81-10.81 5.96 0 10.81 4.85 10.81 10.81 0 5.96-4.85 10.81-10.81 10.81z"/>
                            </svg>
                            <span class="sr-only">Chat WA</span>
                        </a>
                    @endif

                    <a href="{{ route('admin.consultations.pdf', $consultation->id) }}"
                       class="inline-flex items-center justify-center w-11 h-11 rounded-xl bg-orange-500 text-white hover:bg-orange-600 transition shadow-sm"
                       title="Unduh PDF">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1M12 4v12m0 0l-4-4m4 4l4-4"/>
                        </svg>
                        <span class="sr-only">Unduh PDF</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if(session('wa_link'))
        <div class="bg-green-50 border border-green-100 rounded-2xl p-4 flex items-start gap-3">
            <div class="mt-0.5 w-9 h-9 rounded-xl bg-green-100 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 7V3m8 4V3m-9 8h10m-13 9h16a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v11a2 2 0 002 2z"/>
                </svg>
            </div>
            <div class="flex-1">
                <p class="text-green-700 font-medium">Notifikasi WhatsApp siap dikirim.</p>
                <a href="{{ session('wa_link') }}" target="_blank" rel="noopener"
                class="mt-2 inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-green-600 text-white font-semibold hover:bg-green-700">
                    Kirim via WhatsApp
                </a>
            </div>
        </div>
    @endif

    @if(session('success'))
        <div class="bg-green-50 border border-green-100 rounded-2xl p-4 flex items-start gap-3">
            <div class="mt-0.5 w-9 h-9 rounded-xl bg-green-100 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <p class="text-green-700 font-medium">{{ session('success') }}</p>
        </div>
    @endif

    {{-- RIWAYAT AKTIVITAS --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between gap-3">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h3 class="font-montserrat font-bold text-gray-900">Riwayat Aktivitas</h3>
            </div>
            <div class="text-xs text-gray-500 font-lato">Step {{ $activeIndex + 1 }} / {{ $timelineItems->count() }}</div>
        </div>

        <div class="p-6">
            <div class="overflow-x-auto">
                <div class="min-w-[860px]">
                    <div class="h-2 bg-gray-100 rounded-full overflow-hidden mb-5">
                        <div class="h-2 bg-blue-600 rounded-full" style="width: {{ $progressPct }}%"></div>
                    </div>

                    <div class="grid gap-3" style="grid-template-columns: repeat({{ $timelineItems->count() }}, minmax(0, 1fr));">
                        @foreach($timelineItems as $i => $it)
                            @php
                                $isActive = $i === $activeIndex;
                                $isDone   = $i < $activeIndex;
                                $dotClass = $isActive ? 'bg-blue-600 ring-4 ring-blue-100'
                                          : ($isDone ? 'bg-blue-600' : 'bg-gray-300');
                                $cardClass = $isActive ? 'border-blue-200 bg-blue-50/40'
                                           : 'border-gray-200 bg-white';
                            @endphp

                            <div class="px-1">
                                <div class="rounded-2xl border {{ $cardClass }} p-3 text-center">
                                    <div class="flex justify-center mb-2">
                                        <span class="w-3.5 h-3.5 rounded-full {{ $dotClass }}"></span>
                                    </div>
                                    <p class="text-sm font-bold text-gray-900">{{ $it['title'] }}</p>
                                    <p class="mt-1 text-[11px] text-gray-500">
                                        {{ optional($it['time'])->format('d M Y, H:i') }} WIB
                                    </p>

                                    @if($i === $activeIndex && !empty($it['note']))
                                        <div class="mt-3 mx-auto max-w-[240px] bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-[11px] text-gray-600">
                                            “{{ $it['note'] }}”
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- DATA PEMOHON --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center gap-2">
            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
            <h3 class="font-montserrat font-bold text-gray-900">Data Pemohon</h3>
        </div>

        <div class="p-6">
            @if(!$pemohon)
                <div class="p-4 bg-red-50 border border-red-100 rounded-xl text-sm text-red-700">
                    Data pemohon tidak ditemukan.
                </div>
            @else
                @php
                    $jenisPelapor = $userType ? ucwords(str_replace('_',' ', $userType)) : '-';
                    if ($userType === 'masyarakat_umum') $jenisPelapor = 'Masyarakat Umum';
                    if ($userType === 'pegawai') $jenisPelapor = 'Pegawai';

                    $nik = $pemohon->nik ?? null;
                    $alamatDetail = $pemohon->alamat_detail ?? $pemohon->address ?? null;

                    $provName = $pemohon->provinsi ?? $pemohon->provinsi_nama ?? null;
                    $kabName  = $pemohon->kabupaten ?? $pemohon->kabupaten_nama ?? null;
                    $kecName  = $pemohon->kecamatan ?? $pemohon->kecamatan_nama ?? null;
                    $desaName = $pemohon->desa ?? $pemohon->desa_nama ?? null;

                    $pemohonPekerjaan = $pemohon->pekerjaan ?? null;
                @endphp

                <div class="font-lato">
                    <div class="flex flex-wrap items-center gap-2 mb-5">
                        <span class="text-sm text-gray-600">Jenis Pelapor :</span>
                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">
                            {{ $jenisPelapor }}
                        </span>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                        <div class="lg:col-span-8">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div class="rounded-2xl border border-gray-100 bg-white p-4">
                                    <p class="text-xs text-gray-500 mb-1">Nama Lengkap</p>
                                    <p class="font-bold text-gray-900">{{ $pemohon->nama_lengkap ?? $pemohon->name ?? '-' }}</p>
                                </div>

                                <div class="rounded-2xl border border-gray-100 bg-white p-4">
                                    <p class="text-xs text-gray-500 mb-1">Email</p>
                                    <p class="font-bold text-gray-900 break-words">{{ $pemohon->email ?? '-' }}</p>
                                </div>

                                <div class="rounded-2xl border border-gray-100 bg-white p-4">
                                    <p class="text-xs text-gray-500 mb-1">NIK</p>
                                    <p class="font-bold text-gray-900">{{ $nik ?? '-' }}</p>
                                </div>

                                <div class="rounded-2xl border border-gray-100 bg-white p-4">
                                    <p class="text-xs text-gray-500 mb-1">Nomor Telepon</p>
                                    @if($waLink)
                                        <a href="{{ $waLink }}" target="_blank" rel="noopener"
                                           class="inline-flex items-center gap-2 font-bold text-blue-600 hover:underline">
                                            <span>wa.me/{{ $waPhone ?? '-' }}</span>
                                        </a>
                                    @else
                                        <p class="font-bold text-gray-900">{{ $pemohon->phone ?? '-' }}</p>
                                    @endif
                                </div>

                                {{-- ALAMAT (dipisah seperti form) --}}
                                <div class="md:col-span-2">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                                        {{-- Pekerjaan --}}
                                        <div class="rounded-2xl border border-gray-100 bg-white p-4">
                                            <p class="text-xs text-gray-500 mb-1">Pekerjaan</p>
                                            <div class="mt-2 w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 text-gray-900 font-semibold">
                                                {{ $pemohonPekerjaan ?: '-' }}
                                            </div>
                                        </div>

                                        {{-- Provinsi --}}
                                        <div class="rounded-2xl border border-gray-100 bg-white p-4">
                                            <p class="text-xs text-gray-500 mb-1">Provinsi</p>
                                            <div class="mt-2 w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 text-gray-900 font-semibold">
                                                {{ $provName ?: '-' }}
                                            </div>
                                        </div>

                                        {{-- Kabupaten / Kota --}}
                                        <div class="rounded-2xl border border-gray-100 bg-white p-4">
                                            <p class="text-xs text-gray-500 mb-1">Kabupaten / Kota</p>
                                            <div class="mt-2 w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 text-gray-900 font-semibold">
                                                {{ $kabName ?: '-' }}
                                            </div>
                                        </div>

                                        {{-- Kecamatan --}}
                                        <div class="rounded-2xl border border-gray-100 bg-white p-4">
                                            <p class="text-xs text-gray-500 mb-1">Kecamatan</p>
                                            <div class="mt-2 w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 text-gray-900 font-semibold">
                                                {{ $kecName ?: '-' }}
                                            </div>
                                        </div>

                                        {{-- Desa / Kelurahan (full width) --}}
                                        <div class="md:col-span-2 rounded-2xl border border-gray-100 bg-white p-4">
                                            <p class="text-xs text-gray-500 mb-1">Desa / Kelurahan</p>
                                            <div class="mt-2 w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 text-gray-900 font-semibold">
                                                {{ $desaName ?: '-' }}
                                            </div>
                                        </div>

                                        {{-- Alamat Lengkap (full width) --}}
                                        <div class="md:col-span-2 rounded-2xl border border-gray-100 bg-white p-4">
                                            <p class="text-xs text-gray-500 mb-1">Alamat Lengkap</p>
                                            <div class="mt-2 w-full px-4 py-4 rounded-xl border border-gray-200 bg-gray-50 text-gray-900 font-semibold min-h-[110px] whitespace-pre-line">
                                                {{ $alamatDetail ?: '-' }}
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="lg:col-span-4">
                            <div class="rounded-2xl border border-gray-100 bg-white p-4">
                                <div class="flex items-center justify-between mb-3">
                                    <p class="text-sm font-semibold text-gray-700">Foto KTP</p>
                                    @if($ktpPublicUrl)
                                        <a href="{{ $ktpPublicUrl }}" target="_blank" rel="noopener"
                                           class="text-xs font-semibold text-blue-600 hover:underline">
                                            Lihat
                                        </a>
                                    @endif
                                </div>

                                @if($ktpPublicUrl)
                                    <a href="{{ $ktpPublicUrl }}" target="_blank" rel="noopener" class="block">
                                        <img src="{{ $ktpPublicUrl }}" alt="Foto KTP"
                                             class="w-full h-44 object-cover rounded-xl border border-gray-200">
                                    </a>

                                    <div class="mt-3 grid grid-cols-2 gap-2">
                                        <a href="{{ $ktpPublicUrl }}" target="_blank" rel="noopener"
                                           class="inline-flex items-center justify-center gap-2 px-3 py-2 rounded-xl border border-gray-200 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                                            Lihat KTP
                                        </a>
                                        <a href="{{ route('admin.consultations.ktp.download', $consultation->id) }}"
                                           class="inline-flex items-center justify-center gap-2 px-3 py-2 rounded-xl border border-gray-200 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                                            Unduh KTP
                                        </a>
                                    </div>
                                @else
                                    <div class="w-full h-44 rounded-xl border border-gray-200 flex items-center justify-center text-gray-400 bg-gray-50">
                                        <div class="text-center">
                                            <svg class="w-8 h-8 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M3 5h18v14H3V5zm4 10l3-3 4 4 3-3 3 3"/>
                                            </svg>
                                            <p class="text-xs">Tidak ada foto</p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                </div>
            @endif
        </div>
    </div>

    {{-- ISI PENGAJUAN --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center gap-2">
            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <h3 class="font-montserrat font-bold text-gray-900">Isi Pengajuan</h3>
        </div>

        <div class="p-6 space-y-6">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                <div class="lg:col-span-8 space-y-4">
                    <div class="rounded-2xl border border-gray-100 bg-white p-4">
                        <h4 class="font-bold text-gray-900 text-sm mb-2">Judul</h4>
                        <p class="text-gray-700 font-lato">{{ $judul }}</p>
                    </div>

                    <div class="rounded-2xl border border-gray-100 bg-white p-4">
                        <h4 class="font-bold text-gray-900 text-sm mb-2">Deskripsi Lengkap</h4>
                        <div class="p-4 bg-white rounded-xl border border-gray-200 min-h-[240px]">
                            <p class="text-gray-700 font-lato text-sm leading-relaxed whitespace-pre-line">
                                {{ $deskripsi ?: '-' }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-4">
                    <div class="rounded-2xl border border-gray-100 bg-white p-4">
                        <h4 class="font-bold text-gray-900 text-sm mb-3">Dokumen Pendukung</h4>

                        @if($docs && $docs->count() > 0)
                            <div class="space-y-2">
                                @foreach($docs as $doc)
                                    <div class="flex items-center justify-between p-3 border border-gray-200 rounded-xl bg-white">
                                        <div class="min-w-0">
                                            <p class="text-sm font-semibold text-gray-900 truncate">{{ $doc->original_name ?? 'Dokumen' }}</p>
                                            <p class="text-xs text-gray-500">{{ number_format(($doc->file_size ?? 0) / 1024, 2) }} KB</p>
                                        </div>

                                        <div class="flex items-center gap-2 shrink-0">
                                            <a href="{{ route('admin.consultations.document', $doc->id) }}?mode=view"
                                               target="_blank" rel="noopener"
                                               class="inline-flex items-center justify-center w-9 h-9 rounded-xl border border-gray-200 hover:bg-gray-50"
                                               title="Lihat">
                                                <svg class="w-4 h-4 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                          d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                            </a>

                                            <a href="{{ route('admin.consultations.document', $doc->id) }}?mode=download"
                                               class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-blue-600 text-white hover:bg-blue-700"
                                               title="Unduh">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                          d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1M12 4v12m0 0l-4-4m4 4l4-4"/>
                                                </svg>
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="p-4 bg-gray-50 border border-gray-100 rounded-xl text-center text-gray-500 text-sm">
                                Tidak ada dokumen lampiran.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- TINDAK LANJUT --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center gap-2">
            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
            </svg>
            <h3 class="font-montserrat font-bold text-gray-900">Tindak Lanjut</h3>
        </div>

        <div class="p-6">
            <form id="consultation-followup-form" action="{{ route('admin.consultations.update', $consultation->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                    <div class="lg:col-span-7 space-y-5">
                        <div class="rounded-2xl border border-gray-100 bg-white p-4">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Update Status Tiket</label>
                            <select name="status"
                                    class="w-full px-3 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-blue-500 focus:border-blue-500 bg-white">
                                <option value="pending" {{ $statusRaw == 'pending' ? 'selected' : '' }}>Belum Diproses</option>
                                <option value="on_progress" {{ in_array($statusRaw, ['on_progress','in_progress']) ? 'selected' : '' }}>Sedang Diproses</option>
                                <option value="completed" {{ in_array($statusRaw, ['completed','selesai']) ? 'selected' : '' }}>Selesai</option>
                                <option value="rejected" {{ in_array($statusRaw, ['rejected','ditolak']) ? 'selected' : '' }}>Ditolak</option>
                            </select>
                        </div>

                        <div class="rounded-2xl border border-gray-100 bg-white p-4">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Diproses Oleh</label>
                            <div class="grid grid-cols-1 gap-3">
                                <select id="diproses_bidang" name="diproses_bidang"
                                        class="w-full px-3 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-blue-500 focus:border-blue-500 bg-white">
                                    <option value="">-- Pilih Bidang/Unit --</option>
                                    @foreach(array_keys($bidangKelompok) as $bidang)
                                        <option value="{{ $bidang }}" {{ ($oldBidang === $bidang) ? 'selected' : '' }}>
                                            {{ $bidang }}
                                        </option>
                                    @endforeach
                                </select>

                                <select id="diproses_kelompok" name="diproses_kelompok"
                                        class="w-full px-3 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-blue-500 focus:border-blue-500 bg-white"
                                        disabled>
                                    <option value="">-- Pilih Kelompok Kerja --</option>
                                </select>

                                <input type="hidden" id="diproses_oleh" name="diproses_oleh" value="{{ $diprosesOlehCombined ?? '' }}">
                            </div>
                        </div>
                    </div>

                    <div class="lg:col-span-5">
                        <div class="rounded-2xl border border-gray-100 bg-white p-4 h-full">
                            <div class="flex items-center justify-between mb-2">
                                <label class="block text-sm font-semibold text-gray-700">Catatan</label>
                                <span class="text-[11px] text-gray-500">Scroll di kanan</span>
                            </div>

                            <textarea name="admin_notes"
                                      class="with-scrollbar w-full px-3 py-3 border border-gray-300 rounded-xl text-sm focus:ring-blue-500 focus:border-blue-500 bg-white placeholder-gray-400 resize-none overflow-y-auto"
                                      style="height: 260px;"
                                      placeholder="Tuliskan catatan kepada pemohon di sini...">{{ $consultation->admin_response ?? $consultation->admin_notes }}</textarea>

                            <div class="mt-4 flex items-center">
                                <input type="checkbox" id="notify_user" name="notify_user" value="1" checked
                                       class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <label for="notify_user" class="ml-2 text-xs text-gray-500">Kirim notifikasi email kepada pemohon</label>
                            </div>

                            <div class="mt-2 flex items-center">
                                <input type="checkbox" id="notify_whatsapp" name="notify_whatsapp" value="1"
                                    class="w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500">
                                <label for="notify_whatsapp" class="ml-2 text-xs text-gray-500">
                                    Kirim notifikasi via WhatsApp
                                </label>
                            </div>

                            <div class="mt-4">
                                <button type="button"
                                        onclick="openSaveModalConsultation()"
                                        class="w-full py-2.5 bg-blue-700 hover:bg-blue-800 text-white font-bold rounded-xl transition shadow-sm text-sm inline-flex items-center justify-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Simpan Perubahan
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>

{{-- Modal Konfirmasi Simpan --}}
<div id="saveModalConsultation" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-50">
    <div class="bg-white w-full max-w-md rounded-2xl shadow-xl p-6 text-center border border-gray-100">
        <div class="mx-auto mb-4 w-16 h-16 flex items-center justify-center rounded-full bg-blue-100">
            <svg class="w-9 h-9 text-blue-600"
                 fill="none"
                 stroke="currentColor"
                 viewBox="0 0 24 24"
                 stroke-width="2.5"
                 stroke-linecap="round"
                 stroke-linejoin="round">
                <path d="M5 13l4 4L19 7"/>
            </svg>
        </div>

        <h2 class="text-lg font-montserrat font-bold text-gray-900">Konfirmasi Perubahan</h2>
        <p class="text-sm text-gray-600 mt-2">Anda yakin ingin menyimpan perubahan?</p>

        <div class="mt-6 flex justify-center gap-3">
            <button type="button"
                    onclick="closeSaveModalConsultation()"
                    class="px-5 py-2 rounded-xl border border-gray-300 text-gray-700 font-semibold hover:bg-gray-100 transition">
                Batal
            </button>

            <button type="button"
                    onclick="submitConsultationFollowup()"
                    class="px-5 py-2 rounded-xl bg-blue-600 text-white font-semibold hover:bg-blue-700 transition inline-flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Ya, Simpan
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Modal save
    function openSaveModalConsultation() {
        const modal = document.getElementById('saveModalConsultation');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
    function closeSaveModalConsultation() {
        const modal = document.getElementById('saveModalConsultation');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
    function submitConsultationFollowup() {
        const form = document.getElementById('consultation-followup-form');
        if (form) form.submit();
    }
    document.getElementById('saveModalConsultation').addEventListener('click', function (e) {
        if (e.target === this) closeSaveModalConsultation();
    });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeSaveModalConsultation();
    });

    // Dependent dropdown Bidang -> Kelompok
    const BIDANG_KELOMPOK = @json($bidangKelompok);

    const bidangSelect = document.getElementById('diproses_bidang');
    const kelompokSelect = document.getElementById('diproses_kelompok');
    const diprosesOlehHidden = document.getElementById('diproses_oleh');

    const preBidang = @json($oldBidang);
    const preKelompok = @json($oldKelompok);

    function setKelompokOptions(bidang, selectedKelompok = null) {
        kelompokSelect.innerHTML = '<option value="">-- Pilih Kelompok Kerja --</option>';

        const items = BIDANG_KELOMPOK[bidang] || [];
        if (!bidang || items.length === 0) {
            kelompokSelect.disabled = true;
            diprosesOlehHidden.value = '';
            return;
        }

        items.forEach((k) => {
            const opt = document.createElement('option');
            opt.value = k;
            opt.textContent = k;
            if (selectedKelompok && selectedKelompok === k) opt.selected = true;
            kelompokSelect.appendChild(opt);
        });

        kelompokSelect.disabled = false;
        syncHidden();
    }

    function syncHidden() {
        const b = bidangSelect.value || '';
        const k = kelompokSelect.value || '';
        diprosesOlehHidden.value = (b && k) ? `${b} - ${k}` : '';
    }

    bidangSelect.addEventListener('change', () => {
        setKelompokOptions(bidangSelect.value, null);
    });
    kelompokSelect.addEventListener('change', syncHidden);

    // init
    if (preBidang) {
        bidangSelect.value = preBidang;
        setKelompokOptions(preBidang, preKelompok || null);
    } else {
        setKelompokOptions('', null);
    }
</script>
@endpush
