<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Konsultasi {{ $consultation->ticket_id }}</title>

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
        }

        .header h2 {
            font-size: 12pt;
            font-weight: normal;
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

        .ticket-id {
            font-size: 14pt;
            font-weight: bold;
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
        }

        .info-table td:first-child { width: 220px; }
        .info-table td:nth-child(2) { width: 20px; text-align: center; }

        .divider { border-bottom: 1px solid #ccc; }

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
        }

        .history-table th, .history-table td {
            border: 1px solid #000;
            padding: 8px;
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
    </style>
</head>
<body>

@php
    // =========================
    // STATUS LABEL (samakan permohonan)
    // =========================
    $statusLabel = match($consultation->status) {
        'pending'      => 'Menunggu Verifikasi',
        'on_progress',
        'in_progress'  => 'Sedang Diproses',
        'completed',
        'selesai'      => 'Selesai',
        'rejected',
        'ditolak'      => 'Ditolak',
        default        => ucfirst((string)$consultation->status),
    };

    // =========================
    // LOGIKA PEMOHON (samakan permohonan)
    // =========================
    $creator  = $consultation->user;
    $userType = $creator->user_type ?? null;

    $pemohon = ($userType === 'pegawai')
        ? ($consultation->applicant ?? null)
        : $creator;

    $pemohon = $pemohon ?: $creator;

    // =========================
    // IDENTITAS
    // =========================
    $jenisPelapor = $userType === 'masyarakat_umum'
        ? 'Masyarakat Umum'
        : ($userType === 'pegawai' ? 'Pegawai' : '-');

    $nama  = $pemohon->nama_lengkap ?? $pemohon->name ?? '-';
    $email = $pemohon->email ?? '-';
    $telp  = $pemohon->phone ?? ($pemohon->phone_number ?? '-');
    $nik   = $pemohon->nik ?? null;
    $pekerjaan = $pemohon->pekerjaan ?? null;

    $alamatDetail = $pemohon->alamat_detail ?? $pemohon->address ?? null;
    $provName = $pemohon->provinsi ?? $pemohon->provinsi_nama ?? null;
    $kabName  = $pemohon->kabupaten ?? $pemohon->kabupaten_nama ?? null;
    $kecName  = $pemohon->kecamatan ?? $pemohon->kecamatan_nama ?? null;
    $desaName = $pemohon->desa ?? $pemohon->desa_nama ?? null;

    // =========================
    // DIPROSES OLEH
    // =========================
    $diprosesText = $consultation->diproses_oleh ?? null;

    // =========================
    // CATATAN ADMIN
    // =========================
    $catatanAdmin = $consultation->admin_response ?? $consultation->admin_notes ?? null;

    // =========================
    // DOKUMEN
    // =========================
    $docs = $consultation->documents ?? collect();
@endphp

<!-- Header -->
<div class="header">
    <h1>FORMULIR KONSULTASI</h1>
    <h2>HELPDESK SIAPKANGMAS</h2>
    <h2>DINAS PERINDUSTRIAN DAN PERDAGANGAN PROVINSI JAWA TENGAH</h2>
</div>

<div class="note">
    <strong>Catatan:</strong>
    Lampiran dokumen pendukung tidak disertakan dalam berkas PDF ini.
</div>

<div class="ticket-box">
    <div>NOMOR TIKET KONSULTASI</div>
    <div class="ticket-id">{{ $consultation->ticket_id }}</div>
</div>

<!-- IDENTITAS -->
<div class="section">
    <div class="section-title">A. Identitas Pemohon</div>

    <table class="info-table">
        <tr class="divider"><td>Jenis Pelapor</td><td>:</td><td>{{ $jenisPelapor }}</td></tr>
        <tr class="divider"><td>Nama</td><td>:</td><td>{{ $nama }}</td></tr>
        <tr class="divider"><td>Email</td><td>:</td><td>{{ $email }}</td></tr>
        <tr class="divider"><td>Telepon</td><td>:</td><td>{{ $telp }}</td></tr>
        <tr class="divider"><td>Pekerjaan</td><td>:</td><td>{{ $pekerjaan ?: '-' }}</td></tr>
        <tr class="divider"><td>NIK</td><td>:</td><td>{{ $nik ?? '-' }}</td></tr>
        <tr class="divider"><td>Provinsi</td><td>:</td><td>{{ $provName ?: '-' }}</td></tr>
        <tr class="divider"><td>Kabupaten / Kota</td><td>:</td><td>{{ $kabName ?: '-' }}</td></tr>
        <tr class="divider"><td>Kecamatan</td><td>:</td><td>{{ $kecName ?: '-' }}</td></tr>
        <tr class="divider"><td>Desa / Kelurahan</td><td>:</td><td>{{ $desaName ?: '-' }}</td></tr>
        <tr class="divider"><td>Alamat Lengkap</td><td>:</td><td style="white-space: pre-line;">{{ $alamatDetail ?: '-' }}</td></tr>
    </table>
</div>

<!-- RINCIAN -->
<div class="section">
    <div class="section-title">B. Rincian Konsultasi</div>

    <table class="info-table">
        <tr class="divider"><td>Judul</td><td>:</td><td>{{ $consultation->title ?? '-' }}</td></tr>
        <tr class="divider"><td>Tanggal Pengajuan</td><td>:</td><td>{{ optional($consultation->created_at)->format('d F Y, H:i') }} WIB</td></tr>
        <tr class="divider"><td>Status</td><td>:</td><td><span class="status-text">{{ $statusLabel }}</span></td></tr>
        <tr class="divider"><td>Diproses Oleh</td><td>:</td><td>{{ $diprosesText ?: '-' }}</td></tr>
    </table>

    <strong>Deskripsi:</strong>
    <div class="description-box">{{ $consultation->description ?? '-' }}</div>

    <strong>Catatan Admin:</strong>
    <div class="description-box">{{ $catatanAdmin ?: '-' }}</div>

    <strong>Dokumen Pendukung:</strong>
    <div class="description-box">
        @if($docs && $docs->count() > 0)
            @foreach($docs as $i => $doc)
                {{ $i+1 }}. {{ $doc->original_name ?? 'Dokumen' }}
                @if(!$loop->last)<br>@endif
            @endforeach
        @else
            -
        @endif
    </div>
</div>

<!-- RIWAYAT -->
<div class="section">
    <div class="section-title">C. Riwayat Status</div>

    <table class="history-table">
        <tr>
            <th style="width:22%">Tanggal</th>
            <th style="width:18%">Status</th>
            <th>Catatan</th>
        </tr>

        <tr>
            <td>{{ optional($consultation->created_at)->format('d M Y H:i') }}</td>
            <td>Tiket Dibuat</td>
            <td>-</td>
        </tr>

        @php
            $histories = collect($consultation->statusHistories ?? [])
                ->sortBy(fn($h) => optional($h->created_at)->timestamp ?? 0)
                ->values();
        @endphp

        <tr>
            <td>{{ optional($consultation->created_at)->format('d M Y H:i') }}</td>
            <td>Tiket Dibuat</td>
            <td>-</td>
        </tr>
        <tr>
            <td>{{ optional($consultation->created_at)->format('d M Y H:i') }}</td>
            <td>Tiket Diterima Sistem</td>
            <td>-</td>
        </tr>

        @foreach($histories as $history)
            @php
                $historyLabel = match($history->new_status) {
                    'pending'      => 'Menunggu Verifikasi',
                    'in_progress'  => 'Sedang Diproses',
                    'on_progress'  => 'Sedang Diproses',
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

<div class="footer">
    <p><strong>DINAS PERINDUSTRIAN DAN PERDAGANGAN PROVINSI JAWA TENGAH</strong></p>
    <p>Dokumen ini dicetak secara otomatis oleh sistem SIAPKANGMAS</p>
    <p>Tanggal Cetak: {{ now()->format('d F Y, H:i') }} WIB</p>
</div>

</body>
</html>
