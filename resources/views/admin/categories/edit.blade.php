@extends('layouts.admin')

@section('header_title', 'Edit Kategori')
@section('title', 'Edit Kategori')

@section('content')
<div class="max-w-3xl mx-auto bg-white rounded-2xl shadow-sm border border-gray-200 p-8">

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl font-lato mb-6">
            <ul class="list-disc pl-5 space-y-1 text-sm">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <h2 class="font-montserrat font-bold text-2xl text-gray-900 mb-6">Edit Kategori</h2>

    <form id="edit-category-form"
          method="POST"
          action="{{ route('admin.categories.update', $category->id) }}"
          class="space-y-5">
        @csrf
        @method('PUT')

        <div>
            <label class="block font-montserrat font-semibold text-gray-700 mb-2">Nama Layanan</label>
            <select name="type"
                    class="w-full px-4 py-3 rounded-xl border border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500 bg-white">
                @foreach($serviceOptions as $key => $label)
                    <option value="{{ $key }}" {{ old('type', $category->type) === $key ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block font-montserrat font-semibold text-gray-700 mb-2">Nama Kategori</label>
            <input type="text" name="name" value="{{ old('name', $category->name) }}"
                   class="w-full px-4 py-3 rounded-xl border border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
        </div>

        <div>
            <label class="block font-montserrat font-semibold text-gray-700 mb-2">Deskripsi</label>
            <textarea name="description" rows="4"
                      class="w-full px-4 py-3 rounded-xl border border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500 resize-none">{{ old('description', $category->description) }}</textarea>
        </div>

        <div>
            <label class="block font-montserrat font-semibold text-gray-700 mb-2">Status</label>
            <select name="is_active"
                    class="w-full px-4 py-3 rounded-xl border border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500 bg-white">
                <option value="1" {{ (int)old('is_active', $category->is_active) === 1 ? 'selected' : '' }}>Aktif</option>
                <option value="0" {{ (int)old('is_active', $category->is_active) === 0 ? 'selected' : '' }}>Nonaktif</option>
            </select>
        </div>

        <div class="flex gap-3 pt-2">
            <a href="{{ route('admin.categories.kategori', ['type' => $category->type]) }}"
               class="px-5 py-3 rounded-xl border border-blue-600 text-blue-700 font-montserrat font-semibold hover:bg-blue-50 transition">
                Kembali
            </a>

            {{-- tombol simpan -> buka modal konfirmasi --}}
            <button type="button"
                    onclick="openSaveModal()"
                    class="px-5 py-3 rounded-xl bg-blue-700 text-white font-montserrat font-semibold hover:bg-blue-800 transition">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>

{{-- Modal Konfirmasi Simpan (SAMA UKURAN & STYLE KAYA MODAL HAPUS) --}}
<div id="saveModal"
     class="fixed inset-0 bg-black/40 hidden items-center justify-center z-50">

    <div class="bg-white w-full max-w-md rounded-2xl shadow-xl p-6 text-center">
        <div class="mx-auto mb-4 w-16 h-16 flex items-center justify-center rounded-full bg-blue-100">
            {{-- icon dokumen --}}
            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M7 3h7l3 3v15a2 2 0 01-2 2H7.862
                      a2 2 0 01-1.995-1.858L5 7V5a2 2 0 012-2z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M14 3v4a2 2 0 002 2h4"/>
            </svg>
        </div>

        <h2 class="text-lg font-montserrat font-bold text-gray-900">
            Konfirmasi Perubahan
        </h2>

        <p class="text-sm text-gray-600 mt-2">
            Anda yakin ingin menyimpan perubahan?
        </p>

        <div class="mt-6 flex justify-center gap-3">
            <button type="button"
                    onclick="closeSaveModal()"
                    class="px-5 py-2 rounded-xl border border-gray-300 text-gray-700 font-semibold hover:bg-gray-100 transition">
                Batal
            </button>

            <button type="button"
                    onclick="submitEditForm()"
                    class="px-5 py-2 rounded-xl bg-blue-600 text-white font-semibold hover:bg-blue-700 transition">
                Ya, Simpan
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function openSaveModal() {
        const modal = document.getElementById('saveModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeSaveModal() {
        const modal = document.getElementById('saveModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    function submitEditForm() {
        const form = document.getElementById('edit-category-form');
        if (form) form.submit();
    }

    // klik backdrop untuk tutup
    document.getElementById('saveModal').addEventListener('click', function (e) {
        if (e.target === this) closeSaveModal();
    });

    // ESC untuk tutup
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeSaveModal();
    });
</script>
@endpush