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
    </style>

    @stack('styles')
</head>

<body class="bg-gray-50">
<div class="flex h-screen overflow-hidden">

    <!-- SIDEBAR -->
    <aside class="w-64 bg-white border-r border-gray-200 flex-shrink-0 relative">
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
                   {{
                        request()->routeIs('admin.consultations.*')
                        || request()->routeIs('admin.submissions.*')
                        || request()->routeIs('admin.complaints.*')
                        || request()->routeIs('admin.management.*')
                        ? 'bg-blue-100 text-blue-700'
                        : 'text-gray-600 hover:bg-gray-100'
                   }}">
                    <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span class="sidebar-text ml-4 whitespace-nowrap">Manajemen Pengajuan</span>
                </a>

                <!-- Manajemen Kategori (ACTIVE FIX) -->
                <a href="{{ route('admin.categories.kategori') }}"
                   class="nav-item group flex items-center px-2 py-3.5 text-sm font-montserrat font-semibold rounded-xl transition-all
                   {{
                        request()->routeIs('admin.categories.*')
                        || request()->routeIs('admin.categories.kategori')
                        ? 'bg-blue-100 text-blue-700'
                        : 'text-gray-600 hover:bg-gray-100'
                   }}">
                    <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                    </svg>
                    <span class="sidebar-text ml-4 whitespace-nowrap">Manajemen Kategori</span>
                </a>

            </div>
        </nav>

        <div class="absolute bottom-0 w-64 p-4">
            <button type="button" onclick="showLogoutModal()"
                    class="w-full nav-item group flex items-center px-4 py-3.5 text-sm font-montserrat font-semibold rounded-xl transition-all text-red-600 hover:bg-red-50">
                <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                <span class="sidebar-text ml-4 whitespace-nowrap">Keluar</span>
            </button>

            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                @csrf
            </form>
        </div>
    </aside>

    <!-- MAIN -->
    <div class="flex-1 flex flex-col overflow-hidden">

        <!-- HEADER (TITLE - SEARCH - LOGO) -->
        <header class="bg-white border-b border-gray-200 px-8 py-4">
            <div class="flex items-center gap-6">

                <!-- LEFT: Title -->
                <div class="min-w-[260px]">
                    <h1 class="font-montserrat text-2xl font-bold text-blue-600">
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
                               class="w-full bg-gray-50 border border-gray-200 rounded-xl pl-10 pr-10 py-3 text-sm
                                      focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition" />

                        <button type="button"
                                id="clearTicketSearch"
                                class="hidden absolute inset-y-0 right-0 px-3 text-gray-400 hover:text-gray-600">
                            ✕
                        </button>
                    </div>

                    <!-- Dropdown -->
                    <div id="ticketSearchDropdown"
                         class="hidden absolute mt-2 w-full bg-white border border-gray-200 rounded-xl shadow-lg overflow-hidden z-50">
                        <div id="ticketSearchResults" class="max-h-80 overflow-auto"></div>
                        <div id="ticketSearchEmpty" class="hidden px-4 py-4 text-sm text-gray-500">
                            Tidak ada hasil.
                        </div>
                    </div>
                </div>

                <!-- RIGHT: Logo -->
                <div class="flex items-center justify-end min-w-[180px]">
                    <img src="{{ asset('images/logo.png') }}" alt="SIAPKANGMAS" class="h-12">
                </div>

            </div>
        </header>

        <main class="flex-1 overflow-y-auto bg-gray-50 p-8">
            @yield('content')
        </main>

    </div>
</div>

<!-- LOGOUT MODAL (SAME SIZE AS SAVE MODAL) -->
<div id="logoutModal" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-50">
    <div class="bg-white w-full max-w-md rounded-2xl shadow-xl p-6 text-center">
        <div class="mx-auto mb-4 w-16 h-16 flex items-center justify-center rounded-full bg-red-100">
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
                    class="px-5 py-2 rounded-xl border border-gray-300 text-gray-700 font-semibold hover:bg-gray-100 transition">
                Batal
            </button>

            <button type="button"
                    onclick="confirmLogout()"
                    class="px-5 py-2 rounded-xl bg-red-600 text-white font-semibold hover:bg-red-700 transition">
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

    // Close when clicking outside
    document.getElementById('logoutModal')?.addEventListener('click', function(e) {
        if (e.target === this) hideLogoutModal();
    });

    // Close with ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') hideLogoutModal();
    });

    // ===== GLOBAL TICKET SEARCH (AUTOCOMPLETE) =====
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
                row.className = 'block px-4 py-3 hover:bg-gray-50 transition border-b border-gray-100 last:border-b-0';

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
                        <span class="shrink-0 inline-flex px-2.5 py-1 rounded-full text-xs font-semibold ${badgeClass}">
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
</script>

@stack('scripts')
</body>
</html>