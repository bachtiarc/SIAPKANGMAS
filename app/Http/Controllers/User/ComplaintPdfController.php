<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use Barryvdh\DomPDF\Facade\Pdf;

class ComplaintPdfController extends Controller
{
    /**
     * Download complaint as PDF
     * - Jika ada complaint_applicant: gunakan data applicant (CO ADMIN) TANPA fallback ke akun admin
     * - Jika tidak ada applicant: gunakan data user (masyarakat)
     */
    public function download(Complaint $complaint)
    {
        $auth = auth()->user();

        if ((int) $complaint->user_id !== (int) $auth->id) {
            abort(403, 'Unauthorized access.');
        }

        // butuh user untuk fallback masyarakat
        $complaint->load(['handler', 'documents', 'applicant', 'user']);

        $applicant = $complaint->applicant; // complaint_applicants (CO ADMIN)
        $account   = $complaint->user;      // users (masyarakat)

        if ($applicant) {
            $userData = (object)[
                'name' => $applicant->nama_lengkap,
                'nik' => $applicant->nik,
                'email' => $applicant->email, // opsional
                'phone' => $applicant->phone,
                'pekerjaan' => $applicant->pekerjaan ?? null,

                'address' => $applicant->alamat_detail,
                'desa' => $applicant->desa_nama,
                'kecamatan' => $applicant->kecamatan_nama,
                'kabupaten' => $applicant->kabupaten_nama,
                'provinsi' => $applicant->provinsi ?? 'Jawa Tengah',
                'is_kelurahan' => (bool) $applicant->is_kelurahan,
            ];
        } else {
            $userData = (object)[
                'name' => $account->name ?? '-',
                'nik' => $account->nik ?? '-',
                'email' => $account->email ?? null,
                'phone' => $account->phone ?? '-',
                'pekerjaan' => $account->pekerjaan ?? null,

                'address' => $account->address ?? '',
                'desa' => $account->desa ?? '',
                'kecamatan' => $account->kecamatan ?? '',
                'kabupaten' => $account->kabupaten ?? '',
                'provinsi' => $account->provinsi ?? 'Jawa Tengah',
                'is_kelurahan' => (bool) ($account->is_kelurahan ?? false),
            ];
        }

        // format alamat lengkap (samain konsultasi)
        $alamatDetail = trim((string) ($userData->address ?? ''));
        $desa         = trim((string) ($userData->desa ?? ''));
        $kecamatan    = trim((string) ($userData->kecamatan ?? ''));
        $kabupaten    = trim((string) ($userData->kabupaten ?? ''));
        $provinsi     = $userData->provinsi ?? 'Jawa Tengah';

        $isKota = str_contains(strtolower($kabupaten), 'kota');

        $kabLabel = $kabupaten
            ? ($isKota
                ? 'Kota ' . trim(str_ireplace('kota', '', $kabupaten))
                : 'Kab. ' . $kabupaten)
            : null;

        $desaLabel = null;
        if ($desa) {
            $desaLabel = ($userData->is_kelurahan ?? false)
                ? 'Kelurahan ' . $desa
                : 'Desa ' . $desa;
        }

        $alamatLengkap = collect([
            $alamatDetail ?: null,
            $desaLabel,
            $kecamatan ? 'Kec. ' . $kecamatan : null,
            $kabLabel,
            $provinsi,
        ])->filter()->implode(', ');

        // status label (biar blade lama kamu tetap jalan)
        $status = strtolower((string) ($complaint->status ?? ''));
        $statusLabel = match ($status) {
            'pending', 'belum diproses' => 'Menunggu Diproses',
            'diproses', 'in_progress', 'on_progress', 'sedang diproses' => 'Sedang Diproses',
            'selesai', 'completed' => 'Selesai',
            'ditolak', 'rejected' => 'Ditolak',
            default => ucfirst((string) ($complaint->status ?? '-')),
        };

        $pdf = Pdf::loadView('pdfs.masyarakat-complaint', [
            'complaint' => $complaint,
            'user' => $userData,
            'alamatLengkap' => $alamatLengkap,
            'statusLabel' => $statusLabel,
        ]);

        $pdf->setPaper('a4', 'portrait');

        return $pdf->download(($complaint->ticket_number ?? 'pengaduan') . '.pdf');
    }
}