<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Consultation;
use App\Models\Category;
use App\Models\ConsultationDocument;
use App\Mail\ConsultationStatusUpdated;
use App\Support\SupabasePath;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;

class ConsultationController extends Controller
{
    public function index(Request $request)
    {
        $stats = [
            'total'   => Consultation::count(),
            'proses'  => Consultation::where('status', 'on_progress')->count(),
            'selesai' => Consultation::whereIn('status', ['completed', 'rejected'])->count(),
            'belum'   => Consultation::where('status', 'pending')->count(),
        ];

        $query = Consultation::with(['user', 'category']);
        
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

        $consultations = $query->latest()->paginate(10)->withQueryString();
        $categories = Category::where('type', 'konsultasi')->get();

        return view('admin.consultations.konsultasi', compact('consultations', 'categories', 'stats'));
    }

    public function show($id)
    {
        $consultation = Consultation::with(['user', 'category', 'documents', 'statusHistories.changedBy'])
            ->findOrFail($id);

        $supabaseUrl = rtrim(env('SUPABASE_URL'), '/');
        $ktpBucket   = env('SUPABASE_KTP_BUCKET', 'ktp-photos');

        $ktpPublicUrl = null;
        $ktpRaw = $consultation->user->foto_ktp ?? null;

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

        return view('admin.consultations.show', compact('consultation', 'ktpPublicUrl'));
    }

    public function update(Request $request, $id, \App\Services\BrevoMailer $brevo)
    {
        $request->validate([
            'status'      => 'required|in:pending,on_progress,completed,rejected',
            'admin_notes' => 'nullable|string',
            'notify_user' => 'nullable',
        ]);

        $consultation = Consultation::findOrFail($id);
        $oldStatus    = $consultation->status;

        $consultation->update([
            'status'         => $request->status,
            'admin_response' => $request->admin_notes,
            'handled_by'     => Auth::id(),
            'completed_at'   => $request->status == 'completed' ? now() : null,
        ]);

        if ($oldStatus !== $request->status) {
            $consultation->statusHistories()->create([
                'changed_by' => Auth::id(),
                'new_status' => $request->status,
                'old_status' => $oldStatus,
                'notes'      => $request->admin_notes ?? 'Status diperbarui oleh Admin',
            ]);
        }

        if ($request->has('notify_user') && $oldStatus !== $request->status) {
            try {
                $consultation->load(['user', 'category', 'handler']);

                $html = view('emails.consultation_status_updated', [
                    'consultation' => $consultation,
                    'user'         => $consultation->user,
                    'category'     => $consultation->category,
                    'handler'      => $consultation->handler,
                    'note'         => $request->admin_notes,
                    'oldStatus'    => $oldStatus,         
                    'newStatus'    => $request->status,   
                ])->render();

                Log::info('BREVO DEBUG (admin) - about to send consultation status update', [
                    'to'          => $consultation->user->email,
                    'from'        => config('mail.from.address'),
                    'from_name'   => config('mail.from.name'),
                    'has_api_key' => (bool) config('brevo.api_key'),
                    'ticket_id'   => $consultation->ticket_id,
                    'old_status'  => $oldStatus,
                    'new_status'  => $request->status,
                    'app_env'     => config('app.env'),
                ]);

                $brevo->sendTransactional(
                    toEmail: $consultation->user->email,
                    toName: $consultation->user->name ?? null,
                    subject: "Update Status Konsultasi ({$consultation->ticket_id})",
                    htmlContent: $html
                );

                Log::info('BREVO DEBUG (admin) - consultation sent OK', [
                    'to'        => $consultation->user->email,
                    'ticket_id' => $consultation->ticket_id,
                ]);
            } catch (\Throwable $e) {
                Log::error('BREVO DEBUG (admin) - consultation failed', [
                    'to'        => $consultation->user->email ?? null,
                    'ticket_id' => $consultation->ticket_id ?? null,
                    'error'     => $e->getMessage(),
                ]);
            }
        }

        return redirect()->back()->with('success', 'Status konsultasi berhasil diperbarui.');
    }

    public function downloadDocument(Request $request, $id)
    {
        $doc = ConsultationDocument::findOrFail($id);

        $supabaseUrl = rtrim(env('SUPABASE_URL'), '/');
        $bucket = env('SUPABASE_CONSULTATIONS_BUCKET', 'consultations');

        $path = ltrim($doc->file_path, '/');

        // normalisasi path (sesuai punyamu)
        if (Str::startsWith($path, 'consultations/')) {
            $path = Str::after($path, 'consultations/');
        }
        if (Str::startsWith($path, 'submissions/')) {
            $path = Str::after($path, 'submissions/');
        }
        if (Str::startsWith($path, 'consultations/')) {
            $path = Str::after($path, 'consultations/');
        }

        $urlNormal = "{$supabaseUrl}/storage/v1/object/public/{$bucket}/{$path}";
        $urlLegacy = "{$supabaseUrl}/storage/v1/object/public/{$bucket}/consultations/{$path}";

        $res = Http::get($urlNormal);
        if (!$res->successful()) {
            $res = Http::get($urlLegacy);
            if (!$res->successful()) {
                abort(404, 'Dokumen tidak ditemukan.');
            }
        }

        $mode = $request->get('mode', 'download'); // view | download
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
        $consultation = Consultation::with([
            'user',
            'category',
            'documents',
            'statusHistories.changedBy', 
        ])->findOrFail($id);

        $pdf = Pdf::loadView('admin.consultations.pdf', compact('consultation'))
            ->setPaper('A4', 'portrait');

        return $pdf->download('Konsultasi-' . $consultation->ticket_id . '.pdf');
    }

    public function downloadKtp($id)
    {
        $consultation = Consultation::with('user')->findOrFail($id);

        $user = $consultation->user;
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