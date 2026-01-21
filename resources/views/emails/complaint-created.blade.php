<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaduan Diterima</title>
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
            background-color: #dc2626;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background-color: #f9fafb;
            padding: 30px;
            border-radius: 0 0 8px 8px;
        }
        .ticket-box {
            background-color: #fee2e2;
            border-left: 4px solid #dc2626;
            padding: 15px;
            margin: 20px 0;
        }
        .ticket-number {
            font-size: 24px;
            font-weight: bold;
            color: #991b1b;
            margin: 10px 0;
        }
        .info-row {
            margin: 10px 0;
        }
        .label {
            font-weight: bold;
            color: #4b5563;
        }
        .button {
            display: inline-block;
            background-color: #dc2626;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            color: #6b7280;
            font-size: 12px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Pengaduan Anda Telah Diterima</h1>
    </div>
    
    <div class="content">
        <p>Yth. <strong>{{ $complaint->user->name }}</strong>,</p>
        
        <p>Terima kasih telah menyampaikan pengaduan melalui SIAPKANGMAS. Pengaduan Anda telah kami terima dan akan segera diproses oleh tim kami.</p>
        
        <div class="ticket-box">
            <div class="label">NOMOR TIKET PENGADUAN</div>
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
            <span class="label">Tanggal Pengajuan:</span> {{ $complaint->created_at->format('d F Y, H:i') }} WIB
        </div>
        
        <div class="info-row">
            <span class="label">Status:</span> <strong>Menunggu Verifikasi</strong>
        </div>
        
        <p style="margin-top: 20px;">Anda dapat memantau perkembangan pengaduan Anda melalui dashboard atau klik tombol di bawah ini:</p>
        
        <center>
            <a href="{{ route('user.complaints.show', $complaint->id) }}" class="button">
                Lihat Detail Pengaduan
            </a>
        </center>
        
        <p style="margin-top: 20px; font-size: 14px; color: #6b7280;">
            <strong>Catatan:</strong> Kami akan mengirimkan notifikasi email setiap kali ada pembaruan status pada pengaduan Anda.
        </p>
    </div>
    
    <div class="footer">
        <p><strong>SIAPKANGMAS - Sistem Aplikasi Konsultasi, Pengaduan, & Permohonan Informasi</strong></p>
        <p>Dinas Perindustrian dan Perdagangan Provinsi Jawa Tengah</p>
        <p>Email ini dikirim secara otomatis, mohon tidak membalas email ini.</p>
    </div>
</body>
</html>