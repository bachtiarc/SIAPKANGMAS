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
                        // Deteksi route prefix untuk penentuan link dashboard & profil
                        $dashRoute = (auth()->user()->user_type == 'pegawai') ? 'user.dashboard' : 'masyarakat.dashboard';
                        $profileRoute = (auth()->user()->user_type == 'pegawai') ? 'user.profile' : 'masyarakat.profile';
                    @endphp

                    <a href="{{ route($dashRoute) }}" class="nav-item group flex items-center px-4 py-3.5 text-sm font-montserrat font-medium rounded-xl transition-all {{ request()->routeIs($dashRoute) ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:bg-gray-100' }}" title="Dashboard">
                        <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 13a1 1 0 011-1h4a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM13 4a1 1 0 011-1h4a1 1 0 011 1v6a1 1 0 01-1 1h-4a1 1 0 01-1-1V4zM13 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z"></path>
                        </svg>
                        <span class="sidebar-text ml-4 whitespace-nowrap font-semibold">Dashboard</span>
                    </a>

                    <a href="#" class="nav-item group flex items-center px-4 py-3.5 text-sm font-montserrat font-medium rounded-xl transition-all text-gray-600 hover:bg-gray-100" title="Permohonan Informasi">
                        <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <span class="sidebar-text ml-4 whitespace-nowrap font-semibold">Permohonan Informasi</span>
                    </a>

                    <a href="#" class="nav-item group flex items-center px-4 py-3.5 text-sm font-montserrat font-medium rounded-xl transition-all text-gray-600 hover:bg-gray-100" title="Konsultasi">
                        <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                        </svg>
                        <span class="sidebar-text ml-4 whitespace-nowrap font-semibold">Konsultasi</span>
                    </a>

                    <a href="#" class="nav-item group flex items-center px-4 py-3.5 text-sm font-montserrat font-medium rounded-xl transition-all text-gray-600 hover:bg-gray-100" title="Buat Pengaduan">
                        <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        <span class="sidebar-text ml-4 whitespace-nowrap font-semibold">Buat Pengaduan</span>
                    </a>

                    <a href="#" class="nav-item group flex items-center px-4 py-3.5 text-sm font-montserrat font-medium rounded-xl transition-all text-gray-600 hover:bg-gray-100" title="Riwayat Pengajuan">
                        <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="sidebar-text ml-4 whitespace-nowrap font-semibold">Riwayat Pengajuan</span>
                    </a>

                    <a href="{{ route($profileRoute) }}" class="nav-item group flex items-center px-4 py-3.5 text-sm font-montserrat font-medium rounded-xl transition-all {{ request()->routeIs($profileRoute) ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:bg-gray-100' }}" title="Profil Pengguna">
                        <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <span class="sidebar-text ml-4 whitespace-nowrap font-semibold">Profil Pengguna</span>
                    </a>
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

        <div class="sidebar-toggle-btn" id="sidebar-toggle" style="position: absolute; left: 88px; z-index: 50;">
            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path>
            </svg>
        </div>

        <div class="flex-1 flex flex-col overflow-hidden">
            
            <header class="bg-white border-b border-gray-200 sticky top-0 z-40 shadow-sm">
                <div class="px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex-1 max-w-md">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>
                                <input type="text" placeholder="Cari layanan atau tiket..." 
                                    class="font-lato block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
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

    <script>
        const sidebar = document.getElementById('sidebar');
        const sidebarToggle = document.getElementById('sidebar-toggle');
        const logoIcon = document.getElementById('logo-icon');
        const logoFull = document.getElementById('logo-full');
        
        let sidebarExpanded = false;

        sidebarToggle.addEventListener('click', function() {
            sidebarExpanded = !sidebarExpanded;
            
            if (sidebarExpanded) {
                // Expand sidebar
                sidebar.style.width = '280px';
                sidebar.classList.add('expanded');
                
                // BERI CLASS ACTIVE PADA TOGGLE SUPAYA PANAH BERPUTAR KE KIRI
                sidebarToggle.classList.add('active');
                
                // Move toggle button
                sidebarToggle.style.left = '280px';
                
                // Show logo full, hide icon
                setTimeout(() => {
                    logoIcon.classList.add('hidden');
                    logoFull.classList.remove('hidden');
                }, 150);
            } else {
                // Collapse sidebar
                sidebar.style.width = '88px';
                sidebar.classList.remove('expanded');
                
                // HAPUS CLASS ACTIVE SUPAYA PANAH BALIK KE KANAN
                sidebarToggle.classList.remove('active');
                
                // Move toggle button back
                sidebarToggle.style.left = '88px';
                
                // Show logo icon, hide full
                logoIcon.classList.remove('hidden');
                logoFull.classList.add('hidden');
            }
        });

        // Logout Modal Functions
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