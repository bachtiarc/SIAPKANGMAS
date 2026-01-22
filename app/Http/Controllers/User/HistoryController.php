<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Submission;
use App\Models\Consultation;
use App\Models\Complaint;
use Illuminate\Support\Facades\Auth;

class HistoryController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Ambil semua submission user
        $submissions = Submission::where('user_id', $user->id)
            ->get()
            ->map(function($item) {
                return [
                    'id' => $item->id,
                    'ticket_id' => $item->ticket_number,
                    'type' => 'submission',
                    'type_label' => 'Permohonan Informasi',
                    'category' => $item->category->name ?? '-',
                    'title' => $item->information_title,
                    'date' => $item->created_at,
                    'status' => $item->status,
                    'route' => route('user.submissions.show', $item->id)
                ];
            });

        // Ambil semua consultation user
        $consultations = Consultation::where('user_id', $user->id)
            ->get()
            ->map(function($item) {
                return [
                    'id' => $item->id,
                    'ticket_id' => $item->ticket_number,
                    'type' => 'consultation',
                    'type_label' => 'Konsultasi',
                    'category' => $item->category->name ?? '-',
                    'title' => $item->subject,
                    'date' => $item->created_at,
                    'status' => $item->status,
                    'route' => route('user.consultations.show', $item->id)
                ];
            });

        // Ambil semua complaint user
        $complaints = Complaint::where('user_id', $user->id)
            ->get()
            ->map(function($item) {
                return [
                    'id' => $item->id,
                    'ticket_id' => $item->ticket_number,
                    'type' => 'complaint',
                    'type_label' => 'Pengaduan',
                    'category' => $item->category->name ?? '-',
                    'title' => $item->complaint_title,
                    'date' => $item->created_at,
                    'status' => $item->status,
                    'route' => route('user.complaints.show', $item->id)
                ];
            });

        // Gabungkan semua data
        $allSubmissions = collect()
            ->merge($submissions)
            ->merge($consultations)
            ->merge($complaints)
            ->sortByDesc('date'); // Sort by date descending

        // Filter berdasarkan search
        if ($request->has('search') && $request->search != '') {
            $search = strtolower($request->search);
            $allSubmissions = $allSubmissions->filter(function($item) use ($search) {
                return str_contains(strtolower($item['ticket_id']), $search) ||
                       str_contains(strtolower($item['title']), $search) ||
                       str_contains(strtolower($item['type_label']), $search);
            });
        }

        // Filter berdasarkan kategori
        if ($request->has('category') && $request->category != '' && $request->category != 'semua') {
            $allSubmissions = $allSubmissions->filter(function($item) use ($request) {
                return $item['type'] === $request->category;
            });
        }

        // Filter berdasarkan status
        if ($request->has('status') && $request->status != '' && $request->status != 'semua') {
            $statusFilter = strtolower($request->status);
            $allSubmissions = $allSubmissions->filter(function($item) use ($statusFilter) {
                $itemStatus = strtolower($item['status']);
                
                // Grouping status untuk filter
                if (in_array($statusFilter, ['pending', 'diproses', 'on_progress'])) {
                    return in_array($itemStatus, ['pending', 'diproses', 'on_progress', 'in_progress']);
                } elseif (in_array($statusFilter, ['completed', 'selesai'])) {
                    return in_array($itemStatus, ['completed', 'selesai']);
                } elseif (in_array($statusFilter, ['rejected', 'ditolak'])) {
                    return in_array($itemStatus, ['rejected', 'ditolak']);
                }
                
                return $itemStatus === $statusFilter;
            });
        }

        // Convert to array untuk pagination manual
        $allSubmissions = $allSubmissions->values();
        
        // Manual pagination
        $perPage = 15;
        $currentPage = $request->get('page', 1);
        $total = $allSubmissions->count();
        
        $paginatedSubmissions = $allSubmissions->slice(($currentPage - 1) * $perPage, $perPage)->values();
        
        $pagination = new \Illuminate\Pagination\LengthAwarePaginator(
            $paginatedSubmissions,
            $total,
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('user.history.index', [
            'submissions' => $pagination,
            'totalSubmissions' => $total,
            'totalPending' => $allSubmissions->filter(function($item) {
                return in_array(strtolower($item['status']), ['pending', 'diproses', 'on_progress', 'in_progress']);
            })->count(),
            'totalCompleted' => $allSubmissions->filter(function($item) {
                return in_array(strtolower($item['status']), ['completed', 'selesai']);
            })->count(),
            'totalRejected' => $allSubmissions->filter(function($item) {
                return in_array(strtolower($item['status']), ['rejected', 'ditolak']);
            })->count(),
        ]);
    }
}