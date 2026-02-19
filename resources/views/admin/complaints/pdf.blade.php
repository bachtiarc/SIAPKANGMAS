<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaduan {{ $complaint->ticket_number ?? ($complaint->ticket_id ?? $complaint->id) }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: "Times-Roman", serif;
            font-size: 12pt;
            line-height: 1.5;
            color: #000;
            padding: 30px 40px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #000;
            padding-bottom: 15px;
        }

        .header h1 {
            font-size: 14pt;
            font-weight: bold;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .header h2 {
            font-size: 12pt;
            font-weight: normal;
            margin-bottom: 4px;
        }

        .note {
            border: 1px solid #000;
            padding: 10px 15px;
            margin-bottom: 25px;
            font-size: 11pt;
            font-style: italic;
        }

        .ticket-box {
            border: 2px solid #000;
            padding: 15px;
            text-align: center;
            margin-bottom: 25px;
        }

        .ticket-box .label {
            font-size: 11pt;
            margin-bottom: 8px;
            font-weight: bold;
        }

        .ticket-box .ticket-id {
            font-size: 14pt;
            font-weight: bold;
            margin-bottom: 5px;
            letter-spacing: 1px;
        }

        .section { margin-bottom: 30px; }

        .section-title {
            background-color: #000;
            color: #fff;
            padding: 8px 15px;
            font-size: 12pt;
            font-weight: bold;
            margin-bottom: 15px;
            text-transform: uppercase;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .info-table td {
            padding: 6px 0;
            vertical-align: top;
            line-height: 1.5;
        }

        .info-table td:first-child { width: 220px; }
        .info-table td:nth-child(2) { width: 20px; text-align: center; }

        .divider { border-bottom: 1px solid #ccc; }

        .field-label {
            font-weight: bold;
            margin-top: 15px;
            margin-bottom: 5px;
        }

        .description-box {
            border: 1px solid #000;
            padding: 15px;
            white-space: pre-line;
            min-height: 80px;
            margin-top: 10px;
        }

        .history-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 11pt;
        }

        .history-table th, .history-table td {
            border: 1px solid #000;
            padding: 8px;
            vertical-align: top;
        }

        .history-table th {
            background: #f2f2f2;
            text-align: left;
        }

        .status-text {
            font-weight: bold;
            text-transform: uppercase;
        }

        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 11pt;
            border-top: 2px solid #000;
            padding-top: 20px;
        }

        .footer p { margin-bottom: 5px; }
    </style>
</head>
<body>

@php
    $ticketNo = $complaint->ticket_number ?? ($complaint->ticket_id ?? $complaint->id);

    // =========================
    // STATUS LABEL (samakan permohonan)
    // =========================
    $statusLabel = match($complaint->status) {
        'pending'      => 'Menunggu Verifikasi',
        'in_progress'  => 'Sedang Diproses',
        'on_progress'  => 'Sedang Diproses',
        'diproses'     => 'Sedang Diproses',
        'completed'    => 'Selesai',
        'selesai'      => 'Selesai',
        'approved'     => 'Selesai',
        'rejected'     => 'Ditolak',
        'ditolak'      => 'Ditolak',
        default        => ucfirst((string)$complaint->status),
    };

    // =========================
    // PEMOHON LOGIC (samakan show/permohonan)
    // - creator pegawai => pemohon = applicant
    // - creator masyarakat_umum => pemohon = user
    // =========================
    $creator  = $complaint->user;
    $userType = $creator->user_type ?? null;

    $pemohon = ($userType === 'pegawai')
        ? ($complaint->applicant ?? null)
        : $creator;

    $pemohon = $pemohon ?: $creator;

    // =========================
    // IDENTITAS PEMOHON
    // =========================
    $jenisPelapor = $userType ? ucwords(str_replace('_', ' ', $userType)) : '-';
    if ($userType === 'masyarakat_umum') $jenisPelapor = 'Masyarakat Umum';
    if ($userType === 'pegawai') $jenisPelapor = 'Pegawai';

    $namaPemohon  = $pemohon->nama_lengkap ?? $pemohon->name ?? '-';
    $emailPemohon = $pemohon->email ?? '-';
    $telpPemohon  = $pemohon->phone ?? ($pemohon->phone_number ?? '-');

    $nik = $pemohon->nik ?? null;

    // alamat lengkap (samakan permohonan)
    $alamatDetail = $pemohon->alamat_detail ?? $pemohon->address ?? null;
    $provName = $pemohon->provinsi ?? $pemohon->provinsi_nama ?? null;
    $kabName  = $pemohon->kabupaten ?? $pemohon->kabupaten_nama ?? null;
    $kecName  = $pemohon->kecamatan ?? $pemohon->kecamatan_nama ?? null;
    $desaName = $pemohon->desa ?? $pemohon->desa_nama ?? null;

    $pemohonPekerjaan = $pemohon->pekerjaan ?? null;

    // =========================
    // DIPROSES OLEH
    // =========================
    $diprosesBidang   = $complaint->diproses_bidang ?? null;
    $diprosesKelompok = $complaint->diproses_kelompok ?? null;
    $diprosesOleh     = $complaint->diproses_oleh ?? null;

    $diprosesText = $diprosesOleh
        ?: (($diprosesBidang && $diprosesKelompok) ? "{$diprosesBidang} - {$diprosesKelompok}" : null);

    // =========================
    // CATATAN ADMIN
    // =========================
    $catatanAdmin = $complaint->admin_response ?? $complaint->admin_notes ?? null;

    // =========================
    // DOKUMEN PENDUKUNG
    // =========================
    $docs = $complaint->documents ?? collect();
@endphp

<!-- Header -->
<div class="header">
    <h1>FORMULIR PENGADUAN</h1>
    <h2>HELPDESK SIAPKANGMAS</h2>
    <h2>DINAS PERINDUSTRIAN DAN PERDAGANGAN PROVINSI JAWA TENGAH</h2>
</div>

<!-- Note -->
<div class="note">
    <strong>Catatan:</strong>
    Lampiran dokumen pendukung <u>tidak disertakan dalam berkas PDF ini</u> dan
    <strong>diunduh secara terpisah</strong>.
</div>

<!-- Ticket Box -->
<div class="ticket-box">
    <div class="label">NOMOR TIKET PENGADUAN</div>
    <div class="ticket-id">{{ $ticketNo }}</div>
</div>

<!-- Section A: Identitas Pemohon -->
<div class="section">
    <div class="section-title">A. Identitas Pemohon</div>

    <table class="info-table">
        <tr class="divider">
            <td>Jenis Pelapor</td>
            <td>:</td>
            <td>{{ $jenisPelapor }}</td>
        </tr>

        <tr class="divider">
            <td>Nama</td>
            <td>:</td>
            <td>{{ $namaPemohon }}</td>
        </tr>

        <tr class="divider">
            <td>Email</td>
            <td>:</td>
            <td>{{ $emailPemohon }}</td>
        </tr>

        <tr class="divider">
            <td>Telepon</td>
            <td>:</td>
            <td>{{ $telpPemohon }}</td>
        </tr>

        <tr class="divider">
            <td>Pekerjaan</td>
            <td>:</td>
            <td>{{ $pemohonPekerjaan ?: '-' }}</td>
        </tr>

        <tr class="divider">
            <td>NIK</td>
            <td>:</td>
            <td>{{ $nik ?? '-' }}</td>
        </tr>

        <tr class="divider">
            <td>Provinsi</td>
            <td>:</td>
            <td>{{ $provName ?: '-' }}</td>
        </tr>

        <tr class="divider">
            <td>Kabupaten / Kota</td>
            <td>:</td>
            <td>{{ $kabName ?: '-' }}</td>
        </tr>

        <tr class="divider">
            <td>Kecamatan</td>
            <td>:</td>
            <td>{{ $kecName ?: '-' }}</td>
        </tr>

        <tr class="divider">
            <td>Desa / Kelurahan</td>
            <td>:</td>
            <td>{{ $desaName ?: '-' }}</td>
        </tr>

        <tr class="divider">
            <td>Alamat Lengkap</td>
            <td>:</td>
            <td style="white-space: pre-line;">{{ $alamatDetail ?: '-' }}</td>
        </tr>
    </table>
</div>

<!-- Section B: Rincian Pengaduan -->
<div class="section">
    <div class="section-title">B. Rincian Pengaduan</div>

    <table class="info-table">
        <tr class="divider">
            <td>Judul</td>
            <td>:</td>
            <td>{{ $complaint->title ?? '-' }}</td>
        </tr>

        <tr class="divider">
            <td>Kategori</td>
            <td>:</td>
            <td>{{ $complaint->category->name ?? '-' }}</td>
        </tr>

        <tr class="divider">
            <td>Tanggal Pengajuan</td>
            <td>:</td>
            <td>{{ optional($complaint->created_at)->format('d F Y, H:i') }} WIB</td>
        </tr>

        <tr class="divider">
            <td>Status</td>
            <td>:</td>
            <td><span class="status-text">{{ $statusLabel }}</span></td>
        </tr>

        <tr class="divider">
            <td>Diproses Oleh</td>
            <td>:</td>
            <td style="white-space: pre-line;">{{ $diprosesText ?: '-' }}</td>
        </tr>
    </table>

    <div class="field-label">Deskripsi:</div>
    <div class="description-box">{{ $complaint->description ?? '-' }}</div>

    <div class="field-label">Catatan Admin:</div>
    <div class="description-box">{{ $catatanAdmin ?: '-' }}</div>

    <div class="field-label">Dokumen Pendukung:</div>
    <div class="description-box" style="min-height: 60px;">
        @if($docs && $docs->count() > 0)
            @foreach($docs as $i => $doc)
                @php
                    $name = $doc->original_name ?? ('Dokumen ' . ($i + 1));
                    $sizeKb = isset($doc->file_size) ? number_format(($doc->file_size ?? 0) / 1024, 2) . ' KB' : null;
                @endphp
                {{ ($i + 1) . '. ' . $name }}@if($sizeKb) ({{ $sizeKb }})@endif
                @if(!$loop->last)
                    <br>
                @endif
            @endforeach
        @else
            -
        @endif
    </div>
</div>

<!-- Section C: Riwayat Status -->
<div class="section">
    <div class="section-title">C. Riwayat Status</div>

    <table class="history-table">
        <tr>
            <th style="width: 22%;">Tanggal</th>
            <th style="width: 18%;">Status</th>
            <th>Catatan</th>
        </tr>

        {{-- Samakan timeline awal seperti permohonan --}}
        <tr>
            <td>{{ optional($complaint->created_at)->format('d M Y H:i') }}</td>
            <td>Tiket Dibuat</td>
            <td>-</td>
        </tr>
        <tr>
            <td>{{ optional($complaint->created_at)->format('d M Y H:i') }}</td>
            <td>Tiket Diterima Sistem</td>
            <td>-</td>
        </tr>

            @php
                $histories = collect($complaint->statusHistories ?? [])
                    ->sortBy(fn($h) => optional($h->created_at)->timestamp ?? 0)
                    ->values();
            @endphp

            <tr>
                <td>{{ optional($complaint->created_at)->format('d M Y H:i') }}</td>
                <td>Tiket Dibuat</td>
                <td>-</td>
            </tr>
            <tr>
                <td>{{ optional($complaint->created_at)->format('d M Y H:i') }}</td>
                <td>Tiket Diterima Sistem</td>
                <td>-</td>
            </tr>

            @foreach($histories as $history)
                @php
                    $historyLabel = match($history->new_status) {
                        'pending'      => 'Menunggu Verifikasi',
                        'in_progress'  => 'Sedang Diproses',
                        'on_progress'  => 'Sedang Diproses',
                        'diproses'     => 'Sedang Diproses',
                        'completed'    => 'Selesai',
                        'selesai'      => 'Selesai',
                        'approved'     => 'Selesai',
                        'rejected'     => 'Ditolak',
                        'ditolak'      => 'Ditolak',
                        default        => ucfirst((string)$history->new_status)
                    };
                @endphp
                <tr>
                    <td>{{ optional($history->created_at)->format('d M Y H:i') }}</td>
                    <td>{{ $historyLabel }}</td>
                    <td style="white-space: pre-line;">{{ $history->notes ?? '-' }}</td>
                </tr>
            @endforeach
    </table>
</div>

<!-- Footer -->
<div class="footer">
    <p><strong>DINAS PERINDUSTRIAN DAN PERDAGANGAN PROVINSI JAWA TENGAH</strong></p>
    <p>Dokumen ini dicetak secara otomatis oleh sistem SIAPKANGMAS</p>
    <p>Tanggal Cetak: {{ now()->format('d F Y, H:i') }} WIB</p>
</div>

</body>
</html>
