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
            'selesai' => Complaint::where('status', 'selesai')->count(), // ✅ sukses
            'ditolak' => Complaint::where('status', 'ditolak')->count(),
            'belum'   => Complaint::where('status', 'pending')->count(),
        ];

        $query = Complaint::with(['user', 'applicant'])->notArchived();

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
            $st = $request->status;

            if ($st === 'pending') {
                $query->where('status', 'pending');
            } elseif ($st === 'proses') {
                $query->where('status', 'diproses');
            } elseif ($st === 'selesai') {
                $query->where('status', 'selesai');
            } elseif ($st === 'ditolak') {
                $query->where('status', 'ditolak');
            }
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
            'applicant',
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
            'status'            => 'required|in:pending,diproses,selesai,ditolak',
            'admin_notes'       => 'nullable|string',
            'notify_user'       => 'nullable',      // ✅ NEW: supaya ada checkbox email kayak submission/consultation (kalau di blade belum ada, tetap aman)
            'notify_whatsapp'   => 'nullable',      // ✅ NEW
            'diproses_bidang'   => 'nullable|string',
            'diproses_kelompok' => 'nullable|string',
            'diproses_oleh'     => 'nullable|string',
        ]);

        $complaint = Complaint::with(['user', 'handler', 'applicant'])->findOrFail($id);

        $oldStatus = $complaint->status;
        $oldNotes  = $complaint->admin_response ?? $complaint->admin_notes;

        $newStatus = $request->status;
        $newNotes  = $request->admin_notes;

        $statusChanged = ($oldStatus !== $newStatus);
        $notesChanged  = ((string)($oldNotes ?? '') !== (string)($newNotes ?? ''));

        $complaint->update([
            'status'            => $newStatus,
            'admin_response'    => $newNotes,
            'handled_by'        => Auth::id(),
            'completed_at'      => $newStatus === 'selesai' ? now() : null,
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

        // ==========================
        // Pemohon sebenarnya:
        // - masyarakat_umum => user
        // - pegawai => applicant
        // ==========================
        $creator  = $complaint->user;
        $userType = $creator->user_type ?? null;

        $pemohon = ($userType === 'pegawai')
            ? ($complaint->applicant ?? null)
            : $creator;

        $toEmail = $pemohon->email ?? null;
        $toName  = $pemohon->nama_lengkap ?? $pemohon->name ?? null;

        // ==========================
        // EMAIL (opsional checkbox)
        // Kalau blade belum punya notify_user: ini tetap aman, karena has('notify_user') false.
        // ==========================
        if ($request->has('notify_user') && ($statusChanged || $notesChanged)) {
            if (!empty($toEmail)) {
                try {
                    $complaint->load(['user', 'handler', 'applicant']);

                    $html = view('emails.complaint_status_updated', [
                        'complaint'  => $complaint,
                        'user'       => $pemohon,
                        'handler'    => $complaint->handler,
                        'notes'      => $newNotes,
                        'oldStatus'  => $oldStatus,
                        'newStatus'  => $newStatus,
                    ])->render();

                    Log::info('BREVO DEBUG (admin) - about to send complaint status update', [
                        'to'            => $toEmail,
                        'to_name'       => $toName,
                        'creator_type'  => $userType,
                        'from'          => config('mail.from.address'),
                        'from_name'     => config('mail.from.name'),
                        'has_api_key'   => (bool) config('brevo.api_key'),
                        'ticket_no'     => $complaint->ticket_number ?? $complaint->id,
                        'old_status'    => $oldStatus,
                        'new_status'    => $newStatus,
                        'app_env'       => config('app.env'),
                    ]);

                    $brevo->sendTransactional(
                        toEmail: $toEmail,
                        toName: $toName,
                        subject: 'Update Status Pengaduan (' . ($complaint->ticket_number ?? $complaint->id) . ')',
                        htmlContent: $html
                    );

                    Log::info('BREVO DEBUG (admin) - complaint sent OK', [
                        'to'        => $toEmail,
                        'ticket_no' => $complaint->ticket_number ?? $complaint->id,
                    ]);
                } catch (\Throwable $e) {
                    Log::error('BREVO DEBUG (admin) - complaint failed', [
                        'to'        => $toEmail,
                        'ticket_no' => $complaint->ticket_number ?? $complaint->id ?? null,
                        'error'     => $e->getMessage(),
                    ]);
                }
            } else {
                Log::warning('Complaint email skipped: pemohon email empty', [
                    'complaint_id'  => $complaint->id,
                    'ticket_no'     => $complaint->ticket_number ?? null,
                    'creator_type'  => $userType,
                    'pemohon_id'    => $pemohon->id ?? null,
                ]);
            }
        }

        // ==========================
        // WhatsApp (manual link)
        // - jika admin centang notify_whatsapp
        // - ATAU notify_user dicentang tapi email kosong
        // ==========================
        $wantEmail = $request->has('notify_user') && ($statusChanged || $notesChanged);
        $wantWa    = $request->has('notify_whatsapp') && ($statusChanged || $notesChanged);

        $shouldPrepareWa = $wantWa || ($wantEmail && empty($toEmail));

        if ($shouldPrepareWa) {
            $waPhone = $this->normalizeWaNumber(
                $pemohon->phone ?? $pemohon->phone_number ?? null
            );

            if (!$waPhone) {
                Log::warning('WA DEBUG (admin) - complaint skipped, phone missing', [
                    'complaint_id'  => $complaint->id,
                    'ticket_no'     => $complaint->ticket_number ?? null,
                    'creator_type'  => $userType,
                    'pemohon_id'    => $pemohon->id ?? null,
                ]);
            } else {
                $ticketNo = $complaint->ticket_number ?? $complaint->id ?? '-';
                $pemohonName = $pemohon->nama_lengkap ?? $pemohon->name ?? 'Bapak/Ibu';

                $msgLines = [
                    "Halo {$pemohonName},",
                    "",
                    "Update status Pengaduan: {$ticketNo}",
                    "Dari: " . $this->statusTextId($oldStatus),
                    "Menjadi: " . $this->statusTextId($newStatus),
                ];

                if (!empty($newNotes)) {
                    $msgLines[] = "";
                    $msgLines[] = "Catatan Admin:";
                    $msgLines[] = trim((string) $newNotes);
                }

                $msgLines[] = "";
                $msgLines[] = "Terima kasih.";

                $waText = implode("\n", $msgLines);
                $waLink = 'https://wa.me/' . $waPhone . '?text=' . rawurlencode($waText);

                $request->session()->flash('wa_link', $waLink);

                Log::info('WA DEBUG (admin) - complaint prepared wa link', [
                    'ticket_no' => $ticketNo,
                    'to_phone'  => $waPhone,
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
            'applicant',
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
            str_contains($contentType, 'jpeg') || str_contains($contentType, 'jpg') => 'jpg',
            default => pathinfo(parse_url($fileUrl, PHP_URL_PATH) ?? '', PATHINFO_EXTENSION) ?: 'jpg',
        };

        $filename = 'KTP-' . ($user->nik ?? $user->id) . '.' . $ext;

        return response($res->body(), 200, [
            'Content-Type' => $contentType,
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
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

    public function archive($id)
    {
        $complaint = Complaint::findOrFail($id);

        if (!in_array($complaint->status, ['selesai', 'ditolak'])) {
            return back()->with('error', 'Hanya pengaduan selesai/ditolak yang bisa diarsipkan.');
        }

        if (\Illuminate\Support\Facades\Schema::hasColumn('complaints', 'archived_at')) {
            $complaint->update(['archived_at' => now()]);
        } elseif (\Illuminate\Support\Facades\Schema::hasColumn('complaints', 'is_archived')) {
            $complaint->update(['is_archived' => true]);
        } else {
            return back()->with('error', 'Kolom arsip belum ada di tabel complaints.');
        }

        return back()->with('success', 'Pengaduan berhasil diarsipkan.');
    }
}
