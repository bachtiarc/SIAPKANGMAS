<?php

namespace App\Http\Controllers\Masyarakat;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Submission;
use App\Models\Consultation;
use App\Models\Complaint;

class HistoryController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        $q = trim((string) $request->get('q', ''));

        $submissions = Submission::where('user_id', $user->id)
            ->when($q, function ($query) use ($q) {
                $query->where(function ($qq) use ($q) {
                    $qq->where('ticket_id', 'like', "%{$q}%")
                       ->orWhere('subject', 'like', "%{$q}%");
                });
            })
            ->latest()
            ->get()
            ->map(function ($item) {
                $item->service_type = 'Permohonan Informasi';
                $item->ticket_show  = $item->ticket_id ?? $item->ticket_number ?? '-';
                return $item;
            });

        $consultations = Consultation::where('user_id', $user->id)
            ->when($q, function ($query) use ($q) {
                $query->where(function ($qq) use ($q) {
                    $qq->where('ticket_number', 'like', "%{$q}%")
                       ->orWhere('subject', 'like', "%{$q}%");
                });
            })
            ->latest()
            ->get()
            ->map(function ($item) {
                $item->service_type = 'Konsultasi';
                $item->ticket_show  = $item->ticket_number ?? $item->ticket_id ?? '-';
                return $item;
            });

        $complaints = Complaint::where('user_id', $user->id)
            ->when($q, function ($query) use ($q) {
                $query->where(function ($qq) use ($q) {
                    $qq->where('ticket_number', 'like', "%{$q}%")
                       ->orWhere('subject', 'like', "%{$q}%");
                });
            })
            ->latest()
            ->get()
            ->map(function ($item) {
                $item->service_type = 'Pengaduan';
                $item->ticket_show  = $item->ticket_number ?? $item->ticket_id ?? '-';
                return $item;
            });

        $totalPending =
            $submissions->whereIn('status', ['pending'])->count() +
            $consultations->whereIn('status', ['pending'])->count() +
            $complaints->whereIn('status', ['pending', 'belum diproses'])->count();

        $totalProcessing =
            $submissions->whereIn('status', ['in_progress', 'on_progress'])->count() +
            $consultations->whereIn('status', ['in_progress', 'on_progress'])->count() +
            $complaints->whereIn('status', ['diproses', 'in_progress', 'on_progress', 'sedang diproses'])->count();

        $totalFinished =
            $submissions->whereIn('status', ['completed', 'selesai', 'rejected', 'ditolak'])->count() +
            $consultations->whereIn('status', ['completed', 'selesai', 'rejected', 'ditolak'])->count() +
            $complaints->whereIn('status', ['completed', 'selesai', 'rejected', 'ditolak'])->count();

        $histories = $submissions
            ->merge($consultations)
            ->merge($complaints)
            ->sortByDesc('created_at')
            ->values();

        return view('masyarakat.history.index', compact(
            'histories',
            'q',
            'totalPending',
            'totalProcessing',
            'totalFinished'
        ));
    }
}