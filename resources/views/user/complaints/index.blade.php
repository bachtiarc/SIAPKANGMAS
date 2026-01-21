@extends('layouts.dashboard')

@section('title', 'Daftar Pengaduan')

@section('content')
<div class="p-6">
    <!-- Breadcrumb -->
    <nav class="mb-6 text-sm">
        <ol class="flex items-center space-x-2">
            <li>
                <a href="{{ route('user.dashboard') }}" class="text-blue-600 hover:text-blue-800">Beranda</a>
            </li>
            <li class="text-gray-400">/</li>
            <li class="text-gray-600">Pengaduan</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Daftar Pengaduan</h1>
    </div>

    <!-- Action Bar -->
    <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <a href="{{ route('user.complaints.create') }}"
               class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 4v16m8-8H4"/>
                </svg>
                Buat Pengaduan Baru
            </a>

            <form method="GET" action="{{ route('user.complaints.index') }}" class="flex space-x-2">
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Cari ID tiket atau subjek..."
                       class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 w-64">

                <button class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </button>
            </form>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        @if($complaints->count())
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-xs font-semibold text-gray-700 uppercase tracking-wide">ID Tiket</th>
                    <th class="px-6 py-3 text-xs font-semibold text-gray-700 uppercase tracking-wide">Subjek</th>
                    <th class="px-6 py-3 text-xs font-semibold text-gray-700 uppercase tracking-wide">Tanggal</th>
                    <th class="px-6 py-3 text-xs font-semibold text-gray-700 uppercase tracking-wide">Status</th>
                    <th class="px-6 py-3 text-xs font-semibold text-gray-700 uppercase tracking-wide">Aksi</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                @foreach($complaints as $complaint)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-semibold text-gray-900">
                            {{ $complaint->ticket_number }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-gray-900">{{ Str::limit($complaint->subject, 50) }}</div>
                            <div class="text-xs text-gray-500">{{ $complaint->category->name ?? '-' }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $complaint->created_at->format('d M Y') }}<br>
                            <span class="text-xs">{{ $complaint->created_at->format('H:i') }} WIB</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $complaint->status_badge }}">
                                {{ $complaint->status_label }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <a href="{{ route('user.complaints.show', $complaint) }}"
                               class="text-blue-600 hover:text-blue-800">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M2.458 12C3.732 7.943 7.523 5 12 5
                                             c4.478 0 8.268 2.943 9.542 7
                                             -1.274 4.057-5.064 7-9.542 7
                                             -4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t">
            {{ $complaints->links() }}
        </div>
        @else
            <div class="text-center py-12 text-gray-500">
                Belum ada pengaduan.
            </div>
        @endif
    </div>
</div>
@endsection
