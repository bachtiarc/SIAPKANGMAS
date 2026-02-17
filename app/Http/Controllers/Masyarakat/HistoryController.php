<?php

namespace App\Http\Controllers\Masyarakat;

use App\Http\Controllers\Controller;
use App\Models\Submission;
use App\Models\Consultation;
use App\Models\Complaint;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;

class HistoryController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        $q = trim((string) $request->get('q', ''));
        $jenis = trim((string) $request->get('jenis', '')); 
        $status = trim((string) $request->get('status', '')); 
        $submissions = Submission::where('user_id', $user->id)->get();
        $consultations = Consultation::where('user_id', $user->id)->get();
        $complaints = Complaint::where('user_id', $user->id)->get();

        $totalPending =
            $submissions->whereIn('status', ['pending', 'belum diproses'])->count() +
            $consultations->whereIn('status', ['pending', 'belum diproses'])->count() +
            $complaints->whereIn('status', ['pending', 'belum diproses'])->count();

        $totalProcessing =
            $submissions->whereIn('status', ['in_progress', 'on_progress', 'diproses', 'sedang diproses'])->count() +
            $consultations->whereIn('status', ['in_progress', 'on_progress', 'diproses', 'sedang diproses'])->count() +
            $complaints->whereIn('status', ['in_progress', 'on_progress', 'diproses', 'sedang diproses'])->count();

        $totalCompleted =
            $submissions->whereIn('status', ['completed', 'selesai'])->count() +
            $consultations->whereIn('status', ['completed', 'selesai'])->count() +
            $complaints->whereIn('status', ['completed', 'selesai'])->count();

        $totalRejected =
            $submissions->whereIn('status', ['rejected', 'ditolak'])->count() +
            $consultations->whereIn('status', ['rejected', 'ditolak'])->count() +
            $complaints->whereIn('status', ['rejected', 'ditolak'])->count();

        $totalAll =
            $submissions->count() +
            $consultations->count() +
            $complaints->count();
        $merged = collect();

        foreach ($submissions as $item) {
            $date = $item->submitted_at ?? $item->created_at;

            $merged->push([
                'id'          => $item->id,
                'ticket_id'   => (string) ($item->ticket_id ?? '-'),
                'type'        => 'submission',
                'type_label'  => 'Permohonan Informasi',
                'title'       => (string) ($item->title ?? $item->subject ?? '-'),
                'date'        => Carbon::parse($date),
                'status'      => (string) ($item->status ?? ''),
                'route'       => route('masyarakat.submissions.show', $item->id),
            ]);
        }

        foreach ($consultations as $item) {
            $merged->push([
                'id'          => $item->id,
                'ticket_id'   => (string) ($item->ticket_number ?? $item->ticket_id ?? '-'),
                'type'        => 'consultation',
                'type_label'  => 'Konsultasi',
                'title'       => (string) ($item->subject ?? $item->title ?? '-'),
                'date'        => Carbon::parse($item->created_at),
                'status'      => (string) ($item->status ?? ''),
                'route'       => route('masyarakat.consultations.show', $item->id),
            ]);
        }

        foreach ($complaints as $item) {
            $merged->push([
                'id'          => $item->id,
                'ticket_id'   => (string) ($item->ticket_number ?? $item->ticket_id ?? '-'),
                'type'        => 'complaint',
                'type_label'  => 'Pengaduan',
                'title'       => (string) ($item->subject ?? $item->title ?? '-'),
                'date'        => Carbon::parse($item->created_at),
                'status'      => (string) ($item->status ?? ''),
                'route'       => route('masyarakat.complaints.show', $item->id),
            ]);
        }

        if ($q !== '') {
            $qLower = strtolower($q);

            $merged = $merged->filter(function ($row) use ($qLower) {
                return str_contains(strtolower((string) $row['ticket_id']), $qLower)
                    || str_contains(strtolower((string) $row['title']), $qLower);
            })->values();
        }

        if ($jenis !== '') {
            $merged = $merged->where('type', $jenis)->values();
        }

        if ($status !== '') {
            $merged = $merged->filter(function ($row) use ($status) {
                $st = strtolower((string) ($row['status'] ?? ''));

                return match ($status) {
                    'pending'  => in_array($st, ['pending', 'belum diproses']),
                    'diproses' => in_array($st, ['in_progress', 'on_progress', 'diproses', 'sedang diproses']),
                    'selesai'  => in_array($st, ['completed', 'selesai']),
                    'ditolak'  => in_array($st, ['rejected', 'ditolak']),
                    default    => true,
                };
            })->values();
        }

        $merged = $merged->sortByDesc('date')->values();

        $page = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 10;

        $paginated = new LengthAwarePaginator(
            $merged->slice(($page - 1) * $perPage, $perPage)->values(),
            $merged->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('masyarakat.history.index', [
            'histories'       => $paginated,
            'q'               => $q,

            'totalAll'        => $totalAll,
            'totalPending'    => $totalPending,
            'totalProcessing' => $totalProcessing,
            'totalCompleted'  => $totalCompleted,
            'totalRejected'   => $totalRejected,
        ]);
    }
}