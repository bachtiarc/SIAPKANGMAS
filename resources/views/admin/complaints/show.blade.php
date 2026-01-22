@extends('layouts.admin')

@section('header_title', 'Detail Pengaduan')
@section('title', 'Detail Pengaduan #' . ($complaint->ticket_number ?? ($complaint->ticket_id ?? $complaint->id)))

@section('content')
@php
    // =========================
    // WhatsApp link generator
    // =========================
    $rawPhone = $complaint->user->phone ?? '';

    // Ambil angka saja (hapus spasi, +, -, dll)
    $phoneDigits = preg_replace('/\D+/', '', $rawPhone);

    // Normalisasi ke format 62xxxxxxxxxx
    if (str_starts_with($phoneDigits, '0')) {
        $waPhone = '62' . substr($phoneDigits, 1);
    } elseif (str_starts_with($phoneDigits, '62')) {
        $waPhone = $phoneDigits;
    } elseif (str_starts_with($phoneDigits, '8')) {
        $waPhone = '62' . $phoneDigits;
    } else {
        $waPhone = $phoneDigits; // fallback
    }

    // Simple validation minimal (biar gak wa.me/ kosong)
    $waPhone = (strlen($waPhone) >= 10) ? $waPhone : null;

    $ticketNo = $complaint->ticket_number ?? ($complaint->ticket_id ?? $complaint->id);
    $waText = rawurlencode("Halo {$complaint->user->name}, kami dari Admin SIAPKANGMAS terkait Pengaduan #{$ticketNo}.");
    $waLink = $waPhone ? "https://wa.me/{$waPhone}?text={$waText}" : null;
@endphp

<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.complaints.pengaduan') }}" class="p-2 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>

            <div>
                <div class="flex items-center gap-3">
                    <h1 class="font-montserrat text-2xl font-bold text-gray-900">
                        Detail Pengaduan #{{ $ticketNo }}
                    </h1>

                    @php
                        $badgeColor = match($complaint->status) {
                            'pending' => 'bg-gray-100 text-gray-700',
                            'diproses' => 'bg-blue-100 text-blue-800',
                            'selesai' => 'bg-green-100 text-green-800',
                            'rejected' => 'bg-red-100 text-red-800',
                            default => 'bg-gray-100 text-gray-800'
                        };
                        $statusLabel = match($complaint->status) {
                            'pending' => 'Belum Diproses',
                            'diproses' => 'Sedang Diproses',
                            'selesai' => 'Selesai',
                            'rejected' => 'Ditolak',
                            default => ucfirst($complaint->status)
                        };
                    @endphp
                    <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $badgeColor }}">
                        {{ $statusLabel }}
                    </span>
                </div>

                <div class="flex items-center gap-4 mt-1 text-sm text-gray-500 font-lato">
                    <span>Diajukan pada {{ $complaint->created_at->format('d F Y') }}</span>
                    <span>•</span>
                    <span>Layanan : Pengaduan</span>
                    <span>•</span>
                    <span>Kategori : {{ $complaint->category->name ?? '-' }}</span>
                </div>
            </div>
        </div>

        {{-- ACTION BUTTONS --}}
        <div class="flex items-center gap-2">
            @if($waLink)
                <a href="{{ $waLink }}"
                   target="_blank"
                   rel="noopener"
                   class="px-4 py-2 bg-green-600 text-white text-sm font-semibold rounded-lg hover:bg-green-700 transition flex items-center gap-2">
                    <svg class="w-5 h-5" viewBox="0 0 32 32" fill="currentColor">
                        <path d="M19.11 17.22c-.27-.14-1.6-.79-1.85-.88-.25-.09-.43-.14-.61.14-.18.27-.7.88-.86 1.06-.16.18-.32.2-.59.07-.27-.14-1.14-.42-2.17-1.34-.8-.71-1.34-1.59-1.5-1.86-.16-.27-.02-.42.12-.56.12-.12.27-.32.41-.48.14-.16.18-.27.27-.45.09-.18.05-.34-.02-.48-.07-.14-.61-1.47-.84-2.01-.22-.52-.45-.45-.61-.46h-.52c-.18 0-.48.07-.73.34-.25.27-.96.94-.96 2.29 0 1.35.99 2.66 1.12 2.84.14.18 1.95 2.98 4.73 4.18.66.29 1.18.46 1.58.59.66.21 1.26.18 1.74.11.53-.08 1.6-.65 1.83-1.28.23-.63.23-1.17.16-1.28-.07-.11-.25-.18-.52-.32z"/>
                        <path d="M16.02 3C8.86 3 3.05 8.81 3.05 15.97c0 2.28.6 4.51 1.75 6.48L3 29l6.73-1.76a12.9 12.9 0 0 0 6.29 1.61h.01c7.16 0 12.97-5.81 12.97-12.97C28.99 8.81 23.18 3 16.02 3zm0 23.33h-.01c-2.02 0-4-.54-5.74-1.55l-.41-.24-3.99 1.04 1.07-3.89-.26-.4a10.77 10.77 0 0 1-1.67-5.75c0-5.96 4.85-10.81 10.81-10.81 5.96 0 10.81 4.85 10.81 10.81 0 5.96-4.85 10.81-10.81 10.81z"/>
                    </svg>
                    Chat WA
                </a>
            @endif

            <a href="{{ route('admin.complaints.pdf', $complaint->id) }}"
               class="px-4 py-2 bg-orange-500 text-white text-sm font-semibold rounded-lg hover:bg-orange-600 transition flex items-center gap-2">
                Unduh PDF
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-4" role="alert">
            <p class="text-green-700 font-medium">{{ session('success') }}</p>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">

            {{-- Isi Pengaduan --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <h3 class="font-montserrat font-bold text-gray-900">Isi Pengaduan</h3>
                </div>

                <div class="p-6 space-y-6">
                    <div>
                        <h4 class="font-bold text-gray-900 text-sm mb-2">Judul / Subjek</h4>
                        <p class="text-gray-700 font-lato">{{ $complaint->title ?? $complaint->subject ?? '-' }}</p>
                    </div>

                    <div>
                        <h4 class="font-bold text-gray-900 text-sm mb-2">Uraian Pengaduan</h4>
                        <div class="p-4 bg-gray-50 rounded-lg text-gray-700 font-lato text-sm leading-relaxed border border-gray-100 min-h-[100px]">
                            {{ $complaint->description ?? '-' }}
                        </div>
                    </div>

                    @if(isset($complaint->documents))
                        <div>
                            <h4 class="font-bold text-gray-900 text-sm mb-3">Dokumen Pendukung</h4>
                            @if($complaint->documents->count() > 0)
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    @foreach($complaint->documents as $doc)
                                        <a href="{{ route('admin.complaints.document', $doc->id) }}" target="_blank"
                                           class="flex items-center p-3 border border-blue-100 bg-blue-50 rounded-lg hover:bg-blue-100 transition group">
                                            <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center text-blue-600 shadow-sm mr-3 shrink-0">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                                </svg>
                                            </div>
                                            <div class="overflow-hidden">
                                                <p class="text-sm font-medium text-blue-700 truncate group-hover:underline">{{ $doc->original_name }}</p>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-sm text-gray-500 italic">Tidak ada dokumen.</p>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            {{-- Data Pemohon --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    <h3 class="font-montserrat font-bold text-gray-900">Data Pemohon</h3>
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 font-lato">
                        <div class="col-span-2">
                            <p class="text-xs text-gray-500 uppercase tracking-wide font-bold mb-1">Jenis Pelapor</p>
                            <p class="text-sm font-medium text-gray-900 bg-gray-100 px-2 py-1 rounded inline-block">
                                {{ ucfirst($complaint->user->user_type ?? '-') }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs text-gray-500 mb-1">Nama Lengkap</p>
                            <p class="font-bold text-gray-900">{{ $complaint->user->name ?? '-' }}</p>
                        </div>

                        <div>
                            <p class="text-xs text-gray-500 mb-1">Email</p>
                            <p class="font-bold text-gray-900">{{ $complaint->user->email ?? '-' }}</p>
                        </div>

                        <div>
                            <p class="text-xs text-gray-500 mb-1">NIP / Identitas</p>
                            <p class="font-bold text-gray-900">{{ $complaint->user->nip ?? '-' }}</p>
                        </div>

                        <div>
                            <p class="text-xs text-gray-500 mb-1">Nomor Telepon</p>
                            <p class="font-bold text-gray-900">{{ $complaint->user->phone ?? '-' }}</p>
                        </div>

                        @if(($complaint->user->user_type ?? null) == 'pegawai')
                            <div>
                                <p class="text-xs text-gray-500 mb-1">Bidang / Balai</p>
                                <p class="font-bold text-gray-900">{{ $complaint->user->bidang ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 mb-1">Jabatan</p>
                                <p class="font-bold text-gray-900">{{ $complaint->user->jabatan ?? '-' }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

        </div>

        <div class="space-y-6">

            {{-- Tindak Lanjut --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                    </svg>
                    <h3 class="font-montserrat font-bold text-gray-900">Tindak Lanjut</h3>
                </div>

                <div class="p-6">
                    <form action="{{ route('admin.complaints.update', $complaint->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Update Status</label>
                            <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 bg-white">
                                <option value="pending"  {{ $complaint->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="diproses" {{ $complaint->status == 'diproses' ? 'selected' : '' }}>Sedang Diproses</option>
                                <option value="selesai"  {{ $complaint->status == 'selesai' ? 'selected' : '' }}>Selesai</option>
                                <option value="rejected" {{ $complaint->status == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Catatan / Balasan</label>
                            <textarea name="admin_notes" rows="6" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 bg-white placeholder-gray-400" placeholder="Tuliskan balasan pengaduan di sini...">{{ $complaint->admin_response ?? $complaint->admin_notes }}</textarea>
                        </div>

                        <div class="mb-6 flex items-center">
                            <input type="checkbox" id="notify_user" name="notify_user" value="1" checked class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <label for="notify_user" class="ml-2 text-xs text-gray-500">Kirim notifikasi email balasan ke pemohon</label>
                        </div>

                        <button type="submit" class="w-full py-2.5 bg-blue-700 hover:bg-blue-800 text-white font-bold rounded-lg transition shadow-sm text-sm">
                            Simpan & Kirim
                        </button>
                    </form>
                </div>
            </div>

            {{-- Riwayat Aktivitas --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h3 class="font-montserrat font-bold text-gray-900">Riwayat Aktivitas</h3>
                </div>

                <div class="p-6">
                    <ol class="relative border-l border-gray-200 ml-2">
                        @if($complaint->statusHistories && $complaint->statusHistories->count() > 0)
                            @foreach($complaint->statusHistories as $history)
                                <li class="mb-6 ml-6">
                                    <span class="absolute flex items-center justify-center w-4 h-4 bg-blue-600 rounded-full -left-2 ring-4 ring-white"></span>

                                    <h3 class="font-bold text-gray-900 text-sm">
                                        Status: {{ match($history->new_status) {
                                            'pending' => 'Pending',
                                            'diproses' => 'Sedang Diproses',
                                            'selesai' => 'Selesai',
                                            'rejected' => 'Ditolak',
                                            default => ucfirst($history->new_status)
                                        } }}
                                    </h3>

                                    <p class="text-xs text-gray-500 mt-1">
                                        Oleh: {{ $history->changedBy->name ?? 'Sistem' }}
                                    </p>

                                    <time class="block mb-1 text-xs font-normal text-gray-400">{{ $history->created_at->format('d M Y, H:i') }} WIB</time>

                                    @if($history->notes)
                                        <div class="p-3 bg-gray-50 border border-gray-100 rounded-lg mt-2">
                                            <p class="text-xs text-gray-600 italic">"{{ $history->notes }}"</p>
                                        </div>
                                    @endif
                                </li>
                            @endforeach
                        @endif

                        <li class="mb-6 ml-6">
                            <span class="absolute flex items-center justify-center w-4 h-4 bg-gray-200 rounded-full -left-2 ring-4 ring-white"></span>
                            <h3 class="font-bold text-gray-900 text-sm">Pengaduan Diajukan</h3>
                            <time class="block mb-1 text-xs font-normal text-gray-400">{{ $complaint->created_at->format('d M Y, H:i') }} WIB</time>
                        </li>
                    </ol>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection