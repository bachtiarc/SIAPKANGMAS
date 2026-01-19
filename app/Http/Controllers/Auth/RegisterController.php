<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Notifications\CustomVerifyEmail;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        // Organization structure for pegawai
        $organizationStructure = [
            'Sekretariat' => [
                'Sekeretariat',
                'Kasubbag Umum dan Kepegawaian',
                'Kasubbag Keuangan',
                'Kasubbag Program',
                'Subbag Program',
                'Subbag Keuangan',
                'Subbag Umum dan Kepegawaian',
            ],
            'Bidang Perdagangan Dalam Negeri' => [
                'Kepala Bidang Perdagangan Dalam Negeri',
                'Seksi Distribusi dan Logistik',
                'Seksi Promosi dan Informasi Pasar',
                'Seksi Pengembangan Pasar dan Usaha Dagang Kecil Menengah',
            ],
            'Bidang Perdagangan Luar Negeri' => [
                'Kepala Bidang Perdagangan Luar Negeri',
                'Seksi Ekspor Dan Impor',
                'Seksi Promosi Dan Kerjasama Perdagangan Luar Negeri',
                'Seksi Informasi Dan Analisis Pasar',
            ],
            'Bidang Standarisasi Dan Perlindungan Konsumen' => [
                'Kepala Bidang Standarisasi Dan Perlindungan Konsumen',
                'Seksi Perlindungan Konsumen',
                'Seksi Tertib Niaga',
                'Seksi Standarisasi Industri',
            ],
            'Bidang Industri Agro' => [
                'Kepala Bidang Industri Agro',
                'Seksi Pengembangan Sdm Dan Inovasi Industri Agro',
                'Seksi Pengembangan Sarana Dan Prasarana Industri Agro',
                'Seksi Pengendalian Dan Informasi Industri Agro',
            ],
            'Bidang Industri Non Agro' => [
                'Kepala Bidang Industri Non Agro',
                'Seksi Pengembangan SDM, Kreativitas, dan Inovasi Industri Non Agro',
                'Seksi Pengembangan Sarana dan Prasarana Industri Non Agro',
                'Seksi Pengendalian dan Informasi Industri Non Agro',
            ],
            'Balai Pengujian Dan Sertifikasi Mutu Barang Surakarta' => [
                'Kepala Balai Pengujian Dan Sertifikasi Mutu Barang Surakarta',
                'Sub Bagian Tata Usaha',
                'Seksi Pelayanan Teknis Pengujian Dan Kalibrasi',
                'Seksi Pengembangan Jasa Pengujian Dan Kalibrasi',
            ],
            'Balai Pengujian Dan Sertifikasi Mutu Barang Semarang' => [
                'Kepala Balai Pengujian Dan Sertifikasi Mutu Barang Semarang',
                'Sub Bagian Tata Usaha',
                'Seksi Pelayanan Teknis Pengujian Dan Kalibrasi',
                'Seksi Pengembangan Jasa Pengujian Dan Kalibrasi',
            ],
            'Balai Industri Produk Tekstil Dan Alas Kaki' => [
                'Kepala Balai Industri Produk Tekstil Dan Alas Kaki',
                'Sub Bagian Tata Usaha',
                'Seksi Pengembangan Produk Tekstil',
                'Seksi Pengembangan Produk Alas Kaki',
            ],
            'Balai Industri Kreatif Digital Dan Kemasan' => [
                'Kepala Balai Industri Kreatif Digital Dan Kemasan',
                'Sub Bagian Tata Usaha',
                'Seksi Industri Kreatif Digital',
                'Seksi Pengembangan Kemasan',
            ],
            'Balai Industri Logam Dan Kayu' => [
                'Kepala Balai Industri Logam Dan Kayu',
                'Sub Bagian Tata Usaha',
                'Seksi Pelayanan Jasa Keteknikan',
                'Seksi Penerapan Dan Rekayasa',
            ],
            'Tata Usaha' => [
                'Tata Usaha',
                'Kasubbag Tata Usaha',
            ],
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
            'name.required' => 'Nama lengkap wajib diisi.',
            'nik.required' => 'NIK wajib diisi.',
            'nik.size' => 'NIK harus 16 digit.',
            'nik.unique' => 'NIK sudah terdaftar.',
            'address.required' => 'Alamat lengkap wajib diisi.',
            'foto_ktp.required' => 'Foto KTP wajib diunggah.',
        ]);

        if ($validator->fails()) {
            return redirect()->route('register')->withErrors($validator)->withInput();
        }

        $fotoKtpPath = null;
        if ($request->hasFile('foto_ktp')) {
            $fotoKtpPath = $request->file('foto_ktp')->store('ktp-photos', 'public');
        }

        // PROSES INSERT
        $user = User::create([
            'name' => $request->name,
            'nik' => $request->nik,  
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address, 
            'foto_ktp' => $fotoKtpPath,
            'password' => Hash::make($request->password),
            'role' => 'user', 
            'user_type' => 'masyarakat_umum', 
        ]);

        $user->notify(new \App\Notifications\CustomVerifyEmail);

        return redirect()->route('register')->with('registration_success', true);
    }
}