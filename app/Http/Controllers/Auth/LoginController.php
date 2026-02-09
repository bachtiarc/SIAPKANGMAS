<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required',
                'g-recaptcha-response' => 'required|captcha',
            ], [
                'email.required' => 'Email wajib diisi.',
                'email.email' => 'Format email tidak valid.',
                'password.required' => 'Password wajib diisi.',
                'g-recaptcha-response.required' => 'Mohon centang reCAPTCHA.',
                'g-recaptcha-response.captcha' => 'Verifikasi reCAPTCHA gagal.',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()
                ->withErrors($e->errors())
                ->withInput($request->only('email'));
        }

        $credentials = $request->only('email', 'password');
        $remember = $request->filled('remember');

        if (!Auth::attempt($credentials, $remember)) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Email atau password salah.']);
        }

        $request->session()->regenerate();
        $user = Auth::user();

        if (!$user->hasVerifiedEmail()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->with('unverified', 'Akun Anda belum terverifikasi. Silakan cek email.');
        }

        $selectedTab = $request->input('user_type', 'user');
        $isAdmin = $user->role === 'admin';

        if (($selectedTab === 'admin' && !$isAdmin) || ($selectedTab === 'user' && $isAdmin)) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->with('error', $isAdmin
                    ? 'Anda Admin. Gunakan tab Admin.'
                    : 'Anda Pengguna. Gunakan tab Pengguna.'
                );
        }

        Log::info('User logged in', [
            'email' => $user->email,
            'role' => $user->role,
            'user_type' => $user->user_type,
        ]);

        if ($isAdmin) {
            return redirect()->intended(route('admin.dashboard'));
        }

        if ($user->user_type === 'pegawai') {
            return redirect()->intended(route('user.dashboard'));
        }

        return redirect()->intended(route('masyarakat.dashboard'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Anda telah berhasil logout.');
    }
}