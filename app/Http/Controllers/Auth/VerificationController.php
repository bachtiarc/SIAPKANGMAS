<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Verified;
use App\Models\User;

class VerificationController extends Controller
{
    public function show(Request $request)
    {
        // Kalau sudah login dan sudah verified, lempar dashboard sesuai role/type
        if ($request->user() && $request->user()->hasVerifiedEmail()) {
            $role = $request->user()->role;

            if ($role === 'admin') {
                return redirect()->route('admin.dashboard');
            }

            $type = $request->user()->user_type;
            return redirect()->route($type === 'pegawai' ? 'user.dashboard' : 'masyarakat.dashboard');
        }

        return view('auth.verify-email');
    }

    public function verify(Request $request, $id, $hash)
    {
        $user = User::find($id);

        if (!$user) {
            return redirect()->route('login')->with('error', 'User tidak ditemukan.');
        }

        // cek hash
        if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            return redirect()->route('login')->with('error', 'Link verifikasi tidak valid.');
        }

        if ($user->hasVerifiedEmail()) {
            return redirect()->route('login')->with('info', 'Email sudah terverifikasi. Silakan login.');
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return redirect()->route('login')->with('verified', 'Akun sudah terverifikasi, silakan Login!');
    }

    public function resend(Request $request)
    {
        // optional: kamu bisa implement resend via Brevo juga
        return back()->with('error', 'Fitur kirim ulang belum diaktifkan.');
    }
}