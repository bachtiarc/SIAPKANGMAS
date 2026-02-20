<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $complaint->ticket_number }}</title>

    <style>
        @page { margin: 0; }
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 12pt;
            line-height: 1.6;
            color: #000;
            padding: 30px 40px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #000;
            padding-bottom: 20px;
        }

        .header h1 {
            font-size: 16pt;
            font-weight: bold;
            text-transform: uppercase;
        }

        .header h2 {
            font-size: 14pt;
            font-weight: bold;
            margin-top: 5px;
        }

        .header p {
            font-size: 10pt;
            margin-top: 4px;
        }

        .document-title {
            text-align: center;
            margin: 30px 0;
            font-size: 14pt;
            font-weight: bold;
            text-decoration: underline;
            text-transform: uppercase;
        }

        .ticket-info {
            text-align: center;
            margin-bottom: 20px;
            padding: 10px;
            border: 1px solid #000;
            background: #f2f2f2;
        }

        .ticket-id {
            font-weight: bold;
            font-size: 13pt;
        }

        .content-section {
            margin-top: 25px;
        }

        .content-section h3 {
            font-size: 12pt;
            font-weight: bold;
            border-bottom: 1px solid #000;
            margin-bottom: 10px;
            padding-bottom: 5px;
        }

        .info-row {
            display: table;
            width: 100%;
            margin-bottom: 8px;
        }

        .info-label {
            display: table-cell;
            width: 35%;
            font-weight: bold;
            vertical-align: top;
        }

        .info-sep {
            display: table-cell;
            width: 5%;
        }

        .info-content {
            display: table-cell;
            width: 60%;
            text-align: justify;
        }

        .box {
            border: 1px solid #000;
            padding: 12px;
            margin-top: 8px;
            background: #fafafa;
            white-space: pre-line;
        }

        .status-badge {
            display: inline-block;
            border: 1px solid #000;
            padding: 4px 12px;
            font-weight: bold;
        }

        .signature {
            margin-top: 50px;
            text-align: right;
        }

        .signature-space {
            height: 80px;
        }

        .footer {
            margin-top: 40px;
            border-top: 1px solid #000;
            padding-top: 15px;
            font-size: 9pt;
            text-align: center;
            color: #444;
        }
    </style>
</head>
<body>

<!-- HEADER -->
<div class="header">
    <h1>PEMERINTAH PROVINSI JAWA TENGAH</h1>
    <h2>DINAS PERINDUSTRIAN DAN PERDAGANGAN</h2>
    <p>Jl. Pahlawan No.4, Pleburan, Kec. Semarang Sel., Kota Semarang, Jawa Tengah 50241</p>
    <p>Website: www.disperindag.jatengprov.go.id</p>
</div>

<!-- NOTE -->
<div style="margin-bottom:20px; font-size:11pt;">
    <strong>Catatan:</strong>
    Dokumen pendukung yang diunggah pada pengaduan ini
    <u>tidak tercetak secara otomatis</u>.
    Apabila diperlukan, dokumen pendukung dapat dicetak sendiri
    atau diakses melalui sistem SIAPKANGMAS.
</div>

<!-- TITLE -->
<div class="document-title">
    FORMULIR PENGADUAN
</div>

<!-- TICKET -->
<div class="ticket-info">
    <p>Nomor Tiket</p>
    <p class="ticket-id">{{ $complaint->ticket_number }}</p>
    <p>Tanggal: {{ $complaint->created_at->format('d F Y') }}</p>
</div>

@php
    $alamatDetailPdf = trim((string) ($alamatDetail ?? ($user->address ?? '')));
    $desaPdf         = trim((string) ($desa ?? ($user->desa ?? '')));
    $kecamatanPdf    = trim((string) ($kecamatan ?? ($user->kecamatan ?? '')));
    $kabupatenPdf    = trim((string) ($kabupaten ?? ($user->kabupaten ?? '')));
    $provinsiPdf     = trim((string) ($provinsi ?? ($user->provinsi ?? '')));
@endphp

<!-- DATA PEMOHON -->
<div class="content-section">
    <h3>I. DATA PEMOHON</h3>

    <div class="info-row">
        <div class="info-label">Nama Lengkap</div>
        <div class="info-sep">:</div>
        <div class="info-content">{{ $user->name ?? '-' }}</div>
    </div>

    <div class="info-row">
        <div class="info-label">NIK</div>
        <div class="info-sep">:</div>
        <div class="info-content">{{ $user->nik ?? '-' }}</div>
    </div>

    <div class="info-row">
        <div class="info-label">Pekerjaan</div>
        <div class="info-sep">:</div>
        <div class="info-content">{{ $user->pekerjaan ?? '-' }}</div>
    </div>

    <div class="info-row">
        <div class="info-label">Email</div>
        <div class="info-sep">:</div>
        <div class="info-content">
            {{ filled($user->email ?? null) ? $user->email : '-' }}
        </div>
    </div>

    <div class="info-row">
        <div class="info-label">Nomor Telepon</div>
        <div class="info-sep">:</div>
        <div class="info-content">{{ $user->phone ?? '-' }}</div>
    </div>

    <div class="info-row">
        <div class="info-label">Provinsi</div>
        <div class="info-sep">:</div>
        <div class="info-content">{{ $provinsiPdf !== '' ? $provinsiPdf : '-' }}</div>
    </div>

    <div class="info-row">
        <div class="info-label">Kota/Kabupaten</div>
        <div class="info-sep">:</div>
        <div class="info-content">{{ $kabupatenPdf !== '' ? $kabupatenPdf : '-' }}</div>
    </div>

    <div class="info-row">
        <div class="info-label">Kecamatan</div>
        <div class="info-sep">:</div>
        <div class="info-content">{{ $kecamatanPdf !== '' ? $kecamatanPdf : '-' }}</div>
    </div>

    <div class="info-row">
        <div class="info-label">Kelurahan/Desa</div>
        <div class="info-sep">:</div>
        <div class="info-content">{{ $desaPdf !== '' ? $desaPdf : '-' }}</div>
    </div>

    <div class="info-row">
        <div class="info-label">Alamat Lengkap</div>
        <div class="info-sep">:</div>
        <div class="info-content">{{ $alamatDetailPdf !== '' ? $alamatDetailPdf : '-' }}</div>
    </div>
</div>

<!-- RINCIAN PENGADUAN -->
<div class="content-section">
    <h3>II. RINCIAN PENGADUAN</h3>

    <div class="info-row">
        <div class="info-label">Judul Pengaduan</div>
        <div class="info-sep">:</div>
        <div class="info-content">{{ $complaint->subject ?? '-' }}</div>
    </div>

    <div class="info-row">
        <div class="info-label">Tanggal Pengajuan</div>
        <div class="info-sep">:</div>
        <div class="info-content">
            {{ $complaint->created_at->format('d F Y, H:i') }} WIB
        </div>
    </div>

    <div class="info-row">
        <div class="info-label">Status</div>
        <div class="info-sep">:</div>
        <div class="info-content">
            <span class="status-badge">
                {{ $statusLabel ?? '-' }}
            </span>
        </div>
    </div>

    <p style="margin-top:10px;font-weight:bold;">Deskripsi:</p>
    <div class="box">{{ $complaint->description ?? '-' }}</div>
</div>

<!-- TTD -->
<div class="signature">
    <p>Semarang, {{ $complaint->created_at->format('d F Y') }}</p>
    <p>Pemohon,</p>
    <div class="signature-space"></div>
    <p><strong>{{ $user->name ?? '-' }}</strong></p>
</div>

<!-- FOOTER -->
<div class="footer">
    <p>Dokumen ini dicetak secara elektronik dan sah tanpa tanda tangan basah.</p>
    <p>Dicetak pada {{ now()->format('d F Y, H:i') }} WIB</p>
</div>

</body>
</html>