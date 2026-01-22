<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Submission;
use App\Models\Consultation;
use App\Models\Complaint;
use App\Models\User;
use Illuminate\Support\Facades\DB; // Tambahkan ini untuk query chart

class DashboardController extends Controller
{
    /**
     * Display admin dashboard.
     */
    public function index()
    {
        // Calculate statistics (Menggunakan data real dari database)
        $totalTickets = $this->getTotalTickets();
        $inProgressTickets = $this->getInProgressTickets();
        $completedTickets = $this->getCompletedTickets();
        $pendingTickets = $this->getPendingTickets();
        
        // Get recent tickets (last 5)
        $recentTickets = $this->getRecentTickets();
        
        // Chart data (monthly statistics real)
        $chartData = $this->getMonthlyChartData();

        return view('admin.dashboard', compact(
            'totalTickets',
            'inProgressTickets',
            'completedTickets',
            'pendingTickets',
            'recentTickets',
            'chartData'
        ));
    }

    /**
     * Get total tickets from all types
     */
    private function getTotalTickets()
    {
        $total = 0;
        
        if (class_exists('App\Models\Submission')) {
            $total += Submission::count();
        }
        
        if (class_exists('App\Models\Consultation')) {
            $total += Consultation::count();
        }
        
        if (class_exists('App\Models\Complaint')) {
            $total += Complaint::count();
        }
        
        return $total;
    }

    /**
     * Get in-progress tickets
     */
    private function getInProgressTickets()
    {
        $total = 0;
        $statuses = ['in_progress', 'diproses', 'on_progress'];
        
        if (class_exists('App\Models\Submission')) {
            $total += Submission::whereIn('status', $statuses)->count();
        }
        
        if (class_exists('App\Models\Consultation')) {
            $total += Consultation::whereIn('status', $statuses)->count();
        }
        
        if (class_exists('App\Models\Complaint')) {
            $total += Complaint::whereIn('status', $statuses)->count();
        }
        
        return $total;
    }

    /**
     * Get completed tickets
     */
    private function getCompletedTickets()
    {
        $total = 0;
        $statuses = ['completed', 'selesai', 'approved', 'rejected', 'ditolak'];
        
        if (class_exists('App\Models\Submission')) {
            $total += Submission::whereIn('status', $statuses)->count();
        }
        
        if (class_exists('App\Models\Consultation')) {
            $total += Consultation::whereIn('status', $statuses)->count();
        }
        
        if (class_exists('App\Models\Complaint')) {
            $total += Complaint::whereIn('status', $statuses)->count();
        }
        
        return $total;
    }

    /**
     * Get pending tickets
     */
    private function getPendingTickets()
    {
        $total = 0;
        
        if (class_exists('App\Models\Submission')) {
            $total += Submission::where('status', 'pending')->count();
        }
        
        if (class_exists('App\Models\Consultation')) {
            $total += Consultation::where('status', 'pending')->count();
        }
        
        if (class_exists('App\Models\Complaint')) {
            $total += Complaint::where('status', 'pending')->count();
        }
        
        return $total;
    }

    /**
     * Get recent tickets (last 5 mixed)
     */
    private function getRecentTickets()
    {
        $tickets = collect();
        
        // Get submissions
        if (class_exists('App\Models\Submission')) {
            $submissions = Submission::with('user')
                ->latest()
                ->take(5)
                ->get()
                ->map(function($item) {
                    return [
                        'name' => $item->user->name ?? 'Unknown',
                        'ticket_id' => $item->ticket_id,
                        'type' => 'Permohonan Informasi',
                        'date' => $item->created_at
                    ];
                });
            $tickets = $tickets->merge($submissions);
        }
        
        // Get consultations
        if (class_exists('App\Models\Consultation')) {
            $consultations = Consultation::with('user')
                ->latest()
                ->take(5)
                ->get()
                ->map(function($item) {
                    return [
                        'name' => $item->user->name ?? 'Unknown',
                        'ticket_id' => $item->ticket_id ?? 'KL' . str_pad($item->id, 4, '0', STR_PAD_LEFT),
                        'type' => 'Konsultasi',
                        'date' => $item->created_at
                    ];
                });
            $tickets = $tickets->merge($consultations);
        }
        
        // Get complaints
        if (class_exists('App\Models\Complaint')) {
            $complaints = Complaint::with('user')
                ->latest()
                ->take(5)
                ->get()
                ->map(function($item) {
                    return [
                        'name' => $item->user->name ?? 'Unknown',
                        'ticket_id' => $item->ticket_id ?? 'PD' . str_pad($item->id, 4, '0', STR_PAD_LEFT),
                        'type' => 'Pengaduan',
                        'date' => $item->created_at
                    ];
                });
            $tickets = $tickets->merge($complaints);
        }
        
        // Sort by date and take latest 5
        return $tickets->sortByDesc('date')->take(5)->values();
    }

/**
     * Get monthly chart data (PostgreSQL Compatible)
     */
    private function getMonthlyChartData()
    {
        // Inisialisasi array 0 untuk 12 bulan
        $data = [
            'completed' => array_fill(0, 12, 0),
            'processing' => array_fill(0, 12, 0),
            'pending' => array_fill(0, 12, 0),
        ];

        // Daftar Model yang akan dicek
        $models = [
            'App\Models\Submission', 
            'App\Models\Consultation', 
            'App\Models\Complaint'
        ];

        foreach ($models as $modelClass) {
            if (!class_exists($modelClass)) continue;

            // FIX: Gunakan EXTRACT(MONTH FROM ...) untuk PostgreSQL
            $query = $modelClass::selectRaw('EXTRACT(MONTH FROM created_at) as month, status, COUNT(*) as total')
                ->whereYear('created_at', date('Y'))
                ->groupBy('month', 'status')
                ->get();

            foreach ($query as $row) {
                // Index array dimulai dari 0 (Januari = 0)
                // Pastikan casting ke integer karena EXTRACT mengembalikan float/numeric
                $monthIndex = (int)$row->month - 1;
                
                // Masukkan ke kategori yang sesuai
                if (in_array($row->status, ['completed', 'selesai', 'approved'])) {
                    $data['completed'][$monthIndex] += $row->total;
                } elseif (in_array($row->status, ['in_progress', 'diproses'])) {
                    $data['processing'][$monthIndex] += $row->total;
                } elseif ($row->status == 'pending') {
                    $data['pending'][$monthIndex] += $row->total;
                }
            }
        }

        return $data;
    }
}