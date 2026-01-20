<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
        .content {
            background: #ffffff;
            padding: 30px;
            border: 1px solid #e0e0e0;
        }
        .ticket-box {
            background: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 15px;
            margin: 20px 0;
        }
        .footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            border-radius: 0 0 10px 10px;
            font-size: 12px;
            color: #666;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        table td {
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }
        table td:first-child {
            font-weight: bold;
            width: 150px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1 style="margin: 0;">✅ Konsultasi Berhasil Dikirim</h1>
        <p style="margin: 10px 0 0 0;">Dinas Perindustrian dan Perdagangan Provinsi Jawa Tengah</p>
    </div>

    <div class="content">
        <p>Yth. <strong>{{ $consultation->user->name }}</strong>,</p>
        
        <p>Terima kasih telah mengajukan konsultasi kepada Dinas Perindustrian dan Perdagangan Provinsi Jawa Tengah.</p>

        <div class="ticket-box">
            <h3 style="margin-top: 0; color: #667eea;">📋 Nomor Tiket Konsultasi Anda</h3>
            <h2 style="margin: 10px 0; font-size: 24px; color: #333;">{{ $consultation->ticket_number }}</h2>
            <p style="margin: 5px 0 0 0; font-size: 12px; color: #666;">Simpan nomor tiket ini untuk lacak status konsultasi</p>
        </div>

        <h3 style="color: #667eea;">Detail Konsultasi:</h3>
        <table>
            <tr>
                <td>Subjek</td>
                <td>{{ $consultation->subject }}</td>
            </tr>
            <tr>
                <td>Kategori</td>
                <td>{{ $consultation->category->name ?? 'Kategori Konsultasi' }}</td>
            </tr>
            <tr>
                <td>Tanggal Pengajuan</td>
                <td>{{ $consultation->created_at->format('d F Y, H:i') }} WIB</td>
            </tr>
            <tr>
                <td>Status</td>
                <td><strong style="color: #f59e0b;">● Menunggu Proses</strong></td>
            </tr>
        </table>

        <h3 style="color: #667eea;">📝 Deskripsi Konsultasi:</h3>
        <p style="background: #f8f9fa; padding: 15px; border-radius: 5px; font-style: italic;">
            "{{ $consultation->description }}"
        </p>

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ route('user.consultations.show', $consultation->id) }}" class="button">
                👁️ Lihat Detail Konsultasi
            </a>
        </div>

        <hr style="border: none; border-top: 1px solid #eee; margin: 30px 0;">

        <h3 style="color: #667eea;">ℹ️ Informasi Penting:</h3>
        <ul style="line-height: 1.8;">
            <li>Tim kami akan memproses konsultasi Anda dalam 1x24 jam</li>
            <li>Anda akan menerima email notifikasi jika ada update status</li>
            <li>Gunakan nomor tiket untuk melacak status konsultasi</li>
            <li>Anda dapat mengakses detail konsultasi melalui dashboard</li>
        </ul>

        <p style="margin-top: 30px;">
            Jika ada pertanyaan, silakan hubungi kami:<br>
            📧 Email: disperindag@jatengprov.go.id<br>
            📞 Telepon: (024) 3520044
        </p>
    </div>

    <div class="footer">
        <p style="margin: 0;">
            <strong>Dinas Perindustrian dan Perdagangan Provinsi Jawa Tengah</strong><br>
            Jl. Mgr. Soegijapranata No.1, Semarang<br>
            © 2026 SiapKangmas - Sistem Informasi Aplikasi Pelayanan Publik
        </p>
        <p style="margin: 15px 0 0 0; font-size: 11px; color: #999;">
            Email ini dikirim otomatis. Mohon tidak membalas email ini.
        </p>
    </div>
</body>
</html>