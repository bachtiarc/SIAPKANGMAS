<?php

namespace App\Http\Controllers\User;

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
        
        if ($complaint->user_id !== $user->id) {
            abort(403, 'Unauthorized access.');
        }
        $complaint->load(['category', 'handler', 'user', 'documents']);

        $pdf = Pdf::loadView('pdfs.submission', [
            'submission' => $complaint,  
            'user' => $user,
            'submissionType' => 'PENGADUAN'
        ]);

        $pdf->setPaper('a4', 'portrait');

        return $pdf->download($complaint->ticket_number . '.pdf');
    }
}