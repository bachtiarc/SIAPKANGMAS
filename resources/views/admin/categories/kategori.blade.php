{{-- resources/views/admin/categories/kategori.blade.php --}}
@extends('layouts.admin')

@section('header_title', 'Manajemen Kategori')
@section('title', 'Manajemen Kategori')

@section('content')
<div class="space-y-6">

    @if(session('success'))
        <div class="bg-green-50/80 backdrop-blur-xl border border-green-200/70 text-green-800 px-4 py-3 rounded-2xl font-lato shadow-sm ring-1 ring-black/5">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-50/80 backdrop-blur-xl border border-red-200/70 text-red-800 px-4 py-3 rounded-2xl font-lato shadow-sm ring-1 ring-black/5">
            <div class="font-semibold mb-1">Ada yang perlu diperbaiki:</div>
            <ul class="list-disc pl-5 space-y-1 text-sm">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <p class="font-lato text-gray-600">
        Kelola dan tambah kategori pada layanan bantuan Dinas Perindustrian dan Perdagangan Jawa Tengah.
    </p>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        {{-- LEFT: TABLE --}}
        <div class="lg:col-span-8 bg-white/75 backdrop-blur-xl rounded-3xl shadow-sm border border-gray-200/70 overflow-hidden">

            {{-- Tabs --}}
            <div class="p-4 border-b border-gray-200/70">
                @php
                    // safety default biar ga undefined di blade
                    $serviceOptions = $serviceOptions ?? [
                        'konsultasi' => 'Konsultasi',
                        'pengaduan'  => 'Pengaduan',
                        'permohonan' => 'Permohonan Informasi',
                    ];
                    $type = $type ?? request('type', 'konsultasi');
                    $q = $q ?? request('q', '');
                    $tabBase = 'text-center px-4 py-3 rounded-2xl font-montserrat font-semibold text-sm transition ring-1 ring-transparent';
                    $tabOff  = 'text-gray-600 bg-white/60 ring-gray-200/70 hover:bg-gray-50/70 hover:text-blue-600 hover:ring-gray-200/80';
                    $tabOn   = 'text-blue-700 bg-blue-50/70 ring-blue-200/70 shadow-sm';
                @endphp

                <div class="grid grid-cols-3 gap-2">
                    @foreach($serviceOptions as $key => $label)
                        <a href="{{ route('admin.categories.kategori', ['type' => $key, 'q' => request('q')]) }}"
                           class="{{ $tabBase }} {{ $type === $key ? $tabOn : $tabOff }}">
                            {{ $label }}
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- Search --}}
            <div class="p-4 border-b border-gray-200/70">
                <form method="GET" action="{{ route('admin.categories.kategori') }}" class="flex flex-col sm:flex-row gap-3">
                    <input type="hidden" name="type" value="{{ $type }}">

                    <div class="flex-1 relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M21 21l-4.35-4.35m1.85-5.15a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <input
                            type="text"
                            name="q"
                            value="{{ $q }}"
                            placeholder="Cari kategori... (contoh: k / K001 / ekspor)"
                            class="w-full pl-10 pr-4 py-2.5 rounded-2xl border border-gray-300/80 text-sm
                                   focus:ring-4 focus:ring-blue-500/15 focus:border-blue-500/60
                                   text-gray-700 bg-white/70 shadow-sm"
                        >
                    </div>

                    <button type="submit"
                            class="px-4 py-2.5 rounded-2xl bg-blue-700 text-white text-sm font-semibold hover:bg-blue-800 transition shadow-sm active:scale-[.99]">
                        Cari
                    </button>

                    <a href="{{ route('admin.categories.kategori', ['type' => $type]) }}"
                       class="px-4 py-2.5 rounded-2xl border border-blue-600/80 text-blue-700 text-sm font-semibold hover:bg-blue-50/70 transition text-center bg-white/70 shadow-sm active:scale-[.99]">
                        Reset
                    </a>
                </form>
            </div>

            {{-- Table scrollable --}}
            <div class="overflow-x-auto">
                <table class="min-w-[980px] w-full text-left border-collapse">
                    <thead class="bg-gray-50/70 backdrop-blur sticky top-0 z-10">
                        <tr class="text-xs uppercase tracking-wider text-gray-600">
                            <th class="px-5 py-4 font-bold border-b border-gray-200/70 text-center w-[140px] whitespace-nowrap">ID Kategori</th>
                            <th class="px-5 py-4 font-bold border-b border-gray-200/70 text-center w-[260px] whitespace-nowrap">Nama Kategori</th>
                            <th class="px-5 py-4 font-bold border-b border-gray-200/70 text-center w-[180px] whitespace-nowrap">Layanan</th>
                            <th class="px-5 py-4 font-bold border-b border-gray-200/70 text-center min-w-[360px]">Deskripsi</th>
                            <th class="px-5 py-4 font-bold border-b border-gray-200/70 text-center w-[140px] whitespace-nowrap">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100/70">
                        @forelse($categories as $cat)
                            <tr class="hover:bg-gray-50/70 transition">
                                <td class="px-5 py-4 text-sm text-gray-900 text-center font-semibold whitespace-nowrap">
                                    {{ 'K' . str_pad((string)$cat->id, 3, '0', STR_PAD_LEFT) }}
                                </td>

                                <td class="px-5 py-4 text-sm text-gray-900 font-semibold text-center">
                                    <div class="max-w-[260px] mx-auto truncate">{{ $cat->name }}</div>
                                </td>

                                <td class="px-5 py-4 text-sm text-gray-700 text-center whitespace-nowrap">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-50/70 text-blue-700 ring-1 ring-blue-200/70">
                                        {{ $serviceOptions[$cat->type] ?? ucfirst($cat->type) }}
                                    </span>
                                </td>

                                <td class="px-5 py-4 text-sm text-gray-700">
                                    <div class="line-clamp-2">
                                        {{ $cat->description ?? '-' }}
                                    </div>
                                </td>

                                <td class="px-5 py-4 text-center whitespace-nowrap">
                                    <div class="inline-flex items-center gap-2">
                                        <a href="{{ route('admin.categories.edit', $cat->id) }}"
                                           class="inline-flex p-2 rounded-full hover:bg-blue-50/70 text-gray-600 hover:text-blue-700 transition active:scale-[.99]"
                                           title="Edit">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                            </svg>
                                        </a>

                                        <button type="button"
                                                onclick="openDeleteModal({{ $cat->id }})"
                                                class="inline-flex p-2 rounded-full hover:bg-red-50/70 text-gray-600 hover:text-red-600 transition active:scale-[.99]"
                                                title="Hapus">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7h6m2 0H7m3-3h4a1 1 0 011 1v2H9V5a1 1 0 011-1z"/>
                                            </svg>
                                        </button>

                                        {{-- form delete hidden --}}
                                        <form id="delete-form-{{ $cat->id }}"
                                              action="{{ route('admin.categories.destroy', $cat->id) }}"
                                              method="POST"
                                              class="hidden">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-10 text-center text-gray-500">
                                    Tidak ada kategori untuk layanan ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="p-4 border-t border-gray-200/70">
                {{ $categories->appends(['type' => $type, 'q' => request('q')])->links() }}
            </div>
        </div>

        {{-- RIGHT: FORM --}}
        <div class="lg:col-span-4 bg-white/75 backdrop-blur-xl rounded-3xl shadow-sm border border-gray-200/70 p-6">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-12 h-12 rounded-2xl bg-blue-50/80 ring-1 ring-blue-200/70 flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                </div>
                <h2 class="font-montserrat font-bold text-xl text-gray-900">Tambah Kategori</h2>
            </div>

            <form method="POST" action="{{ route('admin.categories.store') }}" class="space-y-5">
                @csrf

                <div>
                    <label class="block font-montserrat font-semibold text-gray-700 mb-2">Nama Layanan</label>
                    <div class="relative">
                        <select name="type"
                            class="w-full appearance-none px-4 py-3 pr-10 rounded-2xl border border-gray-300/80 text-sm
                                   focus:ring-4 focus:ring-blue-500/15 focus:border-blue-500/60 bg-white/70 text-gray-700 shadow-sm">
                            @foreach($serviceOptions as $key => $label)
                                <option value="{{ $key }}" {{ old('type', $type) === $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block font-montserrat font-semibold text-gray-700 mb-2">Nama Kategori</label>
                    <input type="text" name="name" value="{{ old('name') }}"
                           placeholder="Masukkan nama kategori"
                           class="w-full px-4 py-3 rounded-2xl border border-gray-300/80 text-sm
                                  focus:ring-4 focus:ring-blue-500/15 focus:border-blue-500/60 bg-white/70 text-gray-700 shadow-sm">
                </div>

                <div>
                    <label class="block font-montserrat font-semibold text-gray-700 mb-2">Deskripsi</label>
                    <textarea name="description" rows="4"
                              placeholder="Tuliskan deskripsi singkat di sini..."
                              class="w-full px-4 py-3 rounded-2xl border border-gray-300/80 text-sm
                                     focus:ring-4 focus:ring-blue-500/15 focus:border-blue-500/60 bg-white/70 text-gray-700 shadow-sm resize-none">{{ old('description') }}</textarea>
                </div>

                <div class="flex gap-3 pt-2">
                    <a href="{{ route('admin.categories.kategori', ['type' => $type]) }}"
                       class="flex-1 text-center px-4 py-3 rounded-2xl border border-blue-600/80 text-blue-700 font-montserrat font-semibold hover:bg-blue-50/70 transition shadow-sm bg-white/70 active:scale-[.99]">
                        Batal
                    </a>
                    <button type="submit"
                            class="flex-1 px-4 py-3 rounded-2xl bg-blue-700 text-white font-montserrat font-semibold hover:bg-blue-800 transition shadow-sm active:scale-[.99]">
                        Simpan
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>

{{-- Modal Konfirmasi Hapus (custom) --}}
<div id="deleteModal"
     class="fixed inset-0 bg-black/40 hidden items-center justify-center z-50">

    <div class="bg-white/90 backdrop-blur-xl w-full max-w-md rounded-3xl shadow-xl p-6 text-center border border-gray-200/70 ring-1 ring-black/5">
        <div class="mx-auto mb-4 w-16 h-16 flex items-center justify-center rounded-2xl bg-red-50/80 ring-1 ring-red-200/70">
            <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862
                      a2 2 0 01-1.995-1.858L5 7m5-4h4m-4
                      0a1 1 0 00-1 1v1h6V4a1 1 0
                      00-1-1m-4 0h4"/>
            </svg>
        </div>

        <h2 class="text-lg font-montserrat font-bold text-gray-900">
            Konfirmasi Hapus
        </h2>

        <p class="text-sm text-gray-600 mt-2">
            Anda yakin ingin menghapus kategori?
        </p>

        <div class="mt-6 flex justify-center gap-3">
            <button type="button"
                    onclick="closeDeleteModal()"
                    class="px-5 py-2 rounded-2xl border border-gray-300/80 text-gray-700 font-semibold hover:bg-gray-100/70 transition active:scale-[.99]">
                Batal
            </button>

            <button type="button"
                    onclick="submitDelete()"
                    class="px-5 py-2 rounded-2xl bg-red-600 text-white font-semibold hover:bg-red-700 transition shadow-sm active:scale-[.99]">
                Ya, Hapus
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let deleteId = null;

    function openDeleteModal(id) {
        deleteId = id;
        const modal = document.getElementById('deleteModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeDeleteModal() {
        deleteId = null;
        const modal = document.getElementById('deleteModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    function submitDelete() {
        if (!deleteId) return;
        const form = document.getElementById(`delete-form-${deleteId}`);
        if (form) form.submit();
    }

    // klik backdrop untuk tutup
    document.getElementById('deleteModal').addEventListener('click', function (e) {
        if (e.target === this) closeDeleteModal();
    });

    // ESC untuk tutup
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeDeleteModal();
    });
</script>
@endpush
