<?php

namespace App\Http\Controllers\Masyarakat;

use App\Http\Controllers\Controller;
use App\Models\Submission;
use Barryvdh\DomPDF\Facade\Pdf;

class SubmissionPdfController extends Controller
{
    /**
     * Download submission as PDF
     */
    public function download(Submission $submission)
    {
        $user = auth()->user();
        
        if ($submission->user_id !== $user->id) {
            abort(403, 'Unauthorized access.');
        }

        $submission->load(['category', 'user']);

        $submissionType = $this->getSubmissionType($submission->category->type);

        $pdf = Pdf::loadView('pdfs.masyarakat-submission', [
            'submission' => $submission,
            'user' => $user,
            'submissionType' => $submissionType
        ]);

        $pdf->setPaper('a4', 'portrait');

        return $pdf->download($submission->ticket_id . '.pdf');
    }

    /**
     * Get submission type label based on category type
     */
    private function getSubmissionType($categoryType)
    {
        $types = [
            'permohonan' => 'PERMOHONAN INFORMASI',
            'konsultasi' => 'KONSULTASI',
            'pengaduan' => 'BUAT PENGADUAN',
        ];

        return $types[$categoryType] ?? 'PENGAJUAN';
    }
}
