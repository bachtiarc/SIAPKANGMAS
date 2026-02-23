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
            'selesai' => Consultation::where('status', 'completed')->count(), // ✅ sukses
            'ditolak' => Consultation::where('status', 'rejected')->count(),  // ✅ ditolak
            'belum'   => Consultation::where('status', 'pending')->count(),
        ];

        $query = Consultation::with(['user', 'handler', 'applicant'])->notArchived();

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

        if ($request->filled('diproses_bidang')) {
            $bidang = $request->diproses_bidang;

            if (\Illuminate\Support\Facades\Schema::hasColumn('consultations', 'diproses_oleh')) {
                $query->where('diproses_oleh', 'ILIKE', $bidang . ' -%');
            }
        }

        if ($request->filled('status') && $request->status != 'Semua') {
            $st = $request->status;

            if ($st === 'pending') {
                $query->where('status', 'pending');
            } elseif ($st === 'proses') {
                $query->where('status', 'on_progress');
            } elseif ($st === 'selesai') {
                $query->where('status', 'completed');
            } elseif ($st === 'ditolak') {
                $query->where('status', 'rejected');
            }
        }

        $consultations = $query->latest()->paginate(10)->withQueryString();

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

        $creator  = $consultation->user;
        $userType = $creator->user_type ?? null;

        $pemohon = ($userType === 'pegawai')
            ? ($consultation->applicant ?? null)
            : $creator;

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
            'notify_whatsapp'   => 'nullable', // ✅ NEW
            'diproses_bidang'   => 'nullable|string',
            'diproses_kelompok' => 'nullable|string',
            'diproses_oleh'     => 'nullable|string',
        ]);

        $consultation = Consultation::with(['user', 'handler', 'applicant'])->findOrFail($id);
        $oldStatus    = $consultation->status;
        $newStatus    = $request->status;

        $updateData = [
            'status'       => $newStatus,
            'handled_by'   => Auth::id(),
            'completed_at' => $newStatus == 'completed' ? now() : null,
        ];

        if (Schema::hasColumn('consultations', 'admin_response')) {
            $updateData['admin_response'] = $request->admin_notes;
        } elseif (Schema::hasColumn('consultations', 'admin_notes')) {
            $updateData['admin_notes'] = $request->admin_notes;
        }

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

        if ($oldStatus !== $newStatus) {
            $consultation->statusHistories()->create([
                'changed_by' => Auth::id(),
                'new_status' => $newStatus,
                'old_status' => $oldStatus,
                'notes'      => $request->admin_notes ?? 'Status diperbarui oleh Admin',
            ]);
        }

        // ============================
        // Tentukan pemohon (penting untuk pegawai/co-admin)
        // ============================
        $creator  = $consultation->user;
        $userType = $creator->user_type ?? null;

        $pemohon = ($userType === 'pegawai')
            ? ($consultation->applicant ?? null)
            : $creator;

        $toEmail = $pemohon->email ?? null;
        $toName  = $pemohon->nama_lengkap ?? $pemohon->name ?? null;

        // ============================
        // EMAIL via Brevo
        // ============================
        if ($request->has('notify_user') && $oldStatus !== $newStatus) {
            if (empty($toEmail)) {
                $ticketId = $consultation->ticket_id ?? $consultation->ticket_number ?? null;

                Log::warning('BREVO DEBUG (admin) - consultation email skipped: pemohon email empty', [
                    'ticket_id'     => $ticketId,
                    'consultation'  => $consultation->id,
                    'creator_type'  => $userType,
                    'pemohon_id'    => $pemohon->id ?? null,
                ]);
                // jangan return, biar bisa lanjut prepare WhatsApp bila perlu
            } else {
                try {
                    $hasCategoriesTable  = Schema::hasTable('categories');
                    $hasCategoryRelation = method_exists(Consultation::class, 'category');

                    $relations = ['user', 'handler', 'applicant'];
                    if ($hasCategoriesTable && $hasCategoryRelation) {
                        $relations[] = 'category';
                    }

                    $consultation->load($relations);

                    $html = view('emails.consultation_status_updated', [
                        'consultation' => $consultation,
                        'user'         => $pemohon,
                        'category'     => ($hasCategoriesTable && $hasCategoryRelation) ? $consultation->category : null,
                        'handler'      => $consultation->handler,
                        'note'         => $request->admin_notes,
                        'oldStatus'    => $oldStatus,
                        'newStatus'    => $newStatus,
                    ])->render();

                    $ticketId = $consultation->ticket_id ?? $consultation->ticket_number ?? '-';

                    Log::info('BREVO DEBUG (admin) - about to send consultation status update', [
                        'to'           => $toEmail,
                        'to_name'      => $toName,
                        'creator_type' => $userType,
                        'from'         => config('mail.from.address'),
                        'from_name'    => config('mail.from.name'),
                        'has_api_key'  => (bool) config('brevo.api_key'),
                        'ticket_id'    => $ticketId,
                        'old_status'   => $oldStatus,
                        'new_status'   => $newStatus,
                        'app_env'      => config('app.env'),
                    ]);

                    $brevo->sendTransactional(
                        toEmail: $toEmail,
                        toName: $toName,
                        subject: "Update Status Konsultasi ({$ticketId})",
                        htmlContent: $html
                    );

                    Log::info('BREVO DEBUG (admin) - consultation sent OK', [
                        'to'        => $toEmail,
                        'ticket_id' => $ticketId,
                    ]);
                } catch (\Throwable $e) {
                    $ticketId = $consultation->ticket_id ?? $consultation->ticket_number ?? null;

                    Log::error('BREVO DEBUG (admin) - consultation failed', [
                        'to'        => $toEmail,
                        'ticket_id' => $ticketId,
                        'error'     => $e->getMessage(),
                    ]);
                }
            }
        }

        // ============================
        // WhatsApp (manual link)
        // - jika admin centang notify_whatsapp => prepare (walau status sama)
        // - atau notify_user dicentang tapi email kosong => fallback ke WA
        // ============================
        $wantEmail = $request->has('notify_user') && $oldStatus !== $newStatus;
        $wantWa    = $request->has('notify_whatsapp'); // ✅ HAPUS syarat status berubah

        $shouldPrepareWa = $wantWa || ($wantEmail && empty($toEmail));

        if ($shouldPrepareWa) {
            $waPhone = $this->normalizeWaNumber(
                $pemohon->phone ?? $pemohon->phone_number ?? null
            );

            if (!$waPhone) {
                $ticketId = $consultation->ticket_id ?? $consultation->ticket_number ?? null;

                Log::warning('WA DEBUG (admin) - consultation skipped, phone missing', [
                    'ticket_id'     => $ticketId,
                    'consultation'  => $consultation->id,
                    'creator_type'  => $userType,
                    'pemohon_id'    => $pemohon->id ?? null,
                ]);
            } else {
                $ticketId    = $consultation->ticket_id ?? $consultation->ticket_number ?? '-';
                $pemohonName = $pemohon->nama_lengkap ?? $pemohon->name ?? 'Bapak/Ibu';

                $msgLines = [
                    "Halo {$pemohonName},",
                    "",
                    "Informasi Konsultasi: {$ticketId}",
                ];

                // ✅ kalau status berubah baru tampilkan info dari/menjadi
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

                Log::info('WA DEBUG (admin) - consultation prepared wa link', [
                    'ticket_id' => $ticketId,
                    'to_phone'  => $waPhone,
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

        $mode        = $request->get('mode', 'download');
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
     * ✅ Helper normalize KTP URL
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

        if (!str_contains($ktpRaw, '/') && !empty($nik)) {
            $ktpRaw = $nik . '/' . $ktpRaw;
        }

        return "{$supabaseUrl}/storage/v1/object/public/{$ktpBucket}/{$ktpRaw}";
    }

    // ============================
    // Helpers WhatsApp
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
}
