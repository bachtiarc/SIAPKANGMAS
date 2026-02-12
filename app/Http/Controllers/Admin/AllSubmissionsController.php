<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Consultation;
use App\Models\Complaint;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Schema;

class AllSubmissionsController extends Controller
{
    public function index(Request $request)
    {
        $stats = [
            'total' => Consultation::count() + Complaint::count() + Submission::count(),
            'proses' =>
                Consultation::where('status', 'on_progress')->count()
                + Complaint::where('status', 'diproses')->count()
                + Submission::where('status', 'in_progress')->count(),
            'selesai' =>
                Consultation::whereIn('status', ['completed', 'rejected'])->count()
                + Complaint::whereIn('status', ['selesai', 'ditolak'])->count()
                + Submission::whereIn('status', ['completed', 'rejected'])->count(),
            'belum' =>
                Consultation::where('status', 'pending')->count()
                + Complaint::where('status', 'pending')->count()
                + Submission::where('status', 'pending')->count(),
        ];

        $qConsultation = Consultation::with(['user']);
        $qComplaint    = Complaint::with(['user']);
        $qSubmission   = Submission::with(['user']);

        // ================= FILTER TANGGAL =================
        $hasStart = $request->filled('start_date');
        $hasEnd   = $request->filled('end_date');

        if ($hasStart && $hasEnd) {
            $qConsultation->whereBetween('created_at', [
                $request->start_date . ' 00:00:00',
                $request->end_date   . ' 23:59:59',
            ]);
            $qComplaint->whereBetween('created_at', [
                $request->start_date . ' 00:00:00',
                $request->end_date   . ' 23:59:59',
            ]);
            $qSubmission->whereBetween('created_at', [
                $request->start_date . ' 00:00:00',
                $request->end_date   . ' 23:59:59',
            ]);
        } elseif ($hasStart) {
            $qConsultation->whereDate('created_at', $request->start_date);
            $qComplaint->whereDate('created_at', $request->start_date);
            $qSubmission->whereDate('created_at', $request->start_date);
        } elseif ($hasEnd) {
            $qConsultation->whereDate('created_at', $request->end_date);
            $qComplaint->whereDate('created_at', $request->end_date);
            $qSubmission->whereDate('created_at', $request->end_date);
        }
        // ==================================================

        // ================= FILTER JENIS PELAPOR =================
        if ($request->filled('type') && $request->type !== 'Semua') {
            $qConsultation->whereHas('user', fn ($q) => $q->where('user_type', $request->type));
            $qComplaint->whereHas('user', fn ($q) => $q->where('user_type', $request->type));
            $qSubmission->whereHas('user', fn ($q) => $q->where('user_type', $request->type));
        }
        // ========================================================

        // ================= FILTER DIPROSES OLEH (BIDANG) =================
        $bidang = $request->get('diproses_bidang');
        $filterBidangAktif = $bidang && $bidang !== 'Semua';

        if ($filterBidangAktif) {
            // Consultation
            if (Schema::hasColumn('consultations', 'diproses_bidang')) {
                $qConsultation->where('diproses_bidang', $bidang);
            } elseif (Schema::hasColumn('consultations', 'diproses_oleh')) {
                // fallback kalau yang ada cuma gabungan "Bidang - Kelompok"
                $qConsultation->where('diproses_oleh', 'like', $bidang . ' - %');
            }

            // Complaint
            if (Schema::hasColumn('complaints', 'diproses_bidang')) {
                $qComplaint->where('diproses_bidang', $bidang);
            } elseif (Schema::hasColumn('complaints', 'diproses_oleh')) {
                $qComplaint->where('diproses_oleh', 'like', $bidang . ' - %');
            }

            // Submission
            if (Schema::hasColumn('submissions', 'diproses_bidang')) {
                $qSubmission->where('diproses_bidang', $bidang);
            } elseif (Schema::hasColumn('submissions', 'diproses_oleh')) {
                $qSubmission->where('diproses_oleh', 'like', $bidang . ' - %');
            }
        }
        // ==================================================================

        // ================= FILTER STATUS =================
        if ($request->filled('status') && $request->status !== 'Semua') {
            $status = $request->status;

            if ($status === 'pending') {
                $qConsultation->where('status', 'pending');
                $qComplaint->where('status', 'pending');
                $qSubmission->where('status', 'pending');
            } elseif ($status === 'proses') {
                $qConsultation->where('status', 'on_progress');
                $qComplaint->where('status', 'diproses');
                $qSubmission->where('status', 'in_progress');
            } elseif ($status === 'selesai') {
                $qConsultation->whereIn('status', ['completed', 'rejected']);
                $qSubmission->whereIn('status', ['completed', 'rejected']);
                $qComplaint->whereIn('status', ['selesai', 'ditolak']);
            } elseif ($status === 'ditolak') {
                $qConsultation->where('status', 'rejected');
                $qSubmission->where('status', 'rejected');
                $qComplaint->where('status', 'ditolak');
            }
        }
        // =================================================

        $consultations = $qConsultation->latest()->get()->map(function ($x) {
            return [
                'service'    => 'Konsultasi',
                'ticket'     => $x->ticket_id ?? $x->ticket_number ?? $x->id,
                'created_at' => $x->created_at,
                'name'       => $x->user->name ?? '-',
                'email'      => $x->user->email ?? '-',
                'user_type'  => $x->user->user_type ?? '-',
                'subject'    => $x->title ?? $x->subject ?? '-',
                'status'     => $x->status,
                'show_route' => route('admin.consultations.show', $x->id),
            ];
        });

        $complaints = $qComplaint->latest()->get()->map(function ($x) {
            return [
                'service'    => 'Pengaduan',
                'ticket'     => $x->ticket_number ?? $x->ticket_id ?? $x->id,
                'created_at' => $x->created_at,
                'name'       => $x->user->name ?? '-',
                'email'      => $x->user->email ?? '-',
                'user_type'  => $x->user->user_type ?? '-',
                'subject'    => $x->subject ?? $x->title ?? '-',
                'status'     => $x->status,
                'show_route' => route('admin.complaints.show', $x->id),
            ];
        });

        $submissions = $qSubmission->latest()->get()->map(function ($x) {
            return [
                'service'    => 'Permohonan Informasi',
                'ticket'     => $x->ticket_id ?? $x->ticket_number ?? $x->id,
                'created_at' => $x->created_at,
                'name'       => $x->user->name ?? '-',
                'email'      => $x->user->email ?? '-',
                'user_type'  => $x->user->user_type ?? '-',
                'subject'    => $x->title ?? $x->subject ?? '-',
                'status'     => $x->status,
                'show_route' => route('admin.submissions.show', $x->id),
            ];
        });

        $all = $consultations->concat($complaints)->concat($submissions)
            ->sortByDesc(fn ($x) => $x['created_at'])
            ->values();

        $perPage = 10;
        $page = (int) $request->get('page', 1);
        $total = $all->count();

        $items = $all->slice(($page - 1) * $perPage, $perPage)->values();

        $paginated = new LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $page,
            ['path' => url()->current(), 'query' => $request->query()]
        );

        return view('admin.managements.semua', [
            'items' => $paginated,
            'stats' => $stats,
        ]);
    }
}