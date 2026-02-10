<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $consultation->ticket_number ?? '-' }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Times New Roman', Times, serif;
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

        .header h2 { font-size: 12pt; font-weight: normal; margin-bottom: 4px; }

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

        .ticket-box .label { font-size: 11pt; margin-bottom: 8px; font-weight: bold; }
        .ticket-box .ticket-id { font-size: 14pt; font-weight: bold; margin-bottom: 5px; letter-spacing: 1px; }
        .ticket-box .full-ticket { font-size: 10pt; }

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

        .info-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        .info-table td { padding: 6px 0; vertical-align: top; line-height: 1.5; }
        .info-table td:first-child { width: 220px; font-weight: normal; }
        .info-table td:nth-child(2) { width: 20px; text-align: center; }
        .info-table td:last-child { font-weight: normal; }

        .divider { border-bottom: 1px solid #ccc; }

        .description-box {
            border: 1px solid #000;
            padding: 15px;
            white-space: pre-line;
            min-height: 80px;
            margin-top: 10px;
        }

        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 11pt;
            border-top: 2px solid #000;
            padding-top: 20px;
        }

        .footer p { margin-bottom: 5px; }

        .status-text { font-weight: bold; text-transform: uppercase; }
        .field-label { font-weight: bold; margin-top: 15px; margin-bottom: 5px; }

        .document-info { margin-top: 10px; padding-left: 20px; }
    </style>
</head>
<body>
@php
    $ticket = $consultation->ticket_number ?? '-';

    $status = $consultation->status ?? 'pending';
    $statusLabel = match ($status) {
        'pending' => 'Menunggu Verifikasi',
        'in_progress', 'on_progress' => 'Sedang Diproses',
        'completed' => 'Selesai',
        'rejected' => 'Ditolak',
        default => ucwords(str_replace('_', ' ', $status)),
    };

    $alamatPdf = $alamatLengkap ?? '-';
@endphp

    <!-- Header -->
    <div class="header">
        <h1>FORMULIR KONSULTASI</h1>
        <h2>HELPDESK SIAPKANGMAS</h2>
        <h2>DINAS PERINDUSTRIAN DAN PERDAGANGAN PROVINSI JAWA TENGAH</h2>
    </div>

    <!-- Note -->
    <div class="note">
        <strong>Catatan:</strong> Dimohon dokumen pendukung harap dicetak sendiri jika diperlukan
    </div>

    <!-- Ticket Box -->
    <div class="ticket-box">
        <div class="label">NOMOR TIKET PENGAJUAN</div>
        <div class="ticket-id">{{ $ticket }}</div>
    </div>

    <!-- Section A -->
    <div class="section">
        <div class="section-title">A. Identitas Pemohon Pengajuan</div>
        <table class="info-table">
            <tr>
                <td>Nama Lengkap</td><td>:</td><td>{{ $user->name ?? '-' }}</td>
            </tr>
            <tr class="divider">
                <td>NIK</td><td>:</td><td>{{ $user->nik ?? '-' }}</td>
            </tr>
            <tr class="divider">
                <td>Alamat</td><td>:</td><td>{{ $alamatPdf }}</td>
            </tr>
            <tr class="divider">
                <td>Nomor Telepon</td><td>:</td><td>{{ $user->phone ?? '-' }}</td>
            </tr>
            <tr class="divider">
                <td>Email</td><td>:</td><td>{{ $user->email ?? '-' }}</td>
            </tr>
        </table>
    </div>

    <!-- Section B -->
    <div class="section">
        <div class="section-title">B. Rincian Formulir Pengajuan</div>
        <table class="info-table">
            <tr>
                <td>Judul</td><td>:</td><td>{{ $consultation->subject ?? '-' }}</td>
            </tr>

            <tr class="divider">
                <td>Tanggal Pengajuan</td><td>:</td><td>{{ optional($consultation->created_at)->format('d F Y, H:i') }} WIB</td>
            </tr>
            <tr class="divider">
                <td>Status</td><td>:</td>
                <td>
                    <span class="status-text">{{ $statusLabel }}</span>
                </td>
            </tr>
        </table>

        <div class="field-label">Deskripsi Lengkap:</div>
        <div class="description-box">{{ $consultation->description ?? '-' }}</div>

        @if(($consultation->documents ?? collect())->count() > 0)
            <div class="field-label">Dokumen Pendukung:</div>
            <div class="document-info">
                <ul>
                    @foreach($consultation->documents as $doc)
                        <li>{{ $doc->original_name ?? basename($doc->file_path ?? '-') }}</li>
                    @endforeach
                </ul>
                <em>Dokumen dapat diakses melalui sistem atau cetak terpisah</em>
            </div>
        @endif
    </div>

    <!-- Footer -->
    <div class="footer">
        <p><strong>DINAS PERINDUSTRIAN DAN PERDAGANGANG PROVINSI JAWA TENGAH</strong></p>
        <p>Dokumen ini dicetak secara otomatis oleh sistem SIAPKANGMAS</p>
        <p>Tanggal Cetak: {{ now()->format('d F Y, H:i') }} WIB</p>
    </div>
</body>
</html>