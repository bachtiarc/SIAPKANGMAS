<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Notifications\CustomVerifyEmail;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        // Organization structure for pegawai
        $organizationStructure = [
            'Sekretariat' => [
                'Sekretaris',
                'Kepala Sub Bagian Umum dan Kepegawaian',
                'Kepala Sub Bagian Keuangan',
                'Kepala Sub Bagian Program',
                'Sub Bagian Program',
                'Sub Bagian Keuangan',
                'Sub Bagian Umum dan Kepegawaian',
            ],
            'Bidang Pembangunan Sumber Daya Industri Dan Perwilayahan Industri' => [
                'Kepala Bidang Pembangunan Sumber Daya Industri Dan Perwilayahan Industri',
                'Ketua Kelompok Kerja Pengembangan Perwilayahan Industri',
                'Ketua Kelompok Kerja Pengembangan Teknologi Industri',
                'Ketua Kelompok Kerja Pengembangan SDM Industri',
                'Kelompok Kerja Pengembangan Perwilayahan Industri',
                'Kelompok Kerja pengembangan Teknologi Industri',
                'Kelompok Kerja Pengembangan SDM Industri',
            ],
            'Bidang Pemberdayaan Industri' => [
                'Kepala Bidang Pemberdayaan Industri',
                'Ketua Kelompok Kerja Pengembangan Industri',
                'Ketua Kelompok Kerja Promosi dan Kerja Sama Industri',
                'Ketua Kelompok Kerja Industri Hijau',
                'Kelompok Kerja Pengembangan Industri',
                'Kelompok Kerja Promosi dan Kerja Sama Industri',
                'Kelompok Kerja Promosi dan Kerja Sama Industri',
            ],
            'Bidang Pengembangan Sarana Prasarana, Pengawasan Dan Pengendalian Industri' => [
                'Kepala Bidang Pengembangan Sarana Prasarana, Pengawasan Dan Pengendalian Industri',
                'Ketua Kelompok Kerja Pengembangan Sarana Prasarana Industri',
                'Ketua Kelompok Kerja Pengawasan dan Pengendalian Industri',
                'Ketua Kelompok Kerja Data dan Informasi Industri',
                'Kelompok Kerja Pengembangan Sarana Prasarana Industri',
                'Kelompok Kerja Pengawasan dan Pengendalian Industri',
                'Kelompok Kerja Data dan Informasi Industri',
            ],
            'Bidang Perdagangan Dalam Negeri' => [
                'Kepala Bidang Perdagangan Dalam Negeri',
                'Ketua Kelompok Kerja Pengendalian Bapokting, Pengembangan Informasi dan Sarana Perdagangan',
                'Ketua Kelompok Kerja Promosi dan Kerjasama',
                'Ketua Kelompok Kerja Perlindungan Konsumen dan Tertib Niaga',
                'Kelompok Kerja Pengendalian Bapokting, Pengembangan Informasi dan Sarana Perdagangan',
                'Kelompok Kerja Promosi dan Kerjasama',
                'Kelompok Kerja Perlindungan Konsumen dan Tertib Niaga',
            ],
            'Bidang Perdagangan Luar Negeri' => [
                'Kepala Bidang Perdagangan Luar Negeri',
                'Ketua Kelompok Kerja Ekspor dan Impor',
                'Ketua Kelompok Kerja Promosi dan Kerjasama Perdagangan Luar Negeri',
                'Ketua Kelompok Kerja Informasi Dan Analisis Pasar',
                'Kelompok Kerja Ekspor dan Impor',
                'Kelompok Kerja Promosi dan Kerjasama Perdagangan Luar Negeri',
                'Kelompok Kerja Informasi Dan Analisis Pasar',
            ], 
            'Balai Industri Logam dan Kayu (BILK) Kelas A' => [
                'Kepala Sub Bagian Tata Usaha',
                'Ketua Kelompok Kerja Pelayanan Jasa Keteknikan',
                'Ketua Kelompok Kerja Penerapan dan Rekayasa',
                'Kelompok Kerja Pelayanan Jasa Keteknikan,',
                'Kelompok Kerja Penerapan dan Rekayasa',
                'Kelompok Jabatan Fungsional',
            ], 
            'Balai Pengujian dan Sertifikasi Mutu Barang (BPSMB) Surakarta Kelas A' => [
                'Kepala Sub Bagian Tata Usaha',
                'Ketua Kelompok Kerja Pelayanan Teknis Pengujian dan Kalibrasi',
                'Ketua Kelompok Kerja Pengembangan Jasa Pengujian dan Kalibrasi',
                'Kelompok Kerja Pelayanan Teknis Pengujian dan Kalibrasi',
                'Kelompok Kerja Pengembangan Jasa Pengujian dan Kalibrasi',
                'Kelompok Jabatan Fungsional',
            ],
            'Balai Pengujian dan Sertifikasi Mutu Barang (BPSMB) Semarang' => [
                'Kepala Sub Bagian Tata Usaha',
                'Ketua Kelompok Kerja Produk Alas Kaki',
                'Ketua Kelompok Kerja Pengembangan Jasa Pengujian dan Kalibrasi',
                'Kelompok Kerja Pengembangan Produk Alas Kaki',
                'Kelompok Kerja Pengembangan Jasa Pengujian dan Kalibrasi',
                'Kelompok Jabatan Fungsional',
            ],
            'Balai Industri Produk Tekstil dan Alas Kaki (BIPTAK)' => [
                'Kepala Sub Bagian Tata Usaha',
                'Ketua Kelompok Kerja Pengembangan Produk Tekstil',
                'Ketua Kelompok Kerja Pengembangan Produk Alas Kaki',
                'Kelompok Kerja Pengembangan Produk Tekstil',
                'Kelompok Kerja Pengembangan Produk Alas Kaki',
                'Kelompok Jabatan Fungsional',
            ],
            'Balai Industri Kreatif Digital dan Kemasan Kelas A (BIKDK)' => [
                'Kepala Sub Bagian Tata Usaha',
                'Ketua Kelompok Kerja Industri Kreatif Digital',
                'Ketua Kelompok Kerja Pengembangan Kemasan',
                'Kelompok Kerja Industri Kreatif Digital',
                'Kelompok Kerja Pengembangan Kemasan',
                'Kelompok Jabatan Fungsional',
            ], 
            // 'Bidang Standarisasi Dan Perlindungan Konsumen' => [
            //     'Kepala Bidang Standarisasi Dan Perlindungan Konsumen',
            //     'Seksi Perlindungan Konsumen',
            //     'Seksi Tertib Niaga',
            //     'Seksi Standarisasi Industri',
            // ],
            // 'Bidang Industri Agro' => [
            //     'Kepala Bidang Industri Agro',
            //     'Seksi Pengembangan Sdm Dan Inovasi Industri Agro',
            //     'Seksi Pengembangan Sarana Dan Prasarana Industri Agro',
            //     'Seksi Pengendalian Dan Informasi Industri Agro',
            // ],
            // 'Bidang Industri Non Agro' => [
            //     'Kepala Bidang Industri Non Agro',
            //     'Seksi Pengembangan SDM, Kreativitas, dan Inovasi Industri Non Agro',
            //     'Seksi Pengembangan Sarana dan Prasarana Industri Non Agro',
            //     'Seksi Pengendalian dan Informasi Industri Non Agro',
            // ],
            // 'Balai Pengujian Dan Sertifikasi Mutu Barang Surakarta' => [
            //     'Kepala Balai Pengujian Dan Sertifikasi Mutu Barang Surakarta',
            //     'Sub Bagian Tata Usaha',
            //     'Seksi Pelayanan Teknis Pengujian Dan Kalibrasi',
            //     'Seksi Pengembangan Jasa Pengujian Dan Kalibrasi',
            // ],
            // 'Balai Pengujian Dan Sertifikasi Mutu Barang Semarang' => [
            //     'Kepala Balai Pengujian Dan Sertifikasi Mutu Barang Semarang',
            //     'Sub Bagian Tata Usaha',
            //     'Seksi Pelayanan Teknis Pengujian Dan Kalibrasi',
            //     'Seksi Pengembangan Jasa Pengujian Dan Kalibrasi',
            // ],
            // 'Balai Industri Produk Tekstil Dan Alas Kaki' => [
            //     'Kepala Balai Industri Produk Tekstil Dan Alas Kaki',
            //     'Sub Bagian Tata Usaha',
            //     'Seksi Pengembangan Produk Tekstil',
            //     'Seksi Pengembangan Produk Alas Kaki',
            // ],
            // 'Balai Industri Kreatif Digital Dan Kemasan' => [
            //     'Kepala Balai Industri Kreatif Digital Dan Kemasan',
            //     'Sub Bagian Tata Usaha',
            //     'Seksi Industri Kreatif Digital',
            //     'Seksi Pengembangan Kemasan',
            // ],
            // 'Balai Industri Logam Dan Kayu' => [
            //     'Kepala Balai Industri Logam Dan Kayu',
            //     'Sub Bagian Tata Usaha',
            //     'Seksi Pelayanan Jasa Keteknikan',
            //     'Seksi Penerapan Dan Rekayasa',
            // ],
            // 'Tata Usaha' => [
            //     'Tata Usaha',
            //     'Kasubbag Tata Usaha',
            // ],
        ];

        return view('auth.register', compact('organizationStructure'));
    }

    /**
     * Handle PEGAWAI registration
     */
    public function registerPegawai(Request $request)
    {
        // Tetap seperti code awalmu karena sudah benar
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'nip' => 'required|string|max:18|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => [
                'required',
                'string',
                'regex:/^62[0-9]{9,13}$/',
            ],
            'bidang' => 'required|string|max:255',
            'jabatan' => 'required|string|max:255',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'nip.required' => 'NIP wajib diisi.',
            'nip.unique' => 'NIP sudah terdaftar.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'phone.required' => 'Nomor telepon wajib diisi.',
            'phone.regex' => 'Format nomor telepon harus 62xxxxxxxxx. Contoh: 628123456789',
            'bidang.required' => 'Bidang/Balai wajib dipilih.',
            'jabatan.required' => 'Jabatan/Seksi/Subbag wajib dipilih.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        if ($validator->fails()) {
            return redirect()->route('register')
                ->withErrors($validator)
                ->withInput();
        }

        $user = User::create([
            'name' => $request->name,
            'nip' => $request->nip,
            'email' => $request->email,
            'phone' => $request->phone,
            'bidang' => $request->bidang,
            'jabatan' => $request->jabatan,
            'password' => Hash::make($request->password),
            'role' => 'user',
            'user_type' => 'pegawai',
        ]);

        $user->notify(new CustomVerifyEmail);

        return redirect()->route('register')
            ->with('registration_success', true);
    }

    /**
     * Handle MASYARAKAT UMUM registration
     */
    public function registerMasyarakat(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'nik' => 'required|string|size:16|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => ['required', 'string', 'regex:/^62[0-9]{9,13}$/'],
            'address' => 'required|string',
            'foto_ktp' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'foto_ktp.required' => 'Foto KTP wajib diunggah.',
        ]);

        if ($validator->fails()) {
            return redirect()->route('register')
                ->withErrors($validator)
                ->withInput();
        }

        $fotoKtpPath = null;

        if ($request->hasFile('foto_ktp')) {
            $file = $request->file('foto_ktp');

            $ext = strtolower($file->getClientOriginalExtension());
            $fileName = 'ktp_' . $request->nik . '_' . Str::uuid() . '.' . $ext;

            // folder per NIK biar rapi
            $objectPath = $request->nik . '/' . $fileName; // contoh: 3318.../ktp_3318_uuid.jpg

            try {
                Storage::disk('supabase_ktp')->put(
                    $objectPath,
                    file_get_contents($file->getRealPath()),
                    [
                        'ContentType' => $file->getMimeType(),
                        // kalau mau public-read (tergantung policy bucket kamu)
                        // 'ACL' => 'public-read',
                    ]
                );
            } catch (\Throwable $e) {
                return redirect()->route('register')
                    ->withErrors(['foto_ktp' => 'Gagal upload KTP ke Supabase: ' . $e->getMessage()])
                    ->withInput();
            }

            // simpan path RELATIF aja (tanpa bucket)
            $fotoKtpPath = $objectPath;
        }

        $user = User::create([
            'name'      => $request->name,
            'nik'       => $request->nik,
            'email'     => $request->email,
            'phone'     => $request->phone,
            'address'   => $request->address,
            'foto_ktp'  => $fotoKtpPath, // contoh: 3318.../ktp_3318_uuid.jpg
            'password'  => Hash::make($request->password),
            'role'      => 'user',
            'user_type' => 'masyarakat_umum',
        ]);

        $user->notify(new \App\Notifications\CustomVerifyEmail);

        return redirect()->route('register')->with('registration_success', true);
    }
}