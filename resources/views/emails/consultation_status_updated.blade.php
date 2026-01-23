<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Status Konsultasi</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; background-color: #f4f4f4; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 20px auto; background: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header { background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%); color: white; padding: 30px; text-align: center; }
        .header h1 { margin: 0; font-size: 24px; }
        .content { padding: 30px; }
        .ticket-box { background: #f0f9ff; border-left: 4px solid #3b82f6; padding: 20px; margin: 20px 0; border-radius: 4px; }
        .ticket-box strong { color: #1e40af; font-size: 18px; }
        .info-row { display: flex; padding: 10px 0; border-bottom: 1px solid #e5e7eb; }
        .info-row:last-child { border-bottom: none; }
        .info-label { font-weight: 600; color: #6b7280; width: 150px; flex-shrink: 0; }
        .info-value { color: #111827; }

        .status-badge { display: inline-block; padding: 6px 12px; border-radius: 4px; font-size: 14px; font-weight: 700; }
        .badge-yellow { background: #fef3c7; color: #92400e; }
        .badge-green  { background: #dcfce7; color: #166534; }
        .badge-red    { background: #fee2e2; color: #991b1b; }
        .badge-gray   { background: #f3f4f6; color: #374151; }

        .footer { background: #f9fafb; padding: 20px; text-align: center; color: #6b7280; font-size: 14px; }
        .note { background: #fffbeb; border-left: 4px solid #f59e0b; padding: 15px; margin: 20px 0; border-radius: 4px; }
        .note-blue { background: #f3f4f6; border-left: 4px solid #3b82f6; padding: 15px; margin: 20px 0; border-radius: 4px; }
    </style>
</head>
<body>

@php
    $statusLabel = match($consultation->status) {
        'pending'      => 'Belum Diproses',
        'on_progress'  => 'Sedang Diproses',
        'completed'    => 'Selesai',
        'selesai'      => 'Selesai',
        'rejected'     => 'Ditolak',
        'ditolak'      => 'Ditolak',
        default        => ucfirst($consultation->status),
    };

    $badgeClass = match($consultation->status) {
        'on_progress' => 'badge-yellow',
        'completed', 'selesai' => 'badge-green',
        'rejected', 'ditolak'  => 'badge-red',
        'pending' => 'badge-gray',
        default => 'badge-gray',
    };
@endphp

<div class="container">
    <div class="header">
        <h1>Status Konsultasi Diperbarui</h1>
        <p style="margin: 10px 0 0 0; opacity: 0.9;">SIAPKANGMAS - DISPERINDAG Jawa Tengah</p>
    </div>

    <div class="content">
        <p>Yth. <strong>{{ $consultation->user->name ?? 'Pengguna' }}</strong>,</p>

        <p>
            Ada pembaruan pada tiket konsultasi Anda
            <strong>#{{ $consultation->ticket_id }}</strong>.
        </p>

        <div class="ticket-box">
            <div style="text-align: center;">
                <div style="color: #6b7280; font-size: 14px; margin-bottom: 5px;">Nomor Tiket Anda</div>
                <strong>{{ $consultation->ticket_id ?? 'N/A' }}</strong>
            </div>
        </div>

        <h3 style="color: #1e40af; margin-top: 30px;">Detail Pembaruan</h3>

        <div class="info-row">
            <div class="info-label">Status Terbaru</div>
            <div class="info-value">
                <span class="status-badge {{ $badgeClass }}">{{ $statusLabel }}</span>
            </div>
        </div>

        <div class="info-row">
            <div class="info-label">Waktu Update</div>
            <div class="info-value">
                {{ optional($consultation->updated_at)->format('d F Y, H:i') ?? date('d F Y, H:i') }} WIB
            </div>
        </div>

        @if(!empty($note))
            <div class="note-blue">
                <strong style="display:block; margin:0;">Tanggapan / Catatan Admin:</strong>
                <div style="margin-top: 6px;">{{ $note }}</div>
            </div>
        @endif

        <div class="note">
            <strong>Catatan Penting:</strong>
            <ul style="margin: 10px 0 0 0; padding-left: 20px;">
                <li>Simpan nomor tiket untuk melacak status konsultasi</li>
                <li>Anda akan menerima email notifikasi saat status berubah</li>
            </ul>
        </div>
    </div>

    <div class="footer">
        <p style="margin: 0 0 10px 0;"><strong>Dinas Perindustrian dan Perdagangan Provinsi Jawa Tengah</strong></p>
        <p style="margin: 0; font-size: 13px;">Email ini dikirim otomatis oleh sistem. Mohon tidak membalas email ini.</p>
        <p style="margin: 10px 0 0 0; font-size: 13px;">
            Butuh bantuan? Hubungi kami di
            <a href="mailto:siapkangmasdisperindag@gmail.com" style="color: #3b82f6;">siapkangmasdisperindag@gmail.com</a>
        </p>
    </div>
</div>
</body>
</html>
