@extends('layouts.dashboard')

@section('title', 'Permohonan Informasi (CO ADMIN)')

@section('content')
<div class="p-6">

    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                Permohonan Informasi
            </h1>
            <p class="text-gray-500 text-sm">
                Riwayat pengajuan yang dibuat oleh CO ADMIN
            </p>
        </div>

        <a href="{{ route('user.submissions.create') }}"
           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow transition">
            + Buat Permohonan
        </a>
    </div>

    <!-- Success Alert -->
    @if(session('success'))
        <div id="success-alert"
             class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative animate-fade-in">
            <strong>Berhasil!</strong>
            Permohonan dengan nomor tiket
            <strong>{{ session('ticket_id') }}</strong>
            berhasil dibuat.
        </div>

        <script>
            setTimeout(() => {
                const alert = document.getElementById('success-alert');
                if (alert) alert.style.display = 'none';
            }, 5000);
        </script>
    @endif

    <!-- Search & Filter -->
    <form method="GET" class="mb-6 bg-white p-4 rounded-lg shadow-sm">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

            <!-- Search -->
            <input type="text"
                   name="search"
                   value="{{ request('search') }}"
                   placeholder="Cari berdasarkan nomor tiket / judul..."
                   class="border rounded-lg px-3 py-2 w-full focus:ring focus:ring-blue-200">

            <!-- Status -->
            <select name="status"
                    class="border rounded-lg px-3 py-2 w-full focus:ring focus:ring-blue-200">
                <option value="semua">Semua Status</option>
                <option value="pending" {{ request('status')=='pending' ? 'selected' : '' }}>Menunggu Diproses</option>
                <option value="diproses" {{ request('status')=='diproses' ? 'selected' : '' }}>Sedang Diproses</option>
                <option value="selesai" {{ request('status')=='selesai' ? 'selected' : '' }}>Selesai</option>
                <option value="ditolak" {{ request('status')=='ditolak' ? 'selected' : '' }}>Ditolak</option>
            </select>

            <button type="submit"
                    class="bg-gray-800 hover:bg-black text-white px-4 py-2 rounded-lg transition">
                Filter
            </button>
        </div>
    </form>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow-sm overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-100 text-gray-600 uppercase text-xs">
                <tr>
                    <th class="px-4 py-3 text-left">No Tiket</th>
                    <th class="px-4 py-3 text-left">Nama Pemohon</th>
                    <th class="px-4 py-3 text-left">Judul</th>
                    <th class="px-4 py-3 text-left">Tanggal</th>
                    <th class="px-4 py-3 text-left">Status</th>
                    <th class="px-4 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">

            @forelse($submissions as $submission)
                <tr class="hover:bg-gray-50 transition">

                    <td class="px-4 py-3 font-medium text-black-600">
                        {{ $submission->ticket_id }}
                    </td>

                    <td class="px-4 py-3">
                        {{ $submission->applicant_name ?? '-' }}
                    </td>

                    <td class="px-4 py-3">
                        {{ $submission->title }}
                    </td>

                    <td class="px-4 py-3">
                        {{ $submission->created_at->format('d M Y') }}
                    </td>

                    <td class="px-4 py-3">
                        @php
                            $status = strtolower($submission->status);
                        @endphp

                        @if($status == 'pending')
                            <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs">Menunggu Diproses</span>
                        @elseif($status == 'in_progress')
                            <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs">Sedang Diproses</span>
                        @elseif($status == 'completed')
                            <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">Selesai</span>
                        @elseif($status == 'rejected')
                            <span class="bg-red-100 text-red-800 px-2 py-1 rounded text-xs">Ditolak</span>
                        @else
                            <span class="bg-gray-100 text-gray-800 px-2 py-1 rounded text-xs">
                                {{ ucfirst($submission->status) }}
                            </span>
                        @endif
                    </td>

                    <td class="px-4 py-3 text-center">
                        <a href="{{ route('user.submissions.show', $submission->id) }}"
                           class="text-blue-600 hover:text-blue-800 font-medium">
                            Detail
                        </a>
                    </td>

                </tr>
            @empty
                <tr>
                    <td colspan="6"
                        class="text-center px-4 py-6 text-gray-500">
                        Belum ada permohonan informasi.
                    </td>
                </tr>
            @endforelse

            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $submissions->links() }}
    </div>

</div>

<style>
.animate-fade-in {
    animation: fadeIn 0.4s ease-in-out;
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>

@endsection