<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Consultation;
use App\Models\Category;
use App\Models\ConsultationDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Mail\ConsultationCreated;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;

class ConsultationController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        if ($user->user_type !== 'pegawai') {
            abort(403, 'Unauthorized access.');
        }

        $query = Consultation::where('user_id', $user->id)->with(['category', 'handler']);

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('ticket_number', 'ilike', "%{$search}%")
                  ->orWhere('subject', 'ilike', "%{$search}%");
            });
        }

        if ($request->has('status') && $request->status != 'semua' && $request->status != '') {
            $query->where('status', $request->status);
        }

        $consultations = $query->latest()->paginate(10)->withQueryString();
        return view('user.consultations.index', compact('consultations'));
    }

    public function create()
    {
        $user = auth()->user();
        if ($user->user_type !== 'pegawai') abort(403);
        $categories = Category::active()->ofType('konsultasi')->orderBy('name')->get();
        return view('user.consultations.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
            'documents.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:15120', 
        ]);

        $ticketNumber = "KL." . ($user->bidang_code ?? "01") . "." . ($user->sub_bagian_code ?? "106") . "." . now()->format('dmY') . "_" . str_pad(Consultation::count() + 1, 3, '0', STR_PAD_LEFT);

        $consultation = DB::transaction(function () use ($user, $validated, $ticketNumber, $request) {
            $consultation = Consultation::create([
                'user_id' => $user->id,
                'category_id' => $validated['category_id'],
                'consultation_type' => 'konsultasi', 
                'subject' => $validated['subject'],
                'description' => $validated['description'],
                'ticket_number' => $ticketNumber,
                'status' => 'pending',
                'attachment' => null, 
            ]);
            
            if ($request->hasFile('documents')) {
                foreach ($request->file('documents') as $file) {
                    if ($file->isValid()) {
                        $filename = Str::random(40) . '.' . $file->getClientOriginalExtension();
                        
                        // Store file in consultations folder using public disk
                        $path = $consultation->id . '/' . $filename;

                        Storage::disk('supabase_consultations')->put(
                            $path,
                            file_get_contents($file)
                        );

                        ConsultationDocument::create([
                            'consultation_id' => $consultation->id,
                            'original_name' => $file->getClientOriginalName(),
                            'file_path' => $path,
                            'file_type' => $file->getClientOriginalExtension(),
                            'file_size' => $file->getSize(),
                        ]);
                    }
                }
            }
            return $consultation;
        });

        try {
            Mail::to($user->email)->send(new ConsultationCreated($consultation));
        } catch (\Exception $e) {
            \Log::error('Failed to send email: ' . $e->getMessage());
        }

        return redirect()->route('user.consultations.create')
            ->with('success', true)
            ->with('ticket_id', $consultation->ticket_number)
            ->with('consultation_id', $consultation->id);
    }

    public function show(Consultation $consultation)
    {
        $user = auth()->user();
        if ($consultation->user_id !== $user->id) abort(403);

        // Load relasi. Laravel sekarang mencari via Polymorphic (trackable)
        $consultation->load(['category', 'handler', 'statusHistories', 'documents']);

        return view('user.consultations.show', compact('consultation'));
    }
}