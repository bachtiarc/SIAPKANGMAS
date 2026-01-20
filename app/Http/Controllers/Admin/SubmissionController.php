<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Submission;
use App\Models\Category;
use App\Models\SubmissionDocument;
use App\Support\SupabasePath;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class SubmissionController extends Controller
{
    public function index(Request $request)
    {
        $stats = [
            'total' => Submission::count(),
            'proses' => Submission::whereIn('status', ['pending', 'in_progress', 'diproses'])->count(),
            'selesai' => Submission::whereIn('status', ['completed', 'selesai', 'approved'])->count(),
            'belum' => Submission::where('status', 'pending')->count(),
        ];

        $query = Submission::with(['user', 'category']);
        
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        if ($request->filled('type') && $request->type != 'Semua') {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('user_type', $request->type);
            });
        }

        if ($request->filled('category') && $request->category != 'Semua') {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('status') && $request->status != 'Semua') {
            $query->where('status', $request->status);
        }

        $submissions = $query->latest()->paginate(10)->withQueryString();
        $categories = Category::where('type', 'permohonan')->get();

        return view('admin.submissions.permohonan', compact('submissions', 'categories', 'stats'));
    }

    public function show($id)
    {
        $submission = Submission::with(['user', 'category', 'documents', 'statusHistories.changedBy'])
            ->findOrFail($id);

        return view('admin.submissions.show', compact('submission'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,in_progress,completed,rejected',
            'admin_notes' => 'nullable|string',
            'notify_user' => 'nullable'
        ]);

        $submission = Submission::findOrFail($id);
        $oldStatus = $submission->status;

        $submission->update([
            'status' => $request->status,
            'admin_notes' => $request->admin_notes,
            'handled_by' => Auth::id(),
            'completed_at' => $request->status == 'completed' ? now() : null
        ]);

        if ($oldStatus !== $request->status) {
            $submission->statusHistories()->create([
                'changed_by' => Auth::id(),
                'new_status' => $request->status,
                'old_status' => $oldStatus,
                'notes'      => $request->admin_notes ?? 'Status diperbarui oleh Admin'
            ]);
        }

        if ($request->has('notify_user') && $oldStatus !== $request->status) {
            try {
                Mail::to($submission->user->email)->send(
                    new \App\Mail\SubmissionStatusUpdated($submission, $request->admin_notes)
                );
            } catch (\Exception $e) {
                Log::error('Email gagal dikirim: ' . $e->getMessage());
            }
        }

        return redirect()->back()->with('success', 'Status pengajuan berhasil diperbarui.');
    }

    public function downloadDocument($id)
    {
        $doc = SubmissionDocument::findOrFail($id);

        $supabaseUrl = rtrim(env('SUPABASE_URL'), '/');
        $bucket = env('SUPABASE_BUCKET', 'submissions');

        // Ambil path asli dari DB
        $rawPath = ltrim($doc->file_path, '/');

        // Normal path (tanpa prefix submissions/)
        $path = SupabasePath::normalize($rawPath);

        // URL normal
        $urlNormal = "{$supabaseUrl}/storage/v1/object/public/{$bucket}/{$path}";

        // URL legacy (kalau file memang terlanjur disimpan di folder "submissions/" di dalam bucket)
        $urlLegacy = "{$supabaseUrl}/storage/v1/object/public/{$bucket}/submissions/{$path}";

        // Cek dulu object ada di mana (HEAD lebih ringan dari GET)
        $existsNormal = Http::head($urlNormal)->successful();
        $finalUrl = $existsNormal ? $urlNormal : $urlLegacy;

        return redirect()->away($finalUrl . '?download=' . urlencode($doc->original_name));
    }
}