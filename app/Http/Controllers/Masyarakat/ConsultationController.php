<?php

namespace App\Http\Controllers\Masyarakat;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Consultation;
use App\Models\ConsultationDocument;
use App\Mail\ConsultationCreated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ConsultationController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        if ($user->user_type !== 'masyarakat_umum') {
            abort(403, 'Unauthorized access.');
        }

        $query = Consultation::where('user_id', $user->id)->with(['category', 'handler']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('ticket_number', 'ilike', "%{$search}%")
                  ->orWhere('subject', 'ilike', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status') && $request->status !== 'semua') {
            $statusFilter = strtolower($request->status);

            $query->where(function ($q) use ($statusFilter) {
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

        $consultations = $query->latest()->paginate(10)->withQueryString();

        return view('masyarakat.consultations.index', compact('consultations'));
    }

    public function create()
    {
        $user = auth()->user();

        if ($user->user_type !== 'masyarakat_umum') {
            abort(403, 'Unauthorized access.');
        }

        $userType = $user->user_type;

        $categories = Category::active()
            ->ofType('konsultasi')
            ->where(function ($query) use ($userType) {
                $query->where('user_type', $userType)
                      ->orWhere('user_type', 'all');
            })
            ->orderBy('name')
            ->get();

        return view('masyarakat.consultations.create', compact('categories'));
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
            'subject'      => 'required|string|max:255',
            'description'  => 'required|string',
            'documents.*'  => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
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

        $nik = $user->nik ?? '000000000000000000';
        $lastSixNik = substr($nik, 10, 6);
        $date = now()->format('dmY');

        $todayPrefix = "KL.{$lastSixNik}.{$date}_";
        $last = Consultation::where('ticket_number', 'like', $todayPrefix . '%')
            ->orderBy('ticket_number', 'desc')
            ->value('ticket_number');

        $nextSeq = 1;
        if ($last) {
            $parts = explode('_', $last);
            $lastSeq = (int)($parts[1] ?? 0);
            $nextSeq = $lastSeq + 1;
        }

        $sequence = str_pad((string)$nextSeq, 3, '0', STR_PAD_LEFT);
        $ticketNumber = "KL.{$lastSixNik}.{$date}_{$sequence}";

        $consultation = DB::transaction(function () use ($user, $validated, $ticketNumber, $request) {
            $consultation = Consultation::create([
                'user_id'            => $user->id,
                'category_id'        => $validated['category_id'],
                'consultation_type'  => 'konsultasi',
                'subject'            => $validated['subject'],
                'description'        => $validated['description'],
                'ticket_number'      => $ticketNumber,
                'status'             => 'pending',
                'attachment'         => null,
            ]);

            if ($request->hasFile('documents')) {
                foreach ($request->file('documents') as $file) {
                    if ($file && $file->isValid()) {
                        $filename = Str::random(40) . '.' . $file->getClientOriginalExtension();
                        $path = $consultation->id . '/' . $filename;

                        Storage::disk('supabase_consultations')->put(
                            $path,
                            file_get_contents($file)
                        );

                        ConsultationDocument::create([
                            'consultation_id' => $consultation->id,
                            'original_name'   => $file->getClientOriginalName(),
                            'file_path'       => $path,
                            'file_type'       => $file->getClientOriginalExtension(),
                            'file_size'       => $file->getSize(),
                        ]);
                    }
                }
            }

            return $consultation;
        });

        try {
            Mail::to($user->email)->send(new ConsultationCreated($consultation));
        } catch (\Exception $e) {
            \Log::error('Failed to send consultation email: ' . $e->getMessage());
        }

        return redirect()
            ->route('masyarakat.consultations.create', ['from' => $from])
            ->with('success', true)
            ->with('ticket_id', $consultation->ticket_number)
            ->with('consultation_id', $consultation->id);
    }

    public function show(Consultation $consultation)
    {
        $user = auth()->user();

        if ($consultation->user_id !== $user->id) {
            abort(403, 'Unauthorized access.');
        }

        $consultation->load(['category', 'handler', 'statusHistories', 'documents']);

        return view('masyarakat.consultations.show', compact('consultation'));
    }

    public function downloadDocument(ConsultationDocument $document)
    {
        $user = auth()->user();

        if ($document->consultation->user_id !== $user->id) {
            abort(403, 'Unauthorized access.');
        }

        $supabaseUrl = rtrim(env('SUPABASE_URL'), '/');
        $bucket = env('SUPABASE_CONSULTATIONS_BUCKET', 'consultations');
        $filePath = ltrim($document->file_path, '/');

        if (Str::startsWith($filePath, 'consultations/')) {
            $filePath = Str::after($filePath, 'consultations/');
        }

        $publicUrl = "{$supabaseUrl}/storage/v1/object/public/{$bucket}/{$filePath}";

        try {
            $response = \Illuminate\Support\Facades\Http::get($publicUrl);

            if ($response->successful()) {
                $fileName = $document->original_name ?? basename($document->file_path);

                return response($response->body(), 200)
                    ->header('Content-Type', $response->header('Content-Type') ?? 'application/octet-stream')
                    ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
            }

            abort(404, 'File not found');
        } catch (\Exception $e) {
            \Log::error('Download error: ' . $e->getMessage());
            abort(500, 'Failed to download file');
        }
    }
}
