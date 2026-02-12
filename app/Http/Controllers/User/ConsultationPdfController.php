<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Consultation;
use Barryvdh\DomPDF\Facade\Pdf;

class ConsultationPdfController extends Controller
{
    /**
     * Download consultation as PDF
     * - Jika ada consultation_applicant: gunakan data applicant (CO ADMIN) TANPA fallback ke akun admin
     * - Jika tidak ada applicant: gunakan data user (masyarakat)
     */
    public function download(Consultation $consultation)
    {
        $auth = auth()->user();

        // Ownership check (biar masyarakat tetap bisa, co admin juga bisa kalau dia pemilik record tsb)
        if ($consultation->user_id !== $auth->id) {
            abort(403, 'Unauthorized access.');
        }

        // Load relasi yang dibutuhkan
        $consultation->load(['handler', 'documents', 'user', 'applicant']);

        $applicant = $consultation->applicant; // consultation_applicants (CO ADMIN)
        $account   = $consultation->user;      // users (masyarakat)

        // RULE PENTING:
        // Kalau applicant ADA -> PAKAI applicant FULL, email opsional (kalau null nanti '-' di blade)
        // Kalau applicant TIDAK ADA -> baru pakai users
        if ($applicant) {
            $userData = (object)[
                'name' => $applicant->nama_lengkap,
                'nik' => $applicant->nik,
                'email' => $applicant->email,      // opsional
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
            // masyarakat (akun sendiri) -> ambil dari users
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

        // Format alamat lengkap
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

        $pdf = Pdf::loadView('pdfs.masyarakat-consultation', [
            'consultation'  => $consultation,
            'user'          => $userData,
            'alamatLengkap' => $alamatLengkap,
        ]);

        $pdf->setPaper('a4', 'portrait');

        return $pdf->download(($consultation->ticket_number ?? 'konsultasi') . '.pdf');
    }
}