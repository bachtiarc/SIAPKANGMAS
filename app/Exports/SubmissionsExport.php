<?php

namespace App\Exports;

use App\Models\Submission;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class SubmissionsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    public function __construct(protected Request $request) {}

    public function collection()
    {
        $q = Submission::with(['user','category'])->latest();

        if ($this->request->filled('start_date')) $q->whereDate('created_at', '>=', $this->request->start_date);
        if ($this->request->filled('end_date'))   $q->whereDate('created_at', '<=', $this->request->end_date);

        if ($this->request->filled('type') && $this->request->type !== 'Semua') {
            $q->whereHas('user', fn($u) => $u->where('user_type', $this->request->type));
        }

        if ($this->request->filled('category') && $this->request->category !== 'Semua') {
            $q->where('category_id', $this->request->category);
        }

        // di tab permohonan kamu pakai value: pending | in_progress | completed
        if ($this->request->filled('status') && $this->request->status !== 'Semua') {
            $q->where('status', $this->request->status);
        }

        return $q->get();
    }

    public function headings(): array
    {
        return ['ID Tiket','Tanggal','Nama','Email','Jenis Pelapor','Kategori','Judul','Status'];
    }

    public function map($item): array
    {
        return [
            $item->ticket_id ?? $item->ticket_number ?? $item->id,
            optional($item->created_at)->format('d-m-Y'),
            $item->user->name ?? '-',
            $item->user->email ?? '-',
            ucfirst($item->user->user_type ?? '-'),
            $item->category->name ?? '-',
            $item->title ?? $item->subject ?? '-',
            $item->status ?? '-',
        ];
    }
}
