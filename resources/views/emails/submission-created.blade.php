<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Permohonan Informasi Berhasil</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 30px;
        }
        .ticket-box {
            background: #f0f9ff;
            border-left: 4px solid #3b82f6;
            padding: 20px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .ticket-box strong {
            color: #1e40af;
            font-size: 18px;
        }
        .info-row {
            display: flex;
            padding: 10px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-label {
            font-weight: 600;
            color: #6b7280;
            width: 150px;
            flex-shrink: 0;
        }
        .info-value {
            color: #111827;
        }
        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            background: #fef3c7;
            color: #92400e;
            border-radius: 4px;
            font-size: 14px;
            font-weight: 600;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background: #3b82f6;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            margin-top: 20px;
        }
        .button:hover {
            background: #2563eb;
        }
        .footer {
            background: #f9fafb;
            padding: 20px;
            text-align: center;
            color: #6b7280;
            font-size: 14px;
        }
        .note {
            background: #fffbeb;
            border-left: 4px solid #f59e0b;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>✅ Formulir Berhasil Terkirim</h1>
            <p style="margin: 10px 0 0 0; opacity: 0.9;">SIAPKANGMAS - DISPERINDAG Jawa Tengah</p>
        </div>

        <!-- Content -->
        <div class="content">
            <p>Yth. <strong>{{ $user->name ?? 'Pengguna' }}</strong>,</p>
            
            <p>Terima kasih telah mengirimkan permohonan informasi melalui SIAPKANGMAS. Pengajuan Anda telah kami terima dan akan segera ditinjau.</p>

            <!-- Ticket Number -->
            <div class="ticket-box">
                <div style="text-align: center;">
                    <div style="color: #6b7280; font-size: 14px; margin-bottom: 5px;">Nomor Tiket Anda</div>
                    <strong>{{ $submission->ticket_id ?? 'N/A' }}</strong>
                    <div style="color: #6b7280; font-size: 12px; margin-top: 5px;">{{ $submission->full_ticket_number ?? '' }}</div>
                </div>
            </div>

            <!-- Submission Details -->
            <h3 style="color: #1e40af; margin-top: 30px;">Detail Permohonan</h3>
            
            <div class="info-row">
                <div class="info-label">Kategori</div>
                <div class="info-value">{{ $category->name ?? 'N/A' }}</div>
            </div>
            
            <div class="info-row">
                <div class="info-label">Judul</div>
                <div class="info-value">{{ $submission->title ?? 'N/A' }}</div>
            </div>
            
            <div class="info-row">
                <div class="info-label">Tanggal Pengajuan</div>
                <div class="info-value">
                    @if($submission->submitted_at)
                        {{ $submission->submitted_at->format('d F Y, H:i') }} WIB
                    @elseif($submission->created_at)
                        {{ $submission->created_at->format('d F Y, H:i') }} WIB
                    @else
                        {{ date('d F Y, H:i') }} WIB
                    @endif
                </div>
            </div>
            
            <div class="info-row">
                <div class="info-label">Status</div>
                <div class="info-value">
                    <span class="status-badge">Menunggu Verifikasi</span>
                </div>
            </div>

            <!-- Note -->
            <div class="note">
                <strong>📌 Catatan Penting:</strong>
                <ul style="margin: 10px 0 0 0; padding-left: 20px;">
                    <li>Estimasi respon waktu: <strong>1 x 24 jam</strong></li>
                    <li>Simpan nomor tiket untuk melacak status permohonan</li>
                    <li>Anda akan menerima email notifikasi saat status berubah</li>
                </ul>
            </div>

            <!-- CTA Button -->
            <div style="text-align: center; margin-top: 30px;">
                <a href="{{ url('/pegawai/permohonan-informasi/' . $submission->id) }}" class="button">
                    Lacak Status Permohonan
                </a>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p style="margin: 0 0 10px 0;"><strong>Dinas Perindustrian dan Perdagangan Provinsi Jawa Tengah</strong></p>
            <p style="margin: 0; font-size: 13px;">
                Email ini dikirim otomatis oleh sistem. Mohon tidak membalas email ini.
            </p>
            <p style="margin: 10px 0 0 0; font-size: 13px;">
                Butuh bantuan? Hubungi kami di <a href="mailto:siapkangmasdisperindag@gmail.com" style="color: #3b82f6;">siapkangmasdisperindag@gmail.com</a>
            </p>
        </div>
    </div>
</body>
</html>