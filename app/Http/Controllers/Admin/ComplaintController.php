<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\Category;
use App\Models\ComplaintDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ComplaintController extends Controller
{
    public function index(Request $request)
    {
        $stats = [
            'total'   => Complaint::count(),
            'proses'  => Complaint::where('status', 'diproses')->count(),
            'selesai' => Complaint::whereIn('status', ['selesai', 'ditolak'])->count(),
            'belum'   => Complaint::where('status', 'pending')->count(),
        ];

        $query = Complaint::with(['user']);

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

        if ($request->filled('type') && $request->type !== 'Semua') {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('user_type', $request->type);
            });
        }

        if ($request->filled('status') && $request->status !== 'Semua') {
            $query->where('status', $request->status);
        }

        $complaints = $query->latest()->paginate(10)->withQueryString();

        return view('admin.complaints.pengaduan', compact('complaints', 'stats'));
    }

    public function show($id)
    {
        $complaint = Complaint::with([
            'user',
            'documents',
            'statusHistories.changedBy',
        ])->findOrFail($id);

        $supabaseUrl = rtrim(env('SUPABASE_URL'), '/');
        $ktpBucket   = env('SUPABASE_KTP_BUCKET', 'ktp-photos');

        $ktpPublicUrl = null;
        $ktpRaw = $complaint->user->foto_ktp ?? null;

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

        return view('admin.complaints.show', compact('complaint', 'ktpPublicUrl'));
    }

    public function update(Request $request, $id, \App\Services\BrevoMailer $brevo)
    {
        $request->validate([
            'status'      => 'required|in:pending,diproses,selesai,ditolak',
            'admin_notes' => 'nullable|string',
            'diproses_bidang'   => 'nullable|string',
            'diproses_kelompok' => 'nullable|string',
            'diproses_oleh'     => 'nullable|string',
        ]);

        $complaint = Complaint::with(['user', 'handler'])->findOrFail($id);

        $oldStatus = $complaint->status;
        $oldNotes  = $complaint->admin_response ?? $complaint->admin_notes;

        $newStatus = $request->status;
        $newNotes  = $request->admin_notes;

        $statusChanged = ($oldStatus !== $newStatus);
        $notesChanged  = ((string)($oldNotes ?? '') !== (string)($newNotes ?? ''));

        $complaint->update([
            'status'         => $newStatus,
            'admin_response' => $newNotes,
            'handled_by'     => Auth::id(),
            'completed_at'   => $newStatus === 'selesai' ? now() : null,
            'diproses_bidang'   => $request->diproses_bidang,
            'diproses_kelompok' => $request->diproses_kelompok,
            'diproses_oleh'     => $request->diproses_oleh,
        ]);

        if ($statusChanged || $notesChanged) {
            try {
                $complaint->statusHistories()->create([
                    'changed_by' => Auth::id(),
                    'new_status' => $newStatus,
                    'old_status' => $oldStatus,
                    'notes'      => $newNotes ?? 'Perubahan disimpan oleh Admin',
                ]);
            } catch (\Exception $e) {
                Log::error('Gagal simpan status history pengaduan: ' . $e->getMessage());
            }
        }

        if (($statusChanged || $notesChanged) && !empty($complaint->user->email)) {
            try {
                $complaint->load(['user', 'handler']);

                $html = view('emails.complaint_status_updated', [
                    'complaint'  => $complaint,
                    'user'       => $complaint->user,
                    'handler'    => $complaint->handler,
                    'notes'      => $newNotes,
                    'oldStatus'  => $oldStatus,    
                    'newStatus'  => $newStatus,    
                ])->render();

                Log::info('BREVO DEBUG (admin) - about to send complaint status update', [
                    'to'          => $complaint->user->email,
                    'from'        => config('mail.from.address'),
                    'from_name'   => config('mail.from.name'),
                    'has_api_key' => (bool) config('brevo.api_key'),
                    'ticket_no'   => $complaint->ticket_number ?? $complaint->id,
                    'old_status'  => $oldStatus,
                    'new_status'  => $newStatus,
                    'app_env'     => config('app.env'),
                ]);

                $brevo->sendTransactional(
                    toEmail: $complaint->user->email,
                    toName: $complaint->user->name ?? null,
                    subject: 'Update Status Pengaduan (' . ($complaint->ticket_number ?? $complaint->id) . ')',
                    htmlContent: $html
                );

                Log::info('BREVO DEBUG (admin) - complaint sent OK', [
                    'to'        => $complaint->user->email,
                    'ticket_no' => $complaint->ticket_number ?? $complaint->id,
                ]);
            } catch (\Throwable $e) {
                Log::error('BREVO DEBUG (admin) - complaint failed', [
                    'to'        => $complaint->user->email ?? null,
                    'ticket_no' => $complaint->ticket_number ?? $complaint->id ?? null,
                    'error'     => $e->getMessage(),
                ]);
            }
        }

        return redirect()->back()->with('success', 'Status pengaduan berhasil diperbarui.');
    }

    public function downloadDocument(Request $request, $id)
    {
        $doc = ComplaintDocument::findOrFail($id);

        $supabaseUrl = rtrim(env('SUPABASE_URL'), '/');
        $bucket = env('SUPABASE_COMPLAINTS_BUCKET', 'complaints');

        $path = ltrim($doc->file_path, '/');

        if (Str::startsWith($path, 'complaints/')) {
            $path = Str::after($path, 'complaints/');
        }

        $urlNormal = "{$supabaseUrl}/storage/v1/object/public/{$bucket}/{$path}";
        $urlLegacy = "{$supabaseUrl}/storage/v1/object/public/{$bucket}/complaints/{$path}";

        $res = Http::get($urlNormal);
        if (!$res->successful()) {
            $res = Http::get($urlLegacy);
            if (!$res->successful()) {
                abort(404, 'Dokumen tidak ditemukan.');
            }
        }

        $mode = $request->get('mode', 'download'); 

        $contentType = $res->header('Content-Type') ?? 'application/octet-stream';

        $filename = str_replace(['"', "\r", "\n"], '', $doc->original_name ?? 'document');

        $disposition = $mode === 'view'
            ? 'inline; filename="' . $filename . '"'
            : 'attachment; filename="' . $filename . '"';

        return response($res->body(), 200, [
            'Content-Type' => $contentType,
            'Content-Disposition' => $disposition,
        ]);
    }

    public function downloadPdf($id)
    {
        $complaint = Complaint::with([
            'user',
            'documents',
            'statusHistories.changedBy',
        ])->findOrFail($id);

        $pdf = Pdf::loadView('admin.complaints.pdf', compact('complaint'))
            ->setPaper('A4', 'portrait');

        $filename = 'Pengaduan-' . ($complaint->ticket_number ?? $complaint->id) . '.pdf';
        return $pdf->download($filename);
    }

    public function downloadKtp($id)
    {
        $complaint = Complaint::with('user')->findOrFail($id);

        $user = $complaint->user;
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