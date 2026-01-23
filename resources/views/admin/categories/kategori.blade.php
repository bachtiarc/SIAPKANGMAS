{{-- resources/views/admin/categories/kategori.blade.php --}}
@extends('layouts.admin')

@section('header_title', 'Manajemen Kategori')
@section('title', 'Manajemen Kategori')

@section('content')
<div class="space-y-6">

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl font-lato">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl font-lato">
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
        <div class="lg:col-span-8 bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">

            {{-- Tabs --}}
            <div class="p-4 border-b border-gray-200">
                <div class="grid grid-cols-3 gap-2 bg-gray-50 p-2 rounded-2xl">
                    @php
                        // safety default biar ga undefined di blade
                        $serviceOptions = $serviceOptions ?? [
                            'konsultasi' => 'Konsultasi',
                            'pengaduan'  => 'Pengaduan',
                            'permohonan' => 'Permohonan Informasi',
                        ];
                        $type = $type ?? request('type', 'konsultasi');
                        $q = $q ?? request('q', '');
                    @endphp

                    @foreach($serviceOptions as $key => $label)
                        <a href="{{ route('admin.categories.kategori', ['type' => $key, 'q' => request('q')]) }}"
                           class="text-center px-4 py-3 rounded-xl font-montserrat font-semibold text-sm transition
                           {{ $type === $key ? 'bg-blue-700 text-white shadow-sm' : 'text-gray-600 hover:bg-white' }}">
                            {{ $label }}
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- Search --}}
            <div class="p-4 border-b border-gray-200">
                <form method="GET" action="{{ route('admin.categories.kategori') }}" class="flex flex-col sm:flex-row gap-3">
                    <input type="hidden" name="type" value="{{ $type }}">

                    <div class="flex-1 relative">
                        <input
                            type="text"
                            name="q"
                            value="{{ $q }}"
                            placeholder="Cari kategori... (contoh: k / K001 / ekspor)"
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500"
                        >
                    </div>

                    <button type="submit"
                            class="px-4 py-2.5 rounded-xl bg-blue-700 text-white text-sm font-semibold hover:bg-blue-800 transition">
                        Cari
                    </button>

                    <a href="{{ route('admin.categories.kategori', ['type' => $type]) }}"
                       class="px-4 py-2.5 rounded-xl border border-blue-600 text-blue-700 text-sm font-semibold hover:bg-blue-50 transition text-center">
                        Reset
                    </a>
                </form>
            </div>

            {{-- Table scrollable --}}
            <div class="overflow-x-auto">
                <table class="min-w-[980px] w-full text-left border-collapse">
                    <thead class="bg-gray-50">
                        <tr class="text-xs uppercase tracking-wider text-gray-600">
                            <th class="px-5 py-4 font-bold border-b text-center w-[140px]">ID Kategori</th>
                            <th class="px-5 py-4 font-bold border-b text-center w-[260px]">Nama Kategori</th>
                            <th class="px-5 py-4 font-bold border-b text-center w-[180px]">Layanan</th>
                            <th class="px-5 py-4 font-bold border-b text-center min-w-[360px]">Deskripsi</th>
                            <th class="px-5 py-4 font-bold border-b text-center w-[140px]">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100">
                        @forelse($categories as $cat)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-5 py-4 text-sm text-gray-800 text-center font-semibold">
                                    {{ 'K' . str_pad((string)$cat->id, 3, '0', STR_PAD_LEFT) }}
                                </td>

                                <td class="px-5 py-4 text-sm text-gray-900 font-semibold text-center">
                                    {{ $cat->name }}
                                </td>

                                <td class="px-5 py-4 text-sm text-gray-700 text-center">
                                    {{ $serviceOptions[$cat->type] ?? ucfirst($cat->type) }}
                                </td>

                                <td class="px-5 py-4 text-sm text-gray-700">
                                    <div class="line-clamp-2">
                                        {{ $cat->description ?? '-' }}
                                    </div>
                                </td>

                                <td class="px-5 py-4 text-center">
                                    <div class="inline-flex items-center gap-2">

                                        <a href="{{ route('admin.categories.edit', $cat->id) }}"
                                           class="inline-flex p-2 rounded-lg hover:bg-blue-50 text-gray-600 hover:text-blue-700 transition"
                                           title="Edit">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                            </svg>
                                        </a>

                                        {{-- tombol hapus -> buka modal --}}
                                        <button type="button"
                                                onclick="openDeleteModal({{ $cat->id }})"
                                                class="inline-flex p-2 rounded-lg hover:bg-red-50 text-gray-600 hover:text-red-600 transition"
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
            <div class="p-4 border-t border-gray-200">
                {{ $categories->appends(['type' => $type, 'q' => request('q')])->links() }}
            </div>
        </div>

        {{-- RIGHT: FORM --}}
        <div class="lg:col-span-4 bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-12 h-12 rounded-2xl bg-blue-50 flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 4v16m8-8H4"/>
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
                            class="w-full appearance-none px-4 py-3 pr-10 rounded-xl border border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500 bg-white">
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
                           class="w-full px-4 py-3 rounded-xl border border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label class="block font-montserrat font-semibold text-gray-700 mb-2">Deskripsi</label>
                    <textarea name="description" rows="4"
                              placeholder="Tuliskan deskripsi singkat di sini..."
                              class="w-full px-4 py-3 rounded-xl border border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500 resize-none">{{ old('description') }}</textarea>
                </div>

                <div class="flex gap-3 pt-2">
                    <a href="{{ route('admin.categories.kategori', ['type' => $type]) }}"
                       class="flex-1 text-center px-4 py-3 rounded-xl border border-blue-600 text-blue-700 font-montserrat font-semibold hover:bg-blue-50 transition">
                        Batal
                    </a>
                    <button type="submit"
                            class="flex-1 px-4 py-3 rounded-xl bg-blue-700 text-white font-montserrat font-semibold hover:bg-blue-800 transition">
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

    <div class="bg-white w-full max-w-md rounded-2xl shadow-xl p-6 text-center">
        <div class="mx-auto mb-4 w-16 h-16 flex items-center justify-center rounded-full bg-red-100">
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
                    class="px-5 py-2 rounded-xl border border-gray-300 text-gray-700 font-semibold hover:bg-gray-100 transition">
                Batal
            </button>

            <button type="button"
                    onclick="submitDelete()"
                    class="px-5 py-2 rounded-xl bg-red-600 text-white font-semibold hover:bg-red-700 transition">
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