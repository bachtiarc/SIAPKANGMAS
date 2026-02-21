{{-- resources/views/admin/submissions/show.blade.php --}}
@extends('layouts.admin')

@section('header_title', 'Detail Permohonan Informasi')
@section('title', 'Detail Permohonan Informasi ' . ($submission->ticket_id ?? '-'))

@section('content')
@php
    $creator  = $submission->user;
    $userType = $creator->user_type ?? null;

    $pemohon = ($userType === 'pegawai')
        ? ($submission->applicant ?? null)
        : $creator;

    $ticketId = $submission->ticket_id ?? '-';

    $rawPhone    = $pemohon->phone ?? ($pemohon->phone_number ?? '') ?? '';
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

    $ktpPublicUrl = $ktpPublicUrl ?? null;

    $statusRaw = strtolower((string)($submission->status ?? 'pending'));

    $badgeColor = match(true) {
        in_array($statusRaw, ['pending', 'belum diproses'])                                     => 'bg-slate-100 text-slate-600 ring-1 ring-slate-200',
        in_array($statusRaw, ['on_progress','in_progress','diproses','sedang diproses'])        => 'bg-amber-50 text-amber-700 ring-1 ring-amber-200',
        in_array($statusRaw, ['completed','selesai','approved'])                                => 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200',
        in_array($statusRaw, ['rejected','ditolak'])                                            => 'bg-red-50 text-red-700 ring-1 ring-red-200',
        default                                                                                  => 'bg-gray-100 text-gray-700 ring-1 ring-gray-200',
    };

    $statusDot = match(true) {
        in_array($statusRaw, ['pending', 'belum diproses'])                                     => 'bg-slate-400',
        in_array($statusRaw, ['on_progress','in_progress','diproses','sedang diproses'])        => 'bg-amber-400',
        in_array($statusRaw, ['completed','selesai','approved'])                                => 'bg-emerald-500',
        in_array($statusRaw, ['rejected','ditolak'])                                            => 'bg-red-500',
        default                                                                                  => 'bg-gray-400',
    };

    $statusLabel = match(true) {
        in_array($statusRaw, ['pending', 'belum diproses'])                                     => 'Belum Diproses',
        in_array($statusRaw, ['on_progress','in_progress','diproses','sedang diproses'])        => 'Sedang Diproses',
        in_array($statusRaw, ['completed','selesai','approved'])                                => 'Selesai',
        in_array($statusRaw, ['rejected','ditolak'])                                            => 'Ditolak',
        default                                                                                  => ucfirst($statusRaw ?: '-'),
    };

    $statusText = fn($st) => match((string)$st) {
        'pending', 'belum diproses'                                       => 'Belum Diproses',
        'on_progress', 'in_progress', 'diproses', 'sedang diproses'       => 'Sedang Diproses',
        'completed', 'selesai', 'approved'                                => 'Selesai',
        'rejected', 'ditolak'                                             => 'Ditolak',
        default                                                            => ucfirst((string)$st),
    };

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
        'Balai Industri Logam dan Kayu (BILK)' => [
            'Kelompok Kerja Pelayanan Jasa Keteknikan,',
            'Kelompok Kerja Penerapan dan Rekayasa',
            'Kelompok Jabatan Fungsional',
        ],
        'Balai Pengujian dan Sertifikasi Mutu Barang (BPSMB) Surakarta' => [
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
        'Balai Industri Kreatif Digital dan Kemasan (BIKDK)' => [
            'Kelompok Kerja Industri Kreatif Digital',
            'Kelompok Kerja Pengembangan Kemasan',
            'Kelompok Jabatan Fungsional',
        ],
    ];

    $oldBidang   = old('diproses_bidang', $submission->diproses_bidang ?? null);
    $oldKelompok = old('diproses_kelompok', $submission->diproses_kelompok ?? null);

    if ((!$oldBidang || !$oldKelompok) && !empty($submission->diproses_oleh) && str_contains($submission->diproses_oleh, ' - ')) {
        [$bTmp, $kTmp] = array_pad(explode(' - ', $submission->diproses_oleh, 2), 2, null);
        $oldBidang   = $oldBidang ?: $bTmp;
        $oldKelompok = $oldKelompok ?: $kTmp;
    }

    $diprosesOlehCombined = old('diproses_oleh', $submission->diproses_oleh ?? null);

    $histories = collect($submission->statusHistories ?? [])->sortBy('created_at')->values();

    $timelineItems = collect();
    $timelineItems->push(['title' => 'Tiket Dibuat oleh Pemohon', 'time' => $submission->created_at, 'note' => null]);
    $timelineItems->push(['title' => 'Tiket Diterima Sistem', 'time' => $submission->created_at, 'note' => null]);

    foreach ($histories as $h) {
        $timelineItems->push([
            'title' => "Status diubah menjadi '" . $statusText($h->new_status) . "'",
            'time'  => $h->created_at,
            'note'  => $h->notes ?? null,
        ]);
    }

    $activeIndex = max(0, $timelineItems->count() - 1);

    $jenisPelapor = $userType ? ucwords(str_replace('_',' ', $userType)) : '-';
    if ($userType === 'masyarakat_umum') $jenisPelapor = 'Masyarakat Umum';
    if ($userType === 'pegawai') $jenisPelapor = 'Pegawai';

    $nik          = $pemohon->nik ?? null;
    $alamatDetail = $pemohon->alamat_detail ?? $pemohon->address ?? null;
    $provName     = $pemohon->provinsi ?? $pemohon->provinsi_nama ?? null;
    $kabName      = $pemohon->kabupaten ?? $pemohon->kabupaten_nama ?? null;
    $kecName      = $pemohon->kecamatan ?? $pemohon->kecamatan_nama ?? null;
    $desaName     = $pemohon->desa ?? $pemohon->desa_nama ?? null;
    $pemohonPekerjaan = $pemohon->pekerjaan ?? null;

    $judul            = $submission->title ?? '-';
    $deskripsi        = $submission->description ?? '-';
    $tujuanPermohonan = $submission->tujuan_permohonan ?? null;

    $caraPenyampaian = $submission->cara_penyampaian ?? null;
    $opsiDatangRaw   = $submission->datang_langsung_opsi ?? [];

    if (is_string($opsiDatangRaw)) {
        $decoded    = json_decode($opsiDatangRaw, true);
        $opsiDatang = is_array($decoded) ? $decoded : [];
    } elseif (is_array($opsiDatangRaw)) {
        $opsiDatang = $opsiDatangRaw;
    } elseif ($opsiDatangRaw instanceof \Illuminate\Support\Collection) {
        $opsiDatang = $opsiDatangRaw->toArray();
    } else {
        $opsiDatang = [];
    }

    $caraPenyampaianLabel = match($caraPenyampaian) {
        'datang_langsung' => 'Datang Langsung',
        'online'          => 'Online',
        default           => '-',
    };

    $opsiDatangLabel = collect($opsiDatang)->map(function ($v) {
        return match($v) {
            'flashdisk' => 'Flashdisk',
            'cetak'     => 'Cetak',
            default     => ucfirst((string)$v),
        };
    })->filter()->values()->all();

    $docs = $submission->documents ?? collect();
@endphp

<style>
    .scrollbar-thin::-webkit-scrollbar { width: 6px; }
    .scrollbar-thin::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 999px; }
    .scrollbar-thin::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 999px; }
    .scrollbar-thin::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    .scrollbar-thin { scrollbar-color: #cbd5e1 #f1f5f9; scrollbar-width: thin; }

    .field-label {
        font-size: 0.6875rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: #94a3b8;
        margin-bottom: 0.375rem;
    }
    .field-value {
        font-size: 0.9375rem;
        font-weight: 600;
        color: #0f172a;
        line-height: 1.4;
    }
    .card {
        background: #ffffff;
        border: 1px solid #e8edf5;
        border-radius: 16px;
        transition: box-shadow 0.2s ease;
    }
    .card:hover { box-shadow: 0 4px 20px rgba(0,0,0,0.05); }
    .section-card {
        background: #ffffff;
        border: 1px solid #e8edf5;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 1px 4px rgba(0,0,0,0.04);
    }
    .section-header {
        padding: 1.125rem 1.5rem;
        border-bottom: 1px solid #f0f4f8;
        background: linear-gradient(to right, #f8fafc, #f1f5f9);
        display: flex;
        align-items: center;
        gap: 0.625rem;
    }
    .section-header-icon {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        background: #eff6ff;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .section-title {
        font-family: var(--font-montserrat, 'Montserrat', sans-serif);
        font-weight: 700;
        font-size: 0.9375rem;
        color: #1e293b;
    }
    .input-styled {
        width: 100%;
        padding: 0.625rem 0.875rem;
        border: 1.5px solid #e2e8f0;
        border-radius: 10px;
        font-size: 0.875rem;
        color: #1e293b;
        background: #ffffff;
        transition: border-color 0.15s, box-shadow 0.15s;
        appearance: none;
    }
    .input-styled:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59,130,246,0.12);
    }
    .btn-primary {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 0.625rem 1.25rem;
        background: #1d4ed8;
        color: #ffffff;
        font-weight: 700;
        font-size: 0.875rem;
        border-radius: 10px;
        border: none;
        cursor: pointer;
        transition: background 0.15s, box-shadow 0.15s;
    }
    .btn-primary:hover {
        background: #1e40af;
        box-shadow: 0 4px 12px rgba(29,78,216,0.3);
    }
    .btn-outline {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        background: #ffffff;
        color: #374151;
        font-weight: 600;
        font-size: 0.8125rem;
        border-radius: 9px;
        border: 1.5px solid #e2e8f0;
        cursor: pointer;
        transition: background 0.15s, border-color 0.15s;
    }
    .btn-outline:hover { background: #f8fafc; border-color: #cbd5e1; }
    .action-icon-btn {
        width: 36px;
        height: 36px;
        border-radius: 9px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1.5px solid #e2e8f0;
        background: #ffffff;
        cursor: pointer;
        transition: background 0.15s, border-color 0.15s;
        flex-shrink: 0;
    }
    .action-icon-btn:hover { background: #f1f5f9; border-color: #cbd5e1; }
    .timeline-note {
        background: linear-gradient(135deg, #eff6ff, #f0f9ff);
        border: 1px solid #bfdbfe;
        border-radius: 10px;
        padding: 0.5rem 0.75rem;
        font-size: 0.6875rem;
        color: #1d4ed8;
        font-style: italic;
        line-height: 1.5;
    }
    .timeline-empty {
        height: 40px;
        background: #f8fafc;
        border: 1px solid #f1f5f9;
        border-radius: 10px;
    }
    .address-field {
        width: 100%;
        padding: 0.625rem 0.875rem;
        border: 1.5px solid #e2e8f0;
        border-radius: 10px;
        background: #f8fafc;
        font-size: 0.875rem;
        font-weight: 600;
        color: #1e293b;
    }
</style>

<div class="space-y-5">

    {{-- TOP BAR --}}
    <div class="section-card">
        <div class="p-5 md:p-6">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5">
                <div class="flex items-start gap-4">
                    <a href="{{ url()->previous() }}"
                       class="shrink-0 w-10 h-10 flex items-center justify-center rounded-xl border-2 border-gray-200 bg-white hover:bg-gray-50 hover:border-gray-300 transition"
                       title="Kembali">
                        <svg class="w-4.5 h-4.5 text-gray-600" style="width:18px;height:18px" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        <span class="sr-only">Kembali</span>
                    </a>

                    <div class="min-w-0">
                        <div class="flex items-center gap-3 flex-wrap">
                            <h1 class="font-montserrat text-xl font-bold text-gray-900">
                                Detail Permohonan
                                <span class="text-blue-700">{{ $ticketId }}</span>
                            </h1>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold {{ $badgeColor }}">
                                {{ $statusLabel }}
                            </span>
                        </div>

                        <div class="flex items-center gap-4 mt-2 text-xs text-gray-500 flex-wrap">
                            <span class="inline-flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                Diajukan {{ optional($submission->created_at)->format('d F Y') }}
                            </span>
                            <span class="w-1 h-1 rounded-full bg-gray-300"></span>
                            <span class="inline-flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6M7 4h10a2 2 0 012 2v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6a2 2 0 012-2z"/>
                                </svg>
                                Permohonan Informasi
                            </span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    @if($waLink)
                        <a href="{{ $waLink }}"
                           target="_blank" rel="noopener"
                           class="w-10 h-10 rounded-xl flex items-center justify-center bg-emerald-500 text-white hover:bg-emerald-600 transition shadow-sm shadow-emerald-200"
                           title="Chat WhatsApp">
                            <svg class="w-5 h-5" viewBox="0 0 32 32" fill="currentColor">
                                <path d="M19.11 17.22c-.27-.14-1.6-.79-1.85-.88-.25-.09-.43-.14-.61.14-.18.27-.7.88-.86 1.06-.16.18-.32.2-.59.07-.27-.14-1.14-.42-2.17-1.34-.8-.71-1.34-1.59-1.5-1.86-.16-.27-.02-.42.12-.56.12-.12.27-.32.41-.48.14-.16.18-.27.27-.45.09-.18.05-.34-.02-.48-.07-.14-.61-1.47-.84-2.01-.22-.52-.45-.45-.61-.46h-.52c-.18 0-.48.07-.73.34-.25.27-.96.94-.96 2.29 0 1.35.99 2.66 1.12 2.84.14.18 1.95 2.98 4.73 4.18.66.29 1.18.46 1.58.59.66.21 1.26.18 1.74.11.53-.08 1.6-.65 1.83-1.28.23-.63.23-1.17.16-1.28-.07-.11-.25-.18-.52-.32z"/>
                                <path d="M16.02 3C8.86 3 3.05 8.81 3.05 15.97c0 2.28.6 4.51 1.75 6.48L3 29l6.73-1.76a12.9 12.9 0 0 0 6.29 1.61h.01c7.16 0 12.97-5.81 12.97-12.97C28.99 8.81 23.18 3 16.02 3zm0 23.33h-.01c-2.02 0-4-.54-5.74-1.55l-.41-.24-3.99 1.04 1.07-3.89-.26-.4a10.77 10.77 0 0 1-1.67-5.75c0-5.96 4.85-10.81 10.81-10.81 5.96 0 10.81 4.85 10.81 10.81 0 5.96-4.85 10.81-10.81 10.81z"/>
                            </svg>
                            <span class="sr-only">Chat WA</span>
                        </a>
                    @endif

                    <a href="{{ route('admin.submissions.pdf', $submission->id) }}"
                       class="w-10 h-10 rounded-xl flex items-center justify-center bg-orange-500 text-white hover:bg-orange-600 transition shadow-sm shadow-orange-200"
                       title="Unduh PDF">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        <span class="sr-only">Unduh PDF</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if(session('wa_link'))
        <div class="flex items-start gap-3 p-4 bg-emerald-50 border border-emerald-200 rounded-2xl">
            <div class="w-9 h-9 rounded-xl bg-emerald-100 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
            </div>
            <div>
                <p class="text-sm font-semibold text-emerald-800">Notifikasi WhatsApp siap dikirim</p>
                <a href="{{ session('wa_link') }}" target="_blank" rel="noopener"
                   class="mt-2 inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-600 text-white text-sm font-bold hover:bg-emerald-700 transition">
                    <svg class="w-4 h-4" viewBox="0 0 32 32" fill="currentColor">
                        <path d="M19.11 17.22c-.27-.14-1.6-.79-1.85-.88-.25-.09-.43-.14-.61.14-.18.27-.7.88-.86 1.06-.16.18-.32.2-.59.07-.27-.14-1.14-.42-2.17-1.34-.8-.71-1.34-1.59-1.5-1.86-.16-.27-.02-.42.12-.56.12-.12.27-.32.41-.48.14-.16.18-.27.27-.45.09-.18.05-.34-.02-.48-.07-.14-.61-1.47-.84-2.01-.22-.52-.45-.45-.61-.46h-.52c-.18 0-.48.07-.73.34-.25.27-.96.94-.96 2.29 0 1.35.99 2.66 1.12 2.84.14.18 1.95 2.98 4.73 4.18.66.29 1.18.46 1.58.59.66.21 1.26.18 1.74.11.53-.08 1.6-.65 1.83-1.28.23-.63.23-1.17.16-1.28-.07-.11-.25-.18-.52-.32z"/><path d="M16.02 3C8.86 3 3.05 8.81 3.05 15.97c0 2.28.6 4.51 1.75 6.48L3 29l6.73-1.76a12.9 12.9 0 0 0 6.29 1.61h.01c7.16 0 12.97-5.81 12.97-12.97C28.99 8.81 23.18 3 16.02 3zm0 23.33h-.01c-2.02 0-4-.54-5.74-1.55l-.41-.24-3.99 1.04 1.07-3.89-.26-.4a10.77 10.77 0 0 1-1.67-5.75c0-5.96 4.85-10.81 10.81-10.81 5.96 0 10.81 4.85 10.81 10.81 0 5.96-4.85 10.81-10.81 10.81z"/>
                    </svg>
                    Kirim via WhatsApp
                </a>
            </div>
        </div>
    @endif

    @if(session('success'))
        <div class="flex items-center gap-3 p-4 bg-emerald-50 border border-emerald-200 rounded-2xl">
            <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center shrink-0">
                <svg class="w-4.5 h-4.5 text-emerald-700" style="width:18px;height:18px" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <p class="text-sm font-semibold text-emerald-800">{{ session('success') }}</p>
        </div>
    @endif

    {{-- RIWAYAT AKTIVITAS --}}
    <div class="section-card">
        <div class="section-header justify-between">
            <div class="flex items-center gap-2.5">
                <div class="section-header-icon">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <span class="section-title">Riwayat Aktivitas</span>
            </div>
            <span class="text-xs font-semibold text-blue-600 bg-blue-50 px-2.5 py-1 rounded-lg">
                {{ $activeIndex + 1 }} / {{ $timelineItems->count() }} langkah
            </span>
        </div>

        <div class="p-6">
            <div class="overflow-x-auto">
                <div class="min-w-[860px]">
                    @php
                        $totalSteps = $timelineItems->count();
                        $defaultNotes = [
                            0 => 'Permohonan informasi telah diterima oleh sistem.',
                            1 => 'Tiket berhasil diterima dan terdaftar dalam sistem.',
                        ];
                        $defaultNoteActive = 'Dokumen sedang dikaji dan diteruskan ke bidang yang berwenang untuk ditindaklanjuti.';
                    @endphp

                    <div class="relative">
                        <div class="flex justify-between items-start">
                            @foreach($timelineItems as $i => $it)
                                @php
                                    $isActive = $i === $activeIndex;
                                    $isDone   = $i < $activeIndex;
                                    $isFuture = $i > $activeIndex;
                                    $noteVal  = trim((string)($it['note'] ?? ''));
                                    if (empty($noteVal)) {
                                        if (isset($defaultNotes[$i])) {
                                            $noteVal = $defaultNotes[$i];
                                        } elseif ($isActive) {
                                            $noteVal = $defaultNoteActive;
                                        }
                                    }
                                    $hasNote = !empty($noteVal);
                                @endphp

                                <div class="flex-1 flex flex-col items-center relative">
                                    <p class="font-semibold text-xs leading-snug text-center px-2
                                        {{ $isFuture ? 'text-gray-400' : 'text-gray-800' }}">
                                        {{ $it['title'] }}
                                    </p>

                                    <div class="relative flex items-center justify-center w-full mt-3" style="height:22px">
                                        @if($i > 0)
                                            <div class="absolute left-0 right-1/2 top-1/2 -translate-y-1/2 pr-3">
                                                <div class="h-0.5 w-full rounded-full {{ ($isDone || $isActive) ? 'bg-blue-500' : 'bg-gray-200' }}"></div>
                                            </div>
                                        @endif
                                        @if($i < $totalSteps - 1)
                                            <div class="absolute left-1/2 right-0 top-1/2 -translate-y-1/2 pl-3">
                                                <div class="h-0.5 w-full rounded-full {{ $isDone ? 'bg-blue-500' : 'bg-gray-200' }}"></div>
                                            </div>
                                        @endif

                                        <div class="relative z-10">
                                            @if($isDone)
                                                <div class="w-6 h-6 rounded-full bg-blue-600 flex items-center justify-center shadow-md shadow-blue-200">
                                                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                                    </svg>
                                                </div>
                                            @elseif($isActive)
                                                <div class="w-6 h-6 rounded-full bg-blue-600 ring-4 ring-blue-100 shadow-md shadow-blue-200"></div>
                                            @else
                                                <div class="w-6 h-6 rounded-full bg-gray-100 border-2 border-gray-300"></div>
                                            @endif
                                        </div>
                                    </div>

                                    <p class="mt-2 text-[10.5px] text-center font-medium
                                        {{ $isFuture ? 'text-gray-400' : 'text-gray-500' }}">
                                        @if($it['time'])
                                            {{ optional($it['time'])->format('d M Y') }}<br>
                                            <span class="font-bold text-gray-600">{{ optional($it['time'])->format('H:i') }}</span> WIB
                                        @else
                                            —
                                        @endif
                                    </p>

                                    <div class="mt-2.5 w-full px-1.5">
                                        @if(($isDone || $isActive) && $hasNote)
                                            <div class="timeline-note">{{ $noteVal }}</div>
                                        @else
                                            <div class="timeline-empty"></div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- DATA PEMOHON --}}
    <div class="section-card">
        <div class="section-header">
            <div class="section-header-icon">
                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
            <span class="section-title">Data Pemohon</span>
        </div>

        <div class="p-6">
            @if(!$pemohon)
                <div class="p-4 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700 font-medium">
                    Data pemohon tidak ditemukan.
                </div>
            @else
                <div class="mb-5">
                    <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-gray-500 bg-gray-100 px-3 py-1.5 rounded-lg">
                        <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/>
                        </svg>
                        {{ $jenisPelapor }}
                    </span>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">
                    <div class="lg:col-span-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                            <div class="card p-4">
                                <p class="field-label">Nama Lengkap</p>
                                <p class="field-value">{{ $pemohon->nama_lengkap ?? $pemohon->name ?? '-' }}</p>
                            </div>

                            <div class="card p-4">
                                <p class="field-label">Email</p>
                                <p class="field-value break-all">{{ $pemohon->email ?? '-' }}</p>
                            </div>

                            <div class="card p-4">
                                <p class="field-label">NIK</p>
                                <p class="field-value font-mono tracking-wide">{{ $nik ?? '-' }}</p>
                            </div>

                            <div class="card p-4">
                                <p class="field-label">Nomor Telepon</p>
                                @if($waLink)
                                    <a href="{{ $waLink }}" target="_blank" rel="noopener"
                                       class="inline-flex items-center gap-2 font-bold text-sm text-emerald-600 hover:text-emerald-700 hover:underline">
                                        <svg class="w-4 h-4" viewBox="0 0 32 32" fill="currentColor">
                                            <path d="M19.11 17.22c-.27-.14-1.6-.79-1.85-.88-.25-.09-.43-.14-.61.14-.18.27-.7.88-.86 1.06-.16.18-.32.2-.59.07-.27-.14-1.14-.42-2.17-1.34-.8-.71-1.34-1.59-1.5-1.86-.16-.27-.02-.42.12-.56.12-.12.27-.32.41-.48.14-.16.18-.27.27-.45.09-.18.05-.34-.02-.48-.07-.14-.61-1.47-.84-2.01-.22-.52-.45-.45-.61-.46h-.52c-.18 0-.48.07-.73.34-.25.27-.96.94-.96 2.29 0 1.35.99 2.66 1.12 2.84.14.18 1.95 2.98 4.73 4.18.66.29 1.18.46 1.58.59.66.21 1.26.18 1.74.11.53-.08 1.6-.65 1.83-1.28.23-.63.23-1.17.16-1.28-.07-.11-.25-.18-.52-.32z"/>
                                            <path d="M16.02 3C8.86 3 3.05 8.81 3.05 15.97c0 2.28.6 4.51 1.75 6.48L3 29l6.73-1.76a12.9 12.9 0 0 0 6.29 1.61h.01c7.16 0 12.97-5.81 12.97-12.97C28.99 8.81 23.18 3 16.02 3zm0 23.33h-.01c-2.02 0-4-.54-5.74-1.55l-.41-.24-3.99 1.04 1.07-3.89-.26-.4a10.77 10.77 0 0 1-1.67-5.75c0-5.96 4.85-10.81 10.81-10.81 5.96 0 10.81 4.85 10.81 10.81 0 5.96-4.85 10.81-10.81 10.81z"/>
                                        </svg>
                                        +{{ $waPhone }}
                                    </a>
                                @else
                                    <p class="field-value">{{ $pemohon->phone ?? '-' }}</p>
                                @endif
                            </div>

                            <div class="md:col-span-2">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                                    <div class="card p-4">
                                        <p class="field-label">Pekerjaan</p>
                                        <div class="address-field mt-1">{{ $pemohonPekerjaan ?: '-' }}</div>
                                    </div>

                                    <div class="card p-4">
                                        <p class="field-label">Provinsi</p>
                                        <div class="address-field mt-1">{{ $provName ?: '-' }}</div>
                                    </div>

                                    <div class="card p-4">
                                        <p class="field-label">Kabupaten / Kota</p>
                                        <div class="address-field mt-1">{{ $kabName ?: '-' }}</div>
                                    </div>

                                    <div class="card p-4">
                                        <p class="field-label">Kecamatan</p>
                                        <div class="address-field mt-1">{{ $kecName ?: '-' }}</div>
                                    </div>

                                    <div class="md:col-span-2 card p-4">
                                        <p class="field-label">Desa / Kelurahan</p>
                                        <div class="address-field mt-1">{{ $desaName ?: '-' }}</div>
                                    </div>

                                    <div class="md:col-span-2 card p-4">
                                        <p class="field-label">Alamat Lengkap</p>
                                        <div class="address-field mt-1 min-h-[90px] whitespace-pre-line leading-relaxed">{{ $alamatDetail ?: '-' }}</div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="lg:col-span-4">
                        <div class="card p-4 h-full">
                            <div class="flex items-center justify-between mb-3">
                                <p class="field-label mb-0">Foto KTP</p>
                                @if($ktpPublicUrl)
                                    <a href="{{ $ktpPublicUrl }}" target="_blank" rel="noopener"
                                       class="text-xs font-semibold text-blue-600 hover:text-blue-700">
                                        Lihat Penuh
                                    </a>
                                @endif
                            </div>

                            @if($ktpPublicUrl)
                                <a href="{{ $ktpPublicUrl }}" target="_blank" rel="noopener"
                                   class="block overflow-hidden rounded-xl border border-gray-200 group">
                                    <img src="{{ $ktpPublicUrl }}" alt="Foto KTP"
                                         class="w-full h-44 object-cover transition duration-300 group-hover:scale-105">
                                </a>

                                <div class="mt-3 grid grid-cols-2 gap-2">
                                    <a href="{{ $ktpPublicUrl }}" target="_blank" rel="noopener"
                                       class="btn-outline text-center text-xs">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        Lihat KTP
                                    </a>
                                    <a href="{{ route('admin.submissions.ktp.download', $submission->id) }}"
                                       class="btn-outline text-center text-xs">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1M12 4v12m0 0l-4-4m4 4l4-4"/>
                                        </svg>
                                        Unduh KTP
                                    </a>
                                </div>
                            @else
                                <div class="w-full h-44 rounded-xl border-2 border-dashed border-gray-200 flex flex-col items-center justify-center text-gray-400 bg-gray-50">
                                    <svg class="w-8 h-8 mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 5h18v14H3V5zm4 10l3-3 4 4 3-3 3 3"/>
                                    </svg>
                                    <p class="text-xs font-medium">Tidak ada foto KTP</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- ISI PENGAJUAN --}}
    <div class="section-card">
        <div class="section-header">
            <div class="section-header-icon">
                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <span class="section-title">Isi Pengajuan</span>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">
                <div class="lg:col-span-8 space-y-4">

                    <div class="card p-4">
                        <p class="field-label">Judul Permohonan</p>
                        <p class="text-base font-bold text-gray-900 leading-snug">{{ $judul }}</p>
                    </div>

                    <div class="card p-4">
                        <p class="field-label mb-3">Cara Penyampaian</p>
                        <div class="grid grid-cols-2 gap-3">
                            <div class="bg-gray-50 border border-gray-100 rounded-xl p-3">
                                <p class="text-xs text-gray-500 mb-1 font-medium">Metode</p>
                                <p class="text-sm font-bold text-gray-900">{{ $caraPenyampaianLabel }}</p>
                            </div>
                            <div class="bg-gray-50 border border-gray-100 rounded-xl p-3">
                                <p class="text-xs text-gray-500 mb-1 font-medium">Opsi Datang Langsung</p>
                                <p class="text-sm font-bold text-gray-900">
                                    @if($caraPenyampaian === 'datang_langsung' && !empty($opsiDatangLabel))
                                        {{ implode(', ', $opsiDatangLabel) }}
                                    @else
                                        —
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="card p-4">
                        <p class="field-label">Tujuan Permohonan</p>
                        <div class="mt-2 bg-gray-50 border border-gray-100 rounded-xl p-4">
                            <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-line">
                                {{ $tujuanPermohonan ?: '-' }}
                            </p>
                        </div>
                    </div>

                    <div class="card p-4">
                        <p class="field-label">Deskripsi Lengkap</p>
                        <div class="mt-2 bg-gray-50 border border-gray-100 rounded-xl p-4 min-h-[220px]">
                            <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-line">
                                {{ $deskripsi ?: '-' }}
                            </p>
                        </div>
                    </div>

                </div>

                <div class="lg:col-span-4">
                    <div class="card p-4">
                        <p class="field-label mb-3">Dokumen Pendukung</p>

                        @if($docs && $docs->count() > 0)
                            <div class="space-y-2">
                                @foreach($docs as $doc)
                                    <div class="flex items-center justify-between p-3 bg-gray-50 border border-gray-100 rounded-xl hover:bg-white hover:border-gray-200 transition">
                                        <div class="min-w-0 flex-1 mr-3">
                                            <div class="flex items-center gap-2">
                                                <svg class="w-4 h-4 text-blue-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                                <p class="text-xs font-semibold text-gray-800 truncate">{{ $doc->original_name ?? 'Dokumen' }}</p>
                                            </div>
                                            <p class="text-[10px] text-gray-400 mt-0.5 ml-6">{{ number_format(($doc->file_size ?? 0) / 1024, 2) }} KB</p>
                                        </div>

                                        <div class="flex items-center gap-1.5 shrink-0">
                                            <a href="{{ route('admin.submissions.document', $doc->id) }}?mode=view"
                                               target="_blank" rel="noopener"
                                               class="action-icon-btn"
                                               title="Lihat dokumen">
                                                <svg class="w-3.5 h-3.5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                            </a>
                                            <a href="{{ route('admin.submissions.document', $doc->id) }}?mode=download"
                                               class="w-8 h-8 rounded-xl flex items-center justify-center bg-blue-600 text-white hover:bg-blue-700 transition"
                                               title="Unduh dokumen">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1M12 4v12m0 0l-4-4m4 4l4-4"/>
                                                </svg>
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="flex flex-col items-center justify-center py-8 text-gray-400 bg-gray-50 border border-dashed border-gray-200 rounded-xl">
                                <svg class="w-8 h-8 mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                </svg>
                                <p class="text-xs font-medium">Tidak ada dokumen lampiran</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- TINDAK LANJUT --}}
    <div class="section-card">
        <div class="section-header">
            <div class="section-header-icon">
                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
            </div>
            <span class="section-title">Tindak Lanjut</span>
        </div>

        <div class="p-6">
            <form id="submission-followup-form" action="{{ route('admin.submissions.update', $submission->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">
                    <div class="lg:col-span-7 space-y-4">

                        <div class="card p-4">
                            <label class="field-label block mb-2">Update Status Tiket</label>
                            <div class="relative">
                                <select name="status" class="input-styled pr-9 appearance-none">
                                    <option value="pending"      {{ in_array($statusRaw, ['pending','belum diproses'])                                  ? 'selected' : '' }}>Belum Diproses</option>
                                    <option value="on_progress"  {{ in_array($statusRaw, ['on_progress','in_progress','diproses','sedang diproses'])     ? 'selected' : '' }}>Sedang Diproses</option>
                                    <option value="completed"    {{ in_array($statusRaw, ['completed','selesai','approved'])                             ? 'selected' : '' }}>Selesai</option>
                                    <option value="rejected"     {{ in_array($statusRaw, ['rejected','ditolak'])                                         ? 'selected' : '' }}>Ditolak</option>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-3 flex items-center">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div class="card p-4">
                            <label class="field-label block mb-3">Diproses Oleh</label>
                            <div class="space-y-2.5">
                                <div class="relative">
                                    <select id="diproses_bidang" name="diproses_bidang" class="input-styled pr-9 appearance-none">
                                        <option value="">Pilih Bidang / Unit</option>
                                        @foreach(array_keys($bidangKelompok) as $bidang)
                                            <option value="{{ $bidang }}" {{ ($oldBidang === $bidang) ? 'selected' : '' }}>
                                                {{ $bidang }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-3 flex items-center">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </div>
                                </div>

                                <div class="relative">
                                    <select id="diproses_kelompok" name="diproses_kelompok" class="input-styled pr-9 appearance-none" disabled>
                                        <option value="">Pilih Kelompok Kerja</option>
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-3 flex items-center">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </div>
                                </div>

                                <input type="hidden" id="diproses_oleh" name="diproses_oleh" value="{{ $diprosesOlehCombined ?? '' }}">
                            </div>
                        </div>
                    </div>

                    <div class="lg:col-span-5">
                        <div class="card p-4 h-full flex flex-col">
                            <label class="field-label block mb-2">Catatan untuk Pemohon</label>

                            <textarea name="admin_notes"
                                      class="scrollbar-thin flex-1 w-full px-3 py-3 border-1.5 border border-gray-200 rounded-xl text-sm text-gray-800 bg-gray-50 focus:bg-white focus:border-blue-400 focus:ring focus:ring-blue-50 placeholder-gray-400 resize-none transition"
                                      style="min-height:200px"
                                      placeholder="Tuliskan catatan atau respons kepada pemohon...">{{ $submission->admin_response ?? $submission->admin_notes }}</textarea>

                            <div class="mt-4 space-y-2.5">
                                <label class="flex items-center gap-2.5 cursor-pointer group">
                                    <div class="relative">
                                        <input type="checkbox" id="notify_user" name="notify_user" value="1" checked class="sr-only peer">
                                        <div class="w-4 h-4 border-2 border-gray-300 rounded peer-checked:bg-blue-600 peer-checked:border-blue-600 transition"></div>
                                        <svg class="absolute inset-0 w-4 h-4 text-white opacity-0 peer-checked:opacity-100 transition" viewBox="0 0 16 16" fill="none">
                                            <path d="M3 8l3.5 3.5 6.5-7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </div>
                                    <span class="text-xs text-gray-600 group-hover:text-gray-800 transition">Kirim notifikasi email kepada pemohon</span>
                                </label>

                                <label class="flex items-center gap-2.5 cursor-pointer group">
                                    <div class="relative">
                                        <input type="checkbox" id="notify_whatsapp" name="notify_whatsapp" value="1" class="sr-only peer">
                                        <div class="w-4 h-4 border-2 border-gray-300 rounded peer-checked:bg-emerald-600 peer-checked:border-emerald-600 transition"></div>
                                        <svg class="absolute inset-0 w-4 h-4 text-white opacity-0 peer-checked:opacity-100 transition" viewBox="0 0 16 16" fill="none">
                                            <path d="M3 8l3.5 3.5 6.5-7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </div>
                                    <span class="text-xs text-gray-600 group-hover:text-gray-800 transition">Kirim notifikasi via WhatsApp</span>
                                </label>
                            </div>

                            <button type="button"
                                    onclick="openSaveModalSubmission()"
                                    class="btn-primary w-full mt-4">
                                Simpan Perubahan
                            </button>
                        </div>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>

{{-- Modal Konfirmasi --}}
<div id="saveModalSubmission" class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden items-center justify-center z-50 p-4">
    <div class="bg-white w-full max-w-sm rounded-2xl shadow-2xl overflow-hidden">
        <div class="bg-gradient-to-br from-blue-50 to-indigo-50 p-6 text-center">
            <div class="mx-auto mb-4 w-14 h-14 flex items-center justify-center rounded-2xl bg-blue-100 shadow-sm">
                <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <h2 class="text-lg font-bold text-gray-900">Konfirmasi Perubahan</h2>
            <p class="text-sm text-gray-500 mt-1">Pastikan semua data sudah benar sebelum menyimpan.</p>
        </div>

        <div class="p-5 flex gap-3">
            <button type="button"
                    onclick="closeSaveModalSubmission()"
                    class="btn-outline flex-1 justify-center">
                Batal
            </button>
            <button type="button"
                    onclick="submitSubmissionFollowup()"
                    class="btn-primary flex-1 justify-center">
                Ya, Simpan
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function openSaveModalSubmission() {
        const modal = document.getElementById('saveModalSubmission');
        if (!modal) return;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeSaveModalSubmission() {
        const modal = document.getElementById('saveModalSubmission');
        if (!modal) return;
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    function submitSubmissionFollowup() {
        const form = document.getElementById('submission-followup-form');
        if (form) form.submit();
    }

    document.getElementById('saveModalSubmission')?.addEventListener('click', function (e) {
        if (e.target === this) closeSaveModalSubmission();
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeSaveModalSubmission();
    });

    const BIDANG_KELOMPOK = @json($bidangKelompok);

    const bidangSelect       = document.getElementById('diproses_bidang');
    const kelompokSelect     = document.getElementById('diproses_kelompok');
    const diprosesOlehHidden = document.getElementById('diproses_oleh');

    const preBidang   = @json($oldBidang);
    const preKelompok = @json($oldKelompok);

    function setKelompokOptions(bidang, selectedKelompok = null) {
        if (!kelompokSelect) return;

        kelompokSelect.innerHTML = '<option value="">Pilih Kelompok Kerja</option>';

        const items = BIDANG_KELOMPOK?.[bidang] || [];
        if (!bidang || items.length === 0) {
            kelompokSelect.disabled = true;
            if (diprosesOlehHidden) diprosesOlehHidden.value = '';
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
        const b = bidangSelect?.value || '';
        const k = kelompokSelect?.value || '';
        if (diprosesOlehHidden) diprosesOlehHidden.value = (b && k) ? `${b} - ${k}` : '';
    }

    bidangSelect?.addEventListener('change', () => setKelompokOptions(bidangSelect.value, null));
    kelompokSelect?.addEventListener('change', syncHidden);

    if (preBidang && bidangSelect) {
        bidangSelect.value = preBidang;
        setKelompokOptions(preBidang, preKelompok || null);
    } else {
        setKelompokOptions('', null);
    }
</script>
@endpush