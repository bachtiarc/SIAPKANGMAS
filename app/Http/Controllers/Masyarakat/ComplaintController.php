<?php

namespace App\Http\Controllers\Masyarakat;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\ComplaintDocument;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Mail\ComplaintCreated;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;

class ComplaintController extends Controller
{
    /**
     * Display a listing of complaints
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Check if user is masyarakat_umum
        if ($user->user_type !== 'masyarakat_umum') {
            abort(403, 'Unauthorized access.');
        }

        $query = Complaint::where('user_id', $user->id)
            ->with(['category', 'handler']);

        // Search by ticket_number or subject (case-insensitive)
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('ticket_number', 'ilike', "%{$search}%")
                  ->orWhere('subject', 'ilike', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status != 'semua' && $request->status != '') {
            $statusFilter = strtolower($request->status);
            
            $query->where(function($q) use ($statusFilter) {
                // MENUNGGU PROSES
                if ($statusFilter === 'pending') {
                    $q->whereIn('status', ['pending', 'belum diproses']);
                }
                // DIPROSES  
                elseif ($statusFilter === 'diproses') {
                    $q->whereIn('status', ['in_progress', 'on_progress', 'diproses', 'sedang diproses']);
                }
                // SELESAI
                elseif ($statusFilter === 'selesai') {
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
        
        // Filter kategori berdasarkan user type
        $categories = Category::active()
            ->ofType('pengaduan')
            ->where(function($query) use ($userType) {
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

        // Validation
        $request->validate([
            'subject' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'location' => 'required|string|max:255',
            'incident_date' => 'required|date',
            'incident_time' => 'nullable',
            'description' => 'required|string',
            'documents.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        // Generate unique ticket number
        $ticketNumber = 'PGD-' . date('Ymd') . '-' . strtoupper(Str::random(6));

        // Create complaint
        $complaint = Complaint::create([
            'user_id' => $user->id,
            'category_id' => $request->category_id,
            'ticket_number' => $ticketNumber,
            'subject' => $request->subject,
            'description' => $request->description,
            'location' => $request->location,
            'incident_date' => $request->incident_date,
            'incident_time' => $request->incident_time,
            'status' => 'pending',
        ]);

        // Handle documents upload
        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $document) {
                if ($document->isValid()) {
                    $filename = time() . '_' . $document->getClientOriginalName();
                    
                    // Store to Supabase
                    $path = Storage::disk('supabase_complaints')
                        ->putFileAs("{$complaint->id}", $document, $filename);
                    
                    // Save document info to database
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

        // Send email notification
        try {
            Mail::to($user->email)->send(new ComplaintCreated($complaint));
        } catch (\Exception $e) {
            \Log::error('Failed to send complaint email: ' . $e->getMessage());
        }

        // Ambil asal halaman dari input (hidden field) atau query string
        $from = $request->input('from', $request->query('from', 'index'));

        return redirect()->route('masyarakat.complaints.create', ['from' => $from])
            ->with('success', true)
            ->with('ticket_id', $complaint->ticket_number)
            ->with('complaint_id', $complaint->id);
    }

    /**
     * Display the specified complaint
     */
    public function show(Complaint $complaint)
    {
        $user = auth()->user();
        
        // Authorization check
        if ($complaint->user_id !== $user->id) {
            abort(403, 'Unauthorized access.');
        }

        // Load relationships including documents
        $complaint->load(['category', 'handler', 'documents']);

        return view('masyarakat.complaints.show', compact('complaint'));
    }

    /**
     * Show the form for editing the specified complaint
     */
    public function edit(Complaint $complaint)
    {
        $user = auth()->user();
        
        // Authorization check
        if ($complaint->user_id !== $user->id) {
            abort(403, 'Unauthorized access.');
        }

        // Only allow editing if status is pending
        if (!in_array(strtolower($complaint->status), ['pending', 'belum diproses'])) {
            return redirect()->route('masyarakat.complaints.show', $complaint->id)
                ->with('error', 'Pengaduan tidak dapat diedit karena sudah diproses.');
        }

        $userType = $user->user_type;
        
        // Filter kategori berdasarkan user type
        $categories = Category::active()
            ->ofType('pengaduan')
            ->where(function($query) use ($userType) {
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
        
        // Authorization check
        if ($complaint->user_id !== $user->id) {
            abort(403, 'Unauthorized access.');
        }

        // Only allow updating if status is pending
        if (!in_array(strtolower($complaint->status), ['pending', 'belum diproses'])) {
            return redirect()->route('masyarakat.complaints.show', $complaint->id)
                ->with('error', 'Pengaduan tidak dapat diedit karena sudah diproses.');
        }

        // Validation
        $request->validate([
            'subject' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'location' => 'required|string|max:255',
            'incident_date' => 'required|date',
            'incident_time' => 'nullable',
            'description' => 'required|string',
            'documents.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        // Update complaint
        $complaint->update([
            'category_id' => $request->category_id,
            'subject' => $request->subject,
            'description' => $request->description,
            'location' => $request->location,
            'incident_date' => $request->incident_date,
            'incident_time' => $request->incident_time,
        ]);

        // Handle new documents upload
        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $document) {
                if ($document->isValid()) {
                    $filename = time() . '_' . $document->getClientOriginalName();
                    
                    // Store to Supabase
                    $path = Storage::disk('supabase_complaints')
                        ->putFileAs("{$complaint->id}", $document, $filename);
                    
                    // Save document info to database
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
        
        // Authorization check
        if ($complaint->user_id !== $user->id) {
            abort(403, 'Unauthorized access.');
        }

        // Only allow deleting if status is pending
        if (!in_array(strtolower($complaint->status), ['pending', 'belum diproses'])) {
            return redirect()->route('masyarakat.complaints.show', $complaint->id)
                ->with('error', 'Pengaduan tidak dapat dihapus karena sudah diproses.');
        }

        // Delete related documents from storage and database
        foreach ($complaint->documents as $document) {
            Storage::disk('supabase_complaints')->delete($document->file_path);
            $document->delete();
        }

        $complaint->delete();

        return redirect()->route('masyarakat.complaints.index')
            ->with('success', 'Pengaduan berhasil dihapus.');
    }
}
