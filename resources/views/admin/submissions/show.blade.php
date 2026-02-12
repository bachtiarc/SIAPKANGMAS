{{-- resources/views/admin/submissions/show.blade.php --}}
@extends('layouts.admin')

@section('header_title', 'Detail Pengajuan')
@section('title', 'Detail Permohonan Informasi ' . $submission->ticket_id)

@section('content')
@php
    /**
     * =========================
     * Tentukan pemohon (buat tampilan admin)
     * - Kalau pembuat submission = pegawai (CO ADMIN) => pemohon = $submission->applicant
     * - Kalau pembuat submission = masyarakat_umum => pemohon = $submission->user
     * =========================
     */
    $creator  = $submission->user;
    $userType = $creator->user_type ?? null;

    $pemohon = ($userType === 'pegawai')
        ? ($submission->applicant ?? null)
        : $creator;

    // =========================
    // WhatsApp link generator (pakai data PEMOHON)
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
    $waText = rawurlencode("Halo {$pemohonNameForWa}, kami dari Admin SIAPKANGMAS terkait Pengajuan {$submission->ticket_id}.");
    $waLink = $waPhone ? "https://wa.me/{$waPhone}?text={$waText}" : null;

    // KTP public URL sudah dikirim dari controller show()
    $ktpPublicUrl = $ktpPublicUrl ?? null;

    // =========================
    // Helper tampilan alamat
    // =========================
    $isKelurahan = (bool) ($pemohon->is_kelurahan ?? false);

    $labelDesa = $isKelurahan ? 'Kelurahan' : 'Desa';
    $namaDesa = $pemohon->desa_nama ?? $pemohon->desa ?? null;
    $namaKecamatan = $pemohon->kecamatan_nama ?? $pemohon->kecamatan ?? null;
    $namaKabupaten = $pemohon->kabupaten_nama ?? $pemohon->kabupaten ?? null;
    $provinsi = $pemohon->provinsi ?? 'Jawa Tengah';

    $alamatDetail = $pemohon->alamat_detail ?? $pemohon->address ?? null;
    $nik = $pemohon->nik ?? null;

    // =========================
    // Badge status
    // =========================
    $badgeColor = match($submission->status) {
        'pending' => 'bg-gray-100 text-gray-700',
        'in_progress' => 'bg-yellow-100 text-yellow-800',
        'completed', 'selesai', 'approved' => 'bg-green-100 text-green-800',
        'rejected', 'ditolak' => 'bg-red-100 text-red-800',
        default => 'bg-gray-100 text-gray-800'
    };
    $statusLabel = match($submission->status) {
        'pending' => 'Belum Diproses',
        'in_progress' => 'Sedang Diproses',
        'completed', 'selesai', 'approved' => 'Selesai',
        'rejected', 'ditolak' => 'Ditolak',
        default => ucfirst($submission->status)
    };

    $statusText = fn($st) => match($st) {
        'pending' => 'Pending',
        'in_progress' => 'Sedang Diproses',
        'completed', 'selesai', 'approved' => 'Selesai',
        'rejected', 'ditolak' => 'Ditolak',
        default => ucfirst($st)
    };

    // =========================
    // Data dropdown 2 tingkat (Bidang -> Kelompok)
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

    // preselect (support data lama diproses_oleh = "Bidang - Kelompok")
    $oldBidang = old('diproses_bidang', $submission->diproses_bidang ?? null);
    $oldKelompok = old('diproses_kelompok', $submission->diproses_kelompok ?? null);

    if ((!$oldBidang || !$oldKelompok) && !empty($submission->diproses_oleh) && str_contains($submission->diproses_oleh, ' - ')) {
        [$bTmp, $kTmp] = array_pad(explode(' - ', $submission->diproses_oleh, 2), 2, null);
        $oldBidang = $oldBidang ?: $bTmp;
        $oldKelompok = $oldKelompok ?: $kTmp;
    }

    $diprosesOlehCombined = old('diproses_oleh', $submission->diproses_oleh ?? null);

    // =========================
    // Timeline items (persis foto)
    // =========================
    $histories = collect($submission->statusHistories ?? [])->sortBy('created_at')->values();

    $timelineItems = collect();

    // 1) Tiket dibuat oleh pemohon
    $timelineItems->push([
        'title' => 'Tiket Dibuat oleh Pemohon',
        'time'  => $submission->created_at,
        'note'  => null,
        'dot'   => 'gray', // default
    ]);

    // 2) Tiket diterima sistem (pakai created_at juga agar muncul step ke-2)
    $timelineItems->push([
        'title' => 'Tiket Diterima Sistem',
        'time'  => $submission->created_at,
        'note'  => null,
        'dot'   => 'gray',
    ]);

    // 3+) Status history
    foreach ($histories as $h) {
        $timelineItems->push([
            'title' => "Status diubah menjadi '" . $statusText($h->new_status) . "'",
            'time'  => $h->created_at,
            'note'  => $h->notes ?? null,
            'dot'   => 'blue',
        ]);
    }

    $activeIndex = max(0, $timelineItems->count() - 1);
@endphp

<div class="space-y-6">
    {{-- HEADER --}}
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.submissions.permohonan') }}"
               class="p-2 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>

            <div>
                <div class="flex items-center gap-3 flex-wrap">
                    <h1 class="font-montserrat text-2xl font-bold text-gray-900">
                        Detail Pengajuan {{ $submission->ticket_id }}
                    </h1>

                    <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $badgeColor }}">
                        {{ $statusLabel }}
                    </span>
                </div>

                <div class="flex items-center gap-4 mt-1 text-sm text-gray-500 font-lato flex-wrap">
                    <span>Diajukan pada {{ $submission->created_at->format('d F Y') }}</span>
                    <span>•</span>
                    <span>Layanan : Permohonan Informasi</span>
                    <span>•</span>
                    <span>Kategori : {{ $submission->category->name ?? '-' }}</span>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-2">
            @if($waLink)
                <a href="{{ $waLink }}"
                   target="_blank"
                   rel="noopener"
                   class="px-4 py-2 bg-green-600 text-white text-sm font-semibold rounded-lg hover:bg-green-700 transition flex items-center gap-2">
                    <svg class="w-5 h-5" viewBox="0 0 32 32" fill="currentColor">
                        <path d="M19.11 17.22c-.27-.14-1.6-.79-1.85-.88-.25-.09-.43-.14-.61.14-.18.27-.7.88-.86 1.06-.16.18-.32.2-.59.07-.27-.14-1.14-.42-2.17-1.34-.8-.71-1.34-1.59-1.5-1.86-.16-.27-.02-.42.12-.56.12-.12.27-.32.41-.48.14-.16.18-.27.27-.45.09-.18.05-.34-.02-.48-.07-.14-.61-1.47-.84-2.01-.22-.52-.45-.45-.61-.46h-.52c-.18 0-.48.07-.73.34-.25.27-.96.94-.96 2.29 0 1.35.99 2.66 1.12 2.84.14.18 1.95 2.98 4.73 4.18.66.29 1.18.46 1.58.59.66.21 1.26.18 1.74.11.53-.08 1.6-.65 1.83-1.28.23-.63.23-1.17.16-1.28-.07-.11-.25-.18-.52-.32z"/>
                        <path d="M16.02 3C8.86 3 3.05 8.81 3.05 15.97c0 2.28.6 4.51 1.75 6.48L3 29l6.73-1.76a12.9 12.9 0 0 0 6.29 1.61h.01c7.16 0 12.97-5.81 12.97-12.97C28.99 8.81 23.18 3 16.02 3zm0 23.33h-.01c-2.02 0-4-.54-5.74-1.55l-.41-.24-3.99 1.04 1.07-3.89-.26-.4a10.77 10.77 0 0 1-1.67-5.75c0-5.96 4.85-10.81 10.81-10.81 5.96 0 10.81 4.85 10.81 10.81 0 5.96-4.85 10.81-10.81 10.81z"/>
                    </svg>
                    Chat WA
                </a>
            @endif

            <a href="{{ route('admin.submissions.pdf', $submission->id) }}"
               class="px-4 py-2 bg-orange-500 text-white text-sm font-semibold rounded-lg hover:bg-orange-600 transition flex items-center gap-2">
                Unduh PDF
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 p-4" role="alert">
            <p class="text-green-700 font-medium">{{ session('success') }}</p>
        </div>
    @endif

    {{-- =========================
         RIWAYAT AKTIVITAS (PERSIS FOTO - HORIZONTAL TIMELINE)
    ========================= --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center gap-2">
            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <h3 class="font-montserrat font-bold text-gray-900">Riwayat Aktivitas</h3>
        </div>

        <div class="p-6">
            {{-- container bisa scroll kalau layar sempit --}}
            <div class="overflow-x-auto">
                <div class="min-w-[820px]">
                    {{-- Titles row --}}
                    <div class="grid" style="grid-template-columns: repeat({{ $timelineItems->count() }}, minmax(0, 1fr));">
                        @foreach($timelineItems as $i => $it)
                            <div class="text-center px-2">
                                <p class="text-sm font-bold text-gray-900">
                                    {{ $it['title'] }}
                                </p>
                            </div>
                        @endforeach
                    </div>

                    {{-- Dots + line row --}}
                    <div class="relative mt-4">
                        <div class="absolute left-0 right-0 top-1/2 -translate-y-1/2 h-[2px] bg-gray-200"></div>

                        <div class="grid items-center" style="grid-template-columns: repeat({{ $timelineItems->count() }}, minmax(0, 1fr));">
                            @foreach($timelineItems as $i => $it)
                                @php
                                    $isActive = $i === $activeIndex;
                                    $dotClass = $isActive ? 'bg-blue-600' : 'bg-gray-300';
                                @endphp

                                <div class="flex justify-center relative">
                                    <span class="w-3 h-3 rounded-full {{ $dotClass }}"></span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Times row --}}
                    <div class="grid mt-4" style="grid-template-columns: repeat({{ $timelineItems->count() }}, minmax(0, 1fr));">
                        @foreach($timelineItems as $i => $it)
                            <div class="text-center px-2">
                                <p class="text-[11px] text-gray-500">
                                    {{ optional($it['time'])->format('d M Y, H:i') }} WIB
                                </p>
                            </div>
                        @endforeach
                    </div>

                    {{-- Notes row (hanya item aktif) --}}
                    <div class="grid mt-4" style="grid-template-columns: repeat({{ $timelineItems->count() }}, minmax(0, 1fr));">
                        @foreach($timelineItems as $i => $it)
                            <div class="px-2">
                                @if($i === $activeIndex && !empty($it['note']))
                                    <div class="mx-auto max-w-[240px] bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-[11px] text-gray-600 text-center">
                                        “{{ $it['note'] }}”
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- =========================
         DATA PEMOHON (FULL WIDTH - TIDAK DISEJAJARKAN)
    ========================= --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center gap-2">
            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
            <h3 class="font-montserrat font-bold text-gray-900">Data Pemohon</h3>
        </div>

        <div class="p-6">
            @if(!$pemohon)
                <div class="p-4 bg-red-50 border border-red-100 rounded-lg text-sm text-red-700">
                    Data pemohon tidak ditemukan.
                </div>
            @else
                <div class="font-lato">
                    <p class="text-sm text-gray-600 mb-5">
                        Jenis Pelapor :
                        <span class="font-semibold text-gray-900">
                            {{ $userType ? ucwords(str_replace('_', ' ', $userType)) : '-' }}
                        </span>
                    </p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Nama Lengkap</p>
                            <p class="font-bold text-gray-900">{{ $pemohon->nama_lengkap ?? $pemohon->name ?? '-' }}</p>
                        </div>

                        <div>
                            <p class="text-xs text-gray-500 mb-1">Email</p>
                            <p class="font-bold text-gray-900">{{ $pemohon->email ?? '-' }}</p>
                        </div>

                        <div>
                            <p class="text-xs text-gray-500 mb-1">NIK</p>
                            <p class="font-bold text-gray-900">{{ $nik ?? '-' }}</p>
                        </div>

                        <div>
                            <p class="text-xs text-gray-500 mb-1">Nomor Telepon</p>
                            @if($waLink)
                                <a href="{{ $waLink }}" target="_blank" rel="noopener" class="font-bold text-blue-600 hover:underline">
                                    wa.me/{{ $waPhone ?? '-' }}
                                </a>
                            @else
                                <p class="font-bold text-gray-900">{{ $pemohon->phone ?? '-' }}</p>
                            @endif
                        </div>

                        <div class="md:col-span-1">
                            <p class="text-xs text-gray-500 mb-1">Alamat Lengkap</p>
                            <p class="font-bold text-gray-900">{{ $alamatDetail ?: '-' }}</p>
                        </div>

                        <div class="md:col-span-1">
                            <p class="text-xs text-gray-500 mb-1">Foto KTP</p>

                            @if($ktpPublicUrl)
                                <a href="{{ $ktpPublicUrl }}" target="_blank" rel="noopener" class="inline-block">
                                    <img src="{{ $ktpPublicUrl }}" alt="Foto KTP"
                                         class="w-32 h-20 object-cover rounded-lg border border-gray-200">
                                </a>
                            @else
                                <div class="w-32 h-20 rounded-lg border border-gray-200 flex items-center justify-center text-gray-400">
                                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M3 5h18v14H3V5zm4 10l3-3 4 4 3-3 3 3"/>
                                    </svg>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- =========================
         ISI PENGAJUAN
    ========================= --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center gap-2">
            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <h3 class="font-montserrat font-bold text-gray-900">Isi Pengajuan</h3>
        </div>

        <div class="p-6 space-y-6">
            <div>
                <h4 class="font-bold text-gray-900 text-sm mb-2">Judul</h4>
                <p class="text-gray-700 font-lato">{{ $submission->title }}</p>
            </div>

            <div>
                <h4 class="font-bold text-gray-900 text-sm mb-2">Deskripsi Lengkap</h4>
                <div class="p-4 bg-white rounded-lg border border-gray-200 min-h-[240px]">
                    <p class="text-gray-700 font-lato text-sm leading-relaxed whitespace-pre-line">
                        {{ $submission->description ?: '-' }}
                    </p>
                </div>
            </div>

            <div>
                <h4 class="font-bold text-gray-900 text-sm mb-3">Dokumen Pendukung</h4>

                @if($submission->documents && $submission->documents->count() > 0)
                    <div class="space-y-2">
                        @foreach($submission->documents as $doc)
                            <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg bg-white">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="w-9 h-9 bg-blue-50 rounded-lg flex items-center justify-center text-blue-600 shrink-0">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </div>

                                    <div class="min-w-0">
                                        <p class="text-sm font-semibold text-gray-900 truncate">{{ $doc->original_name }}</p>
                                        <p class="text-xs text-gray-500">
                                            {{ number_format(($doc->file_size ?? 0) / 1024, 2) }} KB
                                        </p>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2 shrink-0">
                                    <a href="{{ route('admin.submissions.document', $doc->id) }}?mode=view"
                                       target="_blank" rel="noopener"
                                       class="px-3 py-1.5 rounded-lg text-xs font-semibold border border-gray-200 hover:bg-gray-50">
                                        Lihat
                                    </a>
                                    <a href="{{ route('admin.submissions.document', $doc->id) }}?mode=download"
                                       class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-blue-600 text-white hover:bg-blue-700">
                                        Unduh
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="p-4 bg-gray-50 border border-gray-100 rounded-lg text-center text-gray-500 text-sm">
                        Tidak ada dokumen lampiran.
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- =========================
         TINDAK LANJUT (STATUS + DIPROSES OLEH 2 KOLOM SEPERTI FOTO)
    ========================= --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center gap-2">
            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
            </svg>
            <h3 class="font-montserrat font-bold text-gray-900">Tindak Lanjut</h3>
        </div>

        <div class="p-6">
            <form id="submission-followup-form" action="{{ route('admin.submissions.update', $submission->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- STATUS --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Update Status Tiket</label>
                        <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 bg-white">
                            <option value="pending" {{ $submission->status == 'pending' ? 'selected' : '' }}>Belum Diproses</option>
                            <option value="in_progress" {{ $submission->status == 'in_progress' ? 'selected' : '' }}>Sedang Diproses</option>
                            <option value="completed" {{ $submission->status == 'completed' ? 'selected' : '' }}>Selesai</option>
                            <option value="rejected" {{ $submission->status == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                        </select>
                    </div>

                    {{-- DIPROSES OLEH (2 tingkat) --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Diproses Oleh</label>

                        <div class="grid grid-cols-1 gap-3">
                            <select id="diproses_bidang" name="diproses_bidang"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 bg-white">
                                <option value="">-- Pilih Bidang/Unit --</option>
                                @foreach(array_keys($bidangKelompok) as $bidang)
                                    <option value="{{ $bidang }}" {{ ($oldBidang === $bidang) ? 'selected' : '' }}>
                                        {{ $bidang }}
                                    </option>
                                @endforeach
                            </select>

                            <select id="diproses_kelompok" name="diproses_kelompok"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 bg-white"
                                    disabled>
                                <option value="">-- Pilih Kelompok Kerja --</option>
                            </select>

                            {{-- simpan gabungan buat kompatibilitas --}}
                            <input type="hidden" id="diproses_oleh" name="diproses_oleh" value="{{ $diprosesOlehCombined ?? '' }}">
                        </div>
                    </div>
                </div>

                <div class="mt-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Catatan</label>
                    <textarea name="admin_notes"
                              rows="8"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 bg-white placeholder-gray-400 min-h-[220px] resize-y"
                              placeholder="Tuliskan catatan kepada pemohon di sini...">{{ $submission->admin_notes }}</textarea>
                </div>

                <div class="mt-4 flex items-center">
                    <input type="checkbox" id="notify_user" name="notify_user" value="1" checked
                           class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                    <label for="notify_user" class="ml-2 text-xs text-gray-500">Kirim notifikasi email kepada pemohon</label>
                </div>

                <div class="mt-4">
                    <button type="button"
                            onclick="openSaveModalSubmission()"
                            class="w-full md:w-72 py-2.5 bg-blue-700 hover:bg-blue-800 text-white font-bold rounded-lg transition shadow-sm text-sm">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- =========================
     Modal Konfirmasi Simpan
========================= --}}
<div id="saveModalSubmission" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-50">
    <div class="bg-white w-full max-w-md rounded-2xl shadow-xl p-6 text-center">
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
                    onclick="closeSaveModalSubmission()"
                    class="px-5 py-2 rounded-xl border border-gray-300 text-gray-700 font-semibold hover:bg-gray-100 transition">
                Batal
            </button>

            <button type="button"
                    onclick="submitSubmissionFollowup()"
                    class="px-5 py-2 rounded-xl bg-blue-600 text-white font-semibold hover:bg-blue-700 transition">
                Ya, Simpan
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // =========================
    // Modal save
    // =========================
    function openSaveModalSubmission() {
        const modal = document.getElementById('saveModalSubmission');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
    function closeSaveModalSubmission() {
        const modal = document.getElementById('saveModalSubmission');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
    function submitSubmissionFollowup() {
        const form = document.getElementById('submission-followup-form');
        if (form) form.submit();
    }
    document.getElementById('saveModalSubmission').addEventListener('click', function (e) {
        if (e.target === this) closeSaveModalSubmission();
    });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeSaveModalSubmission();
    });

    // =========================
    // Dependent dropdown Bidang -> Kelompok
    // =========================
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

    // init (prefill)
    if (preBidang) {
        bidangSelect.value = preBidang;
        setKelompokOptions(preBidang, preKelompok || null);
    } else {
        setKelompokOptions('', null);
    }
</script>
@endpush