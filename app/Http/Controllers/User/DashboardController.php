<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Submission;
use App\Models\Consultation;
use App\Models\Complaint;

class DashboardController extends Controller
{
    /**
     * Display the user dashboard.
     */
    public function index()
    {
        $user = auth()->user();

        // Calculate statistics
        // Note: Adjust these queries based on your actual models and relationships
        
        // Total Submissions (all types)
        $totalSubmissions = $this->getTotalSubmissions($user);
        
        // Submissions by status
        $inProgressCount = $this->getInProgressSubmissions($user);
        $completedCount = $this->getCompletedSubmissions($user);
        $rejectedCount = $this->getRejectedSubmissions($user);
        
        // Recent activities (last 5)
        $recentActivities = $this->getRecentActivities($user);

        return view('user.dashboard', compact(
            'totalSubmissions',
            'inProgressCount',
            'completedCount',
            'rejectedCount',
            'recentActivities'
        ));
    }

    /**
     * Get total submissions for user (all types)
     */
    private function getTotalSubmissions($user)
    {
        $total = 0;
        
        // Count submissions if table exists
        if (class_exists('App\Models\Submission')) {
            $total += Submission::where('user_id', $user->id)->count();
        }
        
        // Count consultations if table exists
        if (class_exists('App\Models\Consultation')) {
            $total += Consultation::where('user_id', $user->id)->count();
        }
        
        // Count complaints if table exists
        if (class_exists('App\Models\Complaint')) {
            $total += Complaint::where('user_id', $user->id)->count();
        }
        
        return $total;
    }

    /**
     * Get in-progress submissions
     */
    private function getInProgressSubmissions($user)
    {
        $total = 0;
        
        if (class_exists('App\Models\Submission')) {
            $total += Submission::where('user_id', $user->id)
                ->whereIn('status', ['pending', 'in_progress', 'diproses'])
                ->count();
        }
        
        if (class_exists('App\Models\Consultation')) {
            $total += Consultation::where('user_id', $user->id)
                ->whereIn('status', ['pending', 'in_progress', 'diproses'])
                ->count();
        }
        
        if (class_exists('App\Models\Complaint')) {
            $total += Complaint::where('user_id', $user->id)
                ->whereIn('status', ['pending', 'in_progress', 'diproses'])
                ->count();
        }
        
        return $total;
    }

    /**
     * Get completed submissions
     */
    private function getCompletedSubmissions($user)
    {
        $total = 0;
        
        if (class_exists('App\Models\Submission')) {
            $total += Submission::where('user_id', $user->id)
                ->whereIn('status', ['completed', 'selesai', 'approved'])
                ->count();
        }
        
        if (class_exists('App\Models\Consultation')) {
            $total += Consultation::where('user_id', $user->id)
                ->whereIn('status', ['completed', 'selesai', 'approved'])
                ->count();
        }
        
        if (class_exists('App\Models\Complaint')) {
            $total += Complaint::where('user_id', $user->id)
                ->whereIn('status', ['completed', 'selesai', 'approved'])
                ->count();
        }
        
        return $total;
    }

    /**
     * Get rejected submissions
     */
    private function getRejectedSubmissions($user)
    {
        $total = 0;
        
        if (class_exists('App\Models\Submission')) {
            $total += Submission::where('user_id', $user->id)
                ->whereIn('status', ['rejected', 'ditolak'])
                ->count();
        }
        
        if (class_exists('App\Models\Consultation')) {
            $total += Consultation::where('user_id', $user->id)
                ->whereIn('status', ['rejected', 'ditolak'])
                ->count();
        }
        
        if (class_exists('App\Models\Complaint')) {
            $total += Complaint::where('user_id', $user->id)
                ->whereIn('status', ['rejected', 'ditolak'])
                ->count();
        }
        
        return $total;
    }

    /**
     * Get recent activities
     */
    private function getRecentActivities($user)
    {
        $activities = collect();
        
        // Get submissions
        if (class_exists('App\Models\Submission')) {
            $submissions = Submission::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get()
                ->map(function($item) {
                    return [
                        'ticket_id' => $item->ticket_id ?? 'PI' . str_pad($item->id, 2, '0', STR_PAD_LEFT) . '_' . date('dMY', strtotime($item->created_at)),
                        'type' => 'Permohonan Informasi',
                        'date' => $item->created_at->format('d M Y'),
                        'status' => $item->status,
                        'route' => '#' // TODO: Add route when submission detail page is created
                    ];
                });
            $activities = $activities->merge($submissions);
        }
        
        // Get consultations
        if (class_exists('App\Models\Consultation')) {
            $consultations = Consultation::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get()
                ->map(function($item) {
                    return [
                        'ticket_id' => $item->ticket_id ?? 'KL' . str_pad($item->id, 2, '0', STR_PAD_LEFT) . '_' . date('dMY', strtotime($item->created_at)),
                        'type' => 'Konsultasi',
                        'date' => $item->created_at->format('d M Y'),
                        'status' => $item->status,
                        'route' => '#'
                    ];
                });
            $activities = $activities->merge($consultations);
        }
        
        // Get complaints
        if (class_exists('App\Models\Complaint')) {
            $complaints = Complaint::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get()
                ->map(function($item) {
                    return [
                        'ticket_id' => $item->ticket_id ?? 'PD' . str_pad($item->id, 2, '0', STR_PAD_LEFT) . '_' . date('dMY', strtotime($item->created_at)),
                        'type' => 'Pengaduan',
                        'date' => $item->created_at->format('d M Y'),
                        'status' => $item->status,
                        'route' => '#'
                    ];
                });
            $activities = $activities->merge($complaints);
        }
        
        // Sort by date and take latest 5
        return $activities->sortByDesc('date')->take(5);
    }
}