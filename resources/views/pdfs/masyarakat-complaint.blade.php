<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $complaint->ticket_number ?? 'PENGADUAN' }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 12pt;
            line-height: 1.5;
            color: #000;
            padding: 30px 40px;
        }

        .center { text-align: center; }
        .bold { font-weight: bold; }

        .header {
            text-align: center;
            margin-bottom: 25px;
            border-bottom: 2px solid #000;
            padding-bottom: 15px;
        }

        .header h1 {
            font-size: 14pt;
            font-weight: bold;
            margin-bottom: 6px;
            text-transform: uppercase;
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
            margin-bottom: 6px;
            font-weight: bold;
        }

        .ticket-box .ticket-id {
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
            margin-top: 10px;
        }

        .document-info {
            margin-top: 10px;
            padding-left: 20px;
            font-size: 11pt;
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
    $ticket = $complaint->ticket_number ?? '-';
@endphp

    <!-- HEADER -->
    <div class="header">
        <h1>FORMULIR PENGADUAN</h1>
        <h2>HELPDESK SIAPKANGMAS</h2>
        <h2>DINAS PERINDUSTRIAN DAN PERDAGANGAN PROVINSI JAWA TENGAH</h2>
    </div>

    <!-- NOTE (DOKUMEN PENDUKUNG) -->
    <div class="note">
        <strong>Catatan:</strong>
        Dokumen pendukung yang diunggah pada pengaduan ini
        <u>tidak tercetak secara otomatis</u>.
        Apabila diperlukan, dokumen pendukung dapat dicetak sendiri
        atau diakses melalui sistem SIAPKANGMAS.
    </div>

    <!-- TICKET -->
    <div class="ticket-box">
        <div class="label">NOMOR TIKET PENGADUAN</div>
        <div class="ticket-id">{{ $ticket }}</div>
    </div>

    <!-- A. DATA PEMOHON -->
    <div class="section">
        <div class="section-title">A. Data Pemohon</div>
        <table class="info-table">
            <tr>
                <td>Nama Lengkap</td><td>:</td><td>{{ $user->name ?? '-' }}</td>
            </tr>
            <tr class="divider">
                <td>NIK</td><td>:</td><td>{{ $user->nik ?? '-' }}</td>
            </tr>
            <tr class="divider">
                <td>Alamat</td><td>:</td><td>{{ $alamatLengkap ?? '-' }}</td>
            </tr>
            <tr class="divider">
                <td>Nomor Telepon</td><td>:</td><td>{{ $user->phone ?? '-' }}</td>
            </tr>
            <tr class="divider">
                <td>Email</td><td>:</td><td>{{ $user->email ?? '-' }}</td>
            </tr>
        </table>
    </div>

    <!-- B. RINCIAN PENGADUAN -->
    <div class="section">
        <div class="section-title">B. Rincian Pengaduan</div>
        <table class="info-table">
            <tr>
                <td>Judul Pengaduan</td><td>:</td><td>{{ $complaint->subject ?? '-' }}</td>
            </tr>
            <tr class="divider">
                <td>Tanggal Pengajuan</td><td>:</td>
                <td>{{ optional($complaint->created_at)->translatedFormat('d F Y, H:i') }} WIB</td>
            </tr>
            <tr class="divider">
                <td>Status</td><td>:</td><td><strong>{{ $statusLabel ?? '-' }}</strong></td>
            </tr>
        </table>

        <div class="bold" style="margin-top:15px;">Deskripsi Lengkap:</div>
        <div class="description-box">
            {{ $complaint->description ?? '-' }}
        </div>

        @if(($complaint->documents ?? collect())->count() > 0)
            <div class="bold" style="margin-top:15px;">Dokumen Pendukung:</div>
            <div class="document-info">
                <ul>
                    @foreach($complaint->documents as $doc)
                        <li>{{ $doc->original_name ?? basename($doc->file_path ?? '-') }}</li>
                    @endforeach
                </ul>
                <em>Dokumen pendukung dapat diakses melalui sistem atau dicetak secara terpisah.</em>
            </div>
        @endif
    </div>

    <!-- FOOTER -->
    <div class="footer">
        <p><strong>DINAS PERINDUSTRIAN DAN PERDAGANGAN PROVINSI JAWA TENGAH</strong></p>
        <p>Dokumen ini dicetak secara otomatis oleh sistem SIAPKANGMAS</p>
        <p>Tanggal Cetak: {{ now()->translatedFormat('d F Y, H:i') }} WIB</p>
    </div>
</body>
</html>