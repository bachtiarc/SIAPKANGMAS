{{-- resources/views/pdfs/masyarakat-complaint.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $complaint->ticket_number }}</title>
    <style>
        @page { margin: 0; }
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1.6;
            color: #000;
            padding: 30px 40px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #000;
            padding-bottom: 20px;
        }
        .header h1 { font-size: 16pt; font-weight: bold; margin-bottom: 5px; text-transform: uppercase; }
        .header h2 { font-size: 14pt; font-weight: bold; margin-bottom: 3px; }
        .header p { font-size: 10pt; margin: 2px 0; }

        .document-title {
            text-align: center;
            margin: 30px 0;
            font-size: 14pt;
            font-weight: bold;
            text-decoration: underline;
            text-transform: uppercase;
        }

        .ticket-info {
            text-align: center;
            margin-bottom: 20px;
            padding: 10px;
            background-color: #f0f0f0;
            border: 1px solid #000;
        }
        .ticket-info p { font-size: 11pt; margin: 5px 0; }
        .ticket-id { font-weight: bold; font-size: 13pt; color: #000; }

        .content-section { margin: 20px 0; }
        .content-section h3 {
            font-size: 12pt;
            font-weight: bold;
            margin-bottom: 10px;
            border-bottom: 1px solid #000;
            padding-bottom: 5px;
        }

        .info-row { margin: 10px 0; display: table; width: 100%; }
        .info-label { display: table-cell; width: 35%; font-weight: bold; vertical-align: top; padding: 5px 0; }
        .info-value { display: table-cell; width: 5%; vertical-align: top; padding: 5px 0; }
        .info-content { display: table-cell; width: 60%; vertical-align: top; padding: 5px 0; text-align: justify; }

        .description-box {
            border: 1px solid #000;
            padding: 15px;
            margin: 10px 0;
            background-color: #fafafa;
            white-space: pre-line;
            text-align: justify;
        }

        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border: 1px solid #000;
            font-weight: bold;
            margin: 5px 0;
        }

        .admin-response {
            border: 2px solid #000;
            padding: 15px;
            margin: 15px 0;
            background-color: #f9f9f9;
        }
        .admin-response h4 { font-size: 11pt; font-weight: bold; margin-bottom: 10px; }
        .admin-response p { white-space: pre-line; text-align: justify; margin: 10px 0; }
        .admin-info { margin-top: 15px; font-size: 10pt; font-style: italic; }

        .signature-section { margin-top: 50px; }
        .signature-box { float: right; width: 45%; text-align: center; }
        .signature-box p { margin: 5px 0; }
        .signature-space { height: 80px; margin: 20px 0; }
        .clear { clear: both; }

        .document-footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #000;
            font-size: 9pt;
            text-align: center;
            color: #666;
        }
        .document-footer p { margin: 5px 0; }
    </style>
</head>

<body>
    {{-- Header --}}
    <div class="header">
        <h1>PEMERINTAH PROVINSI JAWA TENGAH</h1>
        <h2>DINAS PERINDUSTRIAN DAN PERDAGANGAN</h2>
        <p>Jl. Pemuda No. 117 Semarang 50132</p>
        <p>Telepon: (024) 3520994, 3545156 | Fax: (024) 3545156</p>
        <p>Website: www.disperindag.jatengprov.go.id | Email: disperindag@jatengprov.go.id</p>
    </div>

    {{-- Document Title --}}
    <div class="document-title">
        {{ $serviceTitle }}
    </div>

    {{-- Ticket Info --}}
    <div class="ticket-info">
        <p>Nomor Tiket:</p>
        <p class="ticket-id">{{ $complaint->ticket_number }}</p>
        <p style="font-size: 9pt; margin-top: 5px;">
            Tanggal: {{ optional($complaint->created_at)->format('d F Y') }}
        </p>
    </div>

    {{-- Personal Information --}}
    <div class="content-section">
        <h3>I. DATA PEMOHON</h3>

        <div class="info-row">
            <div class="info-label">Nama Lengkap</div>
            <div class="info-value">:</div>
            <div class="info-content">{{ $user->name }}</div>
        </div>

        <div class="info-row">
            <div class="info-label">NIK</div>
            <div class="info-value">:</div>
            <div class="info-content">{{ $user->nik ?? '-' }}</div>
        </div>

        <div class="info-row">
            <div class="info-label">Email</div>
            <div class="info-value">:</div>
            <div class="info-content">{{ $user->email ?? '-' }}</div>
        </div>

        <div class="info-row">
            <div class="info-label">Nomor Telepon</div>
            <div class="info-value">:</div>
            <div class="info-content">{{ $user->phone ?? '-' }}</div>
        </div>

        <div class="info-row">
            <div class="info-label">Alamat</div>
            <div class="info-value">:</div>
            <div class="info-content">{{ $user->address ?? '-' }}</div>
        </div>
    </div>

    {{-- Complaint Details --}}
    <div class="content-section">
        <h3>II. RINCIAN PENGADUAN</h3>

        <div class="info-row">
            <div class="info-label">Kategori Pengaduan</div>
            <div class="info-value">:</div>
            <div class="info-content">{{ $complaint->category->name ?? '-' }}</div>
        </div>

        <div class="info-row">
            <div class="info-label">Judul Pengaduan</div>
            <div class="info-value">:</div>
            <div class="info-content">{{ $complaint->subject ?? '-' }}</div>
        </div>

        <div class="info-row">
            <div class="info-label">Tanggal Pengajuan</div>
            <div class="info-value">:</div>
            <div class="info-content">
                {{ optional($complaint->created_at)->format('d F Y, H:i') }} WIB
            </div>
        </div>

        <div class="info-row">
            <div class="info-label">Status</div>
            <div class="info-value">:</div>
            <div class="info-content">
                <span class="status-badge">{{ $statusLabel }}</span>
            </div>
        </div>

        <div style="margin-top: 15px;">
            <p style="font-weight: bold; margin-bottom: 5px;">Deskripsi Lengkap:</p>
            <div class="description-box">{{ $complaint->description }}</div>
        </div>
    </div>

    {{-- Admin Response --}}
    @if(!empty($complaint->admin_response))
        <div class="content-section">
            <h3>III. TANGGAPAN DARI ADMIN</h3>
            <div class="admin-response">
                <h4>Respon:</h4>
                <p>{{ $complaint->admin_response }}</p>

                <div class="admin-info">
                    @if($complaint->handler)
                        <p>Ditangani oleh: {{ $complaint->handler->name }}</p>
                    @endif
                    @if($complaint->completed_at)
                        <p>Tanggal Selesai: {{ $complaint->completed_at->format('d F Y, H:i') }} WIB</p>
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- Signature --}}
    <div class="signature-section">
        <div class="signature-box">
            <p>Semarang, {{ optional($complaint->created_at)->format('d F Y') }}</p>
            <p>Pemohon,</p>
            <div class="signature-space"></div>
            <p style="font-weight: bold;">{{ $user->name }}</p>
        </div>
        <div class="clear"></div>
    </div>

    {{-- Footer --}}
    <div class="document-footer">
        <p>Dokumen ini dicetak secara elektronik dan sah tanpa tanda tangan basah.</p>
        <p>Dicetak pada {{ now()->format('d F Y, H:i') }} WIB</p>
    </div>
</body>
</html>