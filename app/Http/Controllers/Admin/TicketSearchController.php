<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Consultation;
use App\Models\Complaint;
use App\Models\Submission;

class TicketSearchController extends Controller
{
    public function search(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        if ($q === '' || mb_strlen($q) < 2) {
            return response()->json(['data' => []]);
        }

        // helper buat nyeragamkan output
        $pack = function (array $base, $model) {
            return array_merge([
                'ticket'      => $base['ticket'] ?? '-',
                'title'       => $base['title'] ?? null,
                'subject'     => $base['subject'] ?? null,
                'name'        => $base['name'] ?? '-',
                'email'       => $base['email'] ?? '-',
                'type'        => $base['type'] ?? 'lainnya',
                'type_label'  => $base['type_label'] ?? '-',
                'url'         => $base['url'] ?? '#',
                'created_at'  => optional($model->created_at)->timestamp ?? 0,
            ], $base);
        };

        // =========================
        // CONSULTATIONS
        // schema: ticket_number, subject, description, status...
        // =========================
        $consultations = Consultation::with('user')
            ->where(function ($qq) use ($q) {
                $qq->where('ticket_number', 'like', "%{$q}%")
                   ->orWhere('subject', 'like', "%{$q}%")
                   ->orWhereHas('user', function ($u) use ($q) {
                       $u->where('name', 'like', "%{$q}%")
                         ->orWhere('email', 'like', "%{$q}%");
                   });
            })
            ->latest()
            ->limit(7)
            ->get()
            ->map(function ($x) use ($pack) {
                return $pack([
                    'ticket'     => $x->ticket_number ?? $x->id,
                    'title'      => null,
                    'subject'    => $x->subject ?? null,
                    'name'       => $x->user->name ?? '-',
                    'email'      => $x->user->email ?? '-',
                    'type'       => 'konsultasi',
                    'type_label' => 'Konsultasi',
                    'url'        => route('admin.consultations.show', $x->id),
                ], $x);
            });

        // =========================
        // COMPLAINTS
        // schema: ticket_number, subject, description, status...
        // =========================
        $complaints = Complaint::with('user')
            ->where(function ($qq) use ($q) {
                $qq->where('ticket_number', 'like', "%{$q}%")
                   ->orWhere('subject', 'like', "%{$q}%")
                   ->orWhereHas('user', function ($u) use ($q) {
                       $u->where('name', 'like', "%{$q}%")
                         ->orWhere('email', 'like', "%{$q}%");
                   });
            })
            ->latest()
            ->limit(7)
            ->get()
            ->map(function ($x) use ($pack) {
                return $pack([
                    'ticket'     => $x->ticket_number ?? $x->id,
                    'title'      => null,
                    'subject'    => $x->subject ?? null,
                    'name'       => $x->user->name ?? '-',
                    'email'      => $x->user->email ?? '-',
                    'type'       => 'pengaduan',
                    'type_label' => 'Pengaduan',
                    'url'        => route('admin.complaints.show', $x->id),
                ], $x);
            });

        // =========================
        // SUBMISSIONS
        // schema: ticket_id, subject, title, description...
        // =========================
        $submissions = Submission::with('user')
            ->where(function ($qq) use ($q) {
                $qq->where('ticket_id', 'like', "%{$q}%")
                   ->orWhere('title', 'like', "%{$q}%")
                   ->orWhere('subject', 'like', "%{$q}%")
                   ->orWhereHas('user', function ($u) use ($q) {
                       $u->where('name', 'like', "%{$q}%")
                         ->orWhere('email', 'like', "%{$q}%");
                   });
            })
            ->latest()
            ->limit(7)
            ->get()
            ->map(function ($x) use ($pack) {
                return $pack([
                    'ticket'     => $x->ticket_id ?? $x->id,
                    'title'      => $x->title ?? null,
                    'subject'    => $x->subject ?? null,
                    'name'       => $x->user->name ?? '-',
                    'email'      => $x->user->email ?? '-',
                    'type'       => 'permohonan',
                    'type_label' => 'Permohonan',
                    'url'        => route('admin.submissions.show', $x->id),
                ], $x);
            });

        // gabung + sort by created_at desc (timestamp)
        $all = $consultations
            ->concat($complaints)
            ->concat($submissions)
            ->sortByDesc('created_at')
            ->values()
            ->take(15);

        return response()->json(['data' => $all]);
    }
}