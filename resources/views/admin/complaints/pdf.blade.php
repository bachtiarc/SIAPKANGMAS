<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Pengaduan {{ $complaint->ticket_number ?? ($complaint->ticket_id ?? $complaint->id) }}</title>
    <style>
        /**
         * Font resmi dokumen
         * Times-Roman ≈ Times New Roman (DomPDF compatible)
         */
        body {
            font-family: "Times-Roman", serif;
            font-size: 12px;
            line-height: 1.5;
        }

        h1 {
            font-size: 18px;
            margin-bottom: 10px;
            text-align: center;
        }

        hr {
            margin: 12px 0;
        }

        .label {
            font-weight: bold;
            margin-top: 10px;
        }

        .box {
            border: 1px solid #000;
            padding: 8px;
            margin-top: 4px;
            text-align: justify;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th, td {
            border: 1px solid #000;
            padding: 6px;
            font-size: 11px;
            vertical-align: top;
        }

        th {
            background: #f2f2f2;
            text-align: left;
        }

        .note {
            margin-top: 20px;
            font-size: 11px;
            font-style: italic;
        }

        .footer {
            margin-top: 40px;
            font-size: 11px;
        }
    </style>
</head>
<body>

@php
    $ticketNo = $complaint->ticket_number ?? ($complaint->ticket_id ?? $complaint->id);

    $statusLabel = match($complaint->status) {
        'pending' => 'Pending',
        'diproses' => 'Sedang Diproses',
        'selesai' => 'Selesai',
        'rejected' => 'Ditolak',
        default => ucfirst($complaint->status)
    };
@endphp

<h1>DETAIL PENGAJUAN PENGADUAN</h1>

<p><b>No Tiket</b> : {{ $ticketNo }}</p>
<p><b>Status</b> : {{ $statusLabel }}</p>
<p><b>Tanggal Pengajuan</b> : {{ $complaint->created_at->format('d F Y') }}</p>

<hr>

<p class="label">Judul / Subjek</p>
<div class="box">
    {{ $complaint->title ?? $complaint->subject ?? '-' }}
</div>

<p class="label">Deskripsi</p>
<div class="box">
    {{ $complaint->description ?? '-' }}
</div>

<p class="label">Data Pemohon</p>
<table>
    <tr>
        <th width="30%">Nama</th>
        <td>{{ $complaint->user->name ?? '-' }}</td>
    </tr>
    <tr>
        <th>Email</th>
        <td>{{ $complaint->user->email ?? '-' }}</td>
    </tr>
    <tr>
        <th>Telepon</th>
        <td>{{ $complaint->user->phone ?? '-' }}</td>
    </tr>
</table>

<p class="label">Riwayat Status</p>
<table>
    <tr>
        <th width="20%">Tanggal</th>
        <th width="20%">Status</th>
        <th>Catatan</th>
    </tr>
    @foreach($complaint->statusHistories as $history)
        @php
            $historyLabel = match($history->new_status) {
                'pending' => 'Pending',
                'diproses' => 'Sedang Diproses',
                'selesai' => 'Selesai',
                'rejected' => 'Ditolak',
                default => ucfirst($history->new_status)
            };
        @endphp
        <tr>
            <td>{{ $history->created_at->format('d M Y H:i') }}</td>
            <td>{{ $historyLabel }}</td>
            <td>{{ $history->notes ?? '-' }}</td>
        </tr>
    @endforeach
</table>

{{-- ✅ CATATAN LAMPIRAN --}}
<p class="note">
    <b>Catatan:</b><br>
    Lampiran dokumen pendukung <u>tidak disertakan dalam berkas PDF ini</u> dan
    <b>diunduh secara terpisah</b> melalui sistem aplikasi SIAPKANGMAS.
</p>

<div class="footer">
    <p>
        Dokumen ini dihasilkan secara otomatis oleh sistem SIAPKANGMAS dan
        digunakan sebagai arsip administrasi.
    </p>
</div>

</body>
</html>
