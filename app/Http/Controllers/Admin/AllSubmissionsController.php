<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Consultation;
use App\Models\Complaint;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

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

        $categories = Category::whereIn('type', ['konsultasi', 'pengaduan', 'permohonan'])
            ->orderBy('type')
            ->orderBy('name')
            ->get();

        $qConsultation = Consultation::with(['user', 'category']);
        $qComplaint    = Complaint::with(['user', 'category']);
        $qSubmission   = Submission::with(['user', 'category']);

        // ================= FILTER TANGGAL =================
        $hasStart = $request->filled('start_date');
        $hasEnd   = $request->filled('end_date');

        if ($hasStart && $hasEnd) {
            // rentang tanggal
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
            // hanya tanggal mulai → 1 hari itu saja
            $qConsultation->whereDate('created_at', $request->start_date);
            $qComplaint->whereDate('created_at', $request->start_date);
            $qSubmission->whereDate('created_at', $request->start_date);
        } elseif ($hasEnd) {
            // hanya tanggal akhir → 1 hari itu saja
            $qConsultation->whereDate('created_at', $request->end_date);
            $qComplaint->whereDate('created_at', $request->end_date);
            $qSubmission->whereDate('created_at', $request->end_date);
        }
        // ==================================================

        if ($request->filled('type') && $request->type !== 'Semua') {
            $qConsultation->whereHas('user', fn($q) => $q->where('user_type', $request->type));
            $qComplaint->whereHas('user', fn($q) => $q->where('user_type', $request->type));
            $qSubmission->whereHas('user', fn($q) => $q->where('user_type', $request->type));
        }

        if ($request->filled('category') && $request->category !== 'Semua') {
            $qConsultation->where('category_id', $request->category);
            $qComplaint->where('category_id', $request->category);
            $qSubmission->where('category_id', $request->category);
        }

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
                $qComplaint->where('status', 'selesai');
            } elseif ($status === 'ditolak') {
                $qConsultation->where('status', 'rejected');
                $qSubmission->where('status', 'rejected');
                $qComplaint->where('status', 'ditolak');
            }
        }

        $consultations = $qConsultation->latest()->get()->map(function ($x) {
            return [
                'service'    => 'Konsultasi',
                'ticket'     => $x->ticket_id ?? $x->ticket_number ?? $x->id,
                'created_at' => $x->created_at,
                'name'       => $x->user->name ?? '-',
                'email'      => $x->user->email ?? '-',
                'user_type'  => $x->user->user_type ?? '-',
                'category'   => $x->category->name ?? '-',
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
                'category'   => $x->category->name ?? '-',
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
                'category'   => $x->category->name ?? '-',
                'subject'    => $x->title ?? $x->subject ?? '-',
                'status'     => $x->status,
                'show_route' => route('admin.submissions.show', $x->id),
            ];
        });

        $all = $consultations->concat($complaints)->concat($submissions)
            ->sortByDesc(fn($x) => $x['created_at'])
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

        // NOTE: sesuaikan view path kamu (lihat poin #2 di bawah)
        return view('admin.managements.semua', [
            'items'      => $paginated,
            'categories' => $categories,
            'stats'      => $stats,
        ]);
    }
}
