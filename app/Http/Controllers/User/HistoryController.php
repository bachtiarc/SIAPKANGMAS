<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Submission;
use App\Models\Consultation;
use App\Models\Complaint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HistoryController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Get filter parameters
        $search = $request->get('search');
        $categoryFilter = $request->get('category');
        $statusFilter = $request->get('status');
        
        // Initialize collections
        $allSubmissions = collect();
        
        // === PERMOHONAN INFORMASI ===
        if (!$categoryFilter || $categoryFilter === 'submission') {
            $submissions = Submission::where('user_id', $user->id)
                ->with('category')
                ->when($search, function($query) use ($search) {
                    $query->where(function($q) use ($search) {
                        $q->where('ticket_id', 'like', "%{$search}%")
                          ->orWhere('ticket_number', 'like', "%{$search}%")
                          ->orWhere('title', 'like', "%{$search}%")
                          ->orWhere('information_title', 'like', "%{$search}%");
                    });
                })
                ->when($statusFilter, function($query) use ($statusFilter) {
                    $status = strtolower($statusFilter);
                    $query->where(function($q) use ($status) {
                        // MENUNGGU PROSES
                        if ($status === 'pending') {
                            $q->whereIn('status', ['pending', 'belum diproses']);
                        } 
                        // DIPROSES
                        elseif ($status === 'diproses') {
                            $q->whereIn('status', ['in_progress', 'on_progress', 'diproses', 'sedang diproses']);
                        } 
                        // SELESAI
                        elseif ($status === 'selesai') {
                            $q->whereIn('status', ['completed', 'selesai']);
                        } 
                        // DITOLAK
                        elseif ($status === 'ditolak') {
                            $q->whereIn('status', ['rejected', 'ditolak']);
                        }
                    });
                })
                ->get()
                ->map(function($item) {
                    return [
                        // PERBAIKAN: Gunakan ticket_id (bukan ticket_number)
                        'ticket_id' => $item->ticket_id ?? $item->ticket_number ?? '-',
                        'type' => 'submission',
                        'type_label' => 'Permohonan',
                        // PERBAIKAN: Gunakan information_title atau title
                        'category' => $item->category->name ?? '-',
                        'title' => $item->information_title ?? $item->title ?? '-',
                        'date' => $item->created_at,
                        'status' => strtolower($item->status),
                        // PERBAIKAN: Tambahkan parameter ?from=history
                        'route' => route('user.submissions.show', $item->id) . '?from=history'
                    ];
                });
            
            $allSubmissions = $allSubmissions->merge($submissions);
        }
        
        // === KONSULTASI ===
        if (!$categoryFilter || $categoryFilter === 'consultation') {
            $consultations = Consultation::where('user_id', $user->id)
                ->with('category')
                ->when($search, function($query) use ($search) {
                    $query->where(function($q) use ($search) {
                        $q->where('ticket_number', 'like', "%{$search}%")
                          ->orWhere('subject', 'like', "%{$search}%");
                    });
                })
                ->when($statusFilter, function($query) use ($statusFilter) {
                    $status = strtolower($statusFilter);
                    $query->where(function($q) use ($status) {
                        // MENUNGGU PROSES
                        if ($status === 'pending') {
                            $q->whereIn('status', ['pending', 'belum diproses']);
                        } 
                        // DIPROSES
                        elseif ($status === 'diproses') {
                            $q->whereIn('status', ['in_progress', 'on_progress', 'diproses', 'sedang diproses']);
                        } 
                        // SELESAI
                        elseif ($status === 'selesai') {
                            $q->whereIn('status', ['completed', 'selesai']);
                        } 
                        // DITOLAK
                        elseif ($status === 'ditolak') {
                            $q->whereIn('status', ['rejected', 'ditolak']);
                        }
                    });
                })
                ->get()
                ->map(function($item) {
                    return [
                        'ticket_id' => $item->ticket_number,
                        'type' => 'consultation',
                        'type_label' => 'Konsultasi',
                        'category' => $item->category->name ?? '-',
                        'title' => $item->subject,
                        'date' => $item->created_at,
                        'status' => strtolower($item->status),
                        // PERBAIKAN: Tambahkan parameter ?from=history
                        'route' => route('user.consultations.show', $item->id) . '?from=history'
                    ];
                });
            
            $allSubmissions = $allSubmissions->merge($consultations);
        }
        
        // === PENGADUAN ===
        if (!$categoryFilter || $categoryFilter === 'complaint') {
            $complaints = Complaint::where('user_id', $user->id)
                ->with('category')
                ->when($search, function($query) use ($search) {
                    $query->where(function($q) use ($search) {
                        $q->where('ticket_number', 'like', "%{$search}%")
                          ->orWhere('subject', 'like', "%{$search}%")
                          ->orWhere('complaint_title', 'like', "%{$search}%");
                    });
                })
                ->when($statusFilter, function($query) use ($statusFilter) {
                    $status = strtolower($statusFilter);
                    $query->where(function($q) use ($status) {
                        // MENUNGGU PROSES
                        if ($status === 'pending') {
                            $q->whereIn('status', ['pending', 'belum diproses']);
                        } 
                        // DIPROSES
                        elseif ($status === 'diproses') {
                            $q->whereIn('status', ['in_progress', 'on_progress', 'diproses', 'sedang diproses']);
                        } 
                        // SELESAI
                        elseif ($status === 'selesai') {
                            $q->whereIn('status', ['completed', 'selesai']);
                        } 
                        // DITOLAK
                        elseif ($status === 'ditolak') {
                            $q->whereIn('status', ['rejected', 'ditolak']);
                        }
                    });
                })
                ->get()
                ->map(function($item) {
                    return [
                        'ticket_id' => $item->ticket_number,
                        'type' => 'complaint',
                        'type_label' => 'Pengaduan',
                        'category' => $item->category->name ?? '-',
                        // PERBAIKAN: Gunakan subject atau complaint_title
                        'title' => $item->subject ?? $item->complaint_title ?? '-',
                        'date' => $item->created_at,
                        'status' => strtolower($item->status),
                        // PERBAIKAN: Tambahkan parameter ?from=history
                        'route' => route('user.complaints.show', $item->id) . '?from=history'
                    ];
                });
            
            $allSubmissions = $allSubmissions->merge($complaints);
        }
        
        // Sort by date descending
        $allSubmissions = $allSubmissions->sortByDesc('date')->values();
        
        // Calculate statistics
        $totalSubmissions = $allSubmissions->count();
        
        // MENUNGGU PROSES
        $totalPending = $allSubmissions->filter(function($item) {
            return in_array($item['status'], ['pending', 'belum diproses']);
        })->count();
        
        // DIPROSES
        $totalProcessing = $allSubmissions->filter(function($item) {
            return in_array($item['status'], ['in_progress', 'on_progress', 'diproses', 'sedang diproses']);
        })->count();
        
        // SELESAI
        $totalCompleted = $allSubmissions->filter(function($item) {
            return in_array($item['status'], ['completed', 'selesai']);
        })->count();
        
        // DITOLAK
        $totalRejected = $allSubmissions->filter(function($item) {
            return in_array($item['status'], ['rejected', 'ditolak']);
        })->count();
        
        // Manual pagination
        $perPage = 15;
        $currentPage = $request->get('page', 1);
        $offset = ($currentPage - 1) * $perPage;
        
        $paginatedSubmissions = $allSubmissions->slice($offset, $perPage)->values();
        
        // Create paginator
        $submissions = new \Illuminate\Pagination\LengthAwarePaginator(
            $paginatedSubmissions,
            $totalSubmissions,
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );
        
        return view('user.history.index', compact(
            'submissions',
            'totalSubmissions',
            'totalPending',
            'totalProcessing',
            'totalCompleted',
            'totalRejected'
        ));
    }
}