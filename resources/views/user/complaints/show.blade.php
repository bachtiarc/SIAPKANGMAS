@extends('layouts.dashboard')

@section('title', 'Daftar Pengaduan')

@section('content')
<div class="px-6 py-5">

    <!-- Breadcrumb -->
    <nav class="mb-5 text-sm">
        <ol class="flex items-center space-x-2">
            <li>
                <a href="{{ route('user.dashboard') }}" class="text-blue-600 hover:text-blue-800">
                    Beranda
                </a>
            </li>
            <li class="text-gray-400">/</li>
            <li class="text-gray-600">Pengaduan</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm px-5 py-4 mb-5">
        <h1 class="text-xl font-semibold text-gray-900">
            Daftar Pengaduan
        </h1>
    </div>

    <!-- Action -->
    <div class="bg-white rounded-lg shadow-sm px-5 py-4 mb-4 flex justify-between items-center">
        <a href="{{ route('user.complaints.create') }}"
           class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 4v16m8-8H4"/>
            </svg>
            Buat Pengaduan
        </a>

        <form method="GET" class="flex">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Cari tiket atau subjek"
                   class="border border-gray-300 rounded-l-lg px-3 py-2 text-sm">
            <button class="border border-gray-300 border-l-0 rounded-r-lg px-3 py-2 bg-gray-50">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </button>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        @if($complaints->count())
        <div class="overflow-x-auto">
            <table class="min-w-full border-collapse">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <!-- ⚠️ HEADER PAKAI px-2 -->
                        <th class="px-0 py-0 text-left text-xs font-semibold text-gray-700 uppercase">
                            ID Tiket
                        </th>
                        <th class="px-2 py-2 text-left text-xs font-semibold text-gray-700 uppercase">
                            Subjek
                        </th>
                        <th class="px-2 py-2 text-left text-xs font-semibold text-gray-700 uppercase">
                            Tanggal
                        </th>
                        <th class="px-2 py-2 text-left text-xs font-semibold text-gray-700 uppercase">
                            Status
                        </th>
                        <th class="px-2 py-2 text-center text-xs font-semibold text-gray-700 uppercase w-14">
                            Aksi
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y">
                    @foreach($complaints as $complaint)
                        <tr class="hover:bg-gray-50">
                            <td class="px-2 py-2 text-sm font-medium text-gray-900">
                                {{ $complaint->ticket_number }}
                            </td>

                            <td class="px-2 py-2 text-sm text-gray-900">
                                <div>{{ Str::limit($complaint->subject, 50) }}</div>
                                <div class="text-xs text-gray-500">
                                    {{ $complaint->category->name ?? '-' }}
                                </div>
                            </td>

                            <td class="px-2 py-2 text-sm text-gray-600">
                                {{ $complaint->created_at->format('d M Y') }}<br>
                                <span class="text-xs">
                                    {{ $complaint->created_at->format('H:i') }} WIB
                                </span>
                            </td>

                            <td class="px-2 py-2">
                                <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $complaint->status_badge }}">
                                    {{ $complaint->status_label }}
                                </span>
                            </td>

                            <td class="px-2 py-2 text-center">
                                <a href="{{ route('user.complaints.show', $complaint) }}"
                                   class="text-blue-600 hover:text-blue-800 inline-flex justify-center">
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

        <div class="px-4 py-3 border-t">
            {{ $complaints->links() }}
        </div>
        @else
            <div class="py-10 text-center text-gray-500 text-sm">
                Belum ada pengaduan.
            </div>
        @endif
    </div>

</div>
@endsection
