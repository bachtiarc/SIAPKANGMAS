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
        body { font-family: 'Lato', sans-serif; }
        h1, h2, h3, h4, h5, h6, .font-heading { font-family: 'Montserrat', sans-serif; }
        .font-montserrat { font-family: 'Montserrat', sans-serif; }
        .font-lato { font-family: 'Lato', sans-serif; }

        .app-bg {
            background:
                radial-gradient(1100px 600px at 18% 8%, rgba(37, 99, 235, .14), transparent 62%),
                radial-gradient(900px 520px at 86% 16%, rgba(59, 130, 246, .12), transparent 62%),
                radial-gradient(950px 520px at 60% 92%, rgba(148, 163, 184, .18), transparent 58%),
                linear-gradient(180deg, #fbfdff 0%, #f8fafc 100%);
        }

        ::-webkit-scrollbar { width: 10px; height: 10px; }
        ::-webkit-scrollbar-thumb { background: rgba(148,163,184,.55); border-radius: 999px; border: 2px solid rgba(255,255,255,.7); }
        ::-webkit-scrollbar-track { background: transparent; }

        .grain::before{
            content:"";
            position:fixed;
            inset:0;
            pointer-events:none;
            opacity:.06;
            background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='120' height='120'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='.9' numOctaves='2' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='120' height='120' filter='url(%23n)' opacity='.45'/%3E%3C/svg%3E");
        }

        @media (prefers-reduced-motion: reduce) {
            * { scroll-behavior: auto !important; transition: none !important; animation: none !important; }
        }

        /* ===== Sidebar Collapse Mode ===== */
        .sidebar-collapsed { width: 88px !important; }
        .sidebar-collapsed .sidebar-text { display: none !important; }
        .sidebar-collapsed .sidebar-pad { padding: 16px !important; }
        .sidebar-collapsed .sidebar-navpad { padding-left: 12px !important; padding-right: 12px !important; }
        .sidebar-collapsed .nav-item { justify-content: center !important; padding-left: 0.75rem !important; padding-right: 0.75rem !important; }
        .sidebar-collapsed .sidebar-avatar { width: 44px !important; height: 44px !important; border-radius: 18px !important; }

        /* Toggle button positioning */
        #sidebarToggle {
            position: absolute;
            right: -18px;
            top: 24px;
            z-index: 50;
        }
    </style>

    @stack('styles')
</head>

<body class="app-bg grain text-gray-900">
<div class="flex h-screen overflow-hidden">

    <!-- SIDEBAR -->
    <aside id="sidebar" class="w-64 flex-shrink-0 relative transition-all duration-200">
        <div class="h-full bg-white/75 backdrop-blur-xl border-r border-gray-200/70 shadow-[0_1px_0_rgba(15,23,42,.04)] flex flex-col">

            <div class="p-6 sidebar-pad transition-all duration-200">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-white font-bold text-lg
                                bg-gradient-to-br from-blue-600 to-blue-500 shadow-sm ring-1 ring-white/50 sidebar-avatar">
                        {{ $initial }}
                    </div>
                    <div class="min-w-0 sidebar-text">
                        <h3 class="text-sm font-montserrat font-semibold text-gray-900 truncate">
                            {{ $user->name ?? 'Admin' }}
                        </h3>
                        <p class="font-lato text-sm text-gray-600 truncate">Disperindag Jateng</p>
                    </div>
                </div>
            </div>

            <nav class="pb-6 flex-1">
                <div class="px-4 space-y-2 sidebar-navpad transition-all duration-200">

                    @php
                        $navBase = "nav-item group relative flex items-center px-3 py-3 text-sm font-montserrat font-semibold rounded-2xl transition-all duration-200";
                        $navOff  = "text-gray-600 hover:bg-gray-100/70 hover:text-gray-900";
                        $navOn   = "bg-blue-100/70 text-blue-700 ring-1 ring-blue-200/60 shadow-sm";
                    @endphp

                    <!-- Dashboard -->
                    <a href="{{ route('admin.dashboard') }}"
                       class="{{ $navBase }} {{ request()->routeIs('admin.dashboard') ? $navOn : $navOff }}"
                       data-tooltip="Dashboard">
                        <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                        </svg>
                        <span class="ml-2 whitespace-nowrap sidebar-text">Dashboard</span>
                    </a>

                    <!-- Manajemen Pengajuan -->
                    <a href="{{ route('admin.management.semua') }}"
                       class="{{ $navBase }}
                       {{
                            request()->routeIs('admin.consultations.*')
                            || request()->routeIs('admin.submissions.*')
                            || request()->routeIs('admin.complaints.*')
                            || request()->routeIs('admin.management.*')
                            ? $navOn : $navOff
                       }}"
                       data-tooltip="Manajemen Pengajuan">
                        <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span class="ml-2 whitespace-nowrap sidebar-text">Manajemen Pengajuan</span>
                    </a>

                    <!-- Manajemen Kategori -->
                    <a href="{{ route('admin.categories.kategori') }}"
                       class="{{ $navBase }}
                       {{
                            request()->routeIs('admin.categories.*')
                            || request()->routeIs('admin.categories.kategori')
                            ? $navOn : $navOff
                       }}"
                       data-tooltip="Manajemen Kategori">
                        <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                        </svg>
                        <span class="ml-2 whitespace-nowrap sidebar-text">Manajemen Kategori</span>
                    </a>

                </div>
            </nav>

            <div class="p-4 sidebar-navpad transition-all duration-200">
                <button type="button" onclick="showLogoutModal()"
                        class="w-full group relative flex items-center px-4 py-3 text-sm font-montserrat font-semibold rounded-2xl transition-all
                               text-red-600 hover:bg-red-50/70 active:scale-[.99]"
                        data-tooltip="Keluar">
                    <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    <span class="ml-4 whitespace-nowrap sidebar-text">Keluar</span>
                </button>

                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                    @csrf
                </form>
            </div>

        </div>

        <!-- TOGGLE SIDEBAR BUTTON -->
        <button id="sidebarToggle"
                type="button"
                class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-white border border-gray-200
                       shadow-lg hover:shadow-xl transition-all active:scale-95">
            <svg id="chevronIcon" class="w-5 h-5 text-gray-700 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
            </svg>
        </button>
    </aside>

    <!-- MAIN -->
    <div class="flex-1 flex flex-col overflow-hidden">

        <!-- HEADER -->
        <header class="sticky top-0 z-40 bg-white/70 backdrop-blur-xl border-b border-gray-200/70">
            <div class="px-4 sm:px-6 lg:px-8 py-4">
                <div class="flex items-center gap-6">

                    <!-- LEFT: Title -->
                    <div class="min-w-[220px]">
                        <h1 class="font-montserrat text-2xl font-bold tracking-tight text-blue-600">
                            @yield('header_title', 'Dashboard Admin')
                        </h1>
                    </div>

                    <!-- MIDDLE: Search Ticket -->
                    <div class="flex-1 relative">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M21 21l-4.35-4.35m1.85-5.15a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>

                            <input id="globalTicketSearch"
                                   type="text"
                                   autocomplete="off"
                                   placeholder="Cari tiket (ID / subjek / nama / email)..."
                                   class="w-full bg-white/70 border border-gray-200/80 rounded-2xl pl-10 pr-10 py-3 text-sm
                                          shadow-sm ring-1 ring-white/40
                                          focus:ring-4 focus:ring-blue-500/15 focus:border-blue-500/60 outline-none transition" />

                            <button type="button"
                                    id="clearTicketSearch"
                                    class="hidden absolute inset-y-0 right-0 px-3 text-gray-400 hover:text-gray-600">
                                ✕
                            </button>
                        </div>

                        <!-- Dropdown -->
                        <div id="ticketSearchDropdown"
                             class="hidden absolute mt-2 w-full bg-white/85 backdrop-blur-xl border border-gray-200/70 rounded-2xl
                                    shadow-[0_16px_48px_rgba(15,23,42,.12)] overflow-hidden z-50">
                            <div id="ticketSearchResults" class="max-h-80 overflow-auto"></div>
                            <div id="ticketSearchEmpty" class="hidden px-4 py-4 text-sm text-gray-500">
                                Tidak ada hasil.
                            </div>
                        </div>
                    </div>

                    <!-- RIGHT: Logo -->
                    <div class="flex items-center justify-end min-w-[180px]">
                        <div class="bg-white/70 border border-gray-200/70 rounded-2xl px-4 py-2 shadow-sm ring-1 ring-white/30">
                            <img src="{{ asset('images/logo.png') }}" alt="SIAPKANGMAS" class="h-10">
                        </div>
                    </div>

                </div>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto px-4 sm:px-6 lg:px-8 py-6">
            @yield('content')
        </main>

    </div>
</div>

<!-- LOGOUT MODAL -->
<div id="logoutModal" class="fixed inset-0 bg-slate-900/40 hidden items-center justify-center z-50">
    <div class="bg-white/90 backdrop-blur-xl w-full max-w-md rounded-3xl shadow-2xl p-6 text-center border border-gray-200/70">
        <div class="mx-auto mb-4 w-16 h-16 flex items-center justify-center rounded-2xl bg-red-100 ring-1 ring-white/40">
            <svg class="w-9 h-9 text-red-600"
                 fill="none"
                 stroke="currentColor"
                 viewBox="0 0 24 24"
                 stroke-width="2.5"
                 stroke-linecap="round"
                 stroke-linejoin="round">
                <path d="M17 16l4-4m0 0l-4-4m4 4H7"/>
                <path d="M7 4v16"/>
            </svg>
        </div>

        <h2 class="text-lg font-montserrat font-bold text-gray-900">
            Konfirmasi Logout
        </h2>

        <p class="text-sm text-gray-600 mt-2">
            Anda yakin ingin keluar dari akun Anda?
        </p>

        <div class="mt-6 flex justify-center gap-3">
            <button type="button"
                    onclick="hideLogoutModal()"
                    class="px-5 py-2.5 rounded-2xl border border-gray-300 text-gray-700 font-semibold
                           hover:bg-gray-100 transition active:scale-[.99]">
                Batal
            </button>

            <button type="button"
                    onclick="confirmLogout()"
                    class="px-5 py-2.5 rounded-2xl bg-red-600 text-white font-semibold
                           hover:bg-red-700 transition shadow-sm active:scale-[.99]">
                Ya, Keluar
            </button>
        </div>
    </div>
</div>

<script>
    // ===== LOGOUT MODAL =====
    function showLogoutModal() {
        const modal = document.getElementById('logoutModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function hideLogoutModal() {
        const modal = document.getElementById('logoutModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    function confirmLogout() {
        document.getElementById('logout-form').submit();
    }

    document.getElementById('logoutModal')?.addEventListener('click', function(e) {
        if (e.target === this) hideLogoutModal();
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') hideLogoutModal();
    });

    // ===== GLOBAL SEARCH TICKET =====
    (function () {
        const input = document.getElementById('globalTicketSearch');
        const dropdown = document.getElementById('ticketSearchDropdown');
        const resultsBox = document.getElementById('ticketSearchResults');
        const emptyBox = document.getElementById('ticketSearchEmpty');
        const clearBtn = document.getElementById('clearTicketSearch');

        if (!input) return;

        let t = null;
        let lastQuery = '';

        const closeDropdown = () => {
            dropdown.classList.add('hidden');
            resultsBox.innerHTML = '';
            emptyBox.classList.add('hidden');
        };

        const openDropdown = () => dropdown.classList.remove('hidden');

        const escapeHtml = (s) => (s ?? '').toString()
            .replaceAll('&', '&amp;').replaceAll('<', '&lt;').replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;').replaceAll("'", '&#039;');

        const render = (items) => {
            resultsBox.innerHTML = '';

            if (!items || items.length === 0) {
                emptyBox.classList.remove('hidden');
                return;
            }
            emptyBox.classList.add('hidden');

            items.forEach(item => {
                const badgeClass = item.type === 'pengaduan'
                    ? 'bg-red-50 text-red-700'
                    : item.type === 'konsultasi'
                        ? 'bg-yellow-50 text-yellow-700'
                        : 'bg-blue-50 text-blue-700';

                const row = document.createElement('a');
                row.href = item.url;
                row.className =
                    'block px-4 py-3 hover:bg-gray-50/70 transition border-b border-gray-100/80 last:border-b-0 ' +
                    'active:scale-[.998]';

                row.innerHTML = `
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <div class="text-sm font-semibold text-gray-900 truncate">
                                ${escapeHtml(item.ticket)}
                            </div>
                            <div class="text-xs text-gray-600 truncate mt-0.5">
                                ${escapeHtml(item.title || item.subject || '-')}
                            </div>
                            <div class="text-xs text-gray-500 truncate mt-0.5">
                                ${escapeHtml(item.name || '-')} • ${escapeHtml(item.email || '-')}
                            </div>
                        </div>
                        <span class="shrink-0 inline-flex px-2.5 py-1 rounded-full text-xs font-semibold ${badgeClass} ring-1 ring-black/5">
                            ${escapeHtml(item.type_label)}
                        </span>
                    </div>
                `;
                resultsBox.appendChild(row);
            });
        };

        const fetchResults = async (q) => {
            const res = await fetch(`{{ route('admin.tickets.search') }}?q=${encodeURIComponent(q)}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            if (!res.ok) return [];
            const data = await res.json();
            return data?.data ?? [];
        };

        input.addEventListener('input', () => {
            const q = input.value.trim();
            clearBtn.classList.toggle('hidden', q.length === 0);

            if (t) clearTimeout(t);

            if (q.length < 2) {
                closeDropdown();
                return;
            }

            t = setTimeout(async () => {
                lastQuery = q;
                openDropdown();

                const items = await fetchResults(q);
                if (input.value.trim() !== lastQuery) return;

                render(items);
            }, 250);
        });

        clearBtn.addEventListener('click', () => {
            input.value = '';
            clearBtn.classList.add('hidden');
            closeDropdown();
            input.focus();
        });

        document.addEventListener('click', (e) => {
            if (!dropdown.contains(e.target) && e.target !== input) closeDropdown();
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closeDropdown();
        });
    })();

    // ===== SIDEBAR TOGGLE =====
    (function () {
        const sidebar = document.getElementById('sidebar');
        const toggle = document.getElementById('sidebarToggle');
        const chevron = document.getElementById('chevronIcon');
        if (!sidebar || !toggle) return;

        const KEY = 'admin_sidebar_collapsed';

        const apply = (collapsed) => {
            sidebar.classList.toggle('sidebar-collapsed', collapsed);
            if (collapsed) {
                chevron.style.transform = 'rotate(180deg)';
            } else {
                chevron.style.transform = 'rotate(0deg)';
            }
        };

        const saved = localStorage.getItem(KEY);
        apply(saved === '1');

        toggle.addEventListener('click', () => {
            const nowCollapsed = !sidebar.classList.contains('sidebar-collapsed');
            localStorage.setItem(KEY, nowCollapsed ? '1' : '0');
            apply(nowCollapsed);
        });
    })();
</script>

@stack('scripts')
</body>
</html>