<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Consultation;
use App\Models\ConsultationDocument;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use App\Services\BrevoMailer;

class ConsultationController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        if ($user->user_type !== 'pegawai') {
            abort(403, 'Unauthorized access.');
        }

        $query = Consultation::where('user_id', $user->id)
            ->with(['handler', 'applicant']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('ticket_number', 'ilike', "%{$search}%")
                  ->orWhere('subject', 'ilike', "%{$search}%");
            });
        }

        if ($request->filled('status') && $request->status !== 'semua') {
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

        $consultations = $query->latest()->paginate(10)->withQueryString();

        return view('user.consultations.index', compact('consultations'));
    }

    public function create()
    {
        $user = auth()->user();
        if ($user->user_type !== 'pegawai') {
            abort(403, 'Unauthorized access.');
        }

        return view('user.consultations.create');
    }

    public function store(Request $request, BrevoMailer $brevo)
    {
        $user = auth()->user();
        if ($user->user_type !== 'pegawai') {
            abort(403, 'Unauthorized access.');
        }

        $validated = $request->validate([
            'nama_lengkap'   => 'required|string|max:255',
            'nik'            => 'required|string|size:16',
            'email'          => 'nullable|email|max:255',
            'phone'          => 'required|string|max:20',

            // ✅ TAMBAH PEKERJAAN
            'pekerjaan'           => 'required|string|max:255',
            'pekerjaan_lainnya'   => 'nullable|string|max:255',

            'kabupaten_kode' => 'required|string',
            'kecamatan_kode' => 'required|string',
            'desa_kode'      => 'required|string',

            'alamat_detail'  => 'required|string|max:500',
            'foto_ktp'       => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',

            'subject'        => 'required|string|max:255',
            'description'    => 'required|string',

            'documents.*'    => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ], [
            'email.email' => 'Format email tidak valid. Contoh yang benar: ahmadsubari@gmail.com',
        ]);

        // ✅ FINAL PEKERJAAN (kalau Lainnya -> ambil input pekerjaan_lainnya)
        $pekerjaanFinal = $validated['pekerjaan'] === 'Lainnya'
            ? trim((string)($validated['pekerjaan_lainnya'] ?? ''))
            : $validated['pekerjaan'];

        if ($validated['pekerjaan'] === 'Lainnya' && $pekerjaanFinal === '') {
            return back()
                ->withInput()
                ->withErrors(['pekerjaan_lainnya' => 'Pekerjaan (Lainnya) wajib diisi.']);
        }

        if (!empty($validated['nik']) && !empty($validated['email']) && !empty($validated['phone'])) {
            $existsUserAcc =
                User::where('nik', $validated['nik'])->exists()
                || User::where('email', $validated['email'])->exists()
                || User::where('phone', $validated['phone'])->exists();

            if ($existsUserAcc) {
                return back()
                    ->withInput()
                    ->with('toast_error', 'Untuk pengguna yang telah memiliki akun, silakan membuat pengajuan melalui dashboard sendiri.')
                    ->with('toast_duration', 9000);
            }
        }

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

        DB::beginTransaction();

        try {
            $ticketNumber = $this->generateTicketNumber($validated['nik']);

            while (Consultation::where('ticket_number', $ticketNumber)->exists()) {
                $ticketNumber = $this->generateTicketNumber($validated['nik']);
            }

            $consultation = Consultation::create([
                'user_id'           => $user->id,
                'consultation_type' => 'konsultasi',
                'subject'           => $validated['subject'],
                'description'       => $validated['description'],
                'ticket_number'     => $ticketNumber,
                'status'            => 'pending',
                'attachment'        => null,
            ]);

            $kab  = DB::table('wilayah')->where('kode', $validated['kabupaten_kode'])->first();
            $kec  = DB::table('wilayah')->where('kode', $validated['kecamatan_kode'])->first();
            $desa = DB::table('wilayah')->where('kode', $validated['desa_kode'])->first();

            $kabName = $kab->nama ?? null;
            $kecName = $kec->nama ?? null;

            $isKelurahan = $this->detectIsKelurahan($kabName, $kecName);

            // ✅ FIX: FOTO KTP MASUK BUCKET CONSULTATIONS
            // sebelumnya: $request->file('foto_ktp')->store('ktp', 'supabase');
            $ktpFile = $request->file('foto_ktp');
            $ktpName = (string) Str::uuid() . '.' . $ktpFile->getClientOriginalExtension();

            // ✅ simpan sesuai target: <NIK>/<filename>
            $ktpPath = $validated['nik'] . '/' . $ktpName;

            // ✅ upload ke bucket ktp-photos via disk supabase_ktp
            Storage::disk('supabase_ktp')->put(
                $ktpPath,
                file_get_contents($ktpFile)
            );

            $consultation->applicant()->create([
                'nama_lengkap'   => $validated['nama_lengkap'],
                'nik'            => $validated['nik'],
                'email'          => $validated['email'] ?? null,
                'phone'          => $validated['phone'],

                // ✅ SIMPAN PEKERJAAN
                'pekerjaan'      => $pekerjaanFinal,

                'alamat_detail'  => $validated['alamat_detail'],

                'kabupaten_kode' => $validated['kabupaten_kode'],
                'kabupaten_nama' => $kabName,
                'kecamatan_kode' => $validated['kecamatan_kode'],
                'kecamatan_nama' => $kecName,
                'desa_kode'      => $validated['desa_kode'],
                'desa_nama'      => $desa->nama ?? null,

                'provinsi'       => 'Jawa Tengah',
                'is_kelurahan'   => $isKelurahan,
                'foto_ktp'       => $ktpPath,
            ]);

            if ($request->hasFile('documents')) {
                foreach (array_slice($request->file('documents'), 0, 3) as $file) {
                    if ($file && $file->isValid()) {
                        $filename = Str::random(40) . '.' . $file->getClientOriginalExtension();
                        $path = $consultation->id . '/' . $filename;

                        // ✅ DISK INI HARUS BUCKET CONSULTATIONS
                        Storage::disk('supabase_consultations')->put(
                            $path,
                            file_get_contents($file)
                        );

                        ConsultationDocument::create([
                            'consultation_id' => $consultation->id,
                            'original_name'   => $file->getClientOriginalName(),
                            'file_path'       => $path,
                            'file_type'       => $file->getMimeType(),
                            'file_size'       => $file->getSize(),
                        ]);
                    }
                }
            }

            if (!empty($validated['email'])) {
                try {
                    $consultation->load(['applicant']);

                    $html = view('emails.consultation-created', [
                        'consultation' => $consultation,
                    ])->render();

                    $brevo->sendTransactional(
                        toEmail: $validated['email'],
                        toName: $validated['nama_lengkap'],
                        subject: "Konfirmasi Pengajuan Konsultasi - {$consultation->ticket_number}",
                        htmlContent: $html
                    );
                } catch (\Throwable $e) {
                    Log::error('BREVO ERROR CO ADMIN (consultation)', [
                        'error' => $e->getMessage(),
                        'ticket_number' => $consultation->ticket_number,
                    ]);
                }
            }

            DB::commit();

            return redirect()
                ->route('user.consultations.create')
                ->with('success', true)
                ->with('ticket_id', $consultation->ticket_number)
                ->with('consultation_id', $consultation->id);

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('STORE CO ADMIN (consultation) failed', ['error' => $e->getMessage()]);

            return back()
                ->withInput()
                ->with('toast_error', 'Terjadi kesalahan saat menyimpan pengajuan. Silakan coba lagi.')
                ->with('toast_duration', 9000);
        }
    }

    public function show(Consultation $consultation)
    {
        $user = auth()->user();
        if ($user->user_type !== 'pegawai') {
            abort(403, 'Unauthorized access.');
        }

        if ((int) $consultation->user_id !== (int) $user->id) {
            abort(403, 'Unauthorized access.');
        }

        $consultation->load(['handler', 'documents', 'applicant', 'statusHistories']);

        return view('user.consultations.show', compact('consultation'));
    }

    public function downloadDocument(ConsultationDocument $document)
    {
        $user = auth()->user();
        if ($user->user_type !== 'pegawai') {
            abort(403, 'Unauthorized access.');
        }

        if ((int) $document->consultation->user_id !== (int) $user->id) {
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
            Log::error('Download error (consultation co-admin): ' . $e->getMessage());
            abort(500, 'Failed to download file');
        }
    }

    private function generateTicketNumber(string $nik): string
    {
        $nik = preg_replace('/\D+/', '', $nik);
        $nik = str_pad($nik, 16, '0', STR_PAD_RIGHT);

        $nikConsultation = substr($nik, 8, 4) ?: '0000';
        $date = now()->format('dmY');

        $todayPrefix = "KL.CA{$nikConsultation}.{$date}_";

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

        return "KL.CA{$nikConsultation}.{$date}_{$sequence}";
    }

    private function detectIsKelurahan(?string $kabName, ?string $kecName): bool
    {
        $kab = strtolower((string) $kabName);
        $kec = strtolower((string) $kecName);
        return str_contains($kab, 'kota') || str_contains($kec, 'kota');
    }
}