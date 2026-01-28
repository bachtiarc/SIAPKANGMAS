<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Submission;
use App\Models\Consultation;
use App\Models\Complaint;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Hitung total submissions
        $totalSubmissions = Submission::where('user_id', $user->id)->count() +
                           Consultation::where('user_id', $user->id)->count() +
                           Complaint::where('user_id', $user->id)->count();
        
        // Hitung yang sedang diproses (pending, in_progress, on_progress, diproses)
        $inProgressCount = Submission::where('user_id', $user->id)
                                    ->where(function($query) {
                                        $query->where('status', 'LIKE', 'pending')
                                              ->orWhere('status', 'LIKE', 'in_progress')
                                              ->orWhere('status', 'LIKE', 'diproses')
                                              ->orWhere('status', 'LIKE', 'on_progress')
                                              ->orWhere('status', 'LIKE', 'Pending')
                                              ->orWhere('status', 'LIKE', 'In_Progress')
                                              ->orWhere('status', 'LIKE', 'Diproses')
                                              ->orWhere('status', 'LIKE', 'On_Progress');
                                    })
                                    ->count() +
                          Consultation::where('user_id', $user->id)
                                     ->where(function($query) {
                                         $query->where('status', 'LIKE', 'pending')
                                               ->orWhere('status', 'LIKE', 'in_progress')
                                               ->orWhere('status', 'LIKE', 'diproses')
                                               ->orWhere('status', 'LIKE', 'on_progress')
                                               ->orWhere('status', 'LIKE', 'Pending')
                                               ->orWhere('status', 'LIKE', 'In_Progress')
                                               ->orWhere('status', 'LIKE', 'Diproses')
                                               ->orWhere('status', 'LIKE', 'On_Progress');
                                     })
                                     ->count() +
                          Complaint::where('user_id', $user->id)
                                   ->where(function($query) {
                                       $query->where('status', 'LIKE', 'pending')
                                             ->orWhere('status', 'LIKE', 'in_progress')
                                             ->orWhere('status', 'LIKE', 'diproses')
                                             ->orWhere('status', 'LIKE', 'on_progress')
                                             ->orWhere('status', 'LIKE', 'Pending')
                                             ->orWhere('status', 'LIKE', 'In_Progress')
                                             ->orWhere('status', 'LIKE', 'Diproses')
                                             ->orWhere('status', 'LIKE', 'On_Progress');
                                   })
                                   ->count();
        
        // Hitung yang selesai
        $completedCount = Submission::where('user_id', $user->id)
                                   ->where(function($query) {
                                       $query->where('status', 'LIKE', 'completed')
                                             ->orWhere('status', 'LIKE', 'selesai')
                                             ->orWhere('status', 'LIKE', 'Completed')
                                             ->orWhere('status', 'LIKE', 'Selesai');
                                   })
                                   ->count() +
                         Consultation::where('user_id', $user->id)
                                    ->where(function($query) {
                                        $query->where('status', 'LIKE', 'completed')
                                              ->orWhere('status', 'LIKE', 'selesai')
                                              ->orWhere('status', 'LIKE', 'Completed')
                                              ->orWhere('status', 'LIKE', 'Selesai');
                                    })
                                    ->count() +
                         Complaint::where('user_id', $user->id)
                                  ->where(function($query) {
                                      $query->where('status', 'LIKE', 'completed')
                                            ->orWhere('status', 'LIKE', 'selesai')
                                            ->orWhere('status', 'LIKE', 'Completed')
                                            ->orWhere('status', 'LIKE', 'Selesai');
                                  })
                                  ->count();
        
        // Hitung yang ditolak
        $rejectedCount = Submission::where('user_id', $user->id)
                                  ->where(function($query) {
                                      $query->where('status', 'LIKE', 'rejected')
                                            ->orWhere('status', 'LIKE', 'ditolak')
                                            ->orWhere('status', 'LIKE', 'Rejected')
                                            ->orWhere('status', 'LIKE', 'Ditolak');
                                  })
                                  ->count() +
                        Consultation::where('user_id', $user->id)
                                   ->where(function($query) {
                                       $query->where('status', 'LIKE', 'rejected')
                                             ->orWhere('status', 'LIKE', 'ditolak')
                                             ->orWhere('status', 'LIKE', 'Rejected')
                                             ->orWhere('status', 'LIKE', 'Ditolak');
                                   })
                                   ->count() +
                        Complaint::where('user_id', $user->id)
                                 ->where(function($query) {
                                     $query->where('status', 'LIKE', 'rejected')
                                           ->orWhere('status', 'LIKE', 'ditolak')
                                           ->orWhere('status', 'LIKE', 'Rejected')
                                           ->orWhere('status', 'LIKE', 'Ditolak');
                                 })
                                 ->count();
        
        // Ambil aktivitas terkini (10 terbaru)
        $submissions = Submission::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get()
            ->map(function($item) {
                return [
                    'ticket_id' => $item->ticket_number ?? $item->ticket_id,
                    'type' => 'submission',
                    'title' => $item->information_title ?? $item->title,
                    'created_at' => $item->created_at,
                    'status' => strtolower($item->status),
                    'route' => route('user.submissions.show', $item->id)
                ];
            });

        $consultations = Consultation::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get()
            ->map(function($item) {
                return [
                    'ticket_id' => $item->ticket_number,
                    'type' => 'consultation',
                    'title' => $item->subject,
                    'created_at' => $item->created_at,
                    'status' => strtolower($item->status),
                    'route' => route('user.consultations.show', $item->id)
                ];
            });

        $complaints = Complaint::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get()
            ->map(function($item) {
                return [
                    'ticket_id' => $item->ticket_number,
                    'type' => 'complaint',
                    'title' => $item->subject ?? $item->complaint_title,
                    'created_at' => $item->created_at,
                    'status' => strtolower($item->status),
                    'route' => route('user.complaints.show', $item->id)
                ];
            });

        // Gabungkan dan sort by date
        $recentActivities = collect()
            ->concat(collect($submissions))
            ->concat(collect($consultations))
            ->concat(collect($complaints))
            ->sortByDesc(fn ($item) => data_get($item, 'created_at'))
            ->take(10)
            ->values();

        return view('user.dashboard', compact(
            'totalSubmissions',
            'inProgressCount',
            'completedCount',
            'rejectedCount',
            'recentActivities'
        ));
    }
}