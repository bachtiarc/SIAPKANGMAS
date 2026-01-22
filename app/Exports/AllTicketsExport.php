<?php

namespace App\Exports;

use App\Models\Category;
use App\Models\Consultation;
use App\Models\Complaint;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class AllTicketsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    public function __construct(protected Request $request) {}

    public function collection()
    {
        // Base query
        $qConsultation = Consultation::with(['user', 'category']);
        $qComplaint    = Complaint::with(['user', 'category']);
        $qSubmission   = Submission::with(['user', 'category']);

        // Filter tanggal
        if ($this->request->filled('start_date')) {
            $qConsultation->whereDate('created_at', '>=', $this->request->start_date);
            $qComplaint->whereDate('created_at', '>=', $this->request->start_date);
            $qSubmission->whereDate('created_at', '>=', $this->request->start_date);
        }
        if ($this->request->filled('end_date')) {
            $qConsultation->whereDate('created_at', '<=', $this->request->end_date);
            $qComplaint->whereDate('created_at', '<=', $this->request->end_date);
            $qSubmission->whereDate('created_at', '<=', $this->request->end_date);
        }

        // Filter user_type (pegawai/masyarakat)
        if ($this->request->filled('type') && $this->request->type !== 'Semua') {
            $qConsultation->whereHas('user', fn($q) => $q->where('user_type', $this->request->type));
            $qComplaint->whereHas('user', fn($q) => $q->where('user_type', $this->request->type));
            $qSubmission->whereHas('user', fn($q) => $q->where('user_type', $this->request->type));
        }

        // Filter kategori
        if ($this->request->filled('category') && $this->request->category !== 'Semua') {
            $qConsultation->where('category_id', $this->request->category);
            $qComplaint->where('category_id', $this->request->category);
            $qSubmission->where('category_id', $this->request->category);
        }

        // Filter status versi UI: pending | proses | selesai | ditolak | Semua
        if ($this->request->filled('status') && $this->request->status !== 'Semua') {
            $status = $this->request->status;

            if ($status === 'pending') {
                $qConsultation->where('status', 'pending');
                $qComplaint->where('status', 'pending');
                $qSubmission->where('status', 'pending');
            }

            if ($status === 'proses') {
                $qConsultation->where('status', 'on_progress');
                $qComplaint->where('status', 'diproses');
                $qSubmission->where('status', 'in_progress');
            }

            if ($status === 'selesai') {
                $qConsultation->whereIn('status', ['completed', 'rejected']);
                $qSubmission->whereIn('status', ['completed', 'rejected']);
                $qComplaint->where('status', 'selesai');
            }

            if ($status === 'ditolak') {
                $qConsultation->where('status', 'rejected');
                $qSubmission->where('status', 'rejected');
                $qComplaint->where('status', 'ditolak');
            }
        }

        $consultations = $qConsultation->latest()->get()->map(fn($x) => (object)[
            'layanan'    => 'Konsultasi',
            'id_tiket'   => $x->ticket_id ?? $x->ticket_number ?? $x->id,
            'tanggal'    => $x->created_at,
            'nama'       => $x->user->name ?? '-',
            'email'      => $x->user->email ?? '-',
            'jenis'      => $x->user->user_type ?? '-',
            'kategori'   => $x->category->name ?? '-',
            'subjek'     => $x->subject ?? $x->title ?? '-',
            'status'     => $x->status ?? '-',
        ]);

        $complaints = $qComplaint->latest()->get()->map(fn($x) => (object)[
            'layanan'    => 'Pengaduan',
            'id_tiket'   => $x->ticket_number ?? $x->ticket_id ?? $x->id,
            'tanggal'    => $x->created_at,
            'nama'       => $x->user->name ?? '-',
            'email'      => $x->user->email ?? '-',
            'jenis'      => $x->user->user_type ?? '-',
            'kategori'   => $x->category->name ?? '-',
            'subjek'     => $x->subject ?? $x->title ?? '-',
            'status'     => $x->status ?? '-',
        ]);

        $submissions = $qSubmission->latest()->get()->map(fn($x) => (object)[
            'layanan'    => 'Permohonan Informasi',
            'id_tiket'   => $x->ticket_id ?? $x->ticket_number ?? $x->id,
            'tanggal'    => $x->created_at,
            'nama'       => $x->user->name ?? '-',
            'email'      => $x->user->email ?? '-',
            'jenis'      => $x->user->user_type ?? '-',
            'kategori'   => $x->category->name ?? '-',
            'subjek'     => $x->title ?? $x->subject ?? '-',
            'status'     => $x->status ?? '-',
        ]);

        return $consultations
            ->concat($complaints)
            ->concat($submissions)
            ->sortByDesc(fn($x) => $x->tanggal)
            ->values();
    }

    public function headings(): array
    {
        return [
            'Layanan',
            'ID Tiket',
            'Tanggal Pengajuan',
            'Nama Pelapor',
            'Email Pelapor',
            'Jenis Pelapor',
            'Kategori',
            'Subjek',
            'Status',
        ];
    }

    public function map($row): array
    {
        return [
            $row->layanan,
            $row->id_tiket,
            optional($row->tanggal)->format('d-m-Y'),
            $row->nama,
            $row->email,
            ucfirst($row->jenis),
            $row->kategori,
            $row->subjek,
            $row->status,
        ];
    }
}