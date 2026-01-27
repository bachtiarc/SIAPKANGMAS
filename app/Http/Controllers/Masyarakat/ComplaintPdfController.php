<?php

namespace App\Http\Controllers\Masyarakat;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use Barryvdh\DomPDF\Facade\Pdf;

class ComplaintPdfController extends Controller
{
    /**
     * Download complaint as PDF
     */
    public function download(Complaint $complaint)
    {
        $user = auth()->user();
        
        // Authorization check
        if ($complaint->user_id !== $user->id) {
            abort(403, 'Unauthorized access.');
        }

        // Load relationships
        $complaint->load(['category', 'handler', 'user', 'documents']);

        // Generate PDF using the same view as pdfs.submission
        $pdf = Pdf::loadView('pdfs.submission', [
            'submission' => $complaint,  // Pakai variable 'submission' biar kompatibel
            'user' => $user,
            'submissionType' => 'PENGADUAN'
        ]);

        // Set paper size
        $pdf->setPaper('a4', 'portrait');

        // Download dengan nama file sesuai ticket_number
        return $pdf->download($complaint->ticket_number . '.pdf');
    }
}