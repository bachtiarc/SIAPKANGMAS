@extends('layouts.admin')

@section('header_title', 'Detail Pengajuan')
@section('title', 'Detail Pengajuan #' . $submission->ticket_id)

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.submissions.permohonan') }}" class="p-2 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="font-montserrat text-2xl font-bold text-gray-900">
                        Detail Pengajuan #{{ $submission->ticket_id }}
                    </h1>
                    @php
                        $badgeColor = match($submission->status) {
                            'pending' => 'bg-gray-100 text-gray-700',
                            'in_progress' => 'bg-yellow-100 text-yellow-800',
                            'completed', 'selesai', 'approved' => 'bg-green-100 text-green-800',
                            'rejected' => 'bg-red-100 text-red-800',
                            default => 'bg-gray-100 text-gray-800'
                        };
                        $statusLabel = match($submission->status) {
                            'pending' => 'Belum Diproses',
                            'in_progress' => 'Sedang Diproses',
                            'completed', 'selesai', 'approved' => 'Selesai',
                            'rejected' => 'Ditolak',
                            default => ucfirst($submission->status)
                        };
                    @endphp
                    <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $badgeColor }}">
                        {{ $statusLabel }}
                    </span>
                </div>
                <div class="flex items-center gap-4 mt-1 text-sm text-gray-500 font-lato">
                    <span>Diajukan pada {{ $submission->created_at->format('d F Y') }}</span>
                    <span>•</span>
                    <span>Layanan : Permohonan Informasi</span>
                    <span>•</span>
                    <span>Kategori : {{ $submission->category->name ?? '-' }}</span>
                </div>
            </div>
        </div>
        
        <button type="button" class="px-4 py-2 bg-orange-500 text-white text-sm font-semibold rounded-lg hover:bg-orange-600 transition flex items-center gap-2">
            Unduh PDF
        </button>
    </div>

    @if(session('success'))
    <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-4" role="alert">
        <p class="text-green-700 font-medium">{{ session('success') }}</p>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <div class="lg:col-span-2 space-y-6">
            
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    <h3 class="font-montserrat font-bold text-gray-900">Isi Pengajuan</h3>
                </div>
                <div class="p-6 space-y-6">
                    <div>
                        <h4 class="font-bold text-gray-900 text-sm mb-2">Judul</h4>
                        <p class="text-gray-700 font-lato">{{ $submission->title }}</p>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-900 text-sm mb-2">Deskripsi Lengkap</h4>
                        <div class="p-4 bg-gray-50 rounded-lg text-gray-700 font-lato text-sm leading-relaxed border border-gray-100 min-h-[100px]">
                            {{ $submission->description }}
                        </div>
                    </div>
                    
                    <div>
                        <h4 class="font-bold text-gray-900 text-sm mb-3">Lampiran Dokumen</h4>
                        @if($submission->documents && $submission->documents->count() > 0)
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                @foreach($submission->documents as $doc)
                                
                                {{-- PERBAIKAN DI SINI: Ubah href ke route download --}}
                                <a href="{{ route('admin.submissions.document', $doc->id) }}" 
                                class="flex items-center p-3 border border-blue-100 bg-blue-50 rounded-lg hover:bg-blue-100 transition group">
                                
                                    <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center text-blue-600 shadow-sm mr-3 shrink-0">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                    </div>
                                    <div class="overflow-hidden">
                                        <p class="text-sm font-medium text-blue-700 truncate group-hover:underline">{{ $doc->original_name }}</p>
                                        <p class="text-xs text-blue-500">{{ number_format($doc->file_size / 1024, 0) }} KB</p>
                                    </div>
                                </a>
                                @endforeach
                            </div>
                        @else
                            <div class="p-4 bg-gray-50 border border-gray-100 rounded-lg text-center text-gray-500 text-sm">
                                Tidak ada dokumen lampiran.
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    <h3 class="font-montserrat font-bold text-gray-900">Data Pemohon</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 font-lato">
                        <div class="col-span-2">
                            <p class="text-xs text-gray-500 uppercase tracking-wide font-bold mb-1">Jenis Pelapor</p>
                            <p class="text-sm font-medium text-gray-900 bg-gray-100 px-2 py-1 rounded inline-block">
                                {{ ucfirst($submission->user->user_type ?? '-') }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs text-gray-500 mb-1">Nama Lengkap</p>
                            <p class="font-bold text-gray-900">{{ $submission->user->name ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Email</p>
                            <p class="font-bold text-gray-900">{{ $submission->user->email ?? '-' }}</p>
                        </div>

                        <div>
                            <p class="text-xs text-gray-500 mb-1">NIP / Identitas</p>
                            <p class="font-bold text-gray-900">{{ $submission->user->nip ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Nomor Telepon</p>
                            <p class="font-bold text-gray-900">{{ $submission->user->phone ?? '-' }}</p>
                        </div>

                        <div>
                            <p class="text-xs text-gray-500 mb-1">Bidang / Balai</p>
                            <p class="font-bold text-gray-900">{{ $submission->user->bidang ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Jabatan</p>
                            <p class="font-bold text-gray-900">{{ $submission->user->jabatan ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                    <h3 class="font-montserrat font-bold text-gray-900">Tindak Lanjut</h3>
                </div>
                <div class="p-6">
                    <form action="{{ route('admin.submissions.update', $submission->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-4">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Update Status Tiket</label>
                            <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 bg-white">
                                <option value="pending" {{ $submission->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="in_progress" {{ $submission->status == 'in_progress' ? 'selected' : '' }}>Sedang Diproses</option>
                                <option value="completed" {{ $submission->status == 'completed' ? 'selected' : '' }}>Selesai</option>
                                <option value="rejected" {{ $submission->status == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Catatan</label>
                            <textarea name="admin_notes" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 bg-white placeholder-gray-400" placeholder="Tuliskan catatan kepada pemohon di sini...">{{ $submission->admin_notes }}</textarea>
                        </div>

                        <div class="mb-6 flex items-center">
                            <input type="checkbox" id="notify_user" name="notify_user" value="1" checked class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <label for="notify_user" class="ml-2 text-xs text-gray-500">Kirim notifikasi email kepada pemohon</label>
                        </div>

                        <button type="submit" class="w-full py-2.5 bg-blue-700 hover:bg-blue-800 text-white font-bold rounded-lg transition shadow-sm text-sm">
                            Simpan Perubahan
                        </button>
                    </form>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <h3 class="font-montserrat font-bold text-gray-900">Riwayat Aktivitas</h3>
                </div>
                <div class="p-6">
                    <ol class="relative border-l border-gray-200 ml-2">
                        @if($submission->statusHistories && $submission->statusHistories->count() > 0)
                            @foreach($submission->statusHistories as $history)
                            <li class="mb-6 ml-6">
                                <span class="absolute flex items-center justify-center w-4 h-4 bg-blue-600 rounded-full -left-2 ring-4 ring-white"></span>
                                
                                <h3 class="font-bold text-gray-900 text-sm">
                                    Status diubah menjadi 
                                    '{{ match($history->new_status) {
                                        'pending' => 'Pending',
                                        'in_progress' => 'Sedang Diproses',
                                        'completed' => 'Selesai',
                                        'rejected' => 'Ditolak',
                                        default => ucfirst($history->new_status)
                                    } }}'
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
                            <h3 class="font-bold text-gray-900 text-sm">Tiket Dibuat oleh Pemohon</h3>
                            <time class="block mb-1 text-xs font-normal text-gray-400">{{ $submission->created_at->format('d M Y, H:i') }} WIB</time>
                        </li>
                    </ol>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection