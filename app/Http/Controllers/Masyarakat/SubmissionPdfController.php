<?php

namespace App\Http\Controllers\Masyarakat;

use App\Http\Controllers\Controller;
use App\Models\Submission;
use Barryvdh\DomPDF\Facade\Pdf;

class SubmissionPdfController extends Controller
{
    public function download(Submission $submission)
    {
        $user = auth()->user();

        if (!$user || $submission->user_id !== $user->id) {
            abort(403, 'Unauthorized access.');
        }

        $submission->load(['user']);


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

        $submissionType = $this->getSubmissionType($submission->type ?? 'permohonan');

        $pdf = Pdf::loadView('pdfs.masyarakat-submission', [
            'submission'     => $submission,
            'user'           => $user,
            'submissionType' => $submissionType,
            'alamatLengkap'  => $alamatLengkap, // ⬅️ PENTING
        ]);

        $pdf->setPaper('a4', 'portrait');

        return $pdf->download($submission->ticket_id . '.pdf');
    }

    private function getSubmissionType(string $type): string
    {
        return match ($type) {
            'konsultasi' => 'KONSULTASI',
            'pengaduan'  => 'PENGADUAN',
            default      => 'PERMOHONAN INFORMASI',
        };
    }
}
