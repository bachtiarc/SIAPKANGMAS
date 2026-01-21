@extends('layouts.admin')

@section('header_title', 'Detail Konsultasi')
@section('title', 'Detail Konsultasi #' . $consultation->ticket_id)

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            {{-- Pastikan route ini mengarah ke halaman list konsultasi Anda --}}
            <a href="{{ route('admin.consultations.konsultasi') }}" class="p-2 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="font-montserrat text-2xl font-bold text-gray-900">
                        Detail Konsultasi #{{ $consultation->ticket_id }}
                    </h1>
                    @php
                        $badgeColor = match($consultation->status) {
                            'pending' => 'bg-gray-100 text-gray-700',
                            'on_progress' => 'bg-blue-100 text-blue-800',
                            'completed', 'selesai' => 'bg-green-100 text-green-800',
                            'rejected' => 'bg-red-100 text-red-800',
                            default => 'bg-gray-100 text-gray-800'
                        };
                        $statusLabel = match($consultation->status) {
                            'pending' => 'Belum Diproses',
                            'on_progress' => 'Sedang Diproses',
                            'completed', 'selesai' => 'Selesai',
                            'rejected' => 'Ditolak',
                            default => ucfirst($consultation->status)
                        };
                    @endphp
                    <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $badgeColor }}">
                        {{ $statusLabel }}
                    </span>
                </div>
                <div class="flex items-center gap-4 mt-1 text-sm text-gray-500 font-lato">
                    <span>Diajukan pada {{ $consultation->created_at->format('d F Y') }}</span>
                    <span>•</span>
                    <span>Layanan : Konsultasi</span>
                    <span>•</span>
                    <span>Kategori : {{ $consultation->category->name ?? '-' }}</span>
                </div>
            </div>
        </div>
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
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
                    <h3 class="font-montserrat font-bold text-gray-900">Isi Konsultasi</h3>
                </div>
                <div class="p-6 space-y-6">
                    <div>
                        <h4 class="font-bold text-gray-900 text-sm mb-2">Judul / Topik</h4>
                        <p class="text-gray-700 font-lato">{{ $consultation->title }}</p>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-900 text-sm mb-2">Uraian Masalah</h4>
                        <div class="p-4 bg-gray-50 rounded-lg text-gray-700 font-lato text-sm leading-relaxed border border-gray-100 min-h-[100px]">
                            {{ $consultation->description }}
                        </div>
                    </div>
                    
                    @if(isset($consultation->documents))
                    <div>
                        <h4 class="font-bold text-gray-900 text-sm mb-3">Dokumen Pendukung</h4>
                        @if($consultation->documents->count() > 0)
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                @foreach($consultation->documents as $doc)
                                <a href="{{ route('admin.consultations.document', $doc->id) }}" target="_blank" class="flex items-center p-3 border border-blue-100 bg-blue-50 rounded-lg hover:bg-blue-100 transition group">
                                    <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center text-blue-600 shadow-sm mr-3 shrink-0">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
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
                                {{ ucfirst($consultation->user->user_type ?? '-') }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs text-gray-500 mb-1">Nama Lengkap</p>
                            <p class="font-bold text-gray-900">{{ $consultation->user->name ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Email</p>
                            <p class="font-bold text-gray-900">{{ $consultation->user->email ?? '-' }}</p>
                        </div>

                        <div>
                            <p class="text-xs text-gray-500 mb-1">NIP / Identitas</p>
                            <p class="font-bold text-gray-900">{{ $consultation->user->nip ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Nomor Telepon</p>
                            <p class="font-bold text-gray-900">{{ $consultation->user->phone ?? '-' }}</p>
                        </div>
                        
                        @if($consultation->user->user_type == 'pegawai')
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Bidang / Balai</p>
                            <p class="font-bold text-gray-900">{{ $consultation->user->bidang ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Jabatan</p>
                            <p class="font-bold text-gray-900">{{ $consultation->user->jabatan ?? '-' }}</p>
                        </div>
                        @endif
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
                    <form action="{{ route('admin.consultations.update', $consultation->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-4">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Update Status</label>
                            <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 bg-white">
                                <option value="pending" {{ $consultation->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="on_progress" {{ $consultation->status == 'on_progress' ? 'selected' : '' }}>Sedang Diproses</option>
                                <option value="completed" {{ $consultation->status == 'completed' ? 'selected' : '' }}>Selesai</option>
                                <option value="rejected" {{ $consultation->status == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Catatan / Balasan</label>
                            <textarea name="admin_notes" rows="6" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 bg-white placeholder-gray-400" placeholder="Tuliskan balasan konsultasi di sini...">{{ $consultation->admin_notes }}</textarea>
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

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <h3 class="font-montserrat font-bold text-gray-900">Riwayat Aktivitas</h3>
                </div>
                <div class="p-6">
                    <ol class="relative border-l border-gray-200 ml-2">
                        @if($consultation->statusHistories && $consultation->statusHistories->count() > 0)
                            @foreach($consultation->statusHistories as $history)
                            <li class="mb-6 ml-6">
                                <span class="absolute flex items-center justify-center w-4 h-4 bg-blue-600 rounded-full -left-2 ring-4 ring-white"></span>
                                
                                <h3 class="font-bold text-gray-900 text-sm">
                                    Status: {{ match($history->new_status) {
                                        'pending' => 'Pending',
                                        'on_progress' => 'Sedang Diproses',
                                        'completed' => 'Selesai',
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
                            <h3 class="font-bold text-gray-900 text-sm">Konsultasi Diajukan</h3>
                            <time class="block mb-1 text-xs font-normal text-gray-400">{{ $consultation->created_at->format('d M Y, H:i') }} WIB</time>
                        </li>
                    </ol>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection