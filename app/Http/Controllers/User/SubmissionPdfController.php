<?php

namespace App\Http\Controllers\User;

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
        
        // Check authorization - user harus pemilik submission
        if ($submission->user_id !== $user->id) {
            abort(403, 'Unauthorized access.');
        }

        // Load relationships
        $submission->load(['category', 'user']);

        // Determine submission type based on category type
        $submissionType = $this->getSubmissionType($submission->category->type);

        // Generate PDF
        $pdf = Pdf::loadView('pdfs.submission', [
            'submission' => $submission,
            'user' => $user,
            'submissionType' => $submissionType
        ]);

        // Set paper size dan orientation
        $pdf->setPaper('a4', 'portrait');

        // Download dengan nama file sesuai ticket_id
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