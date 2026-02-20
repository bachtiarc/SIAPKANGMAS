<?php

namespace App\Http\Controllers\Masyarakat;

use App\Http\Controllers\Controller;
use App\Models\Submission;
use Barryvdh\DomPDF\Facade\Pdf;

class SubmissionPdfController extends Controller
{
    public function download(Submission $submission)
    {
        $user = auth()->user();

        if (!$user || $submission->user_id !== $user->id) {
            abort(403, 'Unauthorized access.');
        }

        $submission->load(['user']);

        $alamatDetail = trim((string) ($user->address ?? ''));
        $desa         = trim((string) ($user->desa ?? ''));
        $kecamatan    = trim((string) ($user->kecamatan ?? ''));
        $kabupaten    = trim((string) ($user->kabupaten ?? ''));
        $provinsi     = trim((string) ($user->provinsi ?? ''));

        $submissionType = $this->getSubmissionType($submission->type ?? 'permohonan');

        $pdf = Pdf::loadView('pdfs.masyarakat-submission', [
            'submission'     => $submission,
            'user'           => $user,
            'submissionType' => $submissionType,
            'alamat_provinsi'  => $provinsi ?: '-',
            'alamat_kabupaten' => $kabupaten ?: '-',
            'alamat_kecamatan' => $kecamatan ?: '-',
            'alamat_desa'      => $desa ?: '-',
            'alamat_detail'    => $alamatDetail ?: '-',
        ]);

        $pdf->setPaper('a4', 'portrait');

        return $pdf->download($submission->ticket_id . '.pdf');
    }

    private function getSubmissionType(string $type): string
    {
        return match ($type) {
            'konsultasi' => 'KONSULTASI',
            'pengaduan'  => 'PENGADUAN',
            default      => 'PERMOHONAN INFORMASI',
        };
    }
}