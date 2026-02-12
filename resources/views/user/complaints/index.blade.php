@extends('layouts.dashboard')

@section('title', 'Pengaduan (CO ADMIN)')

@section('content')
<div class="p-6">

    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Pengaduan</h1>
            <p class="text-gray-500 text-sm">Riwayat pengaduan yang dibuat oleh CO ADMIN</p>
        </div>

        <a href="{{ route('user.complaints.create') }}"
           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow transition">
            + Buat Pengaduan
        </a>
    </div>

    <!-- Success Alert -->
    @if(session('success'))
        <div id="success-alert"
             class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative animate-fade-in">
            <strong>Berhasil!</strong>
            Pengaduan dengan nomor tiket
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

            <input type="text"
                   name="search"
                   value="{{ request('search') }}"
                   placeholder="Cari berdasarkan nomor tiket / subjek..."
                   class="border rounded-lg px-3 py-2 w-full focus:ring focus:ring-blue-200">

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
                    <th class="px-4 py-3 text-left">Subjek</th>
                    <th class="px-4 py-3 text-left">Tanggal</th>
                    <th class="px-4 py-3 text-left">Status</th>
                    <th class="px-4 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">

            @forelse($complaints as $c)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-4 py-3 font-medium text-black-600">
                        {{ $c->ticket_number }}
                    </td>

                    <td class="px-4 py-3">
                        {{ $c->applicant->nama_lengkap ?? '-' }}
                    </td>

                    <td class="px-4 py-3">
                        {{ $c->subject }}
                    </td>

                    <td class="px-4 py-3">
                        {{ optional($c->created_at)->format('d M Y') }}
                    </td>

                    <td class="px-4 py-3">
                        @php $status = strtolower((string) $c->status); @endphp

                        @if(in_array($status, ['pending','belum diproses']))
                            <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs">Menunggu Diproses</span>
                        @elseif(in_array($status, ['diproses','in_progress','on_progress','sedang diproses']))
                            <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs">Sedang Diproses</span>
                        @elseif(in_array($status, ['selesai','completed']))
                            <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">Selesai</span>
                        @elseif(in_array($status, ['ditolak','rejected']))
                            <span class="bg-red-100 text-red-800 px-2 py-1 rounded text-xs">Ditolak</span>
                        @else
                            <span class="bg-gray-100 text-gray-800 px-2 py-1 rounded text-xs">{{ ucfirst($c->status) }}</span>
                        @endif
                    </td>

                    <td class="px-4 py-3 text-center">
                        <a href="{{ route('user.complaints.show', $c->id) }}"
                           class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-gray-300 bg-white hover:bg-gray-50 transition"
                           title="Detail">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center px-4 py-6 text-gray-500">
                        Belum ada pengaduan.
                    </td>
                </tr>
            @endforelse

            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $complaints->links() }}
    </div>

</div>

<style>
.animate-fade-in { animation: fadeIn 0.4s ease-in-out; }
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>
@endsection