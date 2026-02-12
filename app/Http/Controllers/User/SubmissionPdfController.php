<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Submission;
use Barryvdh\DomPDF\Facade\Pdf;

class SubmissionPdfController extends Controller
{
    public function download(Submission $submission)
    {
        $user = auth()->user();

        if ($submission->user_id !== $user->id) {
            abort(403, 'Unauthorized access.');
        }

        $submission->load(['applicant']);

        $applicant = $submission->applicant;

        $userData = (object)[
            'name' => $applicant->nama_lengkap,
            'nik' => $applicant->nik,
            'email' => $applicant->email,
            'phone' => $applicant->phone,
            'address' => $applicant->alamat_detail,
            'desa' => $applicant->desa_nama,
            'kecamatan' => $applicant->kecamatan_nama,
            'kabupaten' => $applicant->kabupaten_nama,
            'provinsi' => $applicant->provinsi,
            'is_kelurahan' => $applicant->is_kelurahan,
        ];

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