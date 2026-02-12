<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Submission;
use App\Models\SubmissionDocument;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Services\BrevoMailer;
use Illuminate\Support\Facades\Storage;

class SubmissionController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        if ($user->user_type !== 'pegawai') {
            abort(403, 'Unauthorized access.');
        }

        $query = Submission::where('user_id', $user->id)
            ->with(['handler', 'applicant']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('ticket_id', 'ilike', "%{$search}%")
                    ->orWhere('title', 'ilike', "%{$search}%")
                    ->orWhere('subject', 'ilike', "%{$search}%");
            });
        }

        if ($request->has('status') && $request->status !== 'semua' && $request->status !== '') {
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

        return view('user.submissions.create');
    }

    private function generateTicketId(string $nik): string
    {
        $nik = preg_replace('/\D/', '', (string) $nik);

        $nikPart = substr($nik, 8, 4);
        $nikPart = $nikPart ?: '0000';

        $today = now();
        $datePart = $today->format('dmy');

        $countThisMonth = Submission::whereYear('created_at', $today->year)
            ->whereMonth('created_at', $today->month)
            ->count();

        $sequence = str_pad($countThisMonth + 1, 3, '0', STR_PAD_LEFT);

        return "PI.CA{$nikPart}.{$datePart}_{$sequence}";
    }

    private function detectIsKelurahan(?string $kabupatenName, ?string $kecamatanName): bool
    {
        $kabupatenName = trim((string) $kabupatenName);
        $kecamatanName = trim((string) $kecamatanName);

        $isKota = str_starts_with(strtolower($kabupatenName), 'kota');

        $kecamatanKota = [
            'banjarnegara','purwokerto timur','purwokerto barat','purwokerto selatan','purwokerto utara',
            'batang','blora','boyolali','brebes','cilacap tengah','cilacap selatan','cilacap utara',
            'demak','purwodadi','jepara','karanganyar','kebumen','kendal','klaten tengah',
            'kota kudus','mungkid','pati','kajen','pemalang','purbalingga','purworejo',
            'rembang','sragen','sukoharjo','slawi','temanggung','wonosobo','wonogiri'
        ];

        $isKecamatanKota = in_array(strtolower($kecamatanName), $kecamatanKota, true);

        return $isKota || $isKecamatanKota;
    }

    public function store(Request $request, BrevoMailer $brevo)
    {
        $user = auth()->user();

        if ($user->user_type !== 'pegawai') {
            abort(403, 'Unauthorized access.');
        }

        $validated = $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'nik' => 'required|string|size:16',
            'email' => 'nullable|email|max:255',
            'phone' => 'required|string|max:20',
            'kabupaten_kode' => 'required|string',
            'kecamatan_kode' => 'required|string',
            'desa_kode' => 'required|string',
            'alamat_detail' => 'required|string|max:500',
            'foto_ktp' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'tujuan_permohonan' => 'required|string|max:5000',
            'cara_penyampaian'  => ['required', Rule::in(['online', 'datang_langsung'])],
            'datang_langsung_opsi' => [
                'nullable',
                Rule::in(['flashdisk', 'cetak', 'keduanya']),
            ],

            'documents.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ], [
            'email.email' => 'Format email tidak valid. Contoh yang benar: ahmadsubari@gmail.com',
        ]);

        if (!empty($validated['nik']) && !empty($validated['email']) && !empty($validated['phone'])) {
            $existsUserAcc = User::where('nik', $validated['nik'])->exists() && User::where('email', $validated['email'])->exists() 
            && User::where('phone', $validated['phone'])->exists();

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
            if ($totalSize > 6291456) {
                return back()
                    ->withErrors(['documents' => 'Total ukuran semua dokumen tidak boleh lebih dari 6MB.'])
                    ->withInput();
            }
        }

        DB::beginTransaction();

        try {
            $ticketId = $this->generateTicketId($validated['nik']);

            while (Submission::where('ticket_id', $ticketId)->exists()) {
                $ticketId = $this->generateTicketId($validated['nik']);
            }

            $caraPenyampaian = $validated['cara_penyampaian'] ?? 'online';
            $opsiDatang = [];

            if ($caraPenyampaian === 'datang_langsung') {
                $raw = $validated['datang_langsung_opsi'] ?? null;

                if ($raw === 'keduanya') {
                    $opsiDatang = ['flashdisk', 'cetak'];
                } elseif ($raw === 'flashdisk') {
                    $opsiDatang = ['flashdisk'];
                } elseif ($raw === 'cetak') {
                    $opsiDatang = ['cetak'];
                }
            }

            $submission = Submission::create([
                'user_id'      => $user->id,
                'ticket_id'    => $ticketId,
                'title'        => $validated['title'],
                'subject'      => $validated['title'],
                'description'  => $validated['description'],
                'status'       => 'pending',
                'tujuan_permohonan'    => $validated['tujuan_permohonan'],
                'cara_penyampaian'     => $caraPenyampaian,
                'datang_langsung_opsi' => $opsiDatang,
            ]);

            $kab = DB::table('wilayah')->where('kode', $validated['kabupaten_kode'])->first();
            $kec = DB::table('wilayah')->where('kode', $validated['kecamatan_kode'])->first();
            $desa = DB::table('wilayah')->where('kode', $validated['desa_kode'])->first();

            $kabName = $kab->nama ?? null;
            $kecName = $kec->nama ?? null;

            // ✅ kelurahan/ desa label based on kab kota OR kecamatan kota (aman)
            $isKelurahan = $this->detectIsKelurahan($kabName, $kecName);

            $file = $request->file('foto_ktp');
            $ktpPath = $validated['nik'].'/'.Str::uuid().'.'.$file->extension();

            Storage::disk('supabase_ktp')->put(
                $ktpPath,
                file_get_contents($file->getRealPath()),
                ['ContentType' => $file->getMimeType()]
            );

            $submission->applicant()->create([
                'nama_lengkap' => $validated['nama_lengkap'],
                'nik' => $validated['nik'],
                'email' => $validated['email'] ?? null,
                'phone' => $validated['phone'],
                'alamat_detail' => $validated['alamat_detail'],

                'kabupaten_kode' => $validated['kabupaten_kode'],
                'kabupaten_nama' => $kabName,
                'kecamatan_kode' => $validated['kecamatan_kode'],
                'kecamatan_nama' => $kecName,
                'desa_kode' => $validated['desa_kode'],
                'desa_nama' => $desa->nama ?? null,

                'is_kelurahan' => $isKelurahan,
                'foto_ktp' => $ktpPath,
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

            if (!empty($validated['email'])) {
                try {
                    $submission->load('applicant');

                    $html = view('emails.submission-created', [
                        'user' => (object)[
                            'name' => $validated['nama_lengkap'],
                            'email' => $validated['email']
                        ],
                        'submission' => $submission,
                    ])->render();

                    $brevo->sendTransactional(
                        toEmail: $validated['email'],
                        toName: $validated['nama_lengkap'],
                        subject: "Permohonan Informasi Diterima ({$submission->ticket_id})",
                        htmlContent: $html
                    );
                } catch (\Throwable $e) {
                    Log::error('BREVO ERROR CO ADMIN', [
                        'error' => $e->getMessage(),
                        'ticket_id' => $submission->ticket_id
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('user.submissions.create')
                ->with('success', true)
                ->with('ticket_id', $submission->ticket_id)
                ->with('submission_id', $submission->id);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('STORE ERROR CO ADMIN', ['error' => $e->getMessage()]);
            return back()->withErrors($e->getMessage())->withInput();
        }
    }

    public function show(Submission $submission)
    {
        $user = auth()->user();

        if ($submission->user_id !== $user->id) {
            abort(403, 'Unauthorized access.');
        }

        $submission->load(['handler', 'documents', 'applicant']);

        return view('user.submissions.show', compact('submission'));
    }
}