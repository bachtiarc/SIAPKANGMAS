<!DOCTYPE html>
<html>
<head>
    <title>Update Status Pengajuan</title>
</head>
<body style="font-family: Arial, sans-serif; padding: 20px;">
    <h2>Halo, {{ $submission->user->name }}</h2>
    <p>Status untuk tiket pengajuan <strong>#{{ $submission->ticket_id }}</strong> telah diperbarui.</p>
    
    <p>Status Baru: <strong>{{ ucfirst($submission->status) }}</strong></p>
    
    @if($note)
    <p><strong>Catatan Admin:</strong><br>
    {{ $note }}</p>
    @endif

    <p>Silakan login ke dashboard Anda untuk melihat detail lebih lanjut.</p>
    <br>
    <p>Terima kasih,<br>Tim Admin SIAPKANGMAS</p>
</body>
</html>