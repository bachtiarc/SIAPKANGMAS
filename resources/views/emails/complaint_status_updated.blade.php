<!DOCTYPE html>
<html>
<head>
    <title>Update Status Pengaduan</title>
</head>
<body style="font-family: Arial, sans-serif; padding: 20px;">
    <h2>Halo, {{ $complaint->user->name ?? 'Pemohon' }}</h2>

    <p>
        Ada pembaruan pada tiket pengaduan Anda
        <strong>#{{ $complaint->ticket_number ?? ($complaint->ticket_id ?? $complaint->id) }}</strong>.
    </p>

    @php
        $statusLabel = match($complaint->status) {
            'pending'  => 'Pending',
            'diproses' => 'Sedang Diproses',
            'selesai'  => 'Selesai',
            'ditolak'  => 'Ditolak',
            default    => ucfirst($complaint->status),
        };
    @endphp

    <p>Status Terbaru: <strong>{{ $statusLabel }}</strong></p>

    @if(!empty($notes))
        <div style="background-color: #f3f4f6; padding: 15px; border-left: 4px solid #3b82f6; margin: 20px 0;">
            <p style="margin: 0; font-weight: bold;">Tanggapan / Catatan Admin:</p>
            <p style="margin-top: 5px;">{{ $notes }}</p>
        </div>
    @endif

    <p>Silakan login ke dashboard SIAPKANGMAS untuk melihat detail lebih lanjut.</p>
    <br>
    <p>Terima kasih,<br>Tim Admin SIAPKANGMAS</p>
</body>
</html>