<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Exports\AllTicketsExport;
use App\Exports\ComplaintsExport;
use App\Exports\ConsultationsExport;
use App\Exports\SubmissionsExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportExportController extends Controller
{
    public function export(Request $request, string $tab)
    {
        $tab = strtolower($tab);

        return match ($tab) {
            'semua'      => Excel::download(new AllTicketsExport($request), 'laporan-semua.xlsx'),
            'konsultasi' => Excel::download(new ConsultationsExport($request), 'laporan-konsultasi.xlsx'),
            'pengaduan'  => Excel::download(new ComplaintsExport($request), 'laporan-pengaduan.xlsx'),
            'permohonan' => Excel::download(new SubmissionsExport($request), 'laporan-permohonan.xlsx'),
            default      => abort(404),
        };
    }
}