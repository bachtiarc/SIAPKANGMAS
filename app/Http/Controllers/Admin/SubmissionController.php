<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Submission;
use App\Models\Category;
use App\Models\SubmissionDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;

class SubmissionController extends Controller
{
    public function index(Request $request)
    {
        $stats = [
            'total'   => Submission::count(),
            'proses'  => Submission::where('status', 'in_progress')->count(),
            'selesai' => Submission::where('status', 'completed')->count(), // ✅ sukses
            'ditolak' => Submission::where('status', 'rejected')->count(),  // ✅ ditolak
            'belum'   => Submission::where('status', 'pending')->count()
        ];

        $hasCategories = Schema::hasTable('categories');

        $query = Submission::with(['user', 'applicant'])->notArchived();
        if ($hasCategories) {
            $query->with('category');
        }

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
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('user_type', $request->type);
            });
        }

        if ($hasCategories && $request->filled('category') && $request->category != 'Semua') {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('status') && $request->status != 'Semua') {
            $st = $request->status;

            if ($st === 'pending') {
                $query->where('status', 'pending');
            } elseif ($st === 'proses') {
                $query->where('status', 'in_progress');
            } elseif ($st === 'selesai') {
                $query->where('status', 'completed');
            } elseif ($st === 'ditolak') {
                $query->where('status', 'rejected');
            }
        }

        $submissions = $query->latest()->paginate(10)->withQueryString();

        $categories = $hasCategories
            ? Category::where('type', 'permohonan')->get()
            : collect();

        return view('admin.submissions.permohonan', compact('submissions', 'categories', 'stats'));
    }

    public function show($id)
    {
        $hasCategories = Schema::hasTable('categories');

        $with = [
            'user',
            'documents',
            'statusHistories.changedBy',
            'applicant',
        ];
        if ($hasCategories) {
            $with[] = 'category';
        }

        $submission = Submission::with($with)->findOrFail($id);

        $supabaseUrl = rtrim(env('SUPABASE_URL'), '/');
        $ktpBucket   = env('SUPABASE_KTP_BUCKET', 'ktp-photos');

        $creator  = $submission->user;
        $userType = $creator->user_type ?? null;

        $pemohon = ($userType === 'pegawai')
            ? ($submission->applicant ?? null)
            : $creator;

        $ktpPublicUrl = null;
        $ktpRaw = $pemohon->foto_ktp ?? null;

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

        $categoryName = $hasCategories ? ($submission->category->name ?? '-') : '-';

        return view('admin.submissions.show', compact('submission', 'ktpPublicUrl', 'categoryName'));
    }

    public function update(Request $request, $id, \App\Services\BrevoMailer $brevo)
    {
        $request->validate([
            // ✅ terima "on_progress" juga (karena di blade value-nya itu)
            'status'            => 'required|in:pending,in_progress,on_progress,completed,rejected',
            'diproses_bidang'   => 'nullable|string|max:255',
            'diproses_kelompok' => 'nullable|string|max:255',
            'diproses_oleh'     => 'nullable|string|max:255',
            'admin_notes'       => 'nullable|string',
            'notify_user'       => 'nullable',
            'notify_whatsapp'   => 'nullable', // ✅ NEW
        ]);

        $submission = Submission::with(['user', 'applicant'])->findOrFail($id);
        $oldStatus  = $submission->status;

        // ✅ Normalisasi supaya DB konsisten pake "in_progress"
        $newStatus = $request->status === 'on_progress'
            ? 'in_progress'
            : $request->status;

        $submission->update([
            'status'            => $newStatus,
            'diproses_bidang'   => $request->diproses_bidang,
            'diproses_kelompok' => $request->diproses_kelompok,
            'diproses_oleh'     => $request->diproses_oleh,
            'admin_notes'       => $request->admin_notes,
            'handled_by'        => Auth::id(),
            'completed_at'      => $newStatus === 'completed' ? now() : null,
        ]);

        if ($oldStatus !== $newStatus) {
            $submission->statusHistories()->create([
                'changed_by' => Auth::id(),
                'new_status' => $newStatus,
                'old_status' => $oldStatus,
                'notes'      => $request->admin_notes ?? 'Status diperbarui oleh Admin',
            ]);
        }

        // ============================
        // Tentukan penerima (pemohon asli)
        // - pegawai => applicant
        // - masyarakat => user
        // ============================
        $submission->load(['user', 'handler', 'applicant']);
        $creator  = $submission->user;
        $userType = $creator->user_type ?? null;

        $recipient = ($userType === 'pegawai' && $submission->applicant)
            ? $submission->applicant
            : $creator;

        $toEmail = $recipient->email ?? null;

        // ✅ Kirim email hanya kalau status berubah & checkbox dicentang
        if ($request->has('notify_user') && $oldStatus !== $newStatus) {
            try {
                $hasCategories = Schema::hasTable('categories');
                if ($hasCategories) {
                    $submission->load('category');
                }

                if ($toEmail) {
                    $html = view('emails.submission_status_updated', [
                        'submission' => $submission,
                        'user'       => $recipient,
                        'category'   => $hasCategories ? $submission->category : null,
                        'handler'    => $submission->handler,
                        'note'       => $request->admin_notes,
                        'oldStatus'  => $oldStatus,
                        'newStatus'  => $newStatus,
                    ])->render();

                    Log::info('BREVO DEBUG (admin) - about to send status update', [
                        'to'          => $toEmail,
                        'from'        => config('mail.from.address'),
                        'from_name'   => config('mail.from.name'),
                        'has_api_key' => (bool) config('brevo.api_key'),
                        'ticket_id'   => $submission->ticket_id,
                        'old_status'  => $oldStatus,
                        'new_status'  => $newStatus,
                        'app_env'     => config('app.env'),
                    ]);

                    $brevo->sendTransactional(
                        toEmail: $toEmail,
                        toName: $recipient->name ?? $recipient->nama_lengkap ?? null,
                        subject: "Update Status Permohonan Informasi ({$submission->ticket_id})",
                        htmlContent: $html
                    );

                    Log::info('BREVO DEBUG (admin) - sent OK', [
                        'to'        => $toEmail,
                        'ticket_id' => $submission->ticket_id,
                    ]);
                } else {
                    Log::warning('BREVO DEBUG (admin) - skipped, recipient email missing', [
                        'ticket_id'      => $submission->ticket_id,
                        'recipient_id'   => $recipient->id ?? null,
                        'recipient_type' => $userType,
                    ]);
                }
            } catch (\Throwable $e) {
                Log::error('BREVO DEBUG (admin) - failed', [
                    'ticket_id' => $submission->ticket_id ?? null,
                    'error'     => $e->getMessage(),
                ]);
            }
        }

        // ✅ WhatsApp (manual link)
        // - kalau admin centang notify_whatsapp => selalu prepare (walau status sama)
        // - atau kalau notify_user dicentang tapi email kosong => prepare wa sebagai fallback
        $wantEmail = $request->has('notify_user') && $oldStatus !== $newStatus;
        $wantWa    = $request->has('notify_whatsapp'); // ⬅️ HAPUS syarat status berubah

        $shouldPrepareWa = $wantWa || ($wantEmail && empty($toEmail));

        if ($shouldPrepareWa) {
            $waPhone = $this->normalizeWaNumber(
                $recipient->phone ?? $recipient->phone_number ?? null
            );

            if (!$waPhone) {
                Log::warning('WA DEBUG (admin) - skipped, phone missing', [
                    'ticket_id'      => $submission->ticket_id,
                    'recipient_id'   => $recipient->id ?? null,
                    'recipient_type' => $userType,
                ]);
            } else {
                $recipientName = $recipient->nama_lengkap ?? $recipient->name ?? 'Bapak/Ibu';

                // optional: kalau status nggak berubah, jangan tampilkan "dari-menjadi"
                $msgLines = [
                    "Halo {$recipientName},",
                    "",
                    "Informasi Permohonan: {$submission->ticket_id}",
                ];

                if ($oldStatus !== $newStatus) {
                    $msgLines[] = "Update status:";
                    $msgLines[] = "Dari: " . $this->statusTextId($oldStatus);
                    $msgLines[] = "Menjadi: " . $this->statusTextId($newStatus);
                }

                if (!empty($request->admin_notes)) {
                    $msgLines[] = "";
                    $msgLines[] = "Catatan Admin:";
                    $msgLines[] = trim((string) $request->admin_notes);
                }

                $msgLines[] = "";
                $msgLines[] = "Terima kasih.";

                $waText = implode("\n", $msgLines);
                $waLink = 'https://wa.me/' . $waPhone . '?text=' . rawurlencode($waText);

                $request->session()->flash('wa_link', $waLink);

                Log::info('WA DEBUG (admin) - prepared wa link', [
                    'ticket_id'      => $submission->ticket_id,
                    'to_phone'       => $waPhone,
                    'recipient_type' => $userType,
                ]);
            }
        }

        $redirect = redirect()
            ->route('admin.submissions.show', $submission->id)
            ->with('success', 'Status permohonan informasi berhasil diperbarui.');

        return $redirect;
    }

    // ============================
    // Helpers
    // ============================
    private function normalizeWaNumber(?string $rawPhone): ?string
    {
        $digits = preg_replace('/\D+/', '', (string) $rawPhone);
        if (!$digits) return null;

        if (str_starts_with($digits, '0')) return '62' . substr($digits, 1);
        if (str_starts_with($digits, '8')) return '62' . $digits;
        if (str_starts_with($digits, '62')) return $digits;

        return $digits;
    }

    private function statusTextId(?string $st): string
    {
        $st = strtolower((string) $st);
        return match (true) {
            in_array($st, ['pending','belum diproses']) => 'Belum Diproses',
            in_array($st, ['on_progress','in_progress','diproses','sedang diproses']) => 'Sedang Diproses',
            in_array($st, ['completed','selesai','approved']) => 'Selesai',
            in_array($st, ['rejected','ditolak']) => 'Ditolak',
            default => ucfirst($st ?: '-'),
        };
    }

    public function downloadDocument(Request $request, $id)
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
        $hasCategories = Schema::hasTable('categories');

        $with = [
            'user',
            'documents',
            'statusHistories.changedBy',
        ];
        if ($hasCategories) $with[] = 'category';

        $submission = Submission::with($with)->findOrFail($id);

        $pdf = Pdf::loadView('admin.submissions.pdf', compact('submission'))
            ->setPaper('A4', 'portrait');

        return $pdf->download('Pengajuan-' . $submission->ticket_id . '.pdf');
    }

    public function downloadKtp($id)
    {
        $submission = Submission::with(['user', 'applicant'])->findOrFail($id);

        $creator  = $submission->user;
        $userType = $creator->user_type ?? null;

        $pemohon = ($userType === 'pegawai')
            ? ($submission->applicant ?? null)
            : $creator;

        if (!$pemohon) {
            abort(404);
        }

        $supabaseUrl = rtrim(env('SUPABASE_URL'), '/');
        $ktpBucket   = env('SUPABASE_KTP_BUCKET', 'ktp-photos');

        $ktpRaw = $pemohon->foto_ktp ?? null;
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
            str_contains($contentType, 'jpeg') || str_contains($contentType, 'jpg') => 'jpg',
            default => pathinfo(parse_url($fileUrl, PHP_URL_PATH) ?? '', PATHINFO_EXTENSION) ?: 'jpg',
        };

        $filename = 'KTP-' . ($pemohon->nik ?? $pemohon->id) . '.' . $ext;

        return response($res->body(), 200, [
            'Content-Type' => $contentType,
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
