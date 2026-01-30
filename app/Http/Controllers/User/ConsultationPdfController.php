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
        
        if ($consultation->user_id !== $user->id) {
            abort(403, 'Unauthorized access.');
        }

        $consultation->load(['category', 'handler', 'user']);

        $pdf = Pdf::loadView('pdfs.submission', [
            'submission' => $consultation, 
            'user' => $user,
            'submissionType' => 'KONSULTASI'
        ]);

        $pdf->setPaper('a4', 'portrait');

        return $pdf->download($consultation->ticket_number . '.pdf');
    }
}