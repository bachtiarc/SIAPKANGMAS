<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Submission;
use App\Models\Consultation;
use App\Models\Complaint;
use App\Models\User;

class DashboardController extends Controller
{
    /**
     * Display admin dashboard.
     */
    public function index()
    {
        // Calculate statistics
        // $totalTickets = $this->getTotalTickets();
        // $inProgressTickets = $this->getInProgressTickets();
        // $completedTickets = $this->getCompletedTickets();
        // $pendingTickets = $this->getPendingTickets();
        $totalTickets = 1240; // Fixed dummy number
        $inProgressTickets = 45;
        $completedTickets = 1150;
        $pendingTickets = 12;

        $recentTickets = [
            ['name' => 'Budi Santoso', 'ticket_id' => 'PI0001_12012026'],
            ['name' => 'Siti Nurhaliza', 'ticket_id' => 'KL0002_12012026'],
            ['name' => 'Ahmad Fauzi', 'ticket_id' => 'PD0003_12012026'],
        ];
        
        // Get recent tickets (last 3)
        // $recentTickets = $this->getRecentTickets();
        
        // Chart data (monthly statistics)
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
        
        if (class_exists('App\Models\Submission')) {
            $total += Submission::whereIn('status', ['in_progress', 'diproses'])->count();
        }
        
        if (class_exists('App\Models\Consultation')) {
            $total += Consultation::whereIn('status', ['in_progress', 'diproses'])->count();
        }
        
        if (class_exists('App\Models\Complaint')) {
            $total += Complaint::whereIn('status', ['in_progress', 'diproses'])->count();
        }
        
        return $total;
    }

    /**
     * Get completed tickets
     */
    private function getCompletedTickets()
    {
        $total = 0;
        
        if (class_exists('App\Models\Submission')) {
            $total += Submission::whereIn('status', ['completed', 'selesai', 'approved'])->count();
        }
        
        if (class_exists('App\Models\Consultation')) {
            $total += Consultation::whereIn('status', ['completed', 'selesai', 'approved'])->count();
        }
        
        if (class_exists('App\Models\Complaint')) {
            $total += Complaint::whereIn('status', ['completed', 'selesai', 'approved'])->count();
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
     * Get recent tickets (last 3)
     */
    private function getRecentTickets()
    {
        $tickets = collect();
        
        // Get submissions
        if (class_exists('App\Models\Submission')) {
            $submissions = Submission::with('user')
                ->orderBy('created_at', 'desc')
                ->take(3)
                ->get()
                ->map(function($item) {
                    return [
                        'name' => $item->user->name ?? 'Unknown',
                        'ticket_id' => $item->ticket_id ?? 'PI' . str_pad($item->id, 4, '0', STR_PAD_LEFT),
                        'type' => 'Permohonan Informasi',
                        'date' => $item->created_at
                    ];
                });
            $tickets = $tickets->merge($submissions);
        }
        
        // Get consultations
        if (class_exists('App\Models\Consultation')) {
            $consultations = Consultation::with('user')
                ->orderBy('created_at', 'desc')
                ->take(3)
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
                ->orderBy('created_at', 'desc')
                ->take(3)
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
        
        // Sort by date and take latest 3
        return $tickets->sortByDesc('date')->take(3)->values();
    }

    /**
     * Get monthly chart data
     */
    private function getMonthlyChartData()
    {
        // Default data for demonstration
        // In production, calculate from actual database records
        return [
            'completed' => [120, 150, 180, 140, 200, 170, 190, 210, 180, 220, 240, 200],
            'processing' => [30, 40, 35, 45, 50, 40, 48, 52, 45, 55, 60, 50],
            'pending' => [10, 15, 12, 18, 20, 15, 17, 20, 18, 22, 25, 20],
        ];
    }
}