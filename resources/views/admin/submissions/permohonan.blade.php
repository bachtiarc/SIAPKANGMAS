@extends('layouts.admin')

@section('header_title', 'Manajemen Pengajuan') @section('title', 'Manajemen Pengajuan')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-2">
        <h2 class="font-montserrat text-2xl font-bold text-blue-700">Manajemen Pengajuan</h2>
        <p class="font-lato text-gray-600">Kelola dan unduh laporan pengajuan layanan bantuan Dinas Perindustrian dan Perdagangan Jawa Tengah.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center mb-4">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            </div>
            <p class="font-lato text-gray-600 text-sm mb-1">Total Tiket Masuk</p>
            <h3 class="font-montserrat text-3xl font-bold text-gray-900">{{ number_format($stats['total']) }}</h3>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <div class="w-10 h-10 bg-yellow-50 rounded-lg flex items-center justify-center mb-4">
                <svg class="w-6 h-6 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
            </div>
            <p class="font-lato text-gray-600 text-sm mb-1">Sedang Diproses</p>
            <h3 class="font-montserrat text-3xl font-bold text-gray-900">{{ number_format($stats['proses']) }}</h3>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <div class="w-10 h-10 bg-green-50 rounded-lg flex items-center justify-center mb-4">
                <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            </div>
            <p class="font-lato text-gray-600 text-sm mb-1">Selesai</p>
            <h3 class="font-montserrat text-3xl font-bold text-gray-900">{{ number_format($stats['selesai']) }}</h3>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <div class="w-10 h-10 bg-red-50 rounded-lg flex items-center justify-center mb-4">
                <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            </div>
            <p class="font-lato text-gray-600 text-sm mb-1">Belum Diproses</p>
            <h3 class="font-montserrat text-3xl font-bold text-gray-900">{{ number_format($stats['belum']) }}</h3>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        
        <div class="flex border-b border-gray-200 p-4 pb-0 gap-2">
            <a href="#" class="px-6 py-3 font-montserrat font-medium text-sm text-gray-600 hover:text-blue-600 hover:bg-gray-50 rounded-t-lg transition">
                Konsultasi
            </a>
            <a href="#" class="px-6 py-3 font-montserrat font-medium text-sm text-gray-600 hover:text-blue-600 hover:bg-gray-50 rounded-t-lg transition">
                Pengaduan
            </a>
            <a href="{{ route('admin.submissions.permohonan') }}" class="px-6 py-3 font-montserrat font-medium text-sm text-white bg-blue-700 rounded-t-lg shadow-sm">
                Permohonan Informasi
            </a>
        </div>

        <div class="p-6 border-b border-gray-200">
            <form action="{{ route('admin.submissions.permohonan') }}" method="GET" class="flex flex-wrap items-end gap-4">
                
                <div class="flex items-center gap-2">
                    <div class="flex flex-col gap-1">
                        <label class="text-xs font-semibold text-gray-600">Tgl Pengajuan :</label>
                        <input type="date" name="start_date" value="{{ request('start_date') }}" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <span class="text-gray-400 mt-5">-</span>
                    <div class="flex flex-col gap-1 mt-5">
                        <input type="date" name="end_date" value="{{ request('end_date') }}" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>

                <div class="flex flex-col gap-1">
                    <label class="text-xs font-semibold text-gray-600">Pelapor :</label>
                    <select name="type" class="px-3 py-2 border border-gray-300 rounded-lg text-sm min-w-[120px] focus:ring-blue-500 focus:border-blue-500">
                        <option value="Semua">Semua</option>
                        <option value="pegawai">Pegawai</option>
                        <option value="masyarakat">Masyarakat</option>
                    </select>
                </div>

                <div class="flex flex-col gap-1">
                    <label class="text-xs font-semibold text-gray-600">Kategori :</label>
                    <select name="category" class="px-3 py-2 border border-gray-300 rounded-lg text-sm min-w-[150px] focus:ring-blue-500 focus:border-blue-500">
                        <option value="Semua">Semua</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex flex-col gap-1">
                    <label class="text-xs font-semibold text-gray-600">Status :</label>
                    <select name="status" class="px-3 py-2 border border-gray-300 rounded-lg text-sm min-w-[120px] focus:ring-blue-500 focus:border-blue-500">
                        <option value="Semua">Semua</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>Diproses</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                    </select>
                </div>

                <div class="flex gap-2 ml-auto">
                    <button type="submit" class="px-4 py-2 bg-blue-700 text-white text-sm font-medium rounded-lg hover:bg-blue-800 transition">
                        Terapkan
                    </button>
                    <a href="{{ route('admin.submissions.permohonan') }}" class="px-4 py-2 border border-blue-600 text-blue-600 text-sm font-medium rounded-lg hover:bg-blue-50 transition">
                        Reset
                    </a>
                    <button type="button" class="px-3 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    </button>
                </div>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="p-4 text-xs font-bold text-gray-600 uppercase tracking-wider border-b">ID Tiket</th>
                        <th class="p-4 text-xs font-bold text-gray-600 uppercase tracking-wider border-b">Tanggal Pengajuan</th>
                        <th class="p-4 text-xs font-bold text-gray-600 uppercase tracking-wider border-b">Nama Pelapor</th>
                        <th class="p-4 text-xs font-bold text-gray-600 uppercase tracking-wider border-b">Email Pelapor</th>
                        <th class="p-4 text-xs font-bold text-gray-600 uppercase tracking-wider border-b">Jenis Pelapor</th>
                        <th class="p-4 text-xs font-bold text-gray-600 uppercase tracking-wider border-b">Kategori</th>
                        <th class="p-4 text-xs font-bold text-gray-600 uppercase tracking-wider border-b">Subjek</th>
                        <th class="p-4 text-xs font-bold text-gray-600 uppercase tracking-wider border-b text-center">Status</th>
                        <th class="p-4 text-xs font-bold text-gray-600 uppercase tracking-wider border-b text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($submissions as $item)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="p-4 text-sm font-semibold text-gray-900">{{ $item->ticket_id }}</td>
                        <td class="p-4 text-sm text-gray-600">{{ $item->created_at->format('d F Y') }}</td>
                        <td class="p-4 text-sm font-medium text-gray-900">{{ $item->user->name ?? '-' }}</td>
                        <td class="p-4 text-sm text-gray-600">{{ $item->user->email ?? '-' }}</td>
                        <td class="p-4 text-sm text-gray-600">
                            {{ ucfirst($item->user->user_type ?? 'Masyarakat') }}
                        </td>
                        <td class="p-4 text-sm text-gray-600">{{ $item->category->name ?? '-' }}</td>
                        <td class="p-4 text-sm text-gray-900 font-medium">{{ Str::limit($item->title, 20) }}</td>
                        <td class="p-4 text-center">
                            @php
                                $statusClass = match($item->status) {
                                    'completed' => 'bg-green-100 text-green-700',
                                    'in_progress' => 'bg-yellow-100 text-yellow-700',
                                    'rejected' => 'bg-red-100 text-red-700',
                                    default => 'bg-gray-100 text-gray-700',
                                };
                                $statusLabel = match($item->status) {
                                    'completed' => 'Selesai',
                                    'in_progress' => 'Sedang diproses',
                                    'rejected' => 'Ditolak',
                                    default => 'Pending',
                                };
                            @endphp
                            <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $statusClass }}">
                                {{ $statusLabel }}
                            </span>
                        </td>
                        <td class="p-4 text-center">
                            <a href="#" class="inline-flex p-2 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-full transition" title="Lihat Detail">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="p-8 text-center text-gray-500">
                            Data permohonan informasi tidak ditemukan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-gray-200">
            {{ $submissions->links() }}
        </div>
    </div>
</div>
@endsection