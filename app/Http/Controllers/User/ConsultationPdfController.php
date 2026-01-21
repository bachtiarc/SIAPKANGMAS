<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Consultation;
use Barryvdh\DomPDF\Facade\Pdf;

class ConsultationPdfController extends Controller
{
    /**
     * Download consultation as PDF
     */
    public function download(Consultation $consultation)
    {
        $user = auth()->user();
        
        // Authorization check
        if ($consultation->user_id !== $user->id) {
            abort(403, 'Unauthorized access.');
        }

        // Load relationships
        $consultation->load(['category', 'handler', 'user']);

        // Generate PDF using the same view as pdfs.submission
        $pdf = Pdf::loadView('pdfs.submission', [
            'submission' => $consultation, 
            'user' => $user,
            'submissionType' => 'KONSULTASI'
        ]);

        // Set paper size
        $pdf->setPaper('a4', 'portrait');

        // Download dengan nama file sesuai ticket_number
        return $pdf->download($consultation->ticket_number . '.pdf');
    }
}