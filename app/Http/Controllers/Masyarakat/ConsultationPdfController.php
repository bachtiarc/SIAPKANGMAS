<?php

namespace App\Http\Controllers\Masyarakat;

use App\Http\Controllers\Controller;
use App\Models\Consultation;
use Barryvdh\DomPDF\Facade\Pdf;

class ConsultationPdfController extends Controller
{
    public function download(Consultation $consultation)
    {
        $user = auth()->user();

        if (!$user || $consultation->user_id !== $user->id) {
            abort(403, 'Unauthorized access.');
        }

        if ($user->user_type !== 'masyarakat_umum') {
            abort(403, 'Unauthorized access.');
        }

        $consultation->load(['handler', 'user', 'documents']);
        $alamatDetail = trim((string) ($user->address ?? ''));
        $desa         = trim((string) ($user->desa ?? ''));
        $kecamatan    = trim((string) ($user->kecamatan ?? ''));
        $kabupaten    = trim((string) ($user->kabupaten ?? ''));
        $provinsi     = trim((string) ($user->provinsi ?? ''));

        $pdf = Pdf::loadView('pdfs.masyarakat-consultation', [
            'consultation'  => $consultation,
            'user'          => $user,
            'alamatDetail'  => $alamatDetail,
            'desa'          => $desa,
            'kecamatan'     => $kecamatan,
            'kabupaten'     => $kabupaten,
            'provinsi'      => $provinsi,
        ]);

        $pdf->setPaper('a4', 'portrait');

        return $pdf->download($consultation->ticket_number . '.pdf');
    }
}