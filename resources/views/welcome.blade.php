@extends('layouts.app')

@section('title', 'Beranda')

@section('content')
<!-- Hero Section -->
<div class="bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="text-center">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">
                Selamat Datang di <span class="text-blue-600">SIAP</span><span class="text-orange-500">KANGMAS</span>
            </h1>
            <p class="text-lg text-gray-600 mb-8 max-w-3xl mx-auto">
                Layanan bantuan terintegrasi untuk mendukung konsultasi, pengaduan, dan permohonan informasi Anda di Dinas Perindustrian dan Perdagangan Jawa Tengah.
            </p>
            
            <div class="flex justify-center space-x-4">
                <a href="{{ route('register') }}" class="bg-orange-500 hover:bg-orange-600 text-white px-8 py-3 rounded-lg font-medium flex items-center transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Ajukan Tiket Baru
                </a>
                <a href="{{ route('login') }}" class="bg-blue-50 hover:bg-blue-100 text-blue-600 px-8 py-3 rounded-lg font-medium transition">
                    Lacak Tiket Anda
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Lacak Tiket Cepat Section (FITUR BARU) -->
<div class="bg-white border-t border-b border-gray-200">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="bg-white rounded-lg shadow-lg p-8 border-2 border-orange-500">
            <h2 class="text-2xl font-bold text-gray-900 mb-2 text-center">Lacak Tiket Cepat</h2>
            <p class="text-gray-600 text-center mb-6">Cek status pengajuan Anda tanpa login.</p>
            
            @if(session('error'))
                <div class="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('ticket.search') }}" method="GET" class="flex gap-2">
                <input 
                    type="text" 
                    name="ticket_id" 
                    placeholder="Masukkan ID Tiket Anda dengan Lengkap 22-23 Karakter" 
                    value="{{ request('ticket_id') }}"
                    class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    required
                >
                <button 
                    type="submit" 
                    class="bg-orange-500 hover:bg-orange-600 text-white px-8 py-3 rounded-lg font-semibold transition shadow-md hover:shadow-lg"
                >
                    Lacak
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Features Section -->
<div class="bg-blue-50 py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl font-bold text-center text-gray-900 mb-4">
            Fitur Utama Layanan Kami
        </h2>
        <p class="text-center text-gray-600 mb-12">
            Kami menyediakan berbagai fitur untuk mempermudah Anda mendapatkan<br>
            bantuan yang dibutuhkan.
        </p>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Feature 1 -->
            <div class="bg-white rounded-lg p-8 shadow-sm hover:shadow-md transition">
                <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mb-6 mx-auto">
                    <svg class="w-8 h-8 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-center text-gray-900 mb-3">Ajukan Permohonan</h3>
                <p class="text-center text-gray-600">
                    Buat tiket permohonan konsultasi, pengaduan, dan permohonan informasi melalui formulir online.
                </p>
            </div>

            <!-- Feature 2 -->
            <div class="bg-white rounded-lg p-8 shadow-sm hover:shadow-md transition">
                <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mb-6 mx-auto">
                    <svg class="w-8 h-8 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-center text-gray-900 mb-3">Lacak Tiket</h3>
                <p class="text-center text-gray-600">
                    Pantau status dan progres tiket Anda secara real-time dari mana saja dan kapan saja.
                </p>
            </div>

            <!-- Feature 3 -->
            <div class="bg-white rounded-lg p-8 shadow-sm hover:shadow-md transition">
                <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mb-6 mx-auto">
                    <svg class="w-8 h-8 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-center text-gray-900 mb-3">Hubungi Kami</h3>
                <p class="text-center text-gray-600">
                    Dapatkan bantuan langsung dari tim support kami melalui berbagai platform komunikasi.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection