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

        .ticket-box .full-ticket {
            font-size: 10pt;
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

    $statusLabel = match($complaint->status) {
        'pending'  => 'Pending',
        'diproses' => 'Sedang Diproses',
        'selesai'  => 'Selesai',
        'rejected' => 'Ditolak',
        'ditolak'  => 'Ditolak',
        default    => ucfirst($complaint->status)
    };
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
    <strong>diunduh secara terpisah</strong> melalui sistem aplikasi SIAPKANGMAS.
</div>

<!-- Ticket Box -->
<div class="ticket-box">
    <div class="label">NOMOR TIKET PENGADUAN</div>
    <div class="ticket-id">{{ $ticketNo }}</div>
</div>

<!-- Section A: Identitas Pemohon -->
<div class="section">
    <div class="section-title">A. Identitas Pemohon Pengaduan</div>

    <table class="info-table">
        <tr>
            <td>Nama</td>
            <td>:</td>
            <td>{{ $complaint->user->name ?? '-' }}</td>
        </tr>
        <tr class="divider">
            <td>Email</td>
            <td>:</td>
            <td>{{ $complaint->user->email ?? '-' }}</td>
        </tr>
        <tr class="divider">
            <td>Telepon</td>
            <td>:</td>
            <td>{{ $complaint->user->phone ?? '-' }}</td>
        </tr>
        <tr class="divider">
            <td>Tanggal Pengajuan</td>
            <td>:</td>
            <td>{{ $complaint->created_at->format('d F Y') }}</td>
        </tr>
        <tr class="divider">
            <td>Status</td>
            <td>:</td>
            <td><span class="status-text">{{ $statusLabel }}</span></td>
        </tr>
    </table>
</div>

<!-- Section B: Rincian Pengaduan -->
<div class="section">
    <div class="section-title">B. Rincian Pengaduan</div>

    <table class="info-table">
        <tr>
            <td>Judul / Subjek</td>
            <td>:</td>
            <td>{{ $complaint->title ?? $complaint->subject ?? '-' }}</td>
        </tr>
    </table>

    <div class="field-label">Deskripsi:</div>
    <div class="description-box">{{ $complaint->description ?? '-' }}</div>
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

        @foreach($complaint->statusHistories as $history)
            @php
                $historyLabel = match($history->new_status) {
                    'pending'  => 'Pending',
                    'diproses' => 'Sedang Diproses',
                    'selesai'  => 'Selesai',
                    'rejected' => 'Ditolak',
                    'ditolak'  => 'Ditolak',
                    default    => ucfirst($history->new_status)
                };
            @endphp
            <tr>
                <td>{{ $history->created_at->format('d M Y H:i') }}</td>
                <td>{{ $historyLabel }}</td>
                <td>{{ $history->notes ?? '-' }}</td>
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