<?php

namespace App\Http\Controllers\User;

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

        $submissions   = Submission::where('user_id', $user->id)->get();
        $consultations = Consultation::where('user_id', $user->id)->get();
        $complaints    = Complaint::where('user_id', $user->id)->get();

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

        $totalSubmissions = $submissions->count() + $consultations->count() + $complaints->count();

        $merged = collect();

        foreach ($submissions as $item) {
            $merged->push([
                'ticket_id'   => $item->ticket_id,
                'type'        => 'submission',
                'type_label'  => 'Permohonan Informasi',
                'category'    => '-', 
                'title'       => $item->title ?? $item->subject ?? '-',
                'date'        => Carbon::parse($item->submitted_at ?? $item->created_at),
                'status'      => $item->status,
                'route'       => route('user.submissions.show', ['submission' => $item->id, 'from' => 'history']),
            ]);
        }

        foreach ($consultations as $item) {
            $merged->push([
                'ticket_id'   => $item->ticket_number ?? $item->ticket_id ?? '-',
                'type'        => 'consultation',
                'type_label'  => 'Konsultasi',
                'category'    => '-', 
                'title'       => $item->subject ?? $item->title ?? '-',
                'date'        => Carbon::parse($item->created_at),
                'status'      => $item->status,
                'route'       => route('user.consultations.show', ['consultation' => $item->id, 'from' => 'history']),
            ]);
        }

        foreach ($complaints as $item) {
            $merged->push([
                'ticket_id'   => $item->ticket_number ?? $item->ticket_id ?? '-',
                'type'        => 'complaint',
                'type_label'  => 'Pengaduan',
                'category'    => '-',
                'title'       => $item->subject ?? $item->title ?? '-',
                'date'        => Carbon::parse($item->created_at),
                'status'      => $item->status,
                'route'       => route('user.complaints.show', ['complaint' => $item->id, 'from' => 'history']),
            ]);
        }

        /* =============================
         * FILTER SEARCH
         * ============================= */
        if ($request->filled('search')) {
            $search = strtolower((string) $request->search);
            $merged = $merged->filter(fn ($item) =>
                str_contains(strtolower((string) $item['ticket_id']), $search) ||
                str_contains(strtolower((string) $item['title']), $search)
            );
        }

        if ($request->filled('category')) {
            $merged = $merged->where('type', (string) $request->category);
        }

        /* =============================
         * FILTER STATUS
         * ============================= */
        if ($request->filled('status')) {
            $status = (string) $request->status;

            $merged = $merged->filter(function ($item) use ($status) {
                return match ($status) {
                    'pending'  => in_array($item['status'], ['pending', 'belum diproses']),
                    'diproses' => in_array($item['status'], ['in_progress', 'on_progress', 'diproses', 'sedang diproses']),
                    'selesai'  => in_array($item['status'], ['completed', 'selesai']),
                    'ditolak'  => in_array($item['status'], ['rejected', 'ditolak']),
                    default    => true,
                };
            });
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

        return view('user.history.index', [
            'submissions'      => $paginated,
            'totalSubmissions' => $totalSubmissions,
            'totalPending'     => $totalPending,
            'totalProcessing'  => $totalProcessing,
            'totalCompleted'   => $totalCompleted,
            'totalRejected'    => $totalRejected,
        ]);
    }
}