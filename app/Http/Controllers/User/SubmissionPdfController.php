<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Submission;
use Barryvdh\DomPDF\Facade\Pdf;

class SubmissionPdfController extends Controller
{
    public function download(Submission $submission)
    {
        $auth = auth()->user();

        if ($submission->user_id !== $auth->id) {
            abort(403, 'Unauthorized access.');
        }

        $submission->load(['applicant', 'user']);

        $applicant = $submission->applicant;
        $account   = $submission->user; 

        if ($applicant) {
            $userData = (object)[
                'name' => $applicant->nama_lengkap,
                'nik' => $applicant->nik,
                'email' => $applicant->email,      
                'phone' => $applicant->phone,
                'pekerjaan' => $applicant->pekerjaan,
                'address' => $applicant->alamat_detail,
                'desa' => $applicant->desa_nama,
                'kecamatan' => $applicant->kecamatan_nama,
                'kabupaten' => $applicant->kabupaten_nama,
                'provinsi' => $applicant->provinsi,
                'is_kelurahan' => $applicant->is_kelurahan,
            ];
        } else {
            $userData = (object)[
                'name' => $account->name ?? '-',
                'nik' => $account->nik ?? '-',
                'email' => $account->email ?? null,
                'phone' => $account->phone ?? '-',
                'pekerjaan' => $account->pekerjaan ?? null,
                'address' => $account->alamat_detail ?? ($account->alamat ?? ''),
                'desa' => $account->desa_nama ?? '-',
                'kecamatan' => $account->kecamatan_nama ?? '-',
                'kabupaten' => $account->kabupaten_nama ?? '-',
                'provinsi' => $account->provinsi ?? 'Jawa Tengah',
                'is_kelurahan' => $account->is_kelurahan ?? false,
            ];
        }

        $submissionType = 'PERMOHONAN INFORMASI';

        $pdf = Pdf::loadView('pdfs.masyarakat-submission', [
            'submission' => $submission,
            'user' => $userData,
            'submissionType' => $submissionType
        ]);

        $pdf->setPaper('a4', 'portrait');

        return $pdf->download($submission->ticket_id . '.pdf');
    }
}