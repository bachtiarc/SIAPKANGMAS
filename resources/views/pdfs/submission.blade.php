<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $submission->ticket_id }}</title>
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

        .admin-notes-box {
            border: 2px solid #000;
            padding: 12px;
            margin-top: 10px;
        }

        .document-info { margin-top: 10px; padding-left: 20px; }
    </style>
</head>
<body>
@php
    use Illuminate\Support\Str;

    $isPermohonanInformasi = Str::contains(Str::lower($submissionType ?? ''), 'permohonan informasi');

    $cara = $submission->cara_penyampaian ?? null; // online | datang_langsung
    $caraLabel = $cara === 'online'
        ? 'Secara Online'
        : ($cara === 'datang_langsung' ? 'Datang langsung di kantor Disperindag' : '-');

    $rawOpsi = $submission->datang_langsung_opsi ?? null;

    $opsiArr = [];
    if (is_array($rawOpsi)) {
        $opsiArr = $rawOpsi;
    } elseif (is_string($rawOpsi)) {
        $decoded = json_decode($rawOpsi, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            $opsiArr = $decoded;
        } else {
            $opsiArr = [$rawOpsi]; // string single
        }
    }

    $opsiArr = array_values(array_filter(array_map(function ($v) {
        return is_string($v) ? strtolower(trim($v)) : null;
    }, $opsiArr)));

    $opsiLabel = '-';
    if (!empty($opsiArr)) {
        $hasFlash = in_array('flashdisk', $opsiArr, true);
        $hasCetak = in_array('cetak', $opsiArr, true);

        if (in_array('keduanya', $opsiArr, true) || ($hasFlash && $hasCetak)) {
            $opsiLabel = 'Keduanya (Flashdisk/Storage + Cetak biaya sendiri)';
        } elseif ($hasFlash) {
            $opsiLabel = 'Membawa flashdisk/storage untuk penyimpanan file';
        } elseif ($hasCetak) {
            $opsiLabel = 'Cetak hasil permohonan dengan biaya sendiri';
        } else {
            // fallback kalau ada nilai lain
            $opsiLabel = strtoupper(implode(', ', $opsiArr));
        }
    }
@endphp

    <!-- Header -->
    <div class="header">
        <h1>FORMULIR {{ $submissionType }}</h1>
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
        <div class="ticket-id">{{ $submission->ticket_id }}</div>
        @if($submission->full_ticket_number)
            <div class="full-ticket">{{ $submission->full_ticket_number }}</div>
        @endif
    </div>

    <!-- Section A -->
    <div class="section">
        <div class="section-title">A. Identitas Pemohon Pengajuan</div>
        <table class="info-table">
            <tr>
                <td>Nama</td><td>:</td><td>{{ $user->name }}</td>
            </tr>
            <tr class="divider">
                <td>NIP</td><td>:</td><td>{{ $user->nip ?? '-' }}</td>
            </tr>
            <tr class="divider">
                <td>Bidang/Balai</td><td>:</td><td>{{ $user->bidang ?? '-' }}</td>
            </tr>
            <tr class="divider">
                <td>Jabatan, Subbag, atau Seksi</td><td>:</td><td>{{ $user->jabatan ?? '-' }}</td>
            </tr>
        </table>
    </div>

    <!-- Section B -->
    <div class="section">
        <div class="section-title">B. Rincian Formulir Pengajuan</div>
        <table class="info-table">
            <tr>
                <td>Judul</td><td>:</td><td>{{ $submission->title }}</td>
            </tr>
            <tr class="divider">
                <td>Kategori</td><td>:</td><td>{{ $submission->category->name }}</td>
            </tr>
            <tr class="divider">
                <td>Tanggal Pengajuan</td><td>:</td><td>{{ $submission->created_at->format('d F Y, H:i') }} WIB</td>
            </tr>
            <tr class="divider">
                <td>Status</td><td>:</td>
                <td>
                    @if($submission->status == 'pending')
                        <span class="status-text">Menunggu Verifikasi</span>
                    @elseif($submission->status == 'in_progress')
                        <span class="status-text">Sedang Diproses</span>
                    @elseif($submission->status == 'completed')
                        <span class="status-text">Selesai</span>
                    @else
                        <span class="status-text">Ditolak</span>
                    @endif
                </td>
            </tr>

            @if($isPermohonanInformasi)
                <tr class="divider">
                    <td>Tujuan Permohonan</td><td>:</td><td>{{ $submission->tujuan_permohonan }}</td>
                </tr>
                <tr class="divider">
                    <td>Cara Penyampaian Feedback</td><td>:</td><td>{{ $caraLabel }}</td>
                </tr>

                @if(($submission->cara_penyampaian ?? null) === 'datang_langsung')
                    <tr class="divider">
                        <td>Opsi Datang Langsung</td><td>:</td><td>{{ $opsiLabel }}</td>
                    </tr>
                @endif
            @endif
        </table>

        <div class="field-label">Deskripsi Lengkap:</div>
        <div class="description-box">{{ $submission->description }}</div>

        @if($submission->document_path)
            <div class="field-label">Dokumen Pendukung:</div>
            <div class="document-info">
                Nama File: {{ basename($submission->document_path) }}<br>
                <em>Dokumen dapat diakses melalui sistem atau cetak terpisah</em>
            </div>
        @endif

        @if($submission->admin_notes)
            <div class="field-label">Catatan Admin:</div>
            <div class="admin-notes-box">
                {{ $submission->admin_notes }}
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