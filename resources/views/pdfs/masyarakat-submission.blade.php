<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $submission->ticket_id }}</title>

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

        ul {
            margin-left: 18px;
            margin-top: 5px;
        }

        li {
            margin-bottom: 3px;
        }
    </style>
</head>
<body>

@php
    $cara = $submission->cara_penyampaian;
    $opsi = $submission->datang_langsung_opsi ?? [];

    if (!is_array($opsi)) {
        $opsi = [];
    }

    if (in_array('keduanya', $opsi)) {
        $opsi = ['flashdisk', 'cetak'];
    }
@endphp

<!-- HEADER -->
<div class="header">
    <h1>PEMERINTAH PROVINSI JAWA TENGAH</h1>
    <h2>DINAS PERINDUSTRIAN DAN PERDAGANGAN</h2>
    <p>Jl. Pahlawan No. 4 Semarang 50132</p>
    <p>Website: www.disperindag.jatengprov.go.id</p>
</div>

<!-- TITLE -->
<div class="document-title">
    {{ $submissionType }}
</div>

<!-- TICKET -->
<div class="ticket-info">
    <p>Nomor Tiket</p>
    <p class="ticket-id">{{ $submission->ticket_id }}</p>
    <p>Tanggal: {{ $submission->created_at->format('d F Y') }}</p>
</div>

<!-- DATA PEMOHON -->
<div class="content-section">
    <h3>I. DATA PEMOHON</h3>

    <div class="info-row">
        <div class="info-label">Nama Lengkap</div>
        <div class="info-sep">:</div>
        <div class="info-content">{{ $user->name }}</div>
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
        <div class="info-content">{{ $user->email }}</div>
    </div>

    <div class="info-row">
        <div class="info-label">Nomor Telepon</div>
        <div class="info-sep">:</div>
        <div class="info-content">{{ $user->phone ?? '-' }}</div>
    </div>

    @php
        $alamat = trim($user->address ?? '');

        $desaLabel = $user->is_kelurahan
            ? 'Kelurahan ' . $user->desa
            : 'Desa ' . $user->desa;

        $kabLabel = str_starts_with(strtolower($user->kabupaten), 'kota')
            ? 'Kota ' . str_replace('Kota ', '', $user->kabupaten)
            : 'Kab. ' . $user->kabupaten;

        $alamatLengkap = collect([
            $alamat,
            $desaLabel,
            'Kec. ' . $user->kecamatan,
            $kabLabel,
            $user->provinsi ?? 'Jawa Tengah',
        ])->implode(', ');
    @endphp

    <div class="info-row">
        <div class="info-label">Alamat</div>
        <div class="info-sep">:</div>
        <div class="info-content">{{ $alamatLengkap }}</div>
    </div>
</div>

<!-- RINCIAN PERMOHONAN -->
<div class="content-section">
    <h3>II. RINCIAN PERMOHONAN</h3>

    <div class="info-row">
        <div class="info-label">Judul Permohonan</div>
        <div class="info-sep">:</div>
        <div class="info-content">{{ $submission->title }}</div>
    </div>

    <div class="info-row">
        <div class="info-label">Tanggal Pengajuan</div>
        <div class="info-sep">:</div>
        <div class="info-content">{{ $submission->created_at->format('d F Y, H:i') }} WIB</div>
    </div>

    <div class="info-row">
        <div class="info-label">Status</div>
        <div class="info-sep">:</div>
        <div class="info-content">
            <span class="status-badge">{{ $submission->status_label }}</span>
        </div>
    </div>

    <div class="info-row">
        <div class="info-label">Tujuan Permohonan</div>
        <div class="info-sep">:</div>
        <div class="info-content">{{ $submission->tujuan_permohonan }}</div>
    </div>

    <div class="info-row">
        <div class="info-label">Penyampaian Feedback</div>
        <div class="info-sep">:</div>
        <div class="info-content">
            @if($cara === 'online')
                Secara Online
            @elseif($cara === 'datang_langsung')
                Datang langsung ke kantor Disperindag
            @else
                -
            @endif

            @if($cara === 'datang_langsung')
                <ul>
                    @if(in_array('flashdisk', $opsi))
                        <li>Membawa flashdisk / media penyimpanan</li>
                    @endif
                    @if(in_array('cetak', $opsi))
                        <li>Cetak hasil permohonan dengan biaya sendiri</li>
                    @endif
                </ul>
            @endif
        </div>
    </div>

    <p style="margin-top:10px;font-weight:bold;">Deskripsi Lengkap:</p>
    <div class="box">{{ $submission->description }}</div>
</div>

<!-- TTD -->
<div class="signature">
    <p>Semarang, {{ $submission->created_at->format('d F Y') }}</p>
    <p>Pemohon,</p>
    <div class="signature-space"></div>
    <p><strong>{{ $user->name }}</strong></p>
</div>

<!-- FOOTER -->
<div class="footer">
    <p>Dokumen ini dicetak secara elektronik dan sah tanpa tanda tangan basah.</p>
    <p>Dicetak pada {{ now()->format('d F Y, H:i') }} WIB</p>
</div>

</body>
</html>