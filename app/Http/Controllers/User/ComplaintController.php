<?php

namespace App\Http\Controllers\User;

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
        
        if ($user->user_type !== 'pegawai') {
            abort(403, 'Unauthorized access.');
        }

        $query = Complaint::where('user_id', $user->id)
            ->with(['category', 'handler']);

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('ticket_number', 'ilike', "%{$search}%")
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

        $complaints = $query->latest()->paginate(10)->withQueryString();

        return view('user.complaints.index', compact('complaints'));
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
            ->where(function($query) use ($userType) {
                $query->where('user_type', $userType)
                    ->orWhere('user_type', 'all');
            })
            ->orderBy('name')
            ->get();
        
        return view('user.complaints.create', compact('categories'));
    }

    /**
     * Store a newly created complaint in storage
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
            'documents.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048', // Max 2MB per file
        ], [
            'category_id.required' => 'Kategori pengaduan wajib dipilih.',
            'category_id.exists' => 'Kategori tidak valid.',
            'subject.required' => 'Subjek pengaduan wajib diisi.',
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

        $kodeLayanan = "PD";
        $kodeBalai = $user->bidang_code ?? "01";
        $kodeSubBagian = $user->sub_bagian_code ?? "106";
        
        $tanggal = now()->format('dmY');
        
        $count = Complaint::whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->count() + 1;
        
        $urutan = str_pad($count, 3, '0', STR_PAD_LEFT);
        
        $ticketNumber = "{$kodeLayanan}.{$kodeBalai}.{$kodeSubBagian}.{$tanggal}_{$urutan}";

        $complaint = Complaint::create([
            'user_id' => $user->id,
            'category_id' => $validated['category_id'],
            'subject' => $validated['subject'],
            'description' => $validated['description'],
            'ticket_number' => $ticketNumber,
            'status' => 'pending',
        ]);

        if ($request->hasFile('documents')) {
            $documents = $request->file('documents');
            
            $documents = array_slice($documents, 0, 3);
            
            foreach ($documents as $document) {
                if ($document->isValid()) {
                    $filename = Str::random(40) . '.' . $document->getClientOriginalExtension();
                    
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

        try {
            Mail::to($user->email)->send(new ComplaintCreated($complaint));
        } catch (\Exception $e) {
            \Log::error('Failed to send complaint email: ' . $e->getMessage());
        }

        $from = $request->query('from', 'index');

        return redirect()->route('user.complaints.create', ['from' => $from])
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
        
        if ($complaint->user_id !== $user->id) {
            abort(403, 'Unauthorized access.');
        }
        $complaint->load(['category', 'handler', 'statusHistories', 'documents']);

        return view('user.complaints.show', compact('complaint'));
    }

    /**
     * View specific document
     */
    public function viewDocument(ComplaintDocument $document)
    {
        $user = auth()->user();

        if ($document->complaint->user_id !== $user->id) {
            abort(403, 'Unauthorized access.');
        }

        $supabaseUrl = rtrim(env('SUPABASE_URL'), '/');
        $bucket = env('SUPABASE_COMPLAINTS_BUCKET', 'complaints');

        $path = ltrim($document->file_path, '/');
        if (Str::startsWith($path, 'complaints/')) {
            $path = Str::after($path, 'complaints/');
        }

        $urlNormal = "{$supabaseUrl}/storage/v1/object/public/{$bucket}/{$path}";

        $urlLegacy = "{$supabaseUrl}/storage/v1/object/public/{$bucket}/complaints/{$path}";

        $res = Http::get($urlNormal);
        $finalUrl = $res->successful() ? $urlNormal : $urlLegacy;

        return redirect()->away($finalUrl);
    }

    /**
     * Download complaint document
     */
    public function downloadDocument(ComplaintDocument $document)
    {
        $user = auth()->user();
        
        if ($document->complaint->user_id !== $user->id) {
            abort(403, 'Unauthorized access.');
        }
        
        $supabaseUrl = rtrim(env('SUPABASE_URL'), '/');
        $bucket = env('SUPABASE_COMPLAINTS_BUCKET', 'complaints');
        $filePath = ltrim($document->file_path, '/');
        
        if (Str::startsWith($filePath, 'complaints/')) {
            $filePath = Str::after($filePath, 'complaints/');
        }

        $publicUrl = "{$supabaseUrl}/storage/v1/object/public/{$bucket}/{$filePath}";
        
        try {
            $response = Http::get($publicUrl);
            
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