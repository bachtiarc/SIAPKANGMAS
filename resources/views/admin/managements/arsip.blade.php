@extends('layouts.admin')

@section('header_title', 'Arsip Pengajuan')
@section('title', 'Arsip Pengajuan')

@section('content')
<div class="space-y-6">

    {{-- HEADER --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div class="flex flex-col gap-2">
            <p class="font-lato text-gray-600">
                Daftar pengajuan yang telah diarsipkan.
            </p>
        </div>
    </div>

    {{-- TABLE --}}
    <div class="bg-white rounded-3xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr class="text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        <th class="px-4 py-3">Layanan</th>
                        <th class="px-4 py-3">No. Tiket</th>
                        <th class="px-4 py-3">Nama Pelapor</th>
                        <th class="px-4 py-3">Email</th>
                        <th class="px-4 py-3">Subjek</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Tanggal Arsip</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse ($items as $row)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-4 py-4 font-medium text-gray-900">
                                {{ $row['service'] }}
                            </td>

                            <td class="px-4 py-4 text-gray-600">
                                {{ $row['ticket'] }}
                            </td>

                            <td class="px-4 py-4 text-gray-900 max-w-[200px] truncate">
                                {{ $row['name'] }}
                            </td>

                            <td class="px-4 py-4 text-gray-600 max-w-[240px] truncate">
                                {{ $row['email'] }}
                            </td>

                            <td class="px-4 py-4 text-gray-600 max-w-[240px] truncate">
                                {{ $row['subject'] }}
                            </td>

                            <td class="px-4 py-4">
                                @php
                                    $status = strtolower($row['status']);
                                @endphp

                                @if(in_array($status, ['completed','selesai']))
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700">
                                        Selesai
                                    </span>
                                @elseif(in_array($status, ['rejected','ditolak']))
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-700">
                                        Ditolak
                                    </span>
                                @else
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-600">
                                        {{ ucfirst($status) }}
                                    </span>
                                @endif
                            </td>

                            <td class="px-4 py-4 text-gray-600">
                                {{ optional($row['archived_at'])->format('d M Y H:i') }}
                            </td>

                            {{-- AKSI: PULIHKAN --}}
                            <td class="px-4 py-4 text-center">
                                <div class="inline-flex items-center gap-2">

                                    {{-- Lihat --}}
                                    <a href="{{ $row['show_route'] }}"
                                       class="inline-flex p-2 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-full transition active:scale-[.99]"
                                       title="Lihat Detail">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7
                                                     -1.274 4.057-5.065 7-9.542 7
                                                     -4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>

                                    {{-- Pulihkan (pakai modal cantik) --}}
                                    <button type="button"
                                            class="restore-btn inline-flex p-2 text-gray-500 hover:text-green-700 hover:bg-green-50 rounded-full transition active:scale-[.99]"
                                            title="Pulihkan"
                                            data-action="{{ $row['unarchive_route'] }}">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M3 10h11a4 4 0 014 4v7m0 0l-3-3m3 3l3-3M7 10V7a2 2 0 012-2h6a2 2 0 012 2v3" />
                                        </svg>
                                    </button>

                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                                Belum ada data arsip.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        @if ($items->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $items->links() }}
            </div>
        @endif
    </div>
</div>

<!-- ========================= -->
<!-- MODAL PULIHKAN (CANTIK) -->
<!-- ========================= -->
<div id="restoreModal" class="fixed inset-0 bg-slate-900/40 hidden items-center justify-center z-50">
    <div class="bg-white/90 backdrop-blur-xl w-full max-w-md rounded-3xl shadow-2xl p-6 text-center border border-gray-200/70">
        <div class="mx-auto mb-4 w-16 h-16 flex items-center justify-center rounded-2xl bg-green-100 ring-1 ring-white/40">
            <svg class="w-9 h-9 text-green-700"
                 fill="none"
                 stroke="currentColor"
                 viewBox="0 0 24 24"
                 stroke-width="2.5"
                 stroke-linecap="round"
                 stroke-linejoin="round">
                <path d="M3 10h11a4 4 0 014 4v7"/>
                <path d="M18 21l-3-3m3 3l3-3"/>
                <path d="M7 10V7a2 2 0 012-2h6a2 2 0 012 2v3"/>
            </svg>
        </div>

        <h2 class="text-lg font-montserrat font-bold text-gray-900">
            Konfirmasi Pulihkan
        </h2>

        <p class="text-sm text-gray-600 mt-2">
            Apakah Anda yakin ingin memulihkan pengajuan ini dari arsip?
        </p>

        <div class="mt-6 flex justify-center gap-3">
            <button type="button"
                    id="cancelRestore"
                    class="px-5 py-2.5 rounded-2xl border border-gray-300 text-gray-700 font-semibold
                           hover:bg-gray-100 transition active:scale-[.99]">
                Batal
            </button>

            <button type="button"
                    id="confirmRestore"
                    class="px-5 py-2.5 rounded-2xl bg-green-700 text-white font-semibold
                           hover:bg-green-800 transition shadow-sm active:scale-[.99]">
                Ya, Pulihkan
            </button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('restoreModal');
    const confirmBtn = document.getElementById('confirmRestore');
    const cancelBtn = document.getElementById('cancelRestore');

    if (!modal || !confirmBtn || !cancelBtn) return;

    let currentAction = null;

    const openModal = () => {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    };

    const closeModal = () => {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        currentAction = null;
    };

    // Klik tombol pulihkan (wajib pakai .restore-btn + data-action)
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.restore-btn');
        if (!btn) return;

        e.preventDefault();

        currentAction = btn.dataset.action;
        if (!currentAction) return;

        openModal();
    });

    cancelBtn.addEventListener('click', closeModal);

    // Klik backdrop => tutup
    modal.addEventListener('click', function(e) {
        if (e.target === this) closeModal();
    });

    // ESC => tutup
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeModal();
    });

    confirmBtn.addEventListener('click', function () {
        if (!currentAction) return;

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = currentAction;

        const csrf = document.createElement('input');
        csrf.type = 'hidden';
        csrf.name = '_token';
        csrf.value = "{{ csrf_token() }}";

        form.appendChild(csrf);
        document.body.appendChild(form);
        form.submit();
    });
});
</script>
@endsection