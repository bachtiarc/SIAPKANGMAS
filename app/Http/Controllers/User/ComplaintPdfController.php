<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use Barryvdh\DomPDF\Facade\Pdf;

class ComplaintPdfController extends Controller
{
    public function download(Complaint $complaint)
    {
        $user = auth()->user();

        if ((int) $complaint->user_id !== (int) $user->id) {
            abort(403, 'Unauthorized access.');
        }
        $complaint->load(['applicant', 'handler', 'documents']);
        $pdf = Pdf::loadView('user.complaints.show', [
            'complaint' => $complaint,
            'isPdf'     => true, 
        ]);

        $pdf->setPaper('a4', 'portrait');

        return $pdf->download($complaint->ticket_number . '.pdf');
    }
}