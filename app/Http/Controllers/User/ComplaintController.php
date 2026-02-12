<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\ComplaintApplicant;
use App\Models\ComplaintDocument;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ComplaintController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        if ($user->user_type !== 'pegawai') abort(403, 'Unauthorized access.');

        $query = Complaint::where('user_id', $user->id)
            ->with(['applicant', 'handler']);

        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            $query->where(function ($q) use ($search) {
                $q->where('ticket_number', 'ilike', "%{$search}%")
                  ->orWhere('subject', 'ilike', "%{$search}%");
            });
        }

        if ($request->filled('status') && $request->status !== 'semua') {
            $statusFilter = strtolower((string) $request->status);

            $query->where(function ($q) use ($statusFilter) {
                if ($statusFilter === 'pending') {
                    $q->whereIn('status', ['pending', 'belum diproses']);
                } elseif ($statusFilter === 'diproses') {
                    $q->whereIn('status', ['diproses', 'in_progress', 'on_progress', 'sedang diproses']);
                } elseif ($statusFilter === 'selesai') {
                    $q->whereIn('status', ['selesai', 'completed']);
                } elseif ($statusFilter === 'ditolak') {
                    $q->whereIn('status', ['ditolak', 'rejected']);
                }
            });
        }

        $complaints = $query->latest()->paginate(10)->withQueryString();
        return view('user.complaints.index', compact('complaints'));
    }

    public function create()
    {
        $user = auth()->user();
        if ($user->user_type !== 'pegawai') abort(403, 'Unauthorized access.');

        return view('user.complaints.create');
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        if ($user->user_type !== 'pegawai') abort(403, 'Unauthorized access.');

        $validated = $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'nik' => 'required|string|size:16',
            'phone' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',

            // pekerjaan optional, tapi kalau "Lainnya" => wajib isi pekerjaan_lainnya
            'pekerjaan' => 'nullable|string|max:100',
            'pekerjaan_lainnya' => 'nullable|string|max:100',

            'kabupaten_kode' => 'required|string|max:50',
            'kecamatan_kode' => 'required|string|max:50',
            'desa_kode' => 'required|string|max:50',
            'alamat_detail' => 'required|string',

            'foto_ktp' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
            'documents.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ], [
            'nama_lengkap.required' => 'Nama lengkap wajib diisi.',
            'nik.required' => 'NIK wajib diisi.',
            'nik.size' => 'NIK harus 16 digit.',
            'phone.required' => 'Nomor telepon/WA wajib diisi.',
            'kabupaten_kode.required' => 'Kabupaten/Kota wajib dipilih.',
            'kecamatan_kode.required' => 'Kecamatan wajib dipilih.',
            'desa_kode.required' => 'Desa/Kelurahan wajib dipilih.',
            'alamat_detail.required' => 'Alamat lengkap wajib diisi.',
            'foto_ktp.required' => 'Foto KTP wajib diupload.',
            'foto_ktp.image' => 'Foto KTP harus berupa gambar.',
            'foto_ktp.max' => 'Ukuran Foto KTP maksimal 2MB.',
            'subject.required' => 'Subjek pengaduan wajib diisi.',
            'description.required' => 'Deskripsi lengkap wajib diisi.',
            'documents.*.mimes' => 'Format dokumen harus PDF, JPG, JPEG, atau PNG.',
            'documents.*.max' => 'Ukuran setiap dokumen maksimal 2MB.',
        ]);

        $pekerjaan = trim((string) ($validated['pekerjaan'] ?? ''));
        $pekerjaanLainnya = trim((string) ($validated['pekerjaan_lainnya'] ?? ''));

        $pekerjaanFinal = null;
        if ($pekerjaan !== '') {
            if (strtolower($pekerjaan) === 'lainnya') {
                $pekerjaanFinal = $pekerjaanLainnya !== '' ? $pekerjaanLainnya : null;
            } else {
                $pekerjaanFinal = $pekerjaan;
            }
        }

        if (strtolower($pekerjaan) === 'lainnya' && $pekerjaanFinal === null) {
            return back()
                ->withErrors(['pekerjaan_lainnya' => 'Pekerjaan lainnya wajib diisi jika memilih "Lainnya".'])
                ->withInput();
        }


        $nik = (string) $validated['nik'];
        $email = trim((string) ($validated['email'] ?? ''));

        $existsByNik = User::where('nik', $nik)->exists();
        $existsByEmail = ($email !== '') ? User::where('email', $email)->exists() : false;

        if ($existsByNik || $existsByEmail) {
            return back()
                ->withInput()
                ->with('toast_error', 'Data pemohon terdeteksi sudah memiliki akun. Silakan pemohon membuat pengaduan melalui dashboard sendiri.')
                ->with('toast_duration', 9000);
        }

        if ($request->hasFile('documents')) {
            $totalSize = 0;
            foreach ($request->file('documents') as $f) {
                if ($f && $f->isValid()) $totalSize += (int) $f->getSize();
            }
            if ($totalSize > 6 * 1024 * 1024) {
                return back()
                    ->withErrors(['documents' => 'Total ukuran semua dokumen tidak boleh lebih dari 6MB.'])
                    ->withInput();
            }
        }

        DB::beginTransaction();

        try {
            // nomor tiket
            $ticketNumber = $this->generateTicketNumber($validated['nik']);
            while (Complaint::where('ticket_number', $ticketNumber)->exists()) {
                $ticketNumber = $this->generateTicketNumber($validated['nik']);
            }

            $complaint = Complaint::create([
                'user_id' => $user->id,
                'subject' => $validated['subject'],
                'description' => $validated['description'],
                'ticket_number' => $ticketNumber,
                'status' => 'pending',
            ]);


            $kab  = DB::table('wilayah')->where('kode', $validated['kabupaten_kode'])->first();
            $kec  = DB::table('wilayah')->where('kode', $validated['kecamatan_kode'])->first();
            $desa = DB::table('wilayah')->where('kode', $validated['desa_kode'])->first();

            $kabName = $kab->nama ?? null;
            $kecName = $kec->nama ?? null;
            $isKelurahan = $this->detectIsKelurahan($kabName, $kecName);

            $ktpPath = null;
            $ktp = $request->file('foto_ktp');

            $ktpName = 'ktp_' . Str::random(32) . '.' . $ktp->getClientOriginalExtension();

            $ktpPath = Storage::disk('supabase_ktp')
                ->putFileAs((string) $complaint->id, $ktp, $ktpName);

            if (!$ktpPath) {
                throw new \RuntimeException('Upload KTP gagal (path kosong).');
            }

            // simpan applicant
            ComplaintApplicant::create([
                'complaint_id' => $complaint->id,
                'nama_lengkap' => $validated['nama_lengkap'],
                'nik' => $validated['nik'],
                'email' => $validated['email'] ?? null,
                'phone' => $validated['phone'],
                'alamat_detail' => $validated['alamat_detail'],

                'pekerjaan' => $pekerjaanFinal,

                'kabupaten_kode' => $validated['kabupaten_kode'],
                'kabupaten_nama' => $kabName,
                'kecamatan_kode' => $validated['kecamatan_kode'],
                'kecamatan_nama' => $kecName,
                'desa_kode' => $validated['desa_kode'],
                'desa_nama' => $desa->nama ?? null,

                'provinsi' => 'Jawa Tengah',
                'is_kelurahan' => $isKelurahan,

                'foto_ktp' => $ktpPath,
            ]);


            if ($request->hasFile('documents')) {
                $docs = array_slice($request->file('documents'), 0, 3);

                foreach ($docs as $doc) {
                    if (!$doc || !$doc->isValid()) continue;

                    $filename = Str::random(40) . '.' . $doc->getClientOriginalExtension();

                    $path = Storage::disk('supabase_complaints')
                        ->putFileAs((string) $complaint->id, $doc, $filename);

                    if (!$path) {
                        throw new \RuntimeException('Upload dokumen gagal (path kosong).');
                    }

                    ComplaintDocument::create([
                        'complaint_id' => $complaint->id,
                        'original_name' => $doc->getClientOriginalName(),
                        'file_path' => $path,
                        'file_type' => $doc->getClientOriginalExtension(),
                        'file_size' => $doc->getSize(),
                    ]);
                }
            }

            DB::commit();

            $from = $request->query('from', 'index');

            return redirect()->route('user.complaints.create', ['from' => $from])
                ->with('success', true)
                ->with('ticket_id', $complaint->ticket_number)
                ->with('complaint_id', $complaint->id);

        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('STORE CO ADMIN (complaint) failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // biar kamu tau akar masalahnya pas dev (tanpa ngubah UI)
            $msg = app()->environment('local')
                ? ('Terjadi kesalahan saat menyimpan pengaduan: ' . $e->getMessage())
                : 'Terjadi kesalahan saat menyimpan pengaduan. Silakan coba lagi.';

            return back()
                ->withInput()
                ->with('toast_error', $msg)
                ->with('toast_duration', 9000);
        }
    }

    public function show(Complaint $complaint)
    {
        $user = auth()->user();
        if ($complaint->user_id !== $user->id) abort(403, 'Unauthorized access.');

        $complaint->load(['applicant', 'handler', 'statusHistories', 'documents']);
        return view('user.complaints.show', compact('complaint'));
    }

    public function viewDocument(ComplaintDocument $document)
    {
        $user = auth()->user();
        if ($document->complaint->user_id !== $user->id) abort(403, 'Unauthorized access.');

        $supabaseUrl = rtrim(env('SUPABASE_URL'), '/');
        $bucket = env('SUPABASE_COMPLAINTS_BUCKET', 'complaints');

        $path = ltrim((string) $document->file_path, '/');
        if (Str::startsWith($path, 'complaints/')) $path = Str::after($path, 'complaints/');

        $urlNormal = "{$supabaseUrl}/storage/v1/object/public/{$bucket}/{$path}";
        return redirect()->away($urlNormal);
    }

    public function downloadDocument(ComplaintDocument $document)
    {
        $user = auth()->user();
        if ($document->complaint->user_id !== $user->id) abort(403, 'Unauthorized access.');

        $supabaseUrl = rtrim(env('SUPABASE_URL'), '/');
        $bucket = env('SUPABASE_COMPLAINTS_BUCKET', 'complaints');

        $filePath = ltrim((string) $document->file_path, '/');
        if (Str::startsWith($filePath, 'complaints/')) $filePath = Str::after($filePath, 'complaints/');

        $publicUrl = "{$supabaseUrl}/storage/v1/object/public/{$bucket}/{$filePath}";

        $response = Http::get($publicUrl);
        if (!$response->successful()) abort(404, 'File not found');

        $fileName = $document->original_name ?? basename((string) $document->file_path);

        return response($response->body(), 200)
            ->header('Content-Type', $response->header('Content-Type') ?? 'application/octet-stream')
            ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
    }

    private function generateTicketNumber(string $nik): string
    {
        $nik = preg_replace('/\D+/', '', $nik);
        $nik = str_pad($nik, 16, '0', STR_PAD_RIGHT);

        $nikPart = substr($nik, 8, 4) ?: '0000';
        $date = now()->format('dmY');

        $todayPrefix = "PD.CA{$nikPart}.{$date}_";

        $last = Complaint::where('ticket_number', 'like', $todayPrefix . '%')
            ->orderBy('ticket_number', 'desc')
            ->value('ticket_number');

        $nextSeq = 1;
        if ($last) {
            $parts = explode('_', $last);
            $lastSeq = (int) ($parts[1] ?? 0);
            $nextSeq = $lastSeq + 1;
        }

        $sequence = str_pad((string) $nextSeq, 3, '0', STR_PAD_LEFT);

        return "PD.CA{$nikPart}.{$date}_{$sequence}";
    }

    private function detectIsKelurahan(?string $kabName, ?string $kecName): bool
    {
        $kab = strtolower((string) $kabName);
        $kec = strtolower((string) $kecName);

        return str_contains($kab, 'kota') || str_contains($kec, 'kota');
    }
}