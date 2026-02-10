<?php

namespace App\Http\Controllers\Masyarakat;

use App\Http\Controllers\Controller;
use App\Models\Consultation;
use Barryvdh\DomPDF\Facade\Pdf;

class ConsultationPdfController extends Controller
{
    /**
     * Download consultation as PDF for masyarakat
     */
    public function download(Consultation $consultation)
    {
        $user = auth()->user();

        if ($consultation->user_id !== $user->id) {
            abort(403, 'Unauthorized access.');
        }

        if ($user->user_type !== 'masyarakat_umum') {
            abort(403, 'Unauthorized access.');
        }
        
        $consultation->load(['handler', 'user', 'documents']);

        $alamatDetail = trim($user->address ?? '');
        $desa         = trim($user->desa ?? '');
        $kecamatan    = trim($user->kecamatan ?? '');
        $kabupaten    = trim($user->kabupaten ?? '');
        $provinsi     = $user->provinsi ?? 'Jawa Tengah';

        $isKota = str_contains(strtolower($kabupaten), 'kota');

        $kabLabel = $isKota
            ? 'Kota ' . trim(str_ireplace('kota', '', $kabupaten))
            : 'Kab. ' . $kabupaten;

        $desaLabel = $isKota
            ? 'Kelurahan ' . $desa
            : 'Desa ' . $desa;

        $alamatLengkap = collect([
            $alamatDetail ?: null,
            $desa ? $desaLabel : null,
            $kecamatan ? 'Kec. ' . $kecamatan : null,
            $kabupaten ? $kabLabel : null,
            $provinsi,
        ])->filter()->implode(', ');

        $pdf = Pdf::loadView('pdfs.masyarakat-consultation', [
            'consultation'  => $consultation,
            'user'          => $user,
            'alamatLengkap' => $alamatLengkap,
        ]);

        $pdf->setPaper('a4', 'portrait');

        return $pdf->download($consultation->ticket_number . '.pdf');
    }
}