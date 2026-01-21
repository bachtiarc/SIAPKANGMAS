<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Consultation;
use App\Models\Category;
use App\Models\ConsultationDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;

class ConsultationController extends Controller
{
    public function index(Request $request)
    {
        // Statistik
        $stats = [
            'total' => Consultation::count(),
            'proses' => Consultation::whereIn('status', ['pending', 'diproses'])->count(),
            'selesai' => Consultation::whereIn('status', ['completed', 'selesai'])->count(),
            'belum' => Consultation::where('status', 'pending')->count(),
        ];

        // Query konsultasi
        $query = Consultation::with(['user', 'category']);
        
        // Filter tanggal mulai
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        // Filter tanggal akhir
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // Filter tipe user
        if ($request->filled('type') && $request->type != 'Semua') {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('user_type', $request->type);
            });
        }

        // Filter kategori
        if ($request->filled('category') && $request->category != 'Semua') {
            $query->where('category_id', $request->category);
        }

        // Filter status
        if ($request->filled('status') && $request->status != 'Semua') {
            $query->where('status', $request->status);
        }

        $consultations = $query->latest()->paginate(10)->withQueryString();
        $categories = Category::where('type', 'konsultasi')->get();

        return view('admin.consultations.konsultasi', compact('consultations', 'categories', 'stats'));
    }

    public function show($id)
    {
        // Ambil data konsultasi dengan relasi yang diperlukan
        // Asumsi: Model Consultation memiliki relasi yang sama dengan Submission
        $consultation = Consultation::with(['user', 'category', 'documents', 'statusHistories.changedBy'])
            ->findOrFail($id);

        return view('admin.consultations.show', compact('consultation'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,in_progress,completed,rejected',
            'admin_notes' => 'nullable|string',
            'notify_user' => 'nullable'
        ]);

        $consultation = Consultation::findOrFail($id);
        $oldStatus = $consultation->status;

        // Update Consultation
        $consultation->update([
            'status' => $request->status,
            'admin_notes' => $request->admin_notes,
            'handled_by' => Auth::id(),
            'completed_at' => $request->status == 'completed' ? now() : null
        ]);

        // Simpan Riwayat Status (Menggunakan nama kolom yang sudah diperbaiki)
        if ($oldStatus !== $request->status) {
            $consultation->statusHistories()->create([
                'changed_by' => Auth::id(),
                'new_status' => $request->status,
                'old_status' => $oldStatus,
                'notes'      => $request->admin_notes ?? 'Status diperbarui oleh Admin'
            ]);
        }

        // Kirim Email Notifikasi
        if ($request->has('notify_user') && $oldStatus !== $request->status) {
            try {
                Mail::to($consultation->user->email)->send(
                    new ConsultationStatusUpdated($consultation, $request->admin_notes)
                );
            } catch (\Exception $e) {
                Log::error('Email konsultasi gagal dikirim: ' . $e->getMessage());
            }
        }

        return redirect()->back()->with('success', 'Status konsultasi berhasil diperbarui.');
    }

    public function downloadDocument($id)
    {
        $document = ConsultationDocument::findOrFail($id);

        // Gunakan storage_path untuk path absolut agar tidak 404
        $filePath = storage_path('app/public/' . $document->file_path);

        if (!file_exists($filePath)) {
            abort(404, 'File fisik tidak ditemukan di server.');
        }

        return response()->download($filePath, $document->original_name);
    }
}