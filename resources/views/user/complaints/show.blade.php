@extends('layouts.dashboard')

@section('title', 'Detail Pengaduan - ' . $complaint->ticket_number)

@section('content')
<div class="p-6 bg-gray-50 min-h-screen">

@php
    $from = request()->get('from', 'index');

    if ($from === 'dashboard') $backUrl = route('user.dashboard');
    elseif ($from === 'history') $backUrl = route('user.history.index');
    elseif ($from === 'search') {
        $searchQuery = request()->get('q', '');
        $backUrl = route('search.result', ['q' => $searchQuery]);
    } else $backUrl = route('user.complaints.index');

    $statusLower = strtolower((string) $complaint->status);
    $isPending    = in_array($statusLower, ['pending','belum diproses'], true);
    $isProcessing = in_array($statusLower, ['diproses','in_progress','on_progress','sedang diproses'], true);
    $isCompleted  = in_array($statusLower, ['selesai','completed'], true);
    $isRejected   = in_array($statusLower, ['ditolak','rejected'], true);
    $isFinished   = $isCompleted || $isRejected;

    if ($isPending) $progressWidth = '25%';
    elseif ($isProcessing) $progressWidth = '58%';
    elseif ($isFinished) $progressWidth = '100%';
    else $progressWidth = '0%';

    $app = $complaint->applicant;

    $alamatDetail = trim((string)($app->alamat_detail ?? ''));
    $desaNama = $app->desa_nama ?? '-';
    $kecNama  = $app->kecamatan_nama ?? '-';
    $kabNama  = $app->kabupaten_nama ?? '-';
    $provNama = $app->provinsi ?? '-';

    // pekerjaan (kalau "Lainnya", pakai isinya)
    $pekerjaan = $app->pekerjaan ?? '-';
    $pekerjaanLainnya = $app->pekerjaan_lainnya ?? null;
    if ($pekerjaan === 'Lainnya' && $pekerjaanLainnya) $pekerjaan = $pekerjaanLainnya;

    $isKel = (isset($app->is_kelurahan) && (bool) $app->is_kelurahan);
    $desaLabel = ($app && $isKel) ? 'Kelurahan ' . $desaNama : 'Desa ' . $desaNama;

    $kabLower = strtolower(trim((string) $kabNama));
    $isKotaKab = str_starts_with($kabLower, 'kota');
    $kabClean = $isKotaKab
        ? trim(preg_replace('/^kota\s+/i', '', (string)$kabNama))
        : trim(preg_replace('/^kab\.?\s+/i', '', (string)$kabNama));
    $kabLabel = $isKotaKab ? ('Kota ' . $kabClean) : ('Kab. ' . $kabClean);

    $alamatLengkap = collect([
        $alamatDetail,
        $desaLabel,
        'Kec. ' . $kecNama,
        $kabLabel,
        $provNama,
    ])->filter(fn($v) => trim((string)$v) !== '' && trim((string)$v) !== '-')->implode(', ');

    $supabaseUrl = rtrim((string) env('SUPABASE_URL'), '/');

    // URL KTP
    $ktpBucket = env('SUPABASE_KTP_BUCKET', 'ktp-photos');
    $ktpRaw = $app->foto_ktp ?? null;
    $ktpUrl = null;

    if ($ktpRaw && $supabaseUrl) {
        $ktpRaw = ltrim((string) $ktpRaw, '/');

        if (\Illuminate\Support\Str::startsWith($ktpRaw, ['http://', 'https://'])) {
            $ktpUrl = $ktpRaw;
        } else {
            if (\Illuminate\Support\Str::startsWith($ktpRaw, $ktpBucket . '/')) {
                $ktpRaw = \Illuminate\Support\Str::after($ktpRaw, $ktpBucket . '/');
            }
            $ktpUrl = "{$supabaseUrl}/storage/v1/object/public/{$ktpBucket}/{$ktpRaw}";
        }
    }

    // bucket dokumen pengaduan (kalau controller view/download kamu sudah benar, blade ga perlu bikin url supabase langsung)
    $bucket = env('SUPABASE_COMPLAINTS_BUCKET', 'complaints');
@endphp

    <!-- Breadcrumb -->
    <nav class="mb-6 text-sm">
        <ol class="flex items-center space-x-2">
            <li><a href="{{ route('user.dashboard') }}" class="text-blue-600 hover:text-blue-800">Beranda</a></li>
            <li class="text-gray-400">/</li>
            <li><a href="{{ route('user.complaints.index') }}" class="text-blue-600 hover:text-blue-800">Pengaduan</a></li>
            <li class="text-gray-400">/</li>
            <li class="text-gray-600">Detail</li>
        </ol>
    </nav>

    <!-- Header Action -->
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center space-x-4">
            @unless($isPdf ?? false)
                <a href="{{ $backUrl }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali
                </a>
            @endunless

            <div>
                <h1 class="text-2xl font-bold text-gray-900">Detail Pengaduan</h1>
                <p class="text-sm text-gray-600">Tiket: <span class="font-semibold text-blue-700">{{ $complaint->ticket_number }}</span></p>
            </div>
        </div>

        @unless($isPdf ?? false)
            <div class="flex items-center gap-2">
                <a href="{{ route('user.complaints.download', $complaint) }}"
                   class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Download PDF
                </a>
            </div>
        @endunless
    </div>

    <!-- Progres -->
    <div class="bg-white rounded-lg shadow-sm p-8 mb-6">
        <h2 class="flex items-center text-lg font-bold text-gray-900 mb-6">
            <svg class="w-6 h-6 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h12M4 18h8"/>
            </svg>
            Progres Pengaduan
        </h2>

        <div class="relative">
            <div class="absolute top-6 left-0 w-full h-0.5 bg-gray-200"></div>
            <div class="absolute top-6 left-0 h-0.5 bg-green-500 transition-all duration-500" style="width: {{ $progressWidth }};"></div>

            <div class="relative flex justify-between">
                <div class="flex flex-col items-center w-1/4">
                    <div class="w-12 h-12 rounded-full bg-green-500 flex items-center justify-center text-white z-10">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <p class="font-semibold text-sm mt-2">Terkirim</p>
                    <p class="text-xs text-gray-500">{{ optional($complaint->created_at)->format('d M Y, H:i') }}</p>
                </div>

                <div class="flex flex-col items-center w-1/4">
                    <div class="w-12 h-12 rounded-full {{ $isPending ? 'bg-yellow-400' : 'bg-green-500' }} flex items-center justify-center text-white z-10">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M6 2h12M6 22h12M8 2v6l4 4-4 4v6M16 2v6l-4 4 4 4v6"/>
                        </svg>
                    </div>
                    <p class="font-semibold text-sm mt-2">Menunggu Diproses</p>
                    <p class="text-xs text-gray-500">{{ optional($complaint->updated_at)->format('d M Y, H:i') }}</p>
                </div>

                <div class="flex flex-col items-center w-1/4">
                    <div class="w-12 h-12 rounded-full {{ $isProcessing ? 'bg-blue-500' : ($isFinished ? 'bg-green-500' : 'bg-gray-300') }} flex items-center justify-center text-white z-10">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6l4 2"/>
                        </svg>
                    </div>
                    <p class="font-semibold text-sm mt-2">Sedang Diproses</p>
                    <p class="text-xs text-gray-500">{{ optional($complaint->updated_at)->format('d M Y, H:i') }}</p>
                </div>

                <div class="flex flex-col items-center w-1/4">
                    <div class="w-12 h-12 rounded-full {{ $isCompleted ? 'bg-green-500' : ($isRejected ? 'bg-red-500' : 'bg-gray-300') }} flex items-center justify-center text-white z-10">
                        @if($isRejected)
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        @else
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        @endif
                    </div>
                    <p class="font-semibold text-sm mt-2">{{ $isCompleted ? 'Selesai' : ($isRejected ? 'Ditolak' : 'Menunggu') }}</p>
                    @if($complaint->completed_at)
                        <p class="text-xs text-gray-500">{{ $complaint->completed_at->format('d M Y, H:i') }}</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="mt-6">
            <span class="inline-flex px-4 py-2 text-sm font-semibold rounded-lg {{ $complaint->status_badge }}">
                Status: {{ $complaint->status_label }}
            </span>
        </div>
    </div>

    <!-- I. Data Pemohon (tampil 1-1) -->
    <div class="bg-white rounded-lg shadow-sm p-8 mb-6">
        <h2 class="text-lg font-bold text-gray-900 mb-6 border-b pb-3">I. Data Pemohon</h2>

        <div class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="text-sm text-gray-600 font-semibold">Nama Lengkap</div>
                <div class="md:col-span-2 text-gray-900">{{ $app->nama_lengkap ?? '-' }}</div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="text-sm text-gray-600 font-semibold">NIK</div>
                <div class="md:col-span-2 text-gray-900">{{ $app->nik ?? '-' }}</div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="text-sm text-gray-600 font-semibold">Email</div>
                <div class="md:col-span-2 text-gray-900">{{ $app->email ?? '-' }}</div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="text-sm text-gray-600 font-semibold">Nomor Telepon</div>
                <div class="md:col-span-2 text-gray-900">{{ $app->phone ?? '-' }}</div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="text-sm text-gray-600 font-semibold">Pekerjaan</div>
                <div class="md:col-span-2 text-gray-900">{{ $pekerjaan ?: '-' }}</div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="text-sm text-gray-600 font-semibold">Provinsi</div>
                <div class="md:col-span-2 text-gray-900">{{ $provNama ?: '-' }}</div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="text-sm text-gray-600 font-semibold">Kabupaten/Kota</div>
                <div class="md:col-span-2 text-gray-900">{{ $kabLabel ?: '-' }}</div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="text-sm text-gray-600 font-semibold">Kecamatan</div>
                <div class="md:col-span-2 text-gray-900">{{ $kecNama ?: '-' }}</div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="text-sm text-gray-600 font-semibold">Desa/Kelurahan</div>
                <div class="md:col-span-2 text-gray-900">{{ $desaLabel ?: '-' }}</div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="text-sm text-gray-600 font-semibold">Alamat Lengkap</div>
                <div class="md:col-span-2 text-gray-900">
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 whitespace-pre-line">
                        {{ $alamatDetail !== '' ? $alamatDetail : '-' }}
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="text-sm text-gray-600 font-semibold">Foto KTP</div>
                <div class="md:col-span-2">
                    @if($ktpUrl)
                        @unless($isPdf ?? false)
                            <a href="{{ $ktpUrl }}" target="_blank"
                               class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition text-blue-600">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                Lihat Foto KTP
                            </a>
                        @else
                            <span class="text-gray-500">Terlampir (lihat di sistem)</span>
                        @endunless
                    @else
                        <span class="text-gray-500">-</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- II. Rincian Pengaduan -->
    <div class="bg-white rounded-lg shadow-sm p-8 mb-6">
        <h2 class="text-lg font-bold text-gray-900 mb-6 border-b pb-3">II. Rincian Pengaduan</h2>

        <div class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="text-sm text-gray-600 font-semibold">Subjek</div>
                <div class="md:col-span-2 text-gray-900">{{ $complaint->subject }}</div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="text-sm text-gray-600 font-semibold">Tanggal Pengajuan</div>
                <div class="md:col-span-2 text-gray-900">{{ optional($complaint->created_at)->format('d F Y, H:i') }} WIB</div>
            </div>

            <div>
                <p class="text-sm font-semibold text-gray-600 mb-2">Deskripsi Lengkap</p>
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-5 whitespace-pre-line text-gray-900">
                    {{ $complaint->description }}
                </div>
            </div>
        </div>
    </div>

    <!-- III. Dokumen Pendukung -->
    <div class="bg-white rounded-lg shadow-sm p-8">
        <h2 class="text-lg font-bold text-gray-900 mb-6 border-b pb-3">III. Dokumen Pendukung</h2>

        @if($complaint->documents && $complaint->documents->count() > 0)
            <div class="space-y-3">
                @foreach($complaint->documents as $doc)
                    @php
                        $fileName = $doc->original_name ?? basename((string)$doc->file_path);

                        // ✅ ini sesuai route yang kamu kasih (DENGAN prefix user.)
                        $viewRoute = route('user.complaints.documents.view', $doc);
                        $downloadRoute = route('user.complaints.document.download', $doc);
                    @endphp

                    <div class="flex items-center justify-between p-4 bg-gray-50 border border-gray-200 rounded-lg hover:bg-gray-100 transition">
                        <div class="min-w-0">
                            <p class="font-semibold text-gray-900 truncate max-w-[260px]">{{ $fileName }}</p>
                            @if(!empty($doc->file_size))
                                <p class="text-xs text-gray-500">{{ number_format(($doc->file_size ?? 0) / 1024, 2) }} KB</p>
                            @endif
                        </div>

                        @unless($isPdf ?? false)
                            <div class="flex items-center gap-2">
                                <a href="{{ $viewRoute }}" target="_blank"
                                   class="inline-flex items-center px-3 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition text-blue-600">
                                    Lihat
                                </a>

                                <a href="{{ $downloadRoute }}"
                                   class="inline-flex items-center px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                                    Download
                                </a>
                            </div>
                        @endunless
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-500">Tidak ada dokumen pendukung.</p>
        @endif
    </div>

</div>
@endsection