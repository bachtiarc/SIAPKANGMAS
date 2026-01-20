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
        // 1. Statistik (Tetap sama)
        $stats = [
            'total' => Submission::count(),
            'proses' => Submission::whereIn('status', ['pending', 'in_progress', 'diproses'])->count(),
            'selesai' => Submission::whereIn('status', ['completed', 'selesai', 'approved'])->count(),
            'belum' => Submission::where('status', 'pending')->count(),
        ];

        // 2. Query Data
        $query = Submission::with(['user', 'category']);

        // --- PERBAIKAN LOGIKA FILTER TANGGAL DI SINI ---
        
        // Jika ada Tanggal Awal: Ambil data dari tanggal tersebut ke depan
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        // Jika ada Tanggal Akhir: Ambil data sampai tanggal tersebut
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }
        
        // Catatan: Jika Start & End diisi tanggal yang sama (misal 20-01-2026), 
        // logika di atas otomatis menjadi: created_at >= 20-01-2026 DAN created_at <= 20-01-2026
        // Hasilnya: Hanya data di tanggal 20-01-2026 yang muncul.

        // ------------------------------------------------

        // Filter Pelapor
        if ($request->filled('type') && $request->type != 'Semua') {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('user_type', $request->type);
            });
        }

        // Filter Kategori
        if ($request->filled('category') && $request->category != 'Semua') {
            $query->where('category_id', $request->category);
        }

        // Filter Status
        if ($request->filled('status') && $request->status != 'Semua') {
            $query->where('status', $request->status);
        }

        // Pagination dengan append query string agar filter tidak hilang saat ganti halaman
        $submissions = $query->latest()->paginate(10)->withQueryString();
        $categories = Category::where('type', 'permohonan')->get();

        return view('admin.submissions.permohonan', compact('submissions', 'categories', 'stats'));
    }
}