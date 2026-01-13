@extends('layouts.admin')

@section('title', 'Dashboard Admin')

@section('content')
<!-- Welcome Banner -->
<div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-2xl shadow-lg p-8 mb-8 text-white">
    <h2 class="font-montserrat text-3xl font-bold mb-2">Selamat Datang, Admin!</h2>
    <p class="font-lato text-blue-100 mb-6">Berikut adalah ringkasan aktivitas helpdesk hari ini.</p>
    
    <div class="flex gap-4">
        <a href="#" class="font-montserrat px-6 py-3 bg-white text-blue-600 font-semibold rounded-lg hover:bg-blue-50 transition">
            Lihat Pengajuan
        </a>
        <a href="#" class="font-montserrat px-6 py-3 bg-blue-500 text-white font-semibold rounded-lg hover:bg-blue-400 transition">
            Lihat Laporan
        </a>
    </div>
</div>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <!-- Total Tiket Masuk -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-start justify-between">
            <div>
                <p class="font-lato text-sm text-gray-600 mb-2">Total Tiket Masuk</p>
                <p class="font-montserrat text-4xl font-bold text-gray-900">{{ $totalTickets }}</p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Sedang Diproses -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-start justify-between">
            <div>
                <p class="font-lato text-sm text-gray-600 mb-2">Sedang Diproses</p>
                <p class="font-montserrat text-4xl font-bold text-gray-900">{{ $inProgressTickets }}</p>
            </div>
            <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Selesai -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-start justify-between">
            <div>
                <p class="font-lato text-sm text-gray-600 mb-2">Selesai</p>
                <p class="font-montserrat text-4xl font-bold text-gray-900">{{ $completedTickets }}</p>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Belum Diproses -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-start justify-between">
            <div>
                <p class="font-lato text-sm text-gray-600 mb-2">Belum Diproses</p>
                <p class="font-montserrat text-4xl font-bold text-gray-900">{{ $pendingTickets }}</p>
            </div>
            <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Chart & Recent Tickets -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    
    <!-- Statistik Bulanan (Chart) -->
    <div class="lg:col-span-2 bg-white rounded-xl shadow-sm p-6">
        <h3 class="font-montserrat text-xl font-bold text-blue-600 mb-2">Statistik Bulanan</h3>
        <p class="font-lato text-sm text-gray-600 mb-6">Overview performa penanganan tiket</p>
        
        <div class="h-80">
            <canvas id="monthlyChart"></canvas>
        </div>
    </div>

    <!-- Daftar Tiket Baru -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="font-montserrat text-xl font-bold text-blue-600 mb-2">Daftar Tiket Baru</h3>
        <p class="font-lato text-sm text-gray-600 mb-6">Tiket terbaru yang masuk</p>
        
        <div class="space-y-4">
            @forelse($recentTickets as $index => $ticket)
            <div class="flex items-start space-x-3 p-3 bg-gray-50 rounded-lg">
                <div class="flex-shrink-0 w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center font-bold text-sm">
                    {{ $index + 1 }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-montserrat text-sm font-semibold text-gray-900 truncate">
                        Nama : {{ $ticket['name'] }}
                    </p>
                    <p class="font-lato text-xs text-gray-600">
                        No. Tiket : {{ $ticket['ticket_id'] }}
                    </p>
                </div>
            </div>
            @empty
            <div class="text-center py-8">
                <svg class="w-16 h-16 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <p class="font-lato text-gray-500">Belum ada tiket baru</p>
            </div>
            @endforelse
        </div>

        @if(count($recentTickets) > 0)
        <div class="mt-6">
            <a href="#" class="font-montserrat block text-center text-blue-600 hover:text-blue-700 font-medium text-sm">
                Lihat Semua →
            </a>
        </div>
        @endif
    </div>

</div>
@endsection

@push('scripts')
<script>
// Chart.js Configuration with Dummy Data
const ctx = document.getElementById('monthlyChart');

new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'],
        datasets: [
            {
                label: 'Selesai',
                data: [120, 150, 180, 140, 200, 170, 190, 210, 180, 220, 240, 200],
                backgroundColor: 'rgba(34, 197, 94, 0.8)',
                borderColor: 'rgb(34, 197, 94)',
                borderWidth: 1
            },
            {
                label: 'Diproses',
                data: [30, 40, 35, 45, 50, 40, 48, 52, 45, 55, 60, 50],
                backgroundColor: 'rgba(234, 179, 8, 0.8)',
                borderColor: 'rgb(234, 179, 8)',
                borderWidth: 1
            },
            {
                label: 'Pending',
                data: [10, 15, 12, 18, 20, 15, 17, 20, 18, 22, 25, 20],
                backgroundColor: 'rgba(239, 68, 68, 0.8)',
                borderColor: 'rgb(239, 68, 68)',
                borderWidth: 1
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    font: {
                        family: 'Montserrat',
                        size: 12
                    },
                    padding: 15,
                    usePointStyle: true
                }
            },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                padding: 12,
                titleFont: {
                    family: 'Montserrat',
                    size: 13,
                    weight: 'bold'
                },
                bodyFont: {
                    family: 'Lato',
                    size: 12
                },
                cornerRadius: 8
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: {
                    color: 'rgba(0, 0, 0, 0.05)'
                },
                ticks: {
                    font: {
                        family: 'Lato',
                        size: 11
                    }
                }
            },
            x: {
                grid: {
                    display: false
                },
                ticks: {
                    font: {
                        family: 'Lato',
                        size: 11
                    }
                }
            }
        }
    }
});
</script>
@endpush