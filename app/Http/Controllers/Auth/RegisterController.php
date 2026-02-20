<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Services\BrevoMailer;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'      => 'required|string|max:255',
            'nik'       => 'required|string|size:16|unique:users',
            'email'     => 'required|email|unique:users',
            'phone'     => ['required','regex:/^(08|62|\+62)[0-9]{9,13}$/'],
            'pekerjaan' => 'required|string|max:255',
            'pekerjaan_lainnya' => 'nullable|string|max:255',
            'provinsi'  => 'required|string',
            'kabupaten' => 'required|string',
            'kecamatan' => 'required|string',
            'desa'      => 'required|string',
            'alamat'    => 'required|string',
            'foto_ktp'  => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'password'  => 'required|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $pekerjaan = $request->pekerjaan === 'Lainnya'
            ? $request->pekerjaan_lainnya
            : $request->pekerjaan;

        $file = $request->file('foto_ktp');
        $path = $request->nik.'/'.Str::uuid().'.'.$file->extension();

        Storage::disk('supabase_ktp')->put(
            $path,
            file_get_contents($file->getRealPath()),
            ['ContentType' => $file->getMimeType()]
        );

        $provinsiName  = DB::table('reg_provinces')->where('code', $request->provinsi)->value('name') ?? $request->provinsi;
        $kabupatenName = DB::table('reg_regencies')->where('code', $request->kabupaten)->value('name') ?? $request->kabupaten;
        $kecamatanName = DB::table('reg_districts')->where('code', $request->kecamatan)->value('name') ?? $request->kecamatan;
        $desaName      = DB::table('reg_villages')->where('code', $request->desa)->value('name') ?? $request->desa;

        $user = User::create([
            'name'        => $request->name,
            'nik'         => $request->nik,
            'email'       => $request->email,
            'phone'       => $request->phone,
            'address'     => $request->alamat,
            'provinsi'    => $provinsiName,
            'kabupaten'   => $kabupatenName,
            'kecamatan'   => $kecamatanName,
            'desa'        => $desaName,
            'pekerjaan'   => $pekerjaan,
            'foto_ktp'    => $path,
            'role'        => 'user',
            'user_type'   => 'masyarakat_umum',
            'password'    => Hash::make($request->password),
        ]);

        $this->sendBrevoVerificationEmail($user);

        return redirect()
            ->route('register')
            ->with('show_verification_modal', true)
            ->with('registered_email', $user->email);
    }

    public function resendVerification(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->with('toast_error', 'Email tidak ditemukan');
        }

        if ($user->hasVerifiedEmail()) {
            return back()->with('toast_info', 'Email sudah diverifikasi');
        }

        $this->sendBrevoVerificationEmail($user);

        return back()->with('toast_success', 'Email verifikasi berhasil dikirim ulang');
    }

    private function sendBrevoVerificationEmail(User $user): void
    {
        $hash = sha1($user->getEmailForVerification());

        $url = route('verification.verify', [
            'id'   => $user->id,
            'hash' => $hash,
        ]);

        app(BrevoMailer::class)->sendTransactional(
            toEmail: $user->email,
            toName: $user->name,
            subject: 'Verifikasi Akun SIAPKANGMAS',
            htmlContent: "
                <p>Halo <b>{$user->name}</b>,</p>
                <p>Silakan klik link berikut untuk verifikasi email Anda:</p>
                <p><a href='{$url}'>{$url}</a></p>
            "
        );
    }
}