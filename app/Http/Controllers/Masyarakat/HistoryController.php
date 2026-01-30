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

        /* =============================
         * AMBIL DATA DARI 3 TABEL
         * ============================= */
        $submissions = Submission::where('user_id', $user->id)->with('category')->get();
        $consultations = Consultation::where('user_id', $user->id)->with('category')->get();
        $complaints = Complaint::where('user_id', $user->id)->with('category')->get();

        $totalPending =
            $submissions->whereIn('status', ['pending'])->count() +
            $consultations->whereIn('status', ['pending'])->count() +
            $complaints->whereIn('status', ['pending'])->count();

        $totalProcessing =
            $submissions->whereIn('status', ['in_progress'])->count() +
            $consultations->whereIn('status', ['on_progress'])->count() +
            $complaints->whereIn('status', ['diproses'])->count();

        $totalCompleted =
            $submissions->whereIn('status', ['completed'])->count() +
            $consultations->whereIn('status', ['completed'])->count() +
            $complaints->whereIn('status', ['selesai'])->count();

        $totalRejected =
            $submissions->whereIn('status', ['rejected'])->count() +
            $consultations->whereIn('status', ['rejected'])->count() +
            $complaints->whereIn('status', ['ditolak'])->count();

        $totalSubmissions =
            $submissions->count() +
            $consultations->count() +
            $complaints->count();

        $merged = collect();

        foreach ($submissions as $item) {
            $merged->push([
                'ticket_id'   => $item->ticket_id,
                'type'        => 'submission',
                'type_label'  => 'Permohonan Informasi',
                'category'    => $item->category->name ?? '-',
                'title'       => $item->title,
                'date'        => Carbon::parse($item->submitted_at),
                'status'      => $item->status,
                'route'       => route('masyarakat.submissions.show', ['submission' => $item->id, 'from' => 'history']),
            ]);
        }

        foreach ($consultations as $item) {
            $merged->push([
                'ticket_id'   => $item->ticket_number,
                'type'        => 'consultation',
                'type_label'  => 'Konsultasi',
                'category'    => $item->category->name ?? '-',
                'title'       => $item->subject,
                'date'        => Carbon::parse($item->created_at),
                'status'      => $item->status,
                'route'       => route('masyarakat.consultations.show', ['consultation' => $item->id, 'from' => 'history']),
            ]);
        }

        foreach ($complaints as $item) {
            $merged->push([
                'ticket_id'   => $item->ticket_number,
                'type'        => 'complaint',
                'type_label'  => 'Pengaduan',
                'category'    => $item->category->name ?? '-',
                'title'       => $item->subject,
                'date'        => Carbon::parse($item->created_at),
                'status'      => $item->status,
                'route'       => route('masyarakat.complaints.show', ['complaint' => $item->id, 'from' => 'history']),
            ]);
        }

        /* =============================
         * FILTER SEARCH
         * ============================= */
        if ($request->filled('search')) {
            $search = strtolower($request->search);
            $merged = $merged->filter(fn ($item) =>
                str_contains(strtolower($item['ticket_id']), $search) ||
                str_contains(strtolower($item['title']), $search)
            );
        }

        /* =============================
         * FILTER CATEGORY
         * ============================= */
        if ($request->filled('category')) {
            $merged = $merged->where('type', $request->category);
        }

        /* =============================
         * FILTER STATUS
         * ============================= */
        if ($request->filled('status')) {
            $status = $request->status;

            $merged = $merged->filter(function ($item) use ($status) {
                return match ($status) {
                    'pending'   => in_array($item['status'], ['pending']),
                    'diproses'  => in_array($item['status'], ['in_progress', 'on_progress', 'diproses']),
                    'selesai'   => in_array($item['status'], ['completed', 'selesai']),
                    'ditolak'   => in_array($item['status'], ['rejected', 'ditolak']),
                    default     => true,
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

        return view('masyarakat.history.index', [
            'submissions'      => $paginated,
            'totalSubmissions' => $totalSubmissions,
            'totalPending'     => $totalPending,
            'totalProcessing'  => $totalProcessing, 
            'totalCompleted'   => $totalCompleted,
            'totalRejected'    => $totalRejected,
        ]);
    }
}