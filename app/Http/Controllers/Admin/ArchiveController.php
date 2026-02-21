<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Consultation;
use App\Models\Complaint;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class ArchiveController extends Controller
{
    public function index(Request $request)
    {
        // arsip hanya yang archived_at not null
        $qConsultation = Consultation::with(['user','applicant'])->archived();
        $qComplaint    = Complaint::with(['user','applicant'])->archived();
        $qSubmission   = Submission::with(['user','applicant'])->archived();

        // (opsional) filter tanggal arsip atau created_at? saya pakai created_at biar konsisten:
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $qConsultation->whereBetween('created_at', [$request->start_date.' 00:00:00', $request->end_date.' 23:59:59']);
            $qComplaint->whereBetween('created_at', [$request->start_date.' 00:00:00', $request->end_date.' 23:59:59']);
            $qSubmission->whereBetween('created_at', [$request->start_date.' 00:00:00', $request->end_date.' 23:59:59']);
        }

        $consultations = $qConsultation->latest()->get()->map(function ($x) {
            $creator  = $x->user;
            $userType = $creator->user_type ?? null;
            $pemohon  = ($userType === 'pegawai') ? ($x->applicant ?? null) : $creator;

            return [
                'service'    => 'Konsultasi',
                'ticket'     => $x->ticket_id ?? $x->ticket_number ?? $x->id,
                'created_at' => $x->created_at,
                'archived_at'=> $x->archived_at,
                'name'       => $pemohon->nama_lengkap ?? $pemohon->name ?? '-',
                'email'      => $pemohon->email ?? '-',
                'user_type'  => $x->user->user_type ?? '-',
                'subject'    => $x->title ?? $x->subject ?? '-',
                'status'     => $x->status,
                'show_route' => route('admin.consultations.show', $x->id),
                'unarchive_route' => route('admin.consultations.unarchive', $x->id),
            ];
        });

        $complaints = $qComplaint->latest()->get()->map(function ($x) {
            $creator  = $x->user;
            $userType = $creator->user_type ?? null;
            $pemohon  = ($userType === 'pegawai') ? ($x->applicant ?? null) : $creator;

            return [
                'service'    => 'Pengaduan',
                'ticket'     => $x->ticket_number ?? $x->ticket_id ?? $x->id,
                'created_at' => $x->created_at,
                'archived_at'=> $x->archived_at,
                'name'       => $pemohon->nama_lengkap ?? $pemohon->name ?? '-',
                'email'      => $pemohon->email ?? '-',
                'user_type'  => $x->user->user_type ?? '-',
                'subject'    => $x->subject ?? $x->title ?? '-',
                'status'     => $x->status,
                'show_route' => route('admin.complaints.show', $x->id),
                'unarchive_route' => route('admin.complaints.unarchive', $x->id),
            ];
        });

        $submissions = $qSubmission->latest()->get()->map(function ($x) {
            $creator  = $x->user;
            $userType = $creator->user_type ?? null;
            $pemohon  = ($userType === 'pegawai') ? ($x->applicant ?? null) : $creator;

            return [
                'service'    => 'Permohonan Informasi',
                'ticket'     => $x->ticket_id ?? $x->ticket_number ?? $x->id,
                'created_at' => $x->created_at,
                'archived_at'=> $x->archived_at,
                'name'       => $pemohon->nama_lengkap ?? $pemohon->name ?? '-',
                'email'      => $pemohon->email ?? '-',
                'user_type'  => $x->user->user_type ?? '-',
                'subject'    => $x->title ?? $x->subject ?? '-',
                'status'     => $x->status,
                'show_route' => route('admin.submissions.show', $x->id),
                'unarchive_route' => route('admin.submissions.unarchive', $x->id),
            ];
        });

        $all = $consultations->concat($complaints)->concat($submissions)
            ->sortByDesc(fn ($x) => $x['archived_at'] ?? $x['created_at'])
            ->values();

        $perPage = 10;
        $page = (int) $request->get('page', 1);
        $total = $all->count();
        $items = $all->slice(($page - 1) * $perPage, $perPage)->values();

        $paginated = new LengthAwarePaginator(
            $items, $total, $perPage, $page,
            ['path' => url()->current(), 'query' => $request->query()]
        );

        return view('admin.managements.arsip', [
            'items' => $paginated,
        ]);
    }

    // ========= ARCHIVE =========
    public function archiveConsultation($id)
    {
        $x = Consultation::findOrFail($id);

        $x->update(['archived_at' => now()]);
        return back()->with('success', 'Konsultasi berhasil diarsipkan.');
    }

    public function archiveComplaint($id)
    {
        $x = Complaint::findOrFail($id);

        $x->update(['archived_at' => now()]);
        return back()->with('success', 'Pengaduan berhasil diarsipkan.');
    }

    public function archiveSubmission($id)
    {
        $x = Submission::findOrFail($id);
       
        $x->update(['archived_at' => now()]);
        return back()->with('success', 'Permohonan berhasil diarsipkan.');
    }

    // ========= UNARCHIVE =========
    public function unarchiveConsultation($id)
    {
        Consultation::whereKey($id)->update(['archived_at' => null]);
        return back()->with('success', 'Konsultasi dipulihkan dari arsip.');
    }

    public function unarchiveComplaint($id)
    {
        Complaint::whereKey($id)->update(['archived_at' => null]);
        return back()->with('success', 'Pengaduan dipulihkan dari arsip.');
    }

    public function unarchiveSubmission($id)
    {
        $x = Submission::findOrFail($id);
        $x->forceFill(['archived_at' => null])->save();

        return back()->with('success', 'Permohonan dipulihkan dari arsip.');
    }
}
