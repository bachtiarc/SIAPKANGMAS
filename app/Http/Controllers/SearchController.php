<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Submission;
use App\Models\Consultation;
use App\Models\Complaint;

class SearchController extends Controller
{
    /**
     * Pencarian tiket TANPA LOGIN (Public)
     * Tampilkan data dasar tiket tanpa informasi sensitif
     */
    public function publicSearch(Request $request)
    {
        $ticketId = trim($request->input('ticket_id'));
        
        if (!$ticketId) {
            return redirect()->route('home')->with('error', 'ID Tiket harus diisi');
        }

        // Cari di semua tabel
        $ticket = null;
        $ticketType = null;

        // Cek di Submissions
        $submission = Submission::with(['category', 'statusHistories'])->where('ticket_id', $ticketId)->first();
        if ($submission) {
            $ticket = $submission;
            $ticketType = 'submission';
        }

        // Cek di Consultations jika tidak ditemukan
        if (!$ticket) {
            $consultation = Consultation::with(['category', 'statusHistories'])->where('ticket_number', $ticketId)->first();
            if ($consultation) {
                $ticket = $consultation;
                $ticketType = 'consultation';
            }
        }

        // Cek di Complaints jika tidak ditemukan
        if (!$ticket) {
            $complaint = Complaint::with(['category', 'statusHistories'])->where('ticket_number', $ticketId)->first();
            if ($complaint) {
                $ticket = $complaint;
                $ticketType = 'complaint';
            }
        }

        if (!$ticket) {
            return redirect()->route('home')->with('error', 'Tiket tidak ditemukan');
        }

        return view('public.ticket-search', compact('ticket', 'ticketType', 'ticketId'));
    }

    /**
     * Preview pencarian (untuk yang sudah login)
     */
    public function preview(Request $request)
    {
        $q = trim($request->q);
        $userId = auth()->id();

        if (strlen($q) < 2) {
            return response()->json([]);
        }

        $submissions = Submission::where('user_id', $userId)
            ->where(function ($x) use ($q) {
                $x->where('title', 'ILIKE', "%{$q}%")
                  ->orWhere('description', 'ILIKE', "%{$q}%")
                  ->orWhere('ticket_id', 'ILIKE', "%{$q}%");
            })
            ->limit(5)
            ->get()
            ->map(fn ($i) => [
                'type' => 'Submission',
                'title' => $i->title,
                'ticket' => $i->ticket_id,
                'url' => route('user.submissions.show', $i->id),
            ]);

        $consultations = Consultation::where('user_id', $userId)
            ->where(function ($x) use ($q) {
                $x->where('subject', 'ILIKE', "%{$q}%")
                  ->orWhere('description', 'ILIKE', "%{$q}%")
                  ->orWhere('ticket_number', 'ILIKE', "%{$q}%");
            })
            ->limit(5)
            ->get()
            ->map(fn ($i) => [
                'type' => 'Consultation',
                'title' => $i->subject,
                'ticket' => $i->ticket_number,
                'url' => route('user.consultations.show', $i->id),
            ]);

        $complaints = Complaint::where('user_id', $userId)
            ->where(function ($x) use ($q) {
                $x->where('subject', 'ILIKE', "%{$q}%")
                  ->orWhere('description', 'ILIKE', "%{$q}%")
                  ->orWhere('ticket_number', 'ILIKE', "%{$q}%");
            })
            ->limit(5)
            ->get()
            ->map(fn ($i) => [
                'type' => 'Complaint',
                'title' => $i->subject,
                'ticket' => $i->ticket_number,
                'url' => route('user.complaints.show', $i->id),
            ]);

        return response()->json(
            $submissions
                ->merge($consultations)
                ->merge($complaints)
                ->values()
        );
    }

    /**
     * Result pencarian (untuk yang sudah login)
     */
    public function result(Request $request)
    {
        $q = trim($request->q);
        $userId = auth()->id();

        $submissions = Submission::where('user_id', $userId)
            ->where(function ($x) use ($q) {
                $x->where('title', 'ILIKE', "%{$q}%")
                  ->orWhere('description', 'ILIKE', "%{$q}%")
                  ->orWhere('ticket_id', 'ILIKE', "%{$q}%");
            })
            ->get();

        $consultations = Consultation::where('user_id', $userId)
            ->where(function ($x) use ($q) {
                $x->where('subject', 'ILIKE', "%{$q}%")
                  ->orWhere('description', 'ILIKE', "%{$q}%")
                  ->orWhere('ticket_number', 'ILIKE', "%{$q}%");
            })
            ->get();

        $complaints = Complaint::where('user_id', $userId)
            ->where(function ($x) use ($q) {
                $x->where('subject', 'ILIKE', "%{$q}%")
                  ->orWhere('description', 'ILIKE', "%{$q}%")
                  ->orWhere('ticket_number', 'ILIKE', "%{$q}%");
            })
            ->get();

        return view('user.search.result', compact(
            'q',
            'submissions',
            'consultations',
            'complaints'
        ));
    }
}