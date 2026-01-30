<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Submission;
use App\Models\Consultation;
use App\Models\Complaint;

class SearchController extends Controller
{
    /**
     * PUBLIC SEARCH
     */
    public function publicSearch(Request $request)
    {
        if ($request->ajax()) {
            return response()->json([]);
        }

        $ticketId = trim((string) $request->input('ticket_id'));

        if ($ticketId === '') {
            return redirect()->route('home')
                ->with('error', 'ID Tiket harus diisi');
        }

        $ticket = null;
        $ticketType = null;

        if (str_starts_with($ticketId, 'PD.')) {
            $ticket = Complaint::with(['category', 'statusHistories'])
                ->where('ticket_number', $ticketId)
                ->first();

            $ticketType = $ticket ? 'complaint' : null;
        }

        if (!$ticket) {
            $ticket = Consultation::with(['category', 'statusHistories'])
                ->where('ticket_number', $ticketId)
                ->first();

            $ticketType = $ticket ? 'consultation' : null;
        }

        if (!$ticket) {
            $ticket = Submission::with(['category', 'statusHistories'])
                ->where('ticket_id', $ticketId)
                ->first();

            $ticketType = $ticket ? 'submission' : null;
        }

        if (!$ticket) {
            return redirect()->route('home')
                ->with('error', 'Tiket tidak ditemukan');
        }

        return view('public.ticket-search', compact(
            'ticket',
            'ticketType',
            'ticketId'
        ));
    }

    /**
     * AJAX PREVIEW 
     */
    public function preview(Request $request)
    {
        $q = trim((string) $request->q);

        if ($q === '') {
            return response()->json([]);
        }

        $user = auth()->user();
        $userId = $user->id;
        $userType = $user->user_type;

        $isPegawai = $userType === 'pegawai';
        $routePrefix = $isPegawai ? '/user' : '/masyarakat';

        $complaints = DB::table('complaints')
            ->select(
                'ticket_number as ticket',
                'subject as title',
                DB::raw("'{$routePrefix}/pengaduan/' || id as url"),
                DB::raw("'complaint' as type")
            )
            ->where('user_id', $userId)
            ->where(function($query) use ($q) {
                $query->where('ticket_number', 'ILIKE', "%{$q}%")
                      ->orWhere('subject', 'ILIKE', "%{$q}%");
            })
            ->limit(5);

        $consultations = DB::table('consultations')
            ->select(
                'ticket_number as ticket',
                'subject as title',
                DB::raw("'{$routePrefix}/konsultasi/' || id as url"),
                DB::raw("'consultation' as type")
            )
            ->where('user_id', $userId) 
            ->where(function($query) use ($q) {
                $query->where('ticket_number', 'ILIKE', "%{$q}%")
                      ->orWhere('subject', 'ILIKE', "%{$q}%");
            })
            ->limit(5);

        $submissions = DB::table('submissions')
            ->select(
                'ticket_id as ticket',
                'title',
                DB::raw("'{$routePrefix}/permohonan-informasi/' || id as url"),
                DB::raw("'submission' as type")
            )
            ->where('user_id', $userId)
            ->where(function($query) use ($q) {
                $query->where('ticket_id', 'ILIKE', "%{$q}%")
                      ->orWhere('title', 'ILIKE', "%{$q}%");
            })
            ->limit(5);

        return response()->json(
            $complaints
                ->unionAll($consultations)
                ->unionAll($submissions)
                ->get()
        );
    }

    /**
     * RESULT PAGE (LOGIN, ENTER) - SEPARATED BY USER TYPE
     */
    public function result(Request $request)
    {
        $q = trim((string) $request->q);
        $user = auth()->user();
        $userId = $user->id;
        $userType = $user->user_type;

        $submissions = Submission::where('user_id', $userId)
            ->where(fn ($x) =>
                $x->where('title', 'ILIKE', "%{$q}%")
                  ->orWhere('description', 'ILIKE', "%{$q}%")
                  ->orWhere('ticket_id', 'ILIKE', "%{$q}%")
            )->get();

        $consultations = Consultation::where('user_id', $userId)
            ->where(fn ($x) =>
                $x->where('subject', 'ILIKE', "%{$q}%")
                  ->orWhere('description', 'ILIKE', "%{$q}%")
                  ->orWhere('ticket_number', 'ILIKE', "%{$q}%")
            )->get();

        $complaints = Complaint::where('user_id', $userId)
            ->where(fn ($x) =>
                $x->where('subject', 'ILIKE', "%{$q}%")
                  ->orWhere('description', 'ILIKE', "%{$q}%")
                  ->orWhere('ticket_number', 'ILIKE', "%{$q}%")
            )->get();

        if ($userType === 'pegawai') {
            return view('user.search.result', compact(
                'q',
                'submissions',
                'consultations',
                'complaints'
            ));
        } else {
            return view('masyarakat.search.result', compact(
                'q',
                'submissions',
                'consultations',
                'complaints'
            ));
        }
    }
}