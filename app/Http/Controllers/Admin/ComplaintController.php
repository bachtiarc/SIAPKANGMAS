<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\Category;
use App\Models\ComplaintDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;

class ComplaintController extends Controller
{
    public function index(Request $request)
    {
        $stats = [
            'total'   => Complaint::count(),
            'proses'  => Complaint::where('status', 'diproses')->count(),
            'selesai' => Complaint::where('status', 'selesai')->count(),
            'belum'   => Complaint::where('status', 'pending')->count(),
        ];

        $query = Complaint::with(['user', 'category']);

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        if ($request->filled('type') && $request->type !== 'Semua') {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('user_type', $request->type);
            });
        }

        if ($request->filled('category') && $request->category !== 'Semua') {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('status') && $request->status !== 'Semua') {
            $query->where('status', $request->status);
        }

        $complaints = $query->latest()->paginate(10)->withQueryString();
        $categories = Category::where('type', 'pengaduan')->get();

        return view('admin.complaints.pengaduan', compact('complaints', 'categories', 'stats'));
    }

    public function show($id)
    {
        $complaint = Complaint::with([
            'user',
            'category',
            'documents',
            'statusHistories.changedBy',
        ])->findOrFail($id);

        return view('admin.complaints.show', compact('complaint'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'status'      => 'required|in:pending,diproses,selesai',
            'admin_notes' => 'nullable|string',
            'notify_user' => 'nullable',
        ]);

        $complaint = Complaint::with('user')->findOrFail($id);
        $oldStatus = $complaint->status;

        $complaint->update([
            'status'         => $request->status,
            'admin_response' => $request->admin_notes, // field complaint
            'handled_by'     => Auth::id(),
            'completed_at'   => $request->status === 'selesai' ? now() : null,
        ]);

        // Simpan status history SETIAP perubahan status
        if ($oldStatus !== $request->status) {
            try {
                $complaint->statusHistories()->create([
                    'changed_by' => Auth::id(),
                    'new_status' => $request->status,
                    'old_status' => $oldStatus,
                    'notes'      => $request->admin_notes ?? 'Status diperbarui oleh Admin',
                ]);
            } catch (\Exception $e) {
                Log::error('Gagal simpan status history pengaduan: ' . $e->getMessage());
            }
        }

        // Kirim email jika dicentang & status berubah
        if ($request->has('notify_user') && $oldStatus !== $request->status) {
            try {
                Mail::to($complaint->user->email)->send(
                    new \App\Mail\ComplaintStatusUpdated($complaint, $request->admin_notes)
                );
            } catch (\Exception $e) {
                Log::error('Email pengaduan gagal dikirim: ' . $e->getMessage());
            }
        }

        return redirect()->back()->with('success', 'Status pengaduan berhasil diperbarui.');
    }

    public function downloadDocument($id)
    {
        $doc = ComplaintDocument::findOrFail($id);

        $filePath = storage_path('app/public/' . ltrim($doc->file_path, '/'));
        if (!file_exists($filePath)) {
            abort(404, 'File tidak ditemukan.');
        }

        return response()->download($filePath, $doc->original_name);
    }

    public function downloadPdf($id)
    {
        $complaint = Complaint::with([
            'user',
            'category',
            'documents',
            'statusHistories.changedBy',
        ])->findOrFail($id);

        $pdf = Pdf::loadView('admin.complaints.pdf', compact('complaint'))
            ->setPaper('A4', 'portrait');

        $filename = 'Pengaduan-' . ($complaint->ticket_number ?? $complaint->id) . '.pdf';
        return $pdf->download($filename);
    }
}