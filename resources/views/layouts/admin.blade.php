@php
    $user = auth()->user();
    $initial = $user?->name ? strtoupper(substr($user->name, 0, 1)) : 'A';
@endphp

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard Admin') - SIAPKANGMAS</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Lato', sans-serif;
        }
        
        h1, h2, h3, h4, h5, h6, .font-heading {
            font-family: 'Montserrat', sans-serif;
        }
        
        .font-montserrat {
            font-family: 'Montserrat', sans-serif;
        }
        
        .font-lato {
            font-family: 'Lato', sans-serif;
        }
    </style>
    
    @stack('styles')
</head>
<body class="bg-gray-50">
    <div class="flex h-screen overflow-hidden">
        
        <aside class="w-64 bg-white border-r border-gray-200 flex-shrink-0">
            <div class="p-6">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold text-lg">
                        {{ $initial }}
                    </div>
                    <div>
                        <h3 class="text-sm font-montserrat font-semibold text-gray-900">
                            {{ $user->name ?? 'Admin' }}
                        </h3>
                        <p class="font-lato text-sm text-gray-600">Disperindag Jateng</p>
                    </div>
                </div>
            </div>

            <nav class="py-6">
                <div class="px-4 space-y-2">

                    <!-- Dashboard -->
                    <a href="{{ route('admin.dashboard') }}"
                    class="nav-item group flex items-center px-2 py-3.5 text-sm font-montserrat font-semibold rounded-xl transition-all
                    {{ request()->routeIs('admin.dashboard') ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:bg-gray-100' }}">
                        <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                        </svg>
                        <span class="sidebar-text ml-4 whitespace-nowrap">Dashboard</span>
                    </a>

                    <!-- Manajemen Pengajuan -->
                    <a href="{{ route('admin.management.semua') }}"
                    class="nav-item group flex items-center px-2 py-3.5 text-sm font-montserrat font-semibold rounded-xl transition-all
                    {{ request()->routeIs('admin.consultations.*')
                        || request()->routeIs('admin.submissions.*')
                        || request()->routeIs('admin.complaints.*')
                        || request()->routeIs('admin.management.*')
                        ? 'bg-blue-100 text-blue-700'
                        : 'text-gray-600 hover:bg-gray-100' }}">
                        <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span class="sidebar-text ml-4 whitespace-nowrap">Manajemen Pengajuan</span>
                    </a>

                    <!-- Manajemen Kategori -->
                    <a href="#"
                    class="nav-item group flex items-center px-2 py-3.5 text-sm font-montserrat font-semibold rounded-xl transition-all
                    text-gray-600 hover:bg-gray-100">
                        <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                        </svg>
                        <span class="sidebar-text ml-4 whitespace-nowrap">Manajemen Kategori</span>
                    </a>

                </div>
            </nav>
            
            <div class="absolute bottom-0 w-64 p-4">
                <button onclick="showLogoutModal()"
                        class="w-full nav-item group flex items-center px-4 py-3.5 text-sm font-montserrat font-semibold rounded-xl transition-all text-red-600 hover:bg-red-50">
                    <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    <span class="sidebar-text ml-4 whitespace-nowrap">Keluar</span>
                </button>
                
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </div>
        </aside>

        <div class="flex-1 flex flex-col overflow-hidden">
            
            <header class="bg-white border-b border-gray-200 px-8 py-4">
                <div class="flex items-center justify-between">
                    <h1 class="font-montserrat text-2xl font-bold text-blue-600">
                        @yield('header_title', 'Dashboard Admin')
                    </h1>
                    
                    <div class="flex items-center space-x-4">
                        <img src="{{ asset('images/logo.png') }}" alt="SIAPKANGMAS" class="h-12">
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto bg-gray-50 p-8">
                @yield('content')
            </main>

        </div>

    </div>

    <div id="logoutModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 hidden items-center justify-center" style="display: none; z-index: 100;">
        <div class="bg-white rounded-2xl shadow-2xl p-8 max-w-md w-full mx-4 transform transition-all">
            <div class="flex justify-center mb-6">
                <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center">
                    <svg class="w-10 h-10 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                </div>
            </div>

            <h2 class="font-montserrat text-2xl font-bold text-gray-900 text-center mb-3">
                Konfirmasi Logout
            </h2>

            <p class="font-lato text-gray-600 text-center mb-8">
                Anda yakin ingin keluar dari akun Anda?
            </p>

            <div class="flex gap-3">
                <button type="button" onclick="hideLogoutModal()" class="flex-1 font-montserrat px-6 py-3 border-2 border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition">
                    Batal
                </button>
                <button type="button" onclick="confirmLogout()" class="flex-1 font-montserrat px-6 py-3 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 transition">
                    Ya, Keluar
                </button>
            </div>
        </div>
    </div>

    <script>
        // Logout Modal Functions
        function showLogoutModal() {
            const modal = document.getElementById('logoutModal');
            modal.style.display = 'flex';
        }

        function hideLogoutModal() {
            const modal = document.getElementById('logoutModal');
            modal.style.display = 'none';
        }

        function confirmLogout() {
            document.getElementById('logout-form').submit();
        }

        // Close modal when clicking outside
        document.getElementById('logoutModal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                hideLogoutModal();
            }
        });

        // Close modal with ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                hideLogoutModal();
            }
        });
    </script>

    @stack('scripts')
</body>
</html>