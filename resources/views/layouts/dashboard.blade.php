<!-- resources/views/layouts/dashboard.blade.php -->
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - SIAPKANGMAS</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    
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

        /* Smooth transitions for sidebar */
        .sidebar {
            transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Hide scrollbar for sidebar */
        .sidebar::-webkit-scrollbar {
            width: 4px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: transparent;
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }

        .sidebar::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* Sidebar toggle button - OUTSIDE SIDEBAR */
        .sidebar-toggle-btn {
            position: fixed;
            top: 50%;
            transform: translateY(-50%);
            width: 22px;
            height: 48px;
            background: white;
            border: 1px solid #e5e7eb;
            border-left: none;
            border-radius: 0 24px 24px 0;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 2px 0 8px rgba(0, 0, 0, 0.05);
            z-index: 50;
        }

        .sidebar-toggle-btn:hover {
            background: #f9fafb;
            box-shadow: 2px 0 12px rgba(0, 0, 0, 0.1);
        }

        /* PERBAIKAN LOGIKA PANAH DI SINI */
        .sidebar-toggle-btn svg {
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            /* Awal (Collapsed): Panah ke kanan → */
            transform: rotate(0deg);
        }

        /* Saat Sidebar Terbuka (Active): Panah berputar 180 derajat ke kiri ← */
        .sidebar-toggle-btn.active svg {
            transform: rotate(180deg);
        }

        /* Smooth fade in/out for text */
        .sidebar-text {
            opacity: 0;
            transition: opacity 0.2s ease;
        }

        .sidebar.expanded .sidebar-text {
            opacity: 1;
            transition: opacity 0.3s ease 0.1s;
        }

        /* Custom styles for search dropdown */
        .search-results {
            max-height: 400px;
            overflow-y: auto;
        }
        .search-result-item:hover {
            background-color: #f3f4f6;
        }
    </style>
    
    @stack('styles')
</head>
<body class="bg-gray-50">
    <div class="flex h-screen overflow-hidden">
        
        <aside id="sidebar" class="sidebar bg-gray-50 border-r border-gray-200 flex-shrink-0 overflow-y-auto relative" style="width: 88px;">

            <div class="h-24 flex items-center justify-center px-4">
                <a href="{{ route('home') }}" class="inline-block">
                    <img id="logo-icon" src="{{ asset('images/logo_icon.png') }}" alt="SIAPKANGMAS" class="h-14 w-14 object-contain cursor-pointer hover:opacity-80 transition">
                    <img id="logo-full" src="{{ asset('images/logo.png') }}" alt="SIAPKANGMAS" class="h-14 object-contain hidden cursor-pointer hover:opacity-80 transition">
                </a>
            </div>

            <nav class="py-6">
                <div class="px-4 space-y-2">
                    @php
                        // Deteksi user type untuk route dinamis
                        $userType = auth()->user()->user_type;
                        $isPegawai = $userType === 'pegawai';
                        $isMasyarakat = $userType === 'masyarakat_umum';
                        
                        // Route dinamis berdasarkan user type
                        $dashRoute = $isPegawai ? 'user.dashboard' : 'masyarakat.dashboard';
                        $profileRoute = $isPegawai ? 'user.profile' : 'masyarakat.profile';
                        $submissionsRoute = $isPegawai ? 'user.submissions.index' : 'masyarakat.submissions.index';
                        $submissionsPattern = $isPegawai ? 'user.submissions.*' : 'masyarakat.submissions.*';
                        $consultationsRoute = $isPegawai ? 'user.consultations.index' : 'masyarakat.consultations.index';
                        $consultationsPattern = $isPegawai ? 'user.consultations.*' : 'masyarakat.consultations.*';
                        $complaintsRoute = $isPegawai ? 'user.complaints.index' : 'masyarakat.complaints.index';
                        $complaintsPattern = $isPegawai ? 'user.complaints.*' : 'masyarakat.complaints.*';
                    @endphp

                    {{-- Dashboard --}}
                    <a href="{{ route($dashRoute) }}" class="nav-item group flex items-center px-4 py-3.5 text-sm font-montserrat font-medium rounded-xl transition-all {{ request()->routeIs($dashRoute) ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:bg-gray-100' }}" title="Dashboard">
                        <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 13a1 1 0 011-1h4a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM13 4a1 1 0 011-1h4a1 1 0 011 1v6a1 1 0 01-1 1h-4a1 1 0 01-1-1V4zM13 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z"></path>
                        </svg>
                        <span class="sidebar-text ml-4 whitespace-nowrap font-semibold">Dashboard</span>
                    </a>

                    {{-- Permohonan Informasi (Dinamis untuk Pegawai & Masyarakat) --}}
                    <a href="{{ route($submissionsRoute) }}" class="nav-item group flex items-center px-4 py-3.5 text-sm font-montserrat font-medium rounded-xl transition-all {{ request()->routeIs($submissionsPattern) ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:bg-gray-100' }}" title="Permohonan Informasi">
                        <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <span class="sidebar-text ml-4 whitespace-nowrap font-semibold">Permohonan Informasi</span>
                    </a>

                    <a href="{{ route($consultationsRoute) }}" class="nav-item group flex items-center px-4 py-3.5 text-sm font-montserrat font-medium rounded-xl transition-all {{ request()->routeIs($consultationsPattern) ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:bg-gray-100' }}">
                        <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                        </svg>
                        <span class="sidebar-text ml-4 font-semibold">Konsultasi</span>
                    </a>

                    {{-- Buat Pengaduan (SEMUA USER) --}}
                    <a href="{{ route($complaintsRoute) }}" class="nav-item group flex items-center px-4 py-3.5 text-sm font-montserrat font-medium rounded-xl transition-all {{ request()->routeIs($complaintsPattern) ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:bg-gray-100' }}" title="Buat Pengaduan">
                        <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        <span class="sidebar-text ml-4 whitespace-nowrap font-semibold">Pengaduan</span>
                    </a>


                    {{-- Riwayat Pengajuan (Dinamis untuk Pegawai & Masyarakat) --}}
                    @php
                        $historyRoute = $isPegawai ? 'user.history.index' : 'masyarakat.history.index';
                        $historyPattern = $isPegawai ? 'user.history.*' : 'masyarakat.history.*';
                    @endphp

                    <a href="{{ route($historyRoute) }}" 
                    class="nav-item group flex items-center px-4 py-3.5 text-sm font-montserrat font-medium rounded-xl transition-all {{ request()->routeIs($historyPattern) ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:bg-gray-100' }}" 
                    title="Riwayat Pengajuan">
                        <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="sidebar-text ml-4 whitespace-nowrap font-semibold">Riwayat Pengajuan</span>
                    </a>

                    {{-- Profil Pengguna --}}
                    @if($isMasyarakat)
                    <a href="{{ route('masyarakat.profile') }}"
                    class="nav-item group flex items-center px-4 py-3.5 text-sm font-montserrat font-medium rounded-xl transition-all
                    {{ request()->routeIs('masyarakat.profile')
                            ? 'bg-blue-100 text-blue-700'
                            : 'text-gray-600 hover:bg-gray-100' }}"
                    title="Profil Pengguna">
                        <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <span class="sidebar-text ml-4 whitespace-nowrap font-semibold">Profil Pengguna</span>
                    </a>
                    @endif

                </div>

                <div class="my-6 px-4">
                    <div class="border-t border-gray-300"></div>
                </div>

                <div class="px-4 space-y-2">
                    <button type="button" onclick="showLogoutModal()" class="w-full nav-item group flex items-center px-4 py-3.5 text-sm font-montserrat font-medium rounded-xl transition-all text-red-600 hover:bg-red-50" title="Keluar">
                        <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                        <span class="sidebar-text ml-4 whitespace-nowrap font-semibold">Keluar</span>
                    </button>
                    
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </div>
            </nav>
        </aside>

        <!-- TOGGLE BUTTON - PENTING: POSISI FIXED -->
        <div class="sidebar-toggle-btn" id="sidebar-toggle" style="left: 88px;">
            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path>
            </svg>
        </div>

        <div class="flex-1 flex flex-col overflow-hidden">
            
            <header class="bg-white border-b border-gray-200 sticky top-0 z-40 shadow-sm">
                <div class="px-6 py-4">
                    <div class="flex items-center justify-between">
                        <!-- Search Bar with Live Preview -->
                        <div class="flex-1 max-w-md relative" id="searchContainer">
                            <form action="{{ route('search.result') }}" method="GET" id="searchForm">
                                <div class="relative">
                                    <input 
                                        type="text" 
                                        name="q" 
                                        id="searchInput"
                                        placeholder="Cari judul pengajuan atau tiket..." 
                                        class="font-lato block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white"
                                        autocomplete="off"
                                        autocorrect="off"
                                        autocapitalize="off"
                                        spellcheck="false"
                                    >
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                        </svg>
                                    </div>
                                </div>
                            </form>
                            
                            <!-- Live Search Results Preview -->
                            <div id="searchResults" class="hidden absolute top-full left-0 right-0 mt-2 bg-white rounded-lg shadow-lg border border-gray-200 search-results z-50">
                                <div id="searchResultsContent" class="py-2">
                                    <!-- Results will be inserted here by JavaScript -->
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center space-x-6 ml-6">
                            <a href="{{ route('home') }}" class="font-montserrat text-sm font-medium text-gray-700 hover:text-blue-600 transition">Beranda</a>
                            <a href="{{ route('about') }}" class="font-montserrat text-sm font-medium text-gray-700 hover:text-blue-600 transition">Tentang Kami</a>
                        </div>
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto bg-gray-50">
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

    <!-- SCRIPT UTAMA - JANGAN DIHAPUS -->
    <script>
        // PASTIKAN SEMUA ELEMENT KELOAD DULU
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const sidebarToggle = document.getElementById('sidebar-toggle');
            const logoIcon = document.getElementById('logo-icon');
            const logoFull = document.getElementById('logo-full');
            const searchInput = document.getElementById('searchInput');
            const searchResults = document.getElementById('searchResults');
            const searchResultsContent = document.getElementById('searchResultsContent');
            const searchForm = document.getElementById('searchForm');
            
            let searchTimeout = null;
            let sidebarExpanded = false;

            // DEBUGGING - CEK APAKAH ELEMENT KEDETECT
            console.log('Sidebar:', sidebar);
            console.log('Toggle Button:', sidebarToggle);
            console.log('Search Input:', searchInput);

            // ==================== LIVE SEARCH FUNCTIONALITY ====================
            if (searchInput && searchResults && searchResultsContent) {
                // Event listener untuk mendeteksi setiap ketikan
                searchInput.addEventListener('input', function(e) {
                    clearTimeout(searchTimeout);
                    const query = this.value.trim();
                    
                    console.log('Input event triggered:', query); // Debug
                    
                    if (query.length < 1) {
                        searchResults.classList.add('hidden');
                        return;
                    }

                    // Show loading state
                    searchResultsContent.innerHTML = `
                        <div class="px-4 py-3 text-sm text-gray-500 text-center">
                            <svg class="animate-spin h-5 w-5 mx-auto text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                    `;
                    searchResults.classList.remove('hidden');

                    searchTimeout = setTimeout(() => {
                        fetchSearchResults(query);
                    }, 300); // Debounce 300ms
                });

                // Also trigger on keyup for better responsiveness
                searchInput.addEventListener('keyup', function(e) {
                    // Skip if arrow keys or enter
                    if (['ArrowUp', 'ArrowDown', 'Enter'].includes(e.key)) {
                        return;
                    }
                    
                    const query = this.value.trim();
                    if (query.length >= 2 && searchResults.classList.contains('hidden')) {
                        clearTimeout(searchTimeout);
                        searchTimeout = setTimeout(() => {
                            fetchSearchResults(query);
                        }, 300);
                    }
                });

                
                // Fetch search results via AJAX
                function fetchSearchResults(query) {
                    console.log('Fetching results for:', query); // Debug

                    fetch(`{{ route('search.preview') }}?q=${encodeURIComponent(query)}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        }
                    })
                    .then(response => {
                        console.log('Response received:', response.status); // Debug

                        // Jika bukan response sukses, anggap tidak ada hasil (BUKAN error)
                        if (!response.ok) {
                            return [];
                        }

                        // Pastikan response benar-benar JSON
                        const contentType = response.headers.get('content-type') || '';
                        if (!contentType.includes('application/json')) {
                            return [];
                        }

                        return response.json();
                    })
                    .then(data => {
                        console.log('Data received:', data); // Debug

                        // Pastikan data valid sebelum diproses
                        if (!Array.isArray(data)) {
                            data = [];
                        }

                        displaySearchResults(data, query);
                    })
                    .catch(error => {
                        // ERROR jaringan / JS saja yang masuk sini
                        console.error('Search error:', error);

                        // Untuk AJAX preview, JANGAN tampilkan pesan error
                        // Cukup sembunyikan hasil
                        searchResults.classList.add('hidden');
                    });
                }


                // Display search results in dropdown
                function displaySearchResults(results, query) {
                    if (results.length === 0) {
                        searchResultsContent.innerHTML = `
                            <div class="px-4 py-3 text-sm text-gray-500">
                                Tidak ada hasil untuk "${query}"
                            </div>
                        `;
                        searchResults.classList.remove('hidden');
                        return;
                    }

                    let html = '';
                    results.forEach(item => {
                        html += `
                            <a href="${item.url}" class="block px-4 py-3 hover:bg-gray-50 search-result-item border-b border-gray-100 last:border-b-0">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="text-xs text-gray-500 font-lato">${item.ticket || ''}</span>
                                        </div>
                                        <div class="font-medium text-gray-900 text-sm">${item.title}</div>
                                    </div>
                                    <svg class="w-4 h-4 text-gray-400 ml-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </div>
                            </a>
                        `;
                    });

                    // Add "See all results" link
                    html += `
                        <div class="px-4 py-3 bg-gray-50 border-t border-gray-200">
                            <button type="submit" form="searchForm" class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                                Lihat semua hasil untuk "${query}" →
                            </button>
                        </div>
                    `;

                    searchResultsContent.innerHTML = html;
                    searchResults.classList.remove('hidden');
                }

                // Close search results when clicking outside
                document.addEventListener('click', function(e) {
                    const searchContainer = document.getElementById('searchContainer');
                    if (searchContainer && !searchContainer.contains(e.target)) {
                        searchResults.classList.add('hidden');
                    }
                });

                // Show results when input is focused if there's content
                searchInput.addEventListener('focus', function() {
                    const query = this.value.trim();
                    if (query.length >= 2) {
                        // If there's already content in results, show it
                        if (searchResultsContent.innerHTML.trim() !== '') {
                            searchResults.classList.remove('hidden');
                        } else {
                            // Otherwise fetch fresh results
                            fetchSearchResults(query);
                        }
                    }
                });

                // Handle paste event
                searchInput.addEventListener('paste', function(e) {
                    setTimeout(() => {
                        const query = this.value.trim();
                        if (query.length >= 2) {
                            clearTimeout(searchTimeout);
                            searchTimeout = setTimeout(() => {
                                fetchSearchResults(query);
                            }, 300);
                        }
                    }, 10);
                });
            }

            // ==================== SIDEBAR TOGGLE ====================
            if (sidebarToggle && sidebar) {
                sidebarToggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    console.log('Toggle clicked! Current state:', sidebarExpanded);
                    
                    sidebarExpanded = !sidebarExpanded;
                    
                    if (sidebarExpanded) {
                        // EXPAND SIDEBAR
                        console.log('Expanding sidebar...');
                        sidebar.style.width = '280px';
                        sidebar.classList.add('expanded');
                        
                        // PANAH MUTER KE KIRI
                        sidebarToggle.classList.add('active');
                        
                        // Move toggle button
                        sidebarToggle.style.left = '280px';
                        
                        // Show logo full, hide icon
                        if (logoIcon && logoFull) {
                            setTimeout(() => {
                                logoIcon.classList.add('hidden');
                                logoFull.classList.remove('hidden');
                            }, 150);
                        }
                    } else {

                        console.log('Collapsing sidebar...');
                        sidebar.style.width = '88px';
                        sidebar.classList.remove('expanded');
                        
                        sidebarToggle.classList.remove('active');
                        
                        sidebarToggle.style.left = '88px';
                        
                        if (logoIcon && logoFull) {
                            logoIcon.classList.remove('hidden');
                            logoFull.classList.add('hidden');
                        }
                    }
                });
            } else {
                console.error('ERROR: Sidebar atau Toggle button tidak ditemukan!');
            }
        });

        function showLogoutModal() {
            const modal = document.getElementById('logoutModal');
            modal.style.display = 'flex';
            setTimeout(() => {
                modal.classList.add('opacity-100');
            }, 10);
        }

        function hideLogoutModal() {
            const modal = document.getElementById('logoutModal');
            modal.classList.remove('opacity-100');
            setTimeout(() => {
                modal.style.display = 'none';
            }, 200);
        }

        function confirmLogout() {
            document.getElementById('logout-form').submit();
        }

        // Close modal when clicking outside
        document.getElementById('logoutModal').addEventListener('click', function(e) {
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