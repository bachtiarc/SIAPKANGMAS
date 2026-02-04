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
use Barryvdh\DomPDF\Facade\Pdf;

class SubmissionController extends Controller
{
    public function index(Request $request)
    {
        $stats = [
            'total' => Submission::count(),
            'proses' => Submission::where('status', 'in_progress')->count(),
            'selesai' => Submission::whereIn('status', ['completed', 'rejected'])->count(),
            'belum' => Submission::where('status', 'pending')->count()
        ];

        $query = Submission::with(['user', 'category']);
        
        // ================= FILTER TANGGAL =================
        $hasStart = $request->filled('start_date');
        $hasEnd   = $request->filled('end_date');

        if ($hasStart && $hasEnd) {
            $query->whereBetween('created_at', [
                $request->start_date . ' 00:00:00',
                $request->end_date   . ' 23:59:59',
            ]);
        } elseif ($hasStart) {
            $query->whereDate('created_at', $request->start_date);
        } elseif ($hasEnd) {
            $query->whereDate('created_at', $request->end_date);
        }
        // ==================================================

        if ($request->filled('type') && $request->type != 'Semua') {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('user_type', $request->type);
            });
        }

        if ($request->filled('category') && $request->category != 'Semua') {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('status') && $request->status != 'Semua') {
            if ($request->status === 'completed') {
                $query->whereIn('status', ['completed', 'rejected']);
            } else {
                $query->where('status', $request->status);
            }
        }

        $submissions = $query->latest()->paginate(10)->withQueryString();
        $categories = Category::where('type', 'permohonan')->get();

        return view('admin.submissions.permohonan', compact('submissions', 'categories', 'stats'));
    }

    public function show($id)
    {
        $submission = Submission::with(['user', 'category', 'documents', 'statusHistories.changedBy'])
            ->findOrFail($id);

        $supabaseUrl = rtrim(env('SUPABASE_URL'), '/');
        $ktpBucket   = env('SUPABASE_KTP_BUCKET', 'ktp-photos');

        $ktpPublicUrl = null;
        $ktpRaw = $submission->user->foto_ktp ?? null;

        if ($ktpRaw && $supabaseUrl) {
            $ktpRaw = ltrim($ktpRaw, '/');

            if (Str::startsWith($ktpRaw, ['http://', 'https://'])) {
                $ktpPublicUrl = $ktpRaw;
            } else {
                if (Str::startsWith($ktpRaw, $ktpBucket . '/')) {
                    $ktpRaw = Str::after($ktpRaw, $ktpBucket . '/');
                }
                $ktpPublicUrl = "{$supabaseUrl}/storage/v1/object/public/{$ktpBucket}/{$ktpRaw}";
            }
        }

        return view('admin.submissions.show', compact('submission', 'ktpPublicUrl'));
    }

    public function update(Request $request, $id, \App\Services\BrevoMailer $brevo)
{
    $request->validate([
        'status'      => 'required|in:pending,in_progress,completed,rejected',
        'admin_notes' => 'nullable|string',
        'notify_user' => 'nullable',
    ]);

    $submission = Submission::findOrFail($id);
    $oldStatus  = $submission->status;

    $submission->update([
        'status'       => $request->status,
        'admin_notes'  => $request->admin_notes,
        'handled_by'   => Auth::id(),
        'completed_at' => $request->status == 'completed' ? now() : null,
    ]);

    if ($oldStatus !== $request->status) {
        $submission->statusHistories()->create([
            'changed_by' => Auth::id(),
            'new_status' => $request->status,
            'old_status' => $oldStatus,
            'notes'      => $request->admin_notes ?? 'Status diperbarui oleh Admin',
        ]);
    }

    if ($request->has('notify_user') && $oldStatus !== $request->status) {
        try {
            $submission->load(['user', 'category', 'handler']);

            $html = view('emails.submission_status_updated', [
                'submission' => $submission,
                'user'       => $submission->user,
                'category'   => $submission->category,
                'handler'    => $submission->handler,
                'note'       => $request->admin_notes,
                'oldStatus'  => $oldStatus,          
                'newStatus'  => $request->status,    
            ])->render();

            Log::info('BREVO DEBUG (admin) - about to send status update', [
                'to'          => $submission->user->email,
                'from'        => config('mail.from.address'),
                'from_name'   => config('mail.from.name'),
                'has_api_key' => (bool) config('brevo.api_key'),
                'ticket_id'   => $submission->ticket_id,
                'old_status'  => $oldStatus,
                'new_status'  => $request->status,
                'app_env'     => config('app.env'),
            ]);

            $brevo->sendTransactional(
                toEmail: $submission->user->email,
                toName: $submission->user->name ?? null,
                subject: "Update Status Permohonan Informasi ({$submission->ticket_id})",
                htmlContent: $html
            );

            Log::info('BREVO DEBUG (admin) - sent OK', [
                'to'        => $submission->user->email,
                'ticket_id' => $submission->ticket_id,
            ]);
        } catch (\Throwable $e) {
            Log::error('BREVO DEBUG (admin) - failed', [
                'to'        => $submission->user->email ?? null,
                'ticket_id' => $submission->ticket_id ?? null,
                'error'     => $e->getMessage(),
            ]);
        }
    }

    return redirect()->back()->with('success', 'Status permohonan informasi berhasil diperbarui.');
}

    public function downloadDocument($id)
{
    $doc = SubmissionDocument::findOrFail($id);

    $supabaseUrl = rtrim(env('SUPABASE_URL'), '/');
    $bucket = env('SUPABASE_SUBMISSIONS_BUCKET', env('SUPABASE_BUCKET', 'submissions'));

    $path = ltrim($doc->file_path, '/');

    if (Str::startsWith($path, 'submissions/')) {
        $path = Str::after($path, 'submissions/');
    }
    if (Str::startsWith($path, 'consultations/')) {
        $path = Str::after($path, 'consultations/');
    }

    if (Str::startsWith($path, 'submission/')) {
        $path = Str::after($path, 'submission/');
    }

    $urlNormal = "{$supabaseUrl}/storage/v1/object/public/{$bucket}/{$path}";
    $urlLegacy = "{$supabaseUrl}/storage/v1/object/public/{$bucket}/submissions/{$path}";
    $res = Http::get($urlNormal);
    $finalUrl = $res->successful() ? $urlNormal : $urlLegacy;

    return redirect()->away($finalUrl . '?download=' . urlencode($doc->original_name));
    }

    public function downloadPdf($id)
    {
        $submission = Submission::with([
            'user',
            'category',
            'documents',
            'statusHistories.changedBy', 
        ])->findOrFail($id);

        $pdf = Pdf::loadView('admin.submissions.pdf', compact('submission'))
            ->setPaper('A4', 'portrait');

        return $pdf->download('Pengajuan-' . $submission->ticket_id . '.pdf');
    }

    public function downloadKtp($id)
    {
        $submission = Submission::with('user')->findOrFail($id);

        $user = $submission->user;
        if (!$user || ($user->user_type ?? null) !== 'masyarakat_umum') {
            abort(404);
        }

        $supabaseUrl = rtrim(env('SUPABASE_URL'), '/');
        $ktpBucket   = env('SUPABASE_KTP_BUCKET', 'ktp-photos');

        $ktpRaw = $user->foto_ktp ?? null;
        if (!$ktpRaw || !$supabaseUrl) {
            abort(404);
        }

        $ktpRaw = ltrim($ktpRaw, '/');

        if (Str::startsWith($ktpRaw, ['http://', 'https://'])) {
            $fileUrl = $ktpRaw;
        } else {
            if (Str::startsWith($ktpRaw, $ktpBucket . '/')) {
                $ktpRaw = Str::after($ktpRaw, $ktpBucket . '/');
            }
            $fileUrl = "{$supabaseUrl}/storage/v1/object/public/{$ktpBucket}/{$ktpRaw}";
        }

        $res = Http::get($fileUrl);
        if (!$res->successful()) {
            abort(404);
        }

        $contentType = $res->header('Content-Type') ?? 'application/octet-stream';

        $ext = match (true) {
            str_contains($contentType, 'png') => 'png',
            str_contains($contentType, 'jpeg'), str_contains($contentType, 'jpg') => 'jpg',
            default => pathinfo(parse_url($fileUrl, PHP_URL_PATH) ?? '', PATHINFO_EXTENSION) ?: 'jpg',
        };

        $filename = 'KTP-' . ($user->nik ?? $user->id) . '.' . $ext;

        return response($res->body(), 200, [
            'Content-Type' => $contentType,
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}