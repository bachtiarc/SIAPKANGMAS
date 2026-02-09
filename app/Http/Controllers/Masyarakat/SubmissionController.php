<?php

namespace App\Http\Controllers\Masyarakat;

use App\Http\Controllers\Controller;
use App\Models\Submission;
use App\Models\SubmissionDocument;
use App\Services\BrevoMailer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class SubmissionController extends Controller
{
    private function ensureMasyarakatUser(): void
    {
        $user = auth()->user();

        if (!$user) {
            abort(403, 'Unauthorized access.');
        }

        if ($user->user_type !== 'masyarakat_umum') {
            abort(403, 'Unauthorized access.');
        }
    }

    public function index(Request $request)
    {
        $this->ensureMasyarakatUser();

        $user = auth()->user();

        $query = Submission::where('user_id', $user->id)
            ->with(['handler']);

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
                    $q->whereIn('status', ['in_progress', 'on_progress', 'diproses']);
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
        $this->ensureMasyarakatUser();
        return view('masyarakat.submissions.create');
    }

    public function store(Request $request, BrevoMailer $brevo)
    {
        $this->ensureMasyarakatUser();

        $user = auth()->user();

        $validated = $request->validate([
            'title'             => 'required|string|max:255',
            'description'       => 'required|string',
            'tujuan_permohonan' => 'required|string|max:5000',

            'cara_penyampaian' => ['required', Rule::in(['online', 'datang_langsung'])],

            'datang_langsung_opsi' => [
                'required_if:cara_penyampaian,datang_langsung',
                Rule::in(['flashdisk', 'cetak', 'keduanya']),
            ],

            'documents.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('documents')) {
            $totalSize = 0;
            foreach ($request->file('documents') as $file) {
                if ($file && $file->isValid()) {
                    $totalSize += $file->getSize();
                }
            }

            if ($totalSize > 6 * 1024 * 1024) {
                return back()
                    ->withErrors(['documents' => 'Total ukuran semua dokumen tidak boleh lebih dari 6MB.'])
                    ->withInput();
            }
        }

        $opsiDatang = [];
        if ($validated['cara_penyampaian'] === 'datang_langsung') {
            if ($validated['datang_langsung_opsi'] === 'keduanya') {
                $opsiDatang = ['flashdisk', 'cetak'];
            } else {
                $opsiDatang = [$validated['datang_langsung_opsi']];
            }
        }

        $submission = Submission::create([
            'user_id'             => $user->id,
            'title'               => $validated['title'],
            'subject'             => $validated['title'],
            'description'         => $validated['description'],
            'status'              => 'pending',
            'tujuan_permohonan'   => $validated['tujuan_permohonan'],
            'cara_penyampaian'    => $validated['cara_penyampaian'],
            'datang_langsung_opsi'=> $opsiDatang,
        ]);

        if ($request->hasFile('documents')) {
            foreach (array_slice($request->file('documents'), 0, 3) as $document) {
                if ($document && $document->isValid()) {
                    $filename = Str::random(40) . '.' . $document->getClientOriginalExtension();
                    $path = $document->storeAs((string) $submission->id, $filename, 'supabase');

                    SubmissionDocument::create([
                        'submission_id' => $submission->id,
                        'original_name' => $document->getClientOriginalName(),
                        'file_path'     => $path,
                        'file_type'     => $document->getMimeType(),
                        'file_size'     => $document->getSize(),
                    ]);
                }
            }
        }

        try {
            $submission->load(['user']);

            $html = view('emails.submission-created', [
                'user'       => $user,
                'submission' => $submission,
            ])->render();

            $brevo->sendTransactional(
                toEmail: $user->email,
                toName: $user->name,
                subject: "Permohonan Informasi Diterima ({$submission->ticket_id})",
                htmlContent: $html
            );

        } catch (\Throwable $e) {
            Log::error('BREVO ERROR (masyarakat)', [
                'ticket_id' => $submission->ticket_id,
                'error' => $e->getMessage(),
            ]);
        }

        $from = $request->query('from', 'index');

        return redirect()
            ->route('masyarakat.submissions.create', ['from' => $from])
            ->with('success', true)
            ->with('ticket_id', $submission->ticket_id)
            ->with('submission_id', $submission->id);
    }

    public function show(Submission $submission)
    {
        $this->ensureMasyarakatUser();

        if ($submission->user_id !== auth()->id()) {
            abort(403);
        }

        $submission->load(['handler', 'documents', 'statusHistories']);

        return view('masyarakat.submissions.show', compact('submission'));
    }

    public function viewDocument(SubmissionDocument $document)
    {
        $this->ensureMasyarakatUser();

        if ($document->submission->user_id !== auth()->id()) {
            abort(403);
        }

        $supabaseUrl = rtrim(env('SUPABASE_URL'), '/');
        $bucket = env('SUPABASE_SUBMISSIONS_BUCKET', 'submissions');
        $path = ltrim($document->file_path, '/');

        if (Str::startsWith($path, 'submissions/')) {
            $path = Str::after($path, 'submissions/');
        }

        return redirect()->away("{$supabaseUrl}/storage/v1/object/public/{$bucket}/{$path}");
    }

    public function downloadDocument(SubmissionDocument $document)
    {
        $this->ensureMasyarakatUser();

        if ($document->submission->user_id !== auth()->id()) {
            abort(403);
        }

        $supabaseUrl = rtrim(env('SUPABASE_URL'), '/');
        $bucket = env('SUPABASE_SUBMISSIONS_BUCKET', 'submissions');
        $path = ltrim($document->file_path, '/');

        if (Str::startsWith($path, 'submissions/')) {
            $path = Str::after($path, 'submissions/');
        }

        $url = "{$supabaseUrl}/storage/v1/object/public/{$bucket}/{$path}";

        $response = Http::get($url);

        if (!$response->successful()) {
            abort(404, 'File tidak ditemukan');
        }

        return response($response->body(), 200)
            ->header('Content-Type', $document->file_type)
            ->header('Content-Disposition', 'attachment; filename="'.$document->original_name.'"');
    }
}