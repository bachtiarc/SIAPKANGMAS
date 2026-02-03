<!-- resources/views/user/dashboard.blade.php -->

@extends('layouts.dashboard')

@section('title', 'Dashboard Pengguna')

@section('content')
<!-- Main Content -->
<div class="p-6">
    <!-- Welcome Section -->
    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <div class="flex items-start justify-between">
            <!-- Left: Welcome Message -->
            <div class="flex-1">
                <h1 class="font-montserrat text-2xl font-bold text-blue-700 mb-2">
                    Selamat Datang, {{ auth()->user()->name }}!
                </h1>
                <p class="font-lato text-gray-600 max-w-2xl">
                    Selamat datang di SIAPKANGMAS (Sistem Aplikasi Konsultasi, Pengaduan, & Permohonan Informasi DISPERINDAG). 
                    Anda total memiliki <strong>{{ $totalSubmissions }} pengajuan</strong>, silahkan gunakan layanan sesuai kebutuhan anda.
                </p>
            </div>

            <!-- Right: User Profile Card -->
            <div class="ml-6 bg-gray-50 rounded-lg p-4 min-w-[240px]">
                <div class="flex items-center space-x-3 mb-3">
                    @php
                        $rawProfile = auth()->user()->profile_photo;
                        $profileUrl = null;

                        if (!empty($rawProfile)) {
                            if (\Illuminate\Support\Str::startsWith($rawProfile, ['http://', 'https://'])) {
                                $profileUrl = $rawProfile;

                            } elseif (\Illuminate\Support\Str::startsWith($rawProfile, ['profile-photos/', 'public/', 'storage/'])) {
                                $normalized = $rawProfile;

                                if (\Illuminate\Support\Str::startsWith($normalized, 'public/')) {
                                    $normalized = \Illuminate\Support\Str::after($normalized, 'public/');
                                }
                                if (\Illuminate\Support\Str::startsWith($normalized, 'storage/')) {
                                    $normalized = \Illuminate\Support\Str::after($normalized, 'storage/');
                                }

                                $profileUrl = asset('storage/' . ltrim($normalized, '/'));

                            } else {
                                $supabaseUrl = rtrim(env('SUPABASE_URL'), '/');
                                $bucket = env('SUPABASE_PROFILE_BUCKET', env('SUPABASE_KTP_BUCKET', 'ktp-photos'));

                                $filePath = ltrim($rawProfile, '/');

                                if (\Illuminate\Support\Str::startsWith($filePath, $bucket . '/')) {
                                    $filePath = \Illuminate\Support\Str::after($filePath, $bucket . '/');
                                }

                                $profileUrl = "{$supabaseUrl}/storage/v1/object/public/{$bucket}/{$filePath}";
                            }
                        }
                    @endphp

                    @if($profileUrl)
                        <img src="{{ $profileUrl }}" alt="{{ auth()->user()->name }}" class="w-12 h-12 rounded-full object-cover border-2 border-blue-600">
                    @else
                        <div class="w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold text-lg">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                    @endif

                    <div>
                        <h3 class="font-montserrat font-semibold text-gray-900">{{ auth()->user()->name }}</h3>
                        <p class="font-lato text-xs text-gray-600">{{ auth()->user()->bidang }}</p>
                        <p class="font-lato text-xs text-gray-500">{{ auth()->user()->jabatan }}</p>
                    </div>
                </div>
                <a href="{{ route('user.profile') }}" class="font-montserrat text-xs text-blue-600 hover:text-blue-700 font-medium underline">Lihat Profil Lengkap Pengguna</a>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <!-- Total Pengajuan -->
        <div class="bg-white rounded-lg shadow-sm p-5 border-l-4 border-blue-500">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="font-lato text-sm text-gray-600">Total Pengajuan</p>
                    <p class="font-montserrat text-3xl font-bold text-gray-900">{{ $totalSubmissions }}</p>
                </div>
            </div>
        </div>

        <!-- Sedang Diproses -->
        <div class="bg-white rounded-lg shadow-sm p-5 border-l-4 border-yellow-500">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="w-8 h-8 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="font-lato text-sm text-gray-600">Sedang Diproses</p>
                    <p class="font-montserrat text-3xl font-bold text-gray-900">{{ $inProgressCount }}</p>
                </div>
            </div>
        </div>

        <!-- Pengajuan Selesai -->
        <div class="bg-white rounded-lg shadow-sm p-5 border-l-4 border-green-500">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="font-lato text-sm text-gray-600">Pengajuan Selesai</p>
                    <p class="font-montserrat text-3xl font-bold text-gray-900">{{ $completedCount }}</p>
                </div>
            </div>
        </div>

        <!-- Pengajuan Ditolak -->
        <div class="bg-white rounded-lg shadow-sm p-5 border-l-4 border-red-500">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="font-lato text-sm text-gray-600">Pengajuan Ditolak</p>
                    <p class="font-montserrat text-3xl font-bold text-gray-900">{{ $rejectedCount }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Layanan Cepat -->
    <div class="mb-6">
        <h2 class="font-montserrat text-lg font-bold text-gray-900 mb-4">Layanan Cepat</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Permohonan Informasi -->
            <a href="{{ route('user.submissions.create', ['from' => 'dashboard']) }}" class="bg-white rounded-lg shadow-sm p-5 hover:shadow-md transition-shadow group">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center group-hover:bg-blue-200 transition">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="font-montserrat font-semibold text-gray-900">Permohonan Informasi</h3>
                            <p class="font-lato text-xs text-gray-500">Ajukan permohonan data informasi publik</p>
                        </div>
                    </div>
                    <svg class="w-5 h-5 text-gray-400 group-hover:text-blue-600 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </div>
            </a>

            <!-- Konsultasi -->
            <a href="{{ route('user.consultations.create', ['from' => 'dashboard']) }}" class="bg-white rounded-lg shadow-sm p-5 hover:shadow-md transition-shadow group">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center group-hover:bg-green-200 transition">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="font-montserrat font-semibold text-gray-900">Konsultasi</h3>
                            <p class="font-lato text-xs text-gray-500">Konsultasi mengenai sektor-sektor yang ada</p>
                        </div>
                    </div>
                    <svg class="w-5 h-5 text-gray-400 group-hover:text-green-600 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </div>
            </a>

            <!-- Buat Pengaduan -->
            <a href="{{ route('user.complaints.create', ['from' => 'dashboard']) }}" class="bg-white rounded-lg shadow-sm p-5 hover:shadow-md transition-shadow group">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center group-hover:bg-purple-200 transition">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="font-montserrat font-semibold text-gray-900">Buat Pengaduan</h3>
                            <p class="font-lato text-xs text-gray-500">Laporkan masalah layanan</p>
                        </div>
                    </div>
                    <svg class="w-5 h-5 text-gray-400 group-hover:text-purple-600 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </div>
            </a>
        </div>
    </div>

    <!-- Aktivitas Terkini -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-montserrat text-lg font-bold text-gray-900">Aktivitas Terkini</h2>
            <a href="{{ route('user.history.index') }}" class="font-montserrat text-sm text-blue-600 hover:text-blue-700 font-medium">Lihat Semua →</a>
        </div>

        @if($recentActivities && count($recentActivities) > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-montserrat font-semibold text-gray-600 uppercase tracking-wider">ID Tiket</th>
                            <th class="px-4 py-3 text-left text-xs font-montserrat font-semibold text-gray-600 uppercase tracking-wider">Jenis Layanan Pengajuan</th>
                            <th class="px-4 py-3 text-left text-xs font-montserrat font-semibold text-gray-600 uppercase tracking-wider">Judul/Subjek</th>
                            <th class="px-4 py-3 text-left text-xs font-montserrat font-semibold text-gray-600 uppercase tracking-wider">Tanggal</th>
                            <th class="px-4 py-3 text-left text-xs font-montserrat font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-montserrat font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($recentActivities as $activity)
                            <tr class="hover:bg-gray-50">
                                {{-- ID Tiket --}}
                                <td class="px-4 py-4 whitespace-nowrap font-lato text-sm text-gray-900 font-medium">
                                    {{ $activity['ticket_id'] ?? '-' }}
                                </td>
                                
                                {{-- Jenis Layanan --}}
                                <td class="px-4 py-4 whitespace-nowrap">
                                    @if($activity['type'] == 'submission')
                                        <span class="px-3 py-1 text-sm font-lato font-medium text-gray-900">
                                            Permohonan Informasi
                                        </span>
                                    @elseif($activity['type'] == 'consultation')
                                        <span class="px-3 py-1 text-sm font-lato font-medium text-gray-900">
                                            Konsultasi
                                        </span>
                                    @else
                                        <span class="px-3 py-1 text-sm font-lato font-medium text-gray-900">
                                            Pengaduan
                                        </span>
                                    @endif
                                </td>
                                
                                {{-- Judul/Subjek --}}
                                <td class="px-4 py-4 font-lato text-sm text-gray-900">
                                    {{ \Illuminate\Support\Str::limit($activity['title'] ?? '-', 50) }}
                                </td>
                                
                                {{-- Tanggal --}}
                                <td class="px-4 py-4 whitespace-nowrap font-lato text-sm text-gray-600">
                                    {{ $activity['created_at']->format('d/m/Y') }}
                                </td>
                                
                                {{-- Status --}}
                                <td class="px-4 py-4 whitespace-nowrap">
                                    @php 
                                        $status = strtolower($activity['status'] ?? 'pending');
                                    @endphp
                                    @if(in_array($status, ['completed', 'selesai']))
                                        <span class="px-3 py-1 text-xs font-montserrat font-semibold rounded-full bg-green-100 text-green-800">Selesai</span>
                                    @elseif(in_array($status, ['pending', 'in_progress', 'on_progress', 'diproses']))
                                        <span class="px-3 py-1 text-xs font-montserrat font-semibold rounded-full bg-yellow-100 text-yellow-800">Diproses</span>
                                    @elseif(in_array($status, ['rejected', 'ditolak']))
                                        <span class="px-3 py-1 text-xs font-montserrat font-semibold rounded-full bg-red-100 text-red-800">Ditolak</span>
                                    @else
                                        <span class="px-3 py-1 text-xs font-montserrat font-semibold rounded-full bg-gray-100 text-gray-800">{{ ucfirst($status) }}</span>
                                    @endif
                                </td>
                                
                                {{-- Aksi --}}
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <a href="{{ $activity['route'] }}?from=dashboard" class="text-gray-600 hover:text-blue-600" title="Lihat Detail">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-12">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <p class="font-lato text-gray-500">Belum ada aktivitas pengajuan</p>
                <a href="{{ route('user.submissions.create', ['from' => 'dashboard']) }}" class="mt-4 inline-block font-montserrat text-sm text-blue-600 hover:text-blue-700 font-medium">Buat Pengajuan Pertama →</a>
            </div>
        @endif
    </div>
</div>
@endsection