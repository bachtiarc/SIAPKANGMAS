<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaduan Diterima</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #1f2937;
            background-color: #f9fafb;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid #e5e7eb;
        }
        .header {
            background-color: #2563eb;
            color: #ffffff;
            padding: 24px;
            text-align: center;
        }
        .content {
            padding: 30px;
        }
        .ticket-box {
            background-color: #eff6ff;
            border-left: 4px solid #2563eb;
            padding: 16px;
            margin: 20px 0;
        }
        .ticket-number {
            font-size: 22px;
            font-weight: bold;
            color: #1e3a8a;
            margin-top: 8px;
        }
        .info-row {
            margin: 8px 0;
        }
        .label {
            font-weight: bold;
            color: #374151;
        }
        .button {
            display: inline-block;
            background-color: #2563eb;
            color: #ffffff;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            margin-top: 20px;
            font-weight: 600;
        }
        .note {
            background-color: #fff7ed;
            border-left: 4px solid #f59e0b;
            padding: 16px;
            margin-top: 30px;
            font-size: 14px;
            color: #92400e;
        }
        .footer {
            text-align: center;
            font-size: 12px;
            color: #6b7280;
            padding: 20px;
            border-top: 1px solid #e5e7eb;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Pengaduan Telah Berhasil Dikirim</h1>
        </div>

        <div class="content">
            <p>Yth. <strong>{{ $complaint->user->name }}</strong>,</p>

            <p>
                Terima kasih telah menyampaikan pengaduan melalui SIAPKANGMAS.
                Pengaduan Anda telah kami terima dan akan diproses oleh tim terkait.
            </p>

            <div class="ticket-box">
                <div class="label">Nomor Tiket Pengaduan</div>
                <div class="ticket-number">{{ $complaint->ticket_number }}</div>
            </div>

            <div class="info-row">
                <span class="label">Subjek:</span> {{ $complaint->subject }}
            </div>
            <div class="info-row">
                <span class="label">Kategori:</span> {{ $complaint->category->name }}
            </div>
            <div class="info-row">
                <span class="label">Prioritas:</span> {{ $complaint->priority_label }}
            </div>
            <div class="info-row">
                <span class="label">Tanggal Pengajuan:</span>
                {{ $complaint->created_at->format('d F Y, H:i') }} WIB
            </div>
            <div class="info-row">
                <span class="label">Status:</span> Menunggu Verifikasi
            </div>

            <div style="text-align:center;">
                <a href="{{ route('user.complaints.show', $complaint->id) }}" class="button">
                    Lihat Detail Pengaduan
                </a>
            </div>

            <div class="note">
                Sistem akan mengirimkan notifikasi email apabila terdapat perubahan
                status atau tindak lanjut atas pengaduan Anda.
            </div>
        </div>

        <div class="footer">
            <strong>SIAPKANGMAS</strong><br>
            Dinas Perindustrian dan Perdagangan Provinsi Jawa Tengah<br>
            Email ini dikirim otomatis, mohon tidak membalas email ini.
        </div>
    </div>
</body>
</html>
