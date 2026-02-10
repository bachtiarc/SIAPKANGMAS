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

        $complaint->load(['handler', 'user', 'documents']);

        $statusRaw = strtolower((string) ($complaint->status ?? 'pending'));
        $statusLabel = match ($statusRaw) {
            'pending', 'belum diproses' => 'Menunggu Proses',
            'diproses', 'in_progress', 'on_progress', 'sedang diproses' => 'Diproses',
            'selesai', 'completed' => 'Selesai',
            'ditolak', 'rejected' => 'Ditolak',
            default => ucfirst($statusRaw),
        };

        $alamatDetail = trim((string) ($user->address ?? ''));
        $desa         = trim((string) ($user->desa ?? ''));
        $kecamatan    = trim((string) ($user->kecamatan ?? ''));
        $kabupaten    = trim((string) ($user->kabupaten ?? ''));
        $provinsi     = trim((string) ($user->provinsi ?? 'Jawa Tengah'));
        $isKota = str_contains(strtolower($kabupaten), 'kota');

        $kabLabel = $kabupaten
            ? ($isKota
                ? 'Kota ' . trim(str_ireplace('kota', '', $kabupaten))
                : 'Kab. ' . $kabupaten
              )
            : null;

        $isKelurahan = (bool) ($user->is_kelurahan ?? false);

        $desaLabel = $desa
            ? ($isKelurahan ? 'Kelurahan ' . $desa : 'Desa ' . $desa)
            : null;

        $alamatLengkap = collect([
            $alamatDetail ?: null,
            $desaLabel,
            $kecamatan ? 'Kec. ' . $kecamatan : null,
            $kabLabel,
            $provinsi ?: null,
        ])->filter()->implode(', ');

        $data = [
            'serviceTitle'   => 'PENGADUAN',
            'complaint'      => $complaint,
            'user'           => $user,
            'statusLabel'    => $statusLabel,
            'alamatLengkap'  => $alamatLengkap,
        ];

        $pdf = Pdf::loadView('pdfs.masyarakat-complaint', $data)
            ->setPaper('a4', 'portrait');

        return $pdf->download(($complaint->ticket_number ?? 'PENGADUAN') . '.pdf');
    }
}