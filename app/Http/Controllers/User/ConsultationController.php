<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Consultation;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Mail\ConsultationCreated;
use Illuminate\Support\Facades\Mail;

class ConsultationController extends Controller
{
    /**
     * Display a listing of consultations
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Check if user is pegawai
        if ($user->user_type !== 'pegawai') {
            abort(403, 'Unauthorized access.');
        }

        $query = Consultation::where('user_id', $user->id)
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

        $consultations = $query->latest()->paginate(10)->withQueryString();

        return view('user.consultations.index', compact('consultations'));
    }

    /**
     * Show the form for creating a new consultation
     */
    public function create()
    {
        $user = auth()->user();
        
        if ($user->user_type !== 'pegawai') {
            abort(403, 'Unauthorized access.');
        }

        // Get categories dari database dengan type 'konsultasi'
        $categories = Category::active()
            ->ofType('konsultasi')
            ->orderBy('name')
            ->get();

        return view('user.consultations.create', compact('categories'));
    }

    /**
     * Store a newly created consultation in storage
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
            'documents.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:15120', 
        ], [
            'category_id.required' => 'Kategori konsultasi wajib dipilih.',
            'category_id.exists' => 'Kategori tidak valid.',
            'subject.required' => 'Subjek konsultasi wajib diisi.',
            'description.required' => 'Deskripsi lengkap wajib diisi.',
            'documents.*.mimes' => 'Format dokumen harus PDF, JPG, JPEG, atau PNG.',
            'documents.*.max' => 'Ukuran setiap dokumen maksimal 5MB.',
        ]);

        $kodeLayanan = "KL"; 
        $kodeBalai = $user->bidang_code ?? "01"; 
        $kodeSubBagian = $user->sub_bagian_code ?? "106"; 
        
        $tanggal = now()->format('dmY'); 

        $count = Consultation::whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->count() + 1;
        
        $urutan = str_pad($count, 3, '0', STR_PAD_LEFT); 
        
        $ticketNumber = "{$kodeLayanan}.{$kodeBalai}.{$kodeSubBagian}.{$tanggal}_{$urutan}";

        $consultation = Consultation::create([
            'user_id' => $user->id,
            'category_id' => $validated['category_id'],
            'consultation_type' => 'konsultasi', 
            'subject' => $validated['subject'],
            'description' => $validated['description'],
            'ticket_number' => $ticketNumber,
            'status' => 'pending',
        ]);

        if ($request->hasFile('documents')) {
            $document = $documents[0]; 
            $path = $document->storeAs('consultations', $filename, 'public');
            $consultation->attachment = $path;
            $consultation->save();
        }


        // Send email notification
        try {
            Mail::to($user->email)->send(new ConsultationCreated($consultation));
        } catch (\Exception $e) {
            \Log::error('Failed to send consultation email: ' . $e->getMessage());
            // Continue execution even if email fails
        }

        return redirect()->route('user.consultations.create')
            ->with('success', true)
            ->with('ticket_id', $consultation->ticket_number)
            ->with('consultation_id', $consultation->id);
    }

    /**
     * Display the specified consultation
     */
    public function show(Consultation $consultation)
    {
        $user = auth()->user();
        
        // Authorization check
        if ($consultation->user_id !== $user->id) {
            abort(403, 'Unauthorized access.');
        }

        // Load relationships
        $consultation->load(['category', 'handler', 'statusHistories']);

        return view('user.consultations.show', compact('consultation'));
    }
}