<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use Barryvdh\DomPDF\Facade\Pdf;

class ComplaintPdfController extends Controller
{
    public function download(Complaint $complaint)
    {
        $auth = auth()->user();

        if ((int) $complaint->user_id !== (int) $auth->id) {
            abort(403, 'Unauthorized access.');
        }

        $complaint->load(['handler', 'documents', 'applicant', 'user']);

        $applicant = $complaint->applicant;
        $account   = $complaint->user;

        if ($applicant) {
            $userData = (object)[
                'name' => $applicant->nama_lengkap,
                'nik' => $applicant->nik,
                'email' => $applicant->email,
                'phone' => $applicant->phone,
                'pekerjaan' => $applicant->pekerjaan ?? null,

                'address' => $applicant->alamat_detail,
                'desa' => $applicant->desa_nama,
                'kecamatan' => $applicant->kecamatan_nama,
                'kabupaten' => $applicant->kabupaten_nama,
                'provinsi' => $applicant->provinsi ?? '-',
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
                'provinsi' => $account->provinsi ?? '-',
            ];
        }

        // alamat lengkap: DESA/KELURAHAN netral
        $alamatDetail = trim((string) ($userData->address ?? ''));
        $desa         = trim((string) ($userData->desa ?? ''));
        $kecamatan    = trim((string) ($userData->kecamatan ?? ''));
        $kabupaten    = trim((string) ($userData->kabupaten ?? ''));
        $provinsi     = $userData->provinsi ?? '-';

        $isKotaKab = str_starts_with(strtolower($kabupaten), 'kota');

        $kabClean = $isKotaKab
            ? trim(preg_replace('/^kota\s+/i', '', $kabupaten))
            : trim(preg_replace('/^kab\.?\s+/i', '', $kabupaten));

        $kabLabel = $kabupaten
            ? ($isKotaKab ? ('Kota ' . $kabClean) : ('Kab. ' . $kabClean))
            : null;

        $desaLabel = $desa ? ('Desa/Kelurahan ' . $desa) : null;

        $alamatLengkap = collect([
            $alamatDetail ?: null,
            $desaLabel,
            $kecamatan ? 'Kec. ' . $kecamatan : null,
            $kabLabel,
            $provinsi,
        ])->filter()->implode(', ');

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