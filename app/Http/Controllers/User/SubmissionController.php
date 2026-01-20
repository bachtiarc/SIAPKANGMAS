<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Submission;
use App\Models\SubmissionDocument;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
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
        
        // Check if user is pegawai
        if ($user->user_type !== 'pegawai') {
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

        return view('user.submissions.index', compact('submissions'));
    }

    /**
     * Show the form for creating a new submission
     */
    public function create()
    {
        $user = auth()->user();
        
        // Check if user is pegawai
        if ($user->user_type !== 'pegawai') {
            abort(403, 'Unauthorized access.');
        }

        // Get active categories for permohonan informasi
        $categories = Category::active()
            ->ofType('permohonan')
            ->orderBy('name')
            ->get();

        return view('user.submissions.create', compact('categories'));
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
            'documents.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120', // Max 5MB per file
        ], [
            'category_id.required' => 'Kategori informasi wajib dipilih.',
            'category_id.exists' => 'Kategori tidak valid.',
            'title.required' => 'Judul permohonan wajib diisi.',
            'description.required' => 'Deskripsi lengkap wajib diisi.',
            'documents.*.mimes' => 'Format dokumen harus PDF, JPG, JPEG, atau PNG.',
            'documents.*.max' => 'Ukuran setiap dokumen maksimal 5MB.',
        ]);

        // Create submission
        $submission = Submission::create([
            'user_id' => $user->id,
            'category_id' => $validated['category_id'],
            'title' => $validated['title'],
            'subject' => $validated['title'],
            'description' => $validated['description'],
            'status' => 'pending',
        ]);

        // Handle multiple document uploads
        if ($request->hasFile('documents')) {
            $documents = $request->file('documents');
            
            // Limit to maximum 3 documents
            $documents = array_slice($documents, 0, 3);
            
            foreach ($documents as $document) {
                if ($document && $document->isValid()) {
                    // Generate unique filename
                    $filename = Str::random(40) . '.' . $document->getClientOriginalExtension();
                    
                    // Store file
                    $path = $document->storeAs('submissions/' . $submission->id, $filename, 'public');
                    
                    // Create document record
                    SubmissionDocument::create([
                        'submission_id' => $submission->id,
                        'original_name' => $document->getClientOriginalName(),
                        'file_path' => $path,
                        'file_type' => $document->getMimeType(),
                        'file_size' => $document->getSize(),
                    ]);
                }
            }
        }

        // Send email notification
        try {
            Mail::to($user->email)->send(new SubmissionCreated($submission));
        } catch (\Exception $e) {
            // Log error but don't fail the request
            \Log::error('Failed to send submission email: ' . $e->getMessage());
        }

        return redirect()->route('user.submissions.create')
            ->with('success', true)
            ->with('ticket_id', $submission->ticket_id);
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

        // Load relationships including documents
        $submission->load(['category', 'handler', 'statusHistories', 'documents']);

        return view('user.submissions.show', compact('submission'));
    }
}