<?php

namespace App\Http\Controllers\Masyarakat;

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

        $complaint->load(['category', 'handler']);

        $statusRaw = strtolower((string) ($complaint->status ?? 'pending'));
        $statusLabel = match ($statusRaw) {
            'pending', 'belum diproses' => 'Menunggu Proses',
            'diproses', 'in_progress', 'on_progress', 'sedang diproses' => 'Diproses',
            'selesai', 'completed' => 'Selesai',
            'ditolak', 'rejected' => 'Ditolak',
            default => ucfirst($statusRaw),
        };

        $data = [
            'serviceTitle' => 'PENGADUAN',
            'complaint' => $complaint,
            'user' => $user,
            'statusLabel' => $statusLabel,
        ];

        $pdf = Pdf::loadView('pdfs.masyarakat-complaint', $data)->setPaper('a4', 'portrait');

        return $pdf->download(($complaint->ticket_number ?? 'PENGADUAN') . '.pdf');
    }
}