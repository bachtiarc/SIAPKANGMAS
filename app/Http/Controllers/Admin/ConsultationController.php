<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Consultation;
use App\Models\Category;
use App\Models\ConsultationDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
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

        // ✅ aman: jangan paksa with('category') kalau relasi ga ada
        $query = Consultation::with(['user', 'handler']);

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

        // ✅ filter bidang (opsional): hanya efektif kalau kamu punya kolom bidang di consultations
        // misal kolom: diproses_oleh dengan format "Bidang - Kelompok"
        if ($request->filled('diproses_bidang')) {
            $bidang = $request->diproses_bidang;

            // kalau suatu saat kolomnya ada, ini langsung jalan:
            if (\Illuminate\Support\Facades\Schema::hasColumn('consultations', 'diproses_oleh')) {
                // Postgres friendly: ILIKE
                $query->where('diproses_oleh', 'ILIKE', $bidang . ' -%');
            }
        }

        if ($request->filled('status') && $request->status != 'Semua') {
            if ($request->status === 'completed') {
                $query->whereIn('status', ['completed', 'rejected']);
            } else {
                $query->where('status', $request->status);
            }
        }

        $consultations = $query->latest()->paginate(10)->withQueryString();

        // kategori sudah tidak dipakai di view baru, jadi ini aman dikosongkan
        $categories = collect();

        return view('admin.consultations.konsultasi', compact('consultations', 'categories', 'stats'));
    }

    public function show($id)
    {
        $hasCategoriesTable  = Schema::hasTable('categories');
        $hasCategoryRelation = method_exists(Consultation::class, 'category');

        $with = ['user', 'documents', 'statusHistories.changedBy', 'applicant'];
        if ($hasCategoriesTable && $hasCategoryRelation) {
            $with[] = 'category';
        }

        $consultation = Consultation::with($with)->findOrFail($id);

        // Tentukan pemohon
        $creator  = $consultation->user;
        $userType = $creator->user_type ?? null;

        $pemohon = ($userType === 'pegawai')
            ? ($consultation->applicant ?? null)
            : $creator;

        // Build KTP public URL (support data baru + legacy)
        $ktpRaw       = $pemohon->foto_ktp ?? null;
        $pemohonNik   = $pemohon->nik ?? null;
        $ktpPublicUrl = $this->buildKtpPublicUrl($ktpRaw, $pemohonNik);

        $categoryName = ($hasCategoriesTable && $hasCategoryRelation)
            ? ($consultation->category->name ?? '-')
            : '-';

        return view('admin.consultations.show', compact('consultation', 'ktpPublicUrl', 'categoryName'));
    }

    public function update(Request $request, $id, \App\Services\BrevoMailer $brevo)
    {
        $request->validate([
            'status'            => 'required|in:pending,on_progress,completed,rejected',
            'admin_notes'       => 'nullable|string',
            'notify_user'       => 'nullable',
            'diproses_bidang'   => 'nullable|string',
            'diproses_kelompok' => 'nullable|string',
            'diproses_oleh'     => 'nullable|string',
        ]);

        $consultation = Consultation::findOrFail($id);
        $oldStatus    = $consultation->status;

        $updateData = [
            'status'       => $request->status,
            'handled_by'   => Auth::id(),
            'completed_at' => $request->status == 'completed' ? now() : null,
        ];

        // admin notes
        if (Schema::hasColumn('consultations', 'admin_response')) {
            $updateData['admin_response'] = $request->admin_notes;
        } elseif (Schema::hasColumn('consultations', 'admin_notes')) {
            $updateData['admin_notes'] = $request->admin_notes;
        }

        // diproses_* (kalau kolom ada)
        if (Schema::hasColumn('consultations', 'diproses_bidang')) {
            $updateData['diproses_bidang'] = $request->diproses_bidang;
        }
        if (Schema::hasColumn('consultations', 'diproses_kelompok')) {
            $updateData['diproses_kelompok'] = $request->diproses_kelompok;
        }
        if (Schema::hasColumn('consultations', 'diproses_oleh')) {
            $updateData['diproses_oleh'] = $request->diproses_oleh;
        }

        $consultation->update($updateData);

        if ($oldStatus !== $request->status) {
            $consultation->statusHistories()->create([
                'changed_by' => Auth::id(),
                'new_status' => $request->status,
                'old_status' => $oldStatus,
                'notes'      => $request->admin_notes ?? 'Status diperbarui oleh Admin',
            ]);
        }

        // notify user (email)
        if ($request->has('notify_user') && $oldStatus !== $request->status) {
            try {
                $hasCategoriesTable  = Schema::hasTable('categories');
                $hasCategoryRelation = method_exists(Consultation::class, 'category');

                $relations = ['user', 'handler'];
                if ($hasCategoriesTable && $hasCategoryRelation) {
                    $relations[] = 'category';
                }

                $consultation->load($relations);

                $html = view('emails.consultation_status_updated', [
                    'consultation' => $consultation,
                    'user'         => $consultation->user,
                    'category'     => ($hasCategoriesTable && $hasCategoryRelation) ? $consultation->category : null,
                    'handler'      => $consultation->handler,
                    'note'         => $request->admin_notes,
                    'oldStatus'    => $oldStatus,
                    'newStatus'    => $request->status,
                ])->render();

                $ticketId = $consultation->ticket_id ?? $consultation->ticket_number ?? '-';

                Log::info('BREVO DEBUG (admin) - about to send consultation status update', [
                    'to'          => $consultation->user->email,
                    'from'        => config('mail.from.address'),
                    'from_name'   => config('mail.from.name'),
                    'has_api_key' => (bool) config('brevo.api_key'),
                    'ticket_id'   => $ticketId,
                    'old_status'  => $oldStatus,
                    'new_status'  => $request->status,
                    'app_env'     => config('app.env'),
                ]);

                $brevo->sendTransactional(
                    toEmail: $consultation->user->email,
                    toName: $consultation->user->name ?? null,
                    subject: "Update Status Konsultasi ({$ticketId})",
                    htmlContent: $html
                );

                Log::info('BREVO DEBUG (admin) - consultation sent OK', [
                    'to'        => $consultation->user->email,
                    'ticket_id' => $ticketId,
                ]);
            } catch (\Throwable $e) {
                $ticketId = $consultation->ticket_id ?? $consultation->ticket_number ?? null;

                Log::error('BREVO DEBUG (admin) - consultation failed', [
                    'to'        => $consultation->user->email ?? null,
                    'ticket_id' => $ticketId,
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
        $bucket      = env('SUPABASE_CONSULTATIONS_BUCKET', 'consultations');

        $path = ltrim($doc->file_path, '/');

        // normalisasi path legacy
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

        $mode        = $request->get('mode', 'download'); // view | download
        $contentType = $res->header('Content-Type') ?? 'application/octet-stream';
        $filename    = str_replace(['"', "\r", "\n"], '', $doc->original_name ?? 'document');

        $disposition = $mode === 'view'
            ? 'inline; filename="' . $filename . '"'
            : 'attachment; filename="' . $filename . '"';

        return response($res->body(), 200, [
            'Content-Type'        => $contentType,
            'Content-Disposition' => $disposition,
        ]);
    }

    public function downloadPdf($id)
    {
        $hasCategoriesTable  = Schema::hasTable('categories');
        $hasCategoryRelation = method_exists(Consultation::class, 'category');

        $with = ['user', 'documents', 'statusHistories.changedBy'];
        if ($hasCategoriesTable && $hasCategoryRelation) {
            $with[] = 'category';
        }

        $consultation = Consultation::with($with)->findOrFail($id);

        $pdf = Pdf::loadView('admin.consultations.pdf', compact('consultation'))
            ->setPaper('A4', 'portrait');

        $ticketId = $consultation->ticket_id ?? $consultation->ticket_number ?? '-';

        return $pdf->download('Konsultasi-' . $ticketId . '.pdf');
    }

    public function downloadKtp($id)
    {
        $consultation = Consultation::with(['user', 'applicant'])->findOrFail($id);

        $creator  = $consultation->user;
        $userType = $creator->user_type ?? null;

        $pemohon = ($userType === 'pegawai')
            ? ($consultation->applicant ?? null)
            : $creator;

        if (!$pemohon) abort(404);

        $ktpRaw     = $pemohon->foto_ktp ?? null;
        $pemohonNik = $pemohon->nik ?? null;

        $fileUrl = $this->buildKtpPublicUrl($ktpRaw, $pemohonNik);
        if (!$fileUrl) abort(404);

        $res = Http::get($fileUrl);
        if (!$res->successful()) abort(404);

        $contentType = $res->header('Content-Type') ?? 'application/octet-stream';

        $ext = match (true) {
            str_contains($contentType, 'png') => 'png',
            str_contains($contentType, 'jpeg') || str_contains($contentType, 'jpg') => 'jpg',
            default => pathinfo(parse_url($fileUrl, PHP_URL_PATH) ?? '', PATHINFO_EXTENSION) ?: 'jpg',
        };

        $filename = 'KTP-' . ($pemohonNik ?? $pemohon->id) . '.' . $ext;

        return response($res->body(), 200, [
            'Content-Type'        => $contentType,
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * ✅ Helper normalize KTP URL (support:
     * - full url
     * - "ktp/<file>"
     * - "<file>"
     * - "<nik>/<file>"
     * - "ktp-photos/<...>"
     */
    private function buildKtpPublicUrl(?string $ktpRaw, ?string $nik): ?string
    {
        $supabaseUrl = rtrim(env('SUPABASE_URL'), '/');
        $ktpBucket   = env('SUPABASE_KTP_BUCKET', 'ktp-photos');

        if (!$ktpRaw || !$supabaseUrl) return null;

        $ktpRaw = ltrim($ktpRaw, '/');

        if (Str::startsWith($ktpRaw, ['http://', 'https://'])) {
            return $ktpRaw;
        }

        if (Str::startsWith($ktpRaw, $ktpBucket . '/')) {
            $ktpRaw = Str::after($ktpRaw, $ktpBucket . '/');
        }

        if (Str::startsWith($ktpRaw, 'ktp/')) {
            $ktpRaw = Str::after($ktpRaw, 'ktp/');
        }

        // kalau cuma filename, prefix NIK
        if (!str_contains($ktpRaw, '/') && !empty($nik)) {
            $ktpRaw = $nik . '/' . $ktpRaw;
        }

        return "{$supabaseUrl}/storage/v1/object/public/{$ktpBucket}/{$ktpRaw}";
    }
}
