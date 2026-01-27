<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Permohonan Informasi Diterima</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9fafb;
            color: #1f2937;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            overflow: hidden;
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
            background: #eff6ff;
            border-left: 4px solid #2563eb;
            padding: 16px;
            margin: 20px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        table td {
            padding: 8px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        table td:first-child {
            font-weight: bold;
            width: 160px;
        }
        .button {
            display: inline-block;
            padding: 12px 28px;
            background-color: #2563eb;
            color: #ffffff;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            margin-top: 20px;
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
            padding: 20px;
            font-size: 12px;
            color: #6b7280;
            border-top: 1px solid #e5e7eb;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Permohonan Berhasil Dikirim</h1>
            <p>Dinas Perindustrian dan Perdagangan Provinsi Jawa Tengah</p>
        </div>

        <div class="content">
            <p>Yth. <strong>{{ $submission->user->name }}</strong>,</p>

            <p>
                Permohonan informasi yang Anda ajukan telah diterima oleh sistem dan
                akan segera ditindaklanjuti oleh petugas terkait.
            </p>

            <div class="ticket-box">
                <strong>Nomor Tiket Permohonan</strong><br>
                {{ $submission->ticket_id }}
            </div>

            <table>
                <tr>
                    <td>Judul</td>
                    <td>{{ $submission->title }}</td>
                </tr>
                <tr>
                    <td>Kategori</td>
                    <td>{{ $submission->category->name ?? 'Kategori Informasi' }}</td>
                </tr>
                <tr>
                    <td>Tanggal Pengajuan</td>
                    <td>{{ $submission->created_at->format('d F Y, H:i') }} WIB</td>
                </tr>
                <tr>
                    <td>Status</td>
                    <td>Menunggu Proses</td>
                </tr>
            </table>

            <p style="margin-top: 20px;"><strong>Deskripsi Permohonan:</strong></p>
            <p style="background:#f9fafb; padding:15px; border-radius:6px;">
                {{ $submission->description }}
            </p>

            <div style="text-align:center;">
                <a href="{{ route('masyarakat.submissions.show', $submission->id) }}" class="button">
                    Lihat Detail Permohonan
                </a>
            </div>

            <div class="note">
                Estimasi waktu respons maksimal 10 hari kerja.
                Notifikasi akan dikirimkan apabila terdapat perubahan status.
            </div>
        </div>

        <div class="footer">
            Dinas Perindustrian dan Perdagangan Provinsi Jawa Tengah<br>
            Email ini dikirim otomatis oleh sistem.
        </div>
    </div>
</body>
</html>