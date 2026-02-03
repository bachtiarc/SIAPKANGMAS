<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Verified;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class VerificationController extends Controller
{
    /**
     * Show the email verification notice.
     */
    public function show(Request $request)
    {
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

        Log::info('Verification attempt', [
            'user_id' => $id,
            'hash' => $hash,
            'expected_hash' => sha1($user->getEmailForVerification()),
            'current_verified_at' => $user->email_verified_at,
            'full_url' => $request->fullUrl(),
        ]);

        if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            Log::error('Invalid verification hash', ['user_id' => $id]);

            return redirect()->route('login')
                ->with('error', 'Link verifikasi tidak valid.');
        }

        if ($user->hasVerifiedEmail()) {
            Log::info('User already verified', ['user_id' => $id]);

            return redirect()->route('login')
                ->with('info', 'Email sudah terverifikasi sebelumnya. Silakan login.');
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
            Log::info('Email verified successfully', [
                'user_id' => $id,
                'verified_at' => $user->fresh()->email_verified_at
            ]);
        }

        return redirect()->route('login')
            ->with('verified', 'Akun sudah terverifikasi, silakan Login!');
    }

    /**
     * Resend the email verification notification.
     * NOTE: kalau kamu mau resend via Brevo juga, ini perlu kita ubah.
     */
    public function resend(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            $role = $request->user()->role;

            if ($role === 'admin') {
                return redirect()->route('admin.dashboard');
            }

            $type = $request->user()->user_type;
            return redirect()->route($type === 'pegawai' ? 'user.dashboard' : 'masyarakat.dashboard');
        }
        $request->user()->sendEmailVerificationNotification();

        Log::info('Verification email resent', ['user_id' => $request->user()->id]);

        return back()->with('success', 'Link verifikasi baru telah dikirim ke email Anda!');
    }
}
