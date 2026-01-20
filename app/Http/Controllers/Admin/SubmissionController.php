<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Submission;
use App\Models\Category;
use Illuminate\Http\Request;

class SubmissionController extends Controller
{
    public function index(Request $request)
    {
        // 1. Statistik Kartu Atas (Hitung Real-time)
        $stats = [
            'total' => Submission::count(),
            'proses' => Submission::whereIn('status', ['pending', 'in_progress'])->count(),
            'selesai' => Submission::where('status', 'completed')->count(),
            'belum' => Submission::where('status', 'pending')->count(),
        ];

        // 2. Query untuk Tabel dengan Filter
        $query = Submission::with(['user', 'category']);

        // Filter Tanggal
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
        }

        // Filter Kategori
        if ($request->filled('category') && $request->category != 'Semua') {
            $query->where('category_id', $request->category);
        }

        // Filter Status
        if ($request->filled('status') && $request->status != 'Semua') {
            $query->where('status', $request->status);
        }

        // Filter Search (Nama/Tiket) - Opsional jika ingin search bar berfungsi
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('ticket_id', 'like', '%'.$request->search.'%')
                  ->orWhereHas('user', function($u) use ($request) {
                      $u->where('name', 'like', '%'.$request->search.'%');
                  });
            });
        }

        $submissions = $query->latest()->paginate(10);
        $categories = Category::where('type', 'permohonan')->get();

        return view('admin.submissions.permohonan', compact('submissions', 'categories', 'stats'));
    }
}