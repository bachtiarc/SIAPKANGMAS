<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Submission;
use App\Models\SubmissionDocument;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Services\BrevoMailer;

class SubmissionController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        if ($user->user_type !== 'pegawai') {
            abort(403, 'Unauthorized access.');
        }

        $query = Submission::where('user_id', $user->id)
            ->with(['category', 'handler']);

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('ticket_id', 'ilike', "%{$search}%")
                  ->orWhere('title', 'ilike', "%{$search}%")
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
                } elseif ($statusFilter === 'ditolak') {
                    $q->whereIn('status', ['rejected', 'ditolak']);
                }
            });
        }

        $submissions = $query->latest()->paginate(10)->withQueryString();

        return view('user.submissions.index', compact('submissions'));
    }

    public function create()
    {
        $user = auth()->user();

        if ($user->user_type !== 'pegawai') {
            abort(403, 'Unauthorized access.');
        }

        $categories = Category::active()
            ->ofType('permohonan')
            ->where(function ($query) {
                $query->where('user_type', 'pegawai')
                    ->orWhere('user_type', 'all');
            })
            ->orderBy('name')
            ->get();

        return view('user.submissions.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'documents.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ], [
            'category_id.required' => 'Kategori informasi wajib dipilih.',
            'category_id.exists'   => 'Kategori tidak valid.',
            'title.required'       => 'Judul permohonan wajib diisi.',
            'description.required' => 'Deskripsi lengkap wajib diisi.',
            'documents.*.mimes'    => 'Format dokumen harus PDF, JPG, JPEG, atau PNG.',
            'documents.*.max'      => 'Ukuran setiap dokumen maksimal 2MB.',
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
            'user_id'      => $user->id,
            'category_id'  => $validated['category_id'],
            'title'        => $validated['title'],
            'subject'      => $validated['title'],
            'description'  => $validated['description'],
            'status'       => 'pending',
        ]);

        if ($request->hasFile('documents')) {
            $documents = array_slice($request->file('documents'), 0, 3);

            foreach ($documents as $document) {
                if ($document && $document->isValid()) {
                    $filename = Str::random(40) . '.' . $document->getClientOriginalExtension();
                    $path = $document->storeAs((string) $submission->id, $filename, 'supabase');

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
            $category = Category::find($submission->category_id);
            $submission->refresh();

            $html = view('emails.submission_created', [
                'user' => $user,
                'submission' => $submission,
                'category' => $category,
            ])->render();

            Log::info('BREVO DEBUG (pegawai) - about to send', [
                'to' => $user->email,
                'from' => env('MAIL_FROM_ADDRESS'),
                'from_name' => env('MAIL_FROM_NAME'),
                'has_api_key' => (bool) env('BREVO_API_KEY'),
                'app_env' => env('APP_ENV'),
            ]);

            $brevo = new BrevoMailer();
            $brevo->sendTransactional(
                $user->email,
                $user->name ?? null,
                "Permohonan Informasi Diterima ({$submission->ticket_id})",
                $html
            );

            Log::info('BREVO DEBUG (pegawai) - sent OK', ['to' => $user->email]);
        } catch (\Throwable $e) {
            Log::error('BREVO DEBUG (pegawai) - failed', [
                'to' => $user->email,
                'error' => $e->getMessage(),
            ]);
        }

        $from = $request->query('from', 'index');

        return redirect()->route('user.submissions.create', ['from' => $from])
            ->with('success', true)
            ->with('ticket_id', $submission->ticket_id)
            ->with('submission_id', $submission->id);
    }

    public function show(Submission $submission)
    {
        $user = auth()->user();

        if ($submission->user_id !== $user->id) {
            abort(403, 'Unauthorized access.');
        }

        $submission->load(['category', 'handler', 'statusHistories', 'documents']);

        return view('user.submissions.show', compact('submission'));
    }

    public function viewDocument(SubmissionDocument $document)
    {
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