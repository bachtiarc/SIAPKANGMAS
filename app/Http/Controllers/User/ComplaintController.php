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
        
        // Check if user is pegawai
        if ($user->user_type !== 'pegawai') {
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
            $query->where('status', $request->status);
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
        
        if ($user->user_type !== 'pegawai') {
            abort(403, 'Unauthorized access.');
        }

        // Get categories dari database dengan type 'pengaduan'
        $categories = Category::active()
            ->ofType('pengaduan')
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
            'documents.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:15120', // Max 15MB per file
        ], [
            'category_id.required' => 'Kategori pengaduan wajib dipilih.',
            'category_id.exists' => 'Kategori tidak valid.',
            'subject.required' => 'Subjek pengaduan wajib diisi.',
            'description.required' => 'Deskripsi lengkap wajib diisi.',
            'documents.*.mimes' => 'Format dokumen harus PDF, JPG, JPEG, atau PNG.',
            'documents.*.max' => 'Ukuran setiap dokumen maksimal 15MB.',
        ]);

        // Generate ticket number dengan format: PD.XX.YY.DDMMYYYY_***
        $kodeLayanan = "PD"; // Pengaduan
        $kodeBalai = $user->bidang_code ?? "01";
        $kodeSubBagian = $user->sub_bagian_code ?? "106";
        
        $tanggal = now()->format('dmY');
        
        // Hitung urutan pengaduan bulan ini
        $count = Complaint::whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->count() + 1;
        
        $urutan = str_pad($count, 3, '0', STR_PAD_LEFT);
        
        $ticketNumber = "{$kodeLayanan}.{$kodeBalai}.{$kodeSubBagian}.{$tanggal}_{$urutan}";

        // Create complaint (TANPA PRIORITY)
        $complaint = Complaint::create([
            'user_id' => $user->id,
            'category_id' => $validated['category_id'],
            'subject' => $validated['subject'],
            'description' => $validated['description'],
            'ticket_number' => $ticketNumber,
            'status' => 'pending',
        ]);

        // ✅ UPLOAD MULTIPLE FILES (Max 3)
        if ($request->hasFile('documents')) {
            $documents = $request->file('documents');
            
            // Limit to 3 files
            $documents = array_slice($documents, 0, 3);
            
            foreach ($documents as $document) {
                if ($document->isValid()) {
                    // Generate unique filename
                    $filename = Str::random(40) . '.' . $document->getClientOriginalExtension();
                    
                    // Store file in complaints folder
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

        return redirect()->route('user.complaints.create')
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
        $complaint->load(['category', 'handler', 'statusHistories', 'documents']);

        return view('user.complaints.show', compact('complaint'));
    }

    /**
     * View specific document
     */
    public function viewDocument(ComplaintDocument $document)
    {
        $user = auth()->user();

        // Authorization check
        if ($document->complaint->user_id !== $user->id) {
            abort(403, 'Unauthorized access.');
        }

        $supabaseUrl = rtrim(env('SUPABASE_URL'), '/');
        $bucket = env('SUPABASE_COMPLAINTS_BUCKET', 'complaints');

        $path = ltrim($document->file_path, '/');

        // buang prefix "complaints/" kalau keburu kesimpen (biar konsisten)
        if (Str::startsWith($path, 'complaints/')) {
            $path = Str::after($path, 'complaints/');
        }

        // normal: bucket/{path}
        $urlNormal = "{$supabaseUrl}/storage/v1/object/public/{$bucket}/{$path}";

        // legacy fallback: bucket/complaints/{path}
        $urlLegacy = "{$supabaseUrl}/storage/v1/object/public/{$bucket}/complaints/{$path}";

        $res = Http::get($urlNormal);
        $finalUrl = $res->successful() ? $urlNormal : $urlLegacy;

        // tampilkan di browser (tanpa download)
        return redirect()->away($finalUrl);
    }
}