<?php

namespace App\Http\Controllers\Masyarakat;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\ComplaintDocument;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Mail\ComplaintCreated;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;

class ComplaintController extends Controller
{
    /**
     * Display a listing of complaints
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        if ($user->user_type !== 'masyarakat_umum') {
            abort(403, 'Unauthorized access.');
        }

        $query = Complaint::where('user_id', $user->id)
            ->with(['category', 'handler']);

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('ticket_number', 'ilike', "%{$search}%")
                  ->orWhere('subject', 'ilike', "%{$search}%");
            });
        }

        if ($request->has('status') && $request->status != 'semua' && $request->status != '') {
            $statusFilter = strtolower($request->status);

            $query->where(function ($q) use ($statusFilter) {
                if ($statusFilter === 'pending') {
                    $q->whereIn('status', ['pending', 'belum diproses']);
                } elseif ($statusFilter === 'diproses') {
                    $q->whereIn('status', ['in_progress', 'on_progress', 'diproses', 'sedang diproses']);
                } elseif ($statusFilter === 'selesai') {
                    $q->whereIn('status', ['completed', 'selesai']);
                }
            });
        }

        $complaints = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        return view('masyarakat.complaints.index', compact('complaints'));
    }

    /**
     * Show the form for creating a new complaint
     */
    public function create()
    {
        $user = auth()->user();
        $userType = $user->user_type;

        $categories = Category::active()
            ->ofType('pengaduan')
            ->where(function ($query) use ($userType) {
                $query->where('user_type', $userType)
                      ->orWhere('user_type', 'all');
            })
            ->orderBy('name')
            ->get();

        return view('masyarakat.complaints.create', compact('categories'));
    }

    /**
     * Store a newly created complaint in storage
     */
    public function store(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'subject' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'required|string',
            'documents.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        $complaint = Complaint::create([
            'user_id' => $user->id,
            'category_id' => $request->category_id,
            'subject' => $request->subject,
            'description' => $request->description,
            'status' => 'pending',
            'ticket_number' => 'TEMP', 
        ]);

        $nik = (string) $user->nik; // pastikan ini ada
        $nikPart = substr($nik, 10, 6); // digit 11–16
        $datePart = now()->format('dmY');
        $counterPart = str_pad((string)$complaint->id, 3, '0', STR_PAD_LEFT);

        $ticketNumber = "PD.{$nikPart}.{$datePart}_{$counterPart}";

        $complaint->update([
            'ticket_number' => $ticketNumber,
        ]);

        return redirect()
            ->route('masyarakat.complaints.create')
            ->with('success', true)
            ->with('ticket_id', $ticketNumber)
            ->with('complaint_id', $complaint->id);
    }

    /**
     * Display the specified complaint
     */
    public function show(Complaint $complaint)
    {
        $user = auth()->user();

        if ($complaint->user_id !== $user->id) {
            abort(403, 'Unauthorized access.');
        }

        $complaint->load(['category', 'handler', 'documents']);

        return view('masyarakat.complaints.show', compact('complaint'));
    }

    /**
     * Show the form for editing the specified complaint
     */
    public function edit(Complaint $complaint)
    {
        $user = auth()->user();

        if ($complaint->user_id !== $user->id) {
            abort(403, 'Unauthorized access.');
        }

        if (!in_array(strtolower($complaint->status), ['pending', 'belum diproses'])) {
            return redirect()->route('masyarakat.complaints.show', $complaint->id)
                ->with('error', 'Pengaduan tidak dapat diedit karena sudah diproses.');
        }

        $userType = $user->user_type;

        $categories = Category::active()
            ->ofType('pengaduan')
            ->where(function ($query) use ($userType) {
                $query->where('user_type', $userType)
                      ->orWhere('user_type', 'all');
            })
            ->orderBy('name')
            ->get();

        return view('masyarakat.complaints.edit', compact('complaint', 'categories'));
    }

    /**
     * Update the specified complaint in storage
     */
    public function update(Request $request, Complaint $complaint)
    {
        $user = auth()->user();

        if ($complaint->user_id !== $user->id) {
            abort(403, 'Unauthorized access.');
        }

        if (!in_array(strtolower($complaint->status), ['pending', 'belum diproses'])) {
            return redirect()->route('masyarakat.complaints.show', $complaint->id)
                ->with('error', 'Pengaduan tidak dapat diedit karena sudah diproses.');
        }

        $request->validate([
            'subject' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'location' => 'nullable|string|max:255',
            'incident_date' => 'nullable|date',
            'incident_time' => 'nullable',
            'description' => 'required|string',
            'documents.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        $complaint->update([
            'category_id' => $request->category_id,
            'subject' => $request->subject,
            'description' => $request->description,
            'location' => $request->location,
            'incident_date' => $request->incident_date,
            'incident_time' => $request->incident_time,
        ]);

        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $document) {
                if ($document && $document->isValid()) {
                    $filename = time() . '_' . $document->getClientOriginalName();

                    $path = Storage::disk('supabase_complaints')
                        ->putFileAs("{$complaint->id}", $document, $filename);

                    ComplaintDocument::create([
                        'complaint_id' => $complaint->id,
                        'original_name' => $document->getClientOriginalName(),
                        'file_path' => $path,
                        'file_type' => $document->getClientOriginalExtension(),
                        'file_size' => $document->getSize(),
                    ]);
                }
            }
        }

        return redirect()->route('masyarakat.complaints.show', $complaint->id)
            ->with('success', 'Pengaduan berhasil diperbarui.');
    }

    /**
     * Remove the specified complaint from storage
     */
    public function destroy(Complaint $complaint)
    {
        $user = auth()->user();

        if ($complaint->user_id !== $user->id) {
            abort(403, 'Unauthorized access.');
        }

        if (!in_array(strtolower($complaint->status), ['pending', 'belum diproses'])) {
            return redirect()->route('masyarakat.complaints.show', $complaint->id)
                ->with('error', 'Pengaduan tidak dapat dihapus karena sudah diproses.');
        }

        foreach ($complaint->documents as $document) {
            Storage::disk('supabase_complaints')->delete($document->file_path);
            $document->delete();
        }

        $complaint->delete();

        return redirect()->route('masyarakat.complaints.index')
            ->with('success', 'Pengaduan berhasil dihapus.');
    }

    /**
     * Generate ticket:
     * PD.{nik[11..16]}.{ddmmyyyy}_{sequence_in_month}
     */
    private function generateComplaintTicket($user): string
    {
        $nik = (string) ($user->nik ?? '');

        if (strlen($nik) < 16) {
            // Kalau field NIK kamu beda, ganti $user->nik di atas.
            throw new \RuntimeException('NIK user tidak valid / belum tersedia untuk generate ticket.');
        }

        $nikPart = substr($nik, 10, 6); // digit ke-11 s/d 16
        $datePart = now()->format('dmY'); // 28012026
        $monthKey = now()->format('Y-m'); // 2026-01
        $seqKey = "PD-{$monthKey}";

        $counter = DB::transaction(function () use ($seqKey) {
            $row = DB::table('ticket_sequences')
                ->where('key', $seqKey)
                ->lockForUpdate()
                ->first();

            if (!$row) {
                DB::table('ticket_sequences')->insert([
                    'key' => $seqKey,
                    'last_number' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                return 1;
            }

            $next = ((int) $row->last_number) + 1;

            DB::table('ticket_sequences')
                ->where('key', $seqKey)
                ->update([
                    'last_number' => $next,
                    'updated_at' => now(),
                ]);

            return $next;
        });

        $counterPart = str_pad((string) $counter, 3, '0', STR_PAD_LEFT); // 001,002,...

        return "PD.{$nikPart}.{$datePart}_{$counterPart}";
    }
}
