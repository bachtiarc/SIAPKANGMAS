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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

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

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('ticket_id', 'ilike', "%{$search}%")
                  ->orWhere('title', 'ilike', "%{$search}%")
                  ->orWhere('subject', 'ilike', "%{$search}%");
            });
        }

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
                // DITOLAK
                elseif ($statusFilter === 'ditolak') {
                    $q->whereIn('status', ['rejected', 'ditolak']);
                }
            });
        }

        $submissions = $query->latest()->paginate(10)->withQueryString();

        return view('user.submissions.index', compact('submissions'));
    }

    /**
     * Show the form for creating a new submission
     */
    public function create()
    {
        $user = auth()->user();
        
        if ($user->user_type !== 'pegawai') {
            abort(403, 'Unauthorized access.');
        }

        $categories = Category::active()
            ->ofType('permohonan')
            ->where(function($query) {
                $query->where('user_type', 'pegawai')
                    ->orWhere('user_type', 'all');
            })
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
        
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'documents.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ], [
            'category_id.required' => 'Kategori informasi wajib dipilih.',
            'category_id.exists' => 'Kategori tidak valid.',
            'title.required' => 'Judul permohonan wajib diisi.',
            'description.required' => 'Deskripsi lengkap wajib diisi.',
            'documents.*.mimes' => 'Format dokumen harus PDF, JPG, JPEG, atau PNG.',
            'documents.*.max' => 'Ukuran setiap dokumen maksimal 2MB.',
        ]);

        if ($request->hasFile('documents')) {
            $totalSize = 0;
            foreach ($request->file('documents') as $file) {
                if ($file && $file->isValid()) {
                    $totalSize += $file->getSize();
                }
            }
            
            if ($totalSize > 6291456) {
                return back()->withErrors([
                    'documents' => 'Total ukuran semua dokumen tidak boleh lebih dari 6MB.'
                ])->withInput();
            }
        }

        $submission = Submission::create([
            'user_id' => $user->id,
            'category_id' => $validated['category_id'],
            'title' => $validated['title'],
            'subject' => $validated['title'],
            'description' => $validated['description'],
            'status' => 'pending',
        ]);

        if ($request->hasFile('documents')) {
            $documents = array_slice($request->file('documents'), 0, 3);

            foreach ($documents as $document) {
                if ($document && $document->isValid()) {

                    $bucket = env('SUPABASE_BUCKET', 'submissions');
                    $supabaseUrl = rtrim(env('SUPABASE_URL'), '/');
                    $token = env('SUPABASE_SERVICE_ROLE_KEY');

                    $filename = Str::random(40) . '.' . $document->getClientOriginalExtension();
                    $path = $document->storeAs($submission->id, $filename, 'supabase');

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

        try {
            Log::info('MAIL DEBUG (masyarakat) - about to send', [
                'to' => $user->email,
                'mailer' => config('mail.default'),
                'host' => config('mail.mailers.smtp.host'),
                'port' => config('mail.mailers.smtp.port'),
                'encryption' => config('mail.mailers.smtp.encryption'),
                'from' => config('mail.from.address'),
                'app_env' => config('app.env'),
            ]);

            Mail::to($user->email)->send(new SubmissionCreated($submission));

            Log::info('MAIL DEBUG (masyarakat) - sent OK', ['to' => $user->email]);
        } catch (\Throwable $e) {
            Log::error('MAIL DEBUG (masyarakat) - failed', [
                'to' => $user->email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }

        $from = $request->query('from', 'index');

        return redirect()->route('user.submissions.create', ['from' => $from])
            ->with('success', true)
            ->with('ticket_id', $submission->ticket_id)
            ->with('submission_id', $submission->id);
    }

    /**
     * Display the specified submission
     */
    public function show(Submission $submission)
    {
        $user = auth()->user();
        
        if ($submission->user_id !== $user->id) {
            abort(403, 'Unauthorized access.');
        }

        $submission->load(['category', 'handler', 'statusHistories', 'documents']);

        return view('user.submissions.show', compact('submission'));
    }

    /**
     * View document in browser
     */
    public function viewDocument(SubmissionDocument $document){
        $user = auth()->user();
        
        if ($document->submission->user_id !== $user->id) {
            abort(403, 'Unauthorized access.');
        }
        
        $supabaseUrl = env('SUPABASE_URL');
        $bucket = env('SUPABASE_BUCKET', 'submissions');
        $filePath = ltrim($document->file_path, '/');
        if (Str::startsWith($filePath, 'submissions/')) {
            $filePath = Str::after($filePath, 'submissions/');
        }

        $publicUrl = "{$supabaseUrl}/storage/v1/object/public/{$bucket}/{$filePath}";
        return redirect()->away($publicUrl);
    }

    /**
     * Download document file
     */
    public function downloadDocument(SubmissionDocument $document)
    {
        $user = auth()->user();
        
        if ($document->submission->user_id !== $user->id) {
            abort(403, 'Unauthorized access.');
        }
        
        $supabaseUrl = rtrim(env('SUPABASE_URL'), '/');
        $bucket = env('SUPABASE_BUCKET', 'submissions');
        $filePath = ltrim($document->file_path, '/');
        
        if (Str::startsWith($filePath, 'submissions/')) {
            $filePath = Str::after($filePath, 'submissions/');
        }

        $publicUrl = "{$supabaseUrl}/storage/v1/object/public/{$bucket}/{$filePath}";
        
        try {
            $response = Http::get($publicUrl);
            
            if ($response->successful()) {
                $fileName = $document->original_name ?? basename($document->file_path);
                
                return response($response->body(), 200)
                    ->header('Content-Type', $document->file_type)
                    ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
            }
            
            abort(404, 'File not found');
        } catch (\Exception $e) {
            Log::error('Download error: ' . $e->getMessage());
            abort(500, 'Failed to download file');
        }
    }
}