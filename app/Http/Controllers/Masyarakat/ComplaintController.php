<?php

namespace App\Http\Controllers\Masyarakat;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Complaint;
use App\Models\ComplaintDocument;
use App\Mail\ComplaintCreated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

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

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('ticket_number', 'ilike', "%{$search}%")
                  ->orWhere('subject', 'ilike', "%{$search}%");
            });
        }

        if ($request->filled('status') && $request->status !== 'semua') {
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

        $from = $request->input('from', $request->query('from', 'index'));

        $request->validate([
            'subject' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'required|string',

            'documents' => 'nullable|array|max:3',
            'documents.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048', 
        ]);

        $nik = (string) ($user->nik ?? '');
        if (strlen($nik) < 16) {
            return back()
                ->withErrors(['nik' => 'NIK user belum ada / tidak valid (minimal 16 digit) untuk generate tiket.'])
                ->withInput();
        }

        $tempTicket = 'TEMP-' . now()->format('YmdHis') . '-' . bin2hex(random_bytes(3));

        $complaint = Complaint::create([
            'user_id' => $user->id,
            'category_id' => $request->category_id,
            'subject' => $request->subject,
            'description' => $request->description,
            'status' => 'pending',
            'ticket_number' => $tempTicket,
        ]);

        $nikPart = substr($nik, 10, 6); 
        $datePart = now()->format('dmY');
        $counterPart = str_pad((string) $complaint->id, 3, '0', STR_PAD_LEFT); 

        $ticketNumber = "PD.{$nikPart}.{$datePart}_{$counterPart}";

        $complaint->update([
            'ticket_number' => $ticketNumber,
        ]);

        if ($request->hasFile('documents')) {
            foreach ((array) $request->file('documents') as $document) {
                if (!$document) continue;
                if (!$document->isValid()) continue;

                $filename = time() . '_' . preg_replace('/\s+/', '_', $document->getClientOriginalName());

                // Jangan utak-atik supabase: tetap pakai disk yang kamu sudah punya
                $path = Storage::disk('supabase_complaints')
                    ->putFileAs((string) $complaint->id, $document, $filename);

                if (!$path) {
                    return back()
                        ->withErrors(['documents' => 'Upload gagal (path kosong). Cek batas upload PHP: upload_max_filesize & post_max_size.'])
                        ->withInput();
                }

                ComplaintDocument::create([
                    'complaint_id' => $complaint->id,
                    'original_name' => $document->getClientOriginalName(),
                    'file_path' => $path,
                    'file_type' => $document->getClientOriginalExtension(),
                    'file_size' => $document->getSize(),
                ]);
            }
        }

        // email notif (opsional)
        try {
            Mail::to($user->email)->send(new ComplaintCreated($complaint));
        } catch (\Throwable $e) {
            \Log::error('Failed to send complaint email: ' . $e->getMessage());
        }

        return redirect()
            ->route('masyarakat.complaints.create', ['from' => $from])
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
            'description' => 'required|string',

            'documents' => 'nullable|array|max:3',
            'documents.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $complaint->update([
            'category_id' => $request->category_id,
            'subject' => $request->subject,
            'description' => $request->description,
        ]);

        if ($request->hasFile('documents')) {
            foreach ((array) $request->file('documents') as $document) {
                if (!$document) continue;
                if (!$document->isValid()) continue;

                $filename = time() . '_' . preg_replace('/\s+/', '_', $document->getClientOriginalName());

                $path = Storage::disk('supabase_complaints')
                    ->putFileAs((string) $complaint->id, $document, $filename);

                if (!$path) {
                    return back()
                        ->withErrors(['documents' => 'Upload gagal (path kosong). Cek batas upload PHP: upload_max_filesize & post_max_size.'])
                        ->withInput();
                }

                ComplaintDocument::create([
                    'complaint_id' => $complaint->id,
                    'original_name' => $document->getClientOriginalName(),
                    'file_path' => $path,
                    'file_type' => $document->getClientOriginalExtension(),
                    'file_size' => $document->getSize(),
                ]);
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
}
