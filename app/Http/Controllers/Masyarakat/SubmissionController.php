<?php

namespace App\Http\Controllers\Masyarakat;

use App\Http\Controllers\Controller;
use App\Models\Submission;
use App\Models\SubmissionDocument;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Mail\MasyarakatSubmissionCreated;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class SubmissionController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        if ($user->user_type !== 'masyarakat_umum') {
            abort(403, 'Unauthorized access.');
        }

        $query = Submission::where('user_id', $user->id)
            ->with(['category', 'handler']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('ticket_id', 'ilike', "%{$search}%")
                  ->orWhere('title', 'ilike', "%{$search}%")
                  ->orWhere('subject', 'ilike', "%{$search}%");
            });
        }

        if ($request->filled('status') && $request->status !== 'semua') {
            $status = strtolower($request->status);
            $query->where(function ($q) use ($status) {
                if ($status === 'pending') {
                    $q->whereIn('status', ['pending', 'belum diproses']);
                } elseif ($status === 'diproses') {
                    $q->whereIn('status', ['in_progress', 'on_progress', 'diproses', 'sedang diproses']);
                } elseif ($status === 'selesai') {
                    $q->whereIn('status', ['completed', 'selesai']);
                } elseif ($status === 'ditolak') {
                    $q->whereIn('status', ['rejected', 'ditolak']);
                }
            });
        }

        $submissions = $query->latest()->paginate(10)->withQueryString();

        return view('masyarakat.submissions.index', compact('submissions'));
    }

    public function create()
    {
        $user = auth()->user();

        if ($user->user_type !== 'masyarakat_umum') {
            abort(403, 'Unauthorized access.');
        }

        $categories = Category::active()
            ->ofType('permohonan')
            ->where(function ($q) {
                $q->where('user_type', 'masyarakat_umum')
                  ->orWhere('user_type', 'all');
            })
            ->orderBy('name')
            ->get();

        return view('masyarakat.submissions.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        if ($user->user_type !== 'masyarakat_umum') {
            abort(403, 'Unauthorized access.');
        }

        $from = $request->query('from', 'index');

        $validated = $request->validate([
            'category_id'  => 'required|exists:categories,id',
            'title'        => 'required|string|max:255',
            'description'  => 'required|string',
            'documents.*'  => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $submission = Submission::create([
            'user_id'     => $user->id,
            'category_id' => $validated['category_id'],
            'title'       => $validated['title'],
            'subject'     => $validated['title'],
            'description' => $validated['description'],
            'status'      => 'pending',
        ]);

        if ($request->hasFile('documents')) {
            foreach (array_slice($request->file('documents'), 0, 3) as $document) {
                if ($document && $document->isValid()) {
                    $filename = Str::random(40) . '.' . $document->getClientOriginalExtension();

                    $path = $document->storeAs(
                        (string) $submission->id,
                        $filename,
                        'supabase'
                    );

                    SubmissionDocument::create([
                        'submission_id'  => $submission->id,
                        'original_name'  => $document->getClientOriginalName(),
                        'file_path'      => $path,
                        'file_type'      => $document->getMimeType(),
                        'file_size'      => $document->getSize(),
                    ]);
                }
            }
        }

        try {
            Mail::to($user->email)->send(new MasyarakatSubmissionCreated($submission));
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
        }

        return redirect()
            ->route('masyarakat.submissions.create', ['from' => $from])
            ->with('success', true)
            ->with('ticket_id', $submission->ticket_id)
            ->with('submission_id', $submission->id);
    }

    public function show(Submission $submission)
    {
        if ($submission->user_id !== auth()->id()) {
            abort(403);
        }

        $submission->load(['category', 'handler', 'statusHistories', 'documents']);

        return view('masyarakat.submissions.show', compact('submission'));
    }

    public function viewDocument(SubmissionDocument $document)
    {
        if ($document->submission->user_id !== auth()->id()) {
            abort(403);
        }

        $base = rtrim(env('SUPABASE_URL'), '/');
        $bucket = env('SUPABASE_SUBMISSIONS_BUCKET', 'submissions');
        $path = ltrim($document->file_path, '/');

        return redirect()->away("{$base}/storage/v1/object/public/{$bucket}/{$path}");
    }

    public function downloadDocument(SubmissionDocument $document)
    {
        if ($document->submission->user_id !== auth()->id()) {
            abort(403);
        }

        $base = rtrim(env('SUPABASE_URL'), '/');
        $bucket = env('SUPABASE_SUBMISSIONS_BUCKET', 'submissions');
        $path = ltrim($document->file_path, '/');

        $url = "{$base}/storage/v1/object/public/{$bucket}/{$path}";

        $response = Http::get($url);

        if (!$response->successful()) {
            abort(404);
        }

        return response($response->body(), 200)
            ->header('Content-Type', $document->file_type)
            ->header('Content-Disposition', 'attachment; filename="' . $document->original_name . '"');
    }
}