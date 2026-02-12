<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Consultation;
use Barryvdh\DomPDF\Facade\Pdf;

class ConsultationPdfController extends Controller
{
    public function download(Consultation $consultation)
    {
        $user = auth()->user();

        if ($user->user_type !== 'pegawai') {
            abort(403, 'Unauthorized access.');
        }

        if ((int) $consultation->user_id !== (int) $user->id) {
            abort(403, 'Unauthorized access.');
        }

        $consultation->load(['handler', 'documents', 'applicant']);

        $app = $consultation->applicant;

        $userData = (object)[
            'name' => $app?->nama_lengkap,
            'nik' => $app?->nik,
            'email' => $app?->email,
            'phone' => $app?->phone,
            'address' => $app?->alamat_detail,
            'desa' => $app?->desa_nama,
            'kecamatan' => $app?->kecamatan_nama,
            'kabupaten' => $app?->kabupaten_nama,
            'provinsi' => $app?->provinsi ?? 'Jawa Tengah',
            'is_kelurahan' => (bool) ($app?->is_kelurahan ?? false),
        ];

        $pdf = Pdf::loadView('pdfs.masyarakat-consultation', [
            'consultation' => $consultation,
            'user' => $userData,
        ]);

        return $pdf->download("Konsultasi-{$consultation->ticket_number}.pdf");
    }
}