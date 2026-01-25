@extends('layouts.app')

@section('title', 'Detail Tiket ' . $ticketId)

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Tombol Kembali -->
        <div class="mb-6">
            <a href="{{ route('home') }}" class="inline-flex items-center text-blue-600 hover:text-blue-700 font-medium">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali
            </a>
        </div>

        <!-- Header Tiket -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex items-start justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-blue-600 mb-2">
                        Tiket {{ $ticketType === 'submission' ? $ticket->ticket_id : $ticket->ticket_number }}
                    </h1>
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $ticket->status_badge }}">
                            {{ $ticket->status_label }}
                        </span>
                        <span class="text-sm text-gray-500">
                            • Diajukan {{ $ticket->created_at->format('d M Y, H:i') }} WIB
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Progress Pengaduan -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex items-center mb-4">
                <svg class="w-6 h-6 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h2 class="text-xl font-semibold text-gray-800">Progres Pengaduan</h2>
            </div>

            @php
                $rawStatus = strtolower($ticket->status);

                $statusMap = [
                    'pending' => 'menunggu',
                    'on_progress' => 'diproses',
                    'in_progress' => 'diproses',
                    'diproses' => 'diproses',
                    'completed' => 'selesai',
                    'selesai' => 'selesai',
                    'rejected' => 'ditolak',
                    'ditolak' => 'ditolak',
                ];

                $status = $statusMap[$rawStatus] ?? 'menunggu';

                $steps = [
                    [
                        'key' => 'terkirim',
                        'label' => 'Pengaduan Terkirim',
                        'time' => $ticket->created_at,
                    ],
                    [
                        'key' => 'menunggu',
                        'label' => 'Menunggu Proses',
                        'time' => $ticket->created_at,
                    ],
                    [
                        'key' => 'diproses',
                        'label' => 'Sedang Diproses',
                        'time' => in_array($status, ['diproses','selesai','ditolak']) ? $ticket->updated_at : null,
                    ],
                    [
                        'key' => 'akhir',
                        'label' => $status === 'ditolak' ? 'Ditolak' : 'Selesai',
                        'time' => in_array($status, ['selesai','ditolak']) ? ($ticket->completed_at ?? $ticket->updated_at) : null,
                    ],
                ];

                $currentIndex = match ($status) {
                    'menunggu' => 1,
                    'diproses' => 2,
                    'selesai', 'ditolak' => 3,
                    default => 0,
                };
            @endphp

            <div class="relative px-4">
                <div class="relative">
                    <div class="absolute top-5 left-0 right-0 h-1 bg-gray-300" style="margin: 0 5%;"></div>

                    @if($currentIndex > 0)
                        <div class="absolute top-5 left-0 h-1 bg-green-500"
                             style="width: {{ ($currentIndex / 3) * 90 }}%; margin-left: 5%;"></div>
                    @endif

                    <div class="relative flex items-start justify-between">
                        @foreach($steps as $index => $step)
                            @php $isActive = $index <= $currentIndex; @endphp
                            <div class="flex flex-col items-center" style="width: 25%;">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center
                                    {{ $isActive ? ($status === 'ditolak' && $index === 3 ? 'bg-red-500' : 'bg-green-500') : 'bg-gray-300' }}
                                    text-white mb-2 relative z-10">
                                    @if($isActive)
                                        {{ $status === 'ditolak' && $index === 3 ? '✕' : '✓' }}
                                    @endif
                                </div>

                                <div class="text-center">
                                    <div class="text-sm font-medium {{ $isActive ? 'text-gray-800' : 'text-gray-400' }}">
                                        {{ $step['label'] }}
                                    </div>
                                    @if($step['time'])
                                        <div class="text-xs text-gray-500 mt-1">
                                            {{ $step['time']->format('d M Y, H:i') }} WIB
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Riwayat Aktivitas -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex items-center mb-4">
                <svg class="w-6 h-6 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h2 class="text-xl font-semibold text-gray-800">Riwayat Aktivitas</h2>
            </div>

            <div class="space-y-4">
                <div class="flex items-start gap-3">
                    <div class="w-3 h-3 rounded-full bg-white-500 mt-1.5"></div>
                    <div>
                        <div class="font-medium">Formulir terkirim</div>
                        <div class="text-xs text-gray-500">{{ $ticket->created_at->format('d/m/Y H:i') }} WIB</div>
                    </div>
                </div>

                @if(in_array($status, ['diproses','selesai','ditolak']))
                <div class="flex items-start gap-3">
                    <div class="w-3 h-3 rounded-full bg-white-500 mt-1.5"></div>
                    <div>
                        <div class="font-medium">Status diperbarui menjadi Diproses</div>
                        <div class="text-xs text-gray-500">{{ $ticket->updated_at->format('d/m/Y H:i') }} WIB</div>
                    </div>
                </div>
                @endif

                @if(in_array($status, ['selesai','ditolak']))
                <div class="flex items-start gap-3">
                    <div class="w-3 h-3 rounded-full {{ $status === 'ditolak' ? 'bg-white-500' : 'bg-white-500' }} mt-1.5"></div>
                    <div>
                        <div class="font-medium">Status diperbarui menjadi {{ ucfirst($status) }}</div>
                        <div class="text-xs text-gray-500">
                            {{ ($ticket->completed_at ?? $ticket->updated_at)->format('d/m/Y H:i') }} WIB
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Informasi Tambahan -->
        <div class="bg-orange-50 border border-orange-200 rounded-lg p-6 mb-6">
            <div class="flex items-start">
                <svg class="w-6 h-6 text-orange-600 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div class="text-sm text-gray-700">
                    <p class="font-semibold mb-2">Catatan penting mengenai privasi dan download file:</p>
                    <ul class="list-disc list-inside space-y-1 text-gray-600">
                        <li>
                            Untuk melihat detail lengkap pengajuan, silakan
                            <a href="{{ route('login') }}"
                            class="text-blue-600 hover:text-blue-700 font-medium underline">
                                login
                            </a>
                            terlebih dahulu
                        </li>
                        <li>File PDF dan dokumen pendukung hanya dapat diunduh setelah login ke sistem</li>
                        <li>Informasi yang ditampilkan di halaman ini terbatas untuk menjaga privasi Anda</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- CTA Login -->
        <div class="text-center mb-8">
            <p class="text-gray-600 mb-4">Ingin melihat detail lengkap atau mengunduh dokumen?</p>
            <div class="flex justify-center gap-4">
                <a href="{{ route('login') }}"
                class="bg-orange-500 hover:bg-orange-600 text-white px-8 py-3 rounded-lg font-semibold transition shadow-md">
                    Login Sekarang
                </a>
                <a href="{{ route('home') }}"
                class="bg-white hover:bg-gray-100 text-gray-700 border border-gray-300 px-8 py-3 rounded-lg font-semibold transition shadow-md">
                    Kembali ke Beranda
                </a>
            </div>
        </div>  
    </div>
</div>
@endsection
