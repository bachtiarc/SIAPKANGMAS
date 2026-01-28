<?php

namespace App\Http\Controllers\Masyarakat;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display the masyarakat dashboard.
     */
    public function index()
    {
        $user = auth()->user();

        // Check if user is masyarakat_umum
        if ($user->user_type !== 'masyarakat_umum') {
            abort(403, 'Unauthorized access.');
        }

        // Get statistics (Logika tetap sama)
        $totalSubmissions = $this->getTotalSubmissions($user);
        $completedCount = $this->getCompletedSubmissions($user); // Nama diubah agar sinkron dengan blade
        $inProgressCount = $this->getPendingSubmissions($user);  // Nama disesuaikan untuk blade
        $rejectedCount = $this->getRejectedSubmissions($user);   // Nama diubah agar sinkron dengan blade

        // Get recent activities
        $recentActivities = $this->getRecentActivities($user);

        return view('masyarakat.dashboard', compact(
            'totalSubmissions',
            'completedCount',
            'inProgressCount',
            'rejectedCount',
            'recentActivities'
        ));
    }

    /**
     * Get total submissions for user
     */
    private function getTotalSubmissions($user)
    {
        $total = 0;
        
        if (class_exists('App\Models\Submission')) {
            $total += \App\Models\Submission::where('user_id', $user->id)->count();
        }
        
        if (class_exists('App\Models\Consultation')) {
            $total += \App\Models\Consultation::where('user_id', $user->id)->count();
        }
        
        if (class_exists('App\Models\Complaint')) {
            $total += \App\Models\Complaint::where('user_id', $user->id)->count();
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
            $total += \App\Models\Submission::where('user_id', $user->id)
                ->whereIn('status', ['completed', 'selesai', 'approved'])
                ->count();
        }
        
        if (class_exists('App\Models\Consultation')) {
            $total += \App\Models\Consultation::where('user_id', $user->id)
                ->whereIn('status', ['completed', 'selesai', 'approved'])
                ->count();
        }
        
        if (class_exists('App\Models\Complaint')) {
            $total += \App\Models\Complaint::where('user_id', $user->id)
                ->whereIn('status', ['completed', 'selesai', 'approved'])
                ->count();
        }
        
        return $total;
    }

    /**
     * Get pending submissions
     */
    private function getPendingSubmissions($user)
    {
        $total = 0;
        
        if (class_exists('App\Models\Submission')) {
            $total += \App\Models\Submission::where('user_id', $user->id)
                ->whereIn('status', ['pending', 'in_progress', 'sedang_diproses'])
                ->count();
        }
        
        if (class_exists('App\Models\Consultation')) {
            $total += \App\Models\Consultation::where('user_id', $user->id)
                ->whereIn('status', ['pending', 'in_progress', 'sedang_diproses'])
                ->count();
        }
        
        if (class_exists('App\Models\Complaint')) {
            $total += \App\Models\Complaint::where('user_id', $user->id)
                ->whereIn('status', ['pending', 'in_progress', 'sedang_diproses'])
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
            $total += \App\Models\Submission::where('user_id', $user->id)
                ->whereIn('status', ['rejected', 'ditolak']) // Menyesuaikan dengan status database
                ->count();
        }
        
        if (class_exists('App\Models\Consultation')) {
            $total += \App\Models\Consultation::where('user_id', $user->id)
                ->whereIn('status', ['rejected', 'ditolak'])
                ->count();
        }
        
        if (class_exists('App\Models\Complaint')) {
            $total += \App\Models\Complaint::where('user_id', $user->id)
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

        // Get from Submissions
        if (class_exists('App\Models\Submission')) {
            $submissions = \App\Models\Submission::where('user_id', $user->id)
                ->latest()
                ->take(5)
                ->get()
                ->map(function($item) {
                    return [
                        'id' => $item->ticket_id ?? 'N/A',
                        'title' => 'Permohonan Informasi',
                        'status' => $item->status,
                        'date' => $item->created_at->format('d M Y'),
                        'url' => route('masyarakat.submissions.show', ['submission' => $item->id, 'from' => 'dashboard']), 
                    ];
                });
            $activities = $activities->merge($submissions);
        }

        // Get from Consultations
        if (class_exists('App\Models\Consultation')) {
            $consultations = \App\Models\Consultation::where('user_id', $user->id)
                ->latest()
                ->take(5)
                ->get()
                ->map(function($item) {
                    return [
                        'id' => $item->ticket_number ?? 'N/A',
                        'title' => 'Konsultasi',
                        'status' => $item->status,
                        'date' => $item->created_at->format('d M Y'),
                        'url' => route('masyarakat.submissions.show', ['submission' => $item->id, 'from' => 'dashboard']), 
                    ];
                });
            $activities = $activities->merge($consultations);
        }

        // Get from Complaints
        if (class_exists('App\Models\Complaint')) {
            $complaints = \App\Models\Complaint::where('user_id', $user->id)
                ->latest()
                ->take(5)
                ->get()
                ->map(function($item) {
                    return [
                        'id' => $item->ticket_number ?? 'N/A',
                        'title' => 'Pengaduan',
                        'status' => $item->status,
                        'date' => $item->created_at->format('d M Y'),
                        'url' => route('masyarakat.submissions.show', ['submission' => $item->id, 'from' => 'dashboard']), 
                    ];
                });
            $activities = $activities->merge($complaints);
        }

        return $activities->sortByDesc('date')->take(5)->values();
    }
}