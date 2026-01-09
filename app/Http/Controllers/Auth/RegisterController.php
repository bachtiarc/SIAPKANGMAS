<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /**
     * Organization structure for dropdowns
     */
    private function getOrganizationStructure()
    {
        return [
            'Sekretariat' => [
                'Subbag Program',
                'Subbag Keuangan',
                'Subbag Umum dan Kepegawaian'
            ],
            'Bidang Perdagangan Dalam Negeri' => [
                'Seksi Distribusi dan Logistik',
                'Seksi Promosi dan Informasi Pasar',
                'Seksi Pengembangan Pasar dan Usaha Dagang Kecil Menengah'
            ],
            'Bidang Perdagangan Luar Negeri' => [
                'Seksi Ekspor Dan Impor',
                'Seksi Promosi Dan Kerjasama Perdagangan Luar Negeri',
                'Seksi Informasi Dan Analisis Pasar'
            ],
            'Bidang Standarisasi Dan Perlindungan Konsumen' => [
                'Seksi Perlindungan Konsumen',
                'Seksi Tertib Niaga',
                'Seksi Standarisasi Industri'
            ],
            'Bidang Industri Agro' => [
                'Seksi Pengembangan SDM Dan Inovasi Industri Agro',
                'Seksi Pengembangan Sarana Dan Prasarana Industri Agro',
                'Seksi Pengendalian Dan Informasi Industri Agro'
            ],
            'Bidang Industri Non Agro' => [
                'Seksi Pengembangan SDM, Kreativitas, dan Inovasi Industri Non Agro',
                'Seksi Pengembangan Sarana dan Prasarana Industri Non Agro',
                'Seksi Pengendalian dan Informasi Industri Non Agro'
            ],
            'Balai Pengujian Dan Sertifikasi Mutu Barang Surakarta' => [
                'Sub Bagian Tata Usaha',
                'Seksi Pelayanan Teknis Pengujian Dan Kalibrasi',
                'Seksi Pengembangan Jasa Pengujian Dan Kalibrasi'
            ],
            'Balai Pengujian Dan Sertifikasi Mutu Barang Semarang' => [
                'Sub Bagian Tata Usaha',
                'Seksi Pelayanan Teknis Pengujian Dan Kalibrasi',
                'Seksi Pengembangan Jasa Pengujian Dan Kalibrasi'
            ],
            'Balai Industri Produk Tekstil Dan Alas Kaki' => [
                'Sub Bagian Tata Usaha',
                'Seksi Pengembangan Produk Tekstil',
                'Seksi Pengembangan Produk Alas Kaki'
            ],
            'Balai Industri Kreatif Digital Dan Kemasan' => [
                'Sub Bagian Tata Usaha',
                'Seksi Industri Kreatif Digital',
                'Seksi Pengembangan Kemasan'
            ],
            'Balai Industri Logam Dan Kayu' => [
                'Sub Bagian Tata Usaha',
                'Seksi Pelayanan Jasa Keteknikan',
                'Seksi Penerapan Dan Rekayasa'
            ],
        ];
    }

    /**
     * Show the registration form
     */
    public function showRegistrationForm()
    {
        $organizationStructure = $this->getOrganizationStructure();
        return view('auth.register', compact('organizationStructure'));
    }

    /**
     * Handle registration request
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'nip' => 'required|string|unique:users,nip|max:18',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'nullable|string|max:15',
            'bidang' => 'required|string|max:255',
            'jabatan' => 'required|string|max:255',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'nip.required' => 'NIP wajib diisi.',
            'nip.unique' => 'NIP sudah terdaftar.',
            'email.required' => 'Email wajib diisi.',
            'email.unique' => 'Email sudah terdaftar.',
            'bidang.required' => 'Bidang/Balai wajib dipilih.',
            'jabatan.required' => 'Jabatan/Seksi/Subbag wajib dipilih.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Create new user
        $user = User::create([
            'name' => $request->name,
            'nip' => $request->nip,
            'email' => $request->email,
            'phone' => $request->phone,
            'role' => 'user', // Default role
            'bidang' => $request->bidang,
            'jabatan' => $request->jabatan,
            'password' => Hash::make($request->password),
        ]);

        // Fire the Registered event (this will send verification email)
        event(new Registered($user));

        return redirect()->route('verification.notice')
            ->with('success', 'Registrasi berhasil! Silakan cek email Anda untuk verifikasi akun.');
    }
}