<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Consultation;
use Barryvdh\DomPDF\Facade\Pdf;

class ConsultationPdfController extends Controller
{
    public function download(Consultation $consultation)
    {
        $auth = auth()->user();

        if ($consultation->user_id !== $auth->id) {
            abort(403, 'Unauthorized access.');
        }

        $consultation->load(['handler', 'documents', 'user', 'applicant']);

        $applicant = $consultation->applicant;
        $account   = $consultation->user;

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
                'provinsi' => $applicant->provinsi,
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
                'provinsi' => $account->provinsi ?? '',
            ];
        }

        $alamatDetail = trim((string) ($userData->address ?? ''));
        $desa         = trim((string) ($userData->desa ?? ''));
        $kecamatan    = trim((string) ($userData->kecamatan ?? ''));
        $kabupaten    = trim((string) ($userData->kabupaten ?? ''));
        $provinsi     = trim((string) ($userData->provinsi ?? ''));

        $alamatLengkap = collect([
            $alamatDetail ?: null,
            $desa ? ('Desa/Kelurahan ' . $desa) : null,
            $kecamatan ? ('Kec. ' . $kecamatan) : null,
            $kabupaten ?: null,
            $provinsi ?: null,
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