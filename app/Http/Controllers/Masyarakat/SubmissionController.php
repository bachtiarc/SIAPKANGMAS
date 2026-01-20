<?php

namespace App\Http\Controllers\Masyarakat;

use App\Http\Controllers\Controller;
use App\Models\Submission;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Mail\SubmissionCreated;
use Illuminate\Support\Facades\Mail;

class SubmissionController extends Controller
{
    /**
     * Display a listing of submissions
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Check if user is masyarakat_umum
        if ($user->user_type !== 'masyarakat_umum') {
            abort(403, 'Unauthorized access.');
        }

        $query = Submission::where('user_id', $user->id)
            ->with(['category', 'handler']);

        // Filter by status
        if ($request->has('status') && $request->status != 'semua') {
            $query->where('status', $request->status);
        }

        // Search by ticket_id or title
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('ticket_id', 'like', "%{$search}%")
                  ->orWhere('full_ticket_number', 'like', "%{$search}%")
                  ->orWhere('title', 'like', "%{$search}%");
            });
        }

        $submissions = $query->latest()->paginate(10);

        return view('masyarakat.submissions.index', compact('submissions'));
    }

    /**
     * Show the form for creating a new submission
     */
    public function create()
    {
        $user = auth()->user();
        
        // Check if user is masyarakat_umum
        if ($user->user_type !== 'masyarakat_umum') {
            abort(403, 'Unauthorized access.');
        }

        // Get active categories for permohonan informasi
        $categories = Category::active()
            ->ofType('permohonan')
            ->orderBy('name')
            ->get();

        return view('masyarakat.submissions.create', compact('categories'));
    }

    /**
     * Store a newly created submission in storage
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        
        // Validate request
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120', // 5MB max
        ], [
            'category_id.required' => 'Kategori informasi wajib dipilih.',
            'category_id.exists' => 'Kategori tidak valid.',
            'title.required' => 'Judul permohonan wajib diisi.',
            'description.required' => 'Deskripsi lengkap wajib diisi.',
            'document.mimes' => 'Format dokumen harus PDF, JPG, JPEG, atau PNG.',
            'document.max' => 'Ukuran dokumen maksimal 5MB.',
        ]);

        // Upload document if exists
        $documentPath = null;
        if ($request->hasFile('document')) {
            $documentPath = $request->file('document')->store('submission-documents', 'public');
        }

        // Create submission (ticket generated automatically in model)
        $submission = Submission::create([
            'user_id' => $user->id,
            'category_id' => $validated['category_id'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'document_path' => $documentPath,
            'status' => 'pending',
        ]);

        // Send email notification
        try {
            Mail::to($user->email)->send(new SubmissionCreated($submission));
        } catch (\Exception $e) {
            // Log error but don't fail the request
            \Log::error('Failed to send submission email: ' . $e->getMessage());
        }

        return redirect()->route('masyarakat.submissions.show', $submission->id)
            ->with('success', 'Formulir berhasil dikirim! Nomor tiket Anda: ' . $submission->ticket_id);
    }

    /**
     * Display the specified submission
     */
    public function show(Submission $submission)
    {
        $user = auth()->user();
        
        // Check authorization
        if ($submission->user_id !== $user->id) {
            abort(403, 'Unauthorized access.');
        }

        $submission->load(['category', 'handler', 'statusHistories']);

        return view('masyarakat.submissions.show', compact('submission'));
    }
}