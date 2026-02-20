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

        // Tab dari form login (punyamu: user_type = admin / user)
        $selectedTab = $request->input('user_type', 'user');

        // ====== PENENTUAN AKTOR (100% dari DB) ======
        $adminType = strtolower((string) ($user->admin_type ?? '')); // super / co_admin / null
        $role      = strtolower((string) ($user->role ?? 'user'));   // admin / user

        $isSuperAdmin = ($adminType === 'super') || ($role === 'admin');
        $isCoAdmin    = ($adminType === 'co_admin');

        // kalau super, jangan dianggap co_admin
        if ($isSuperAdmin) {
            $isCoAdmin = false;
        }

        // ====== VALIDASI TAB ======
        if ($selectedTab === 'admin') {
            if (!($isSuperAdmin || $isCoAdmin)) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')
                    ->with('error', 'Anda Pengguna. Gunakan tab Pengguna.');
            }
        } else { // tab user
            if ($isSuperAdmin || $isCoAdmin) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')
                    ->with('error', 'Anda Admin. Gunakan tab Admin.');
            }
        }

        // IMPORTANT: biar gak nyangkut redirect ke /admin/dashboard lagi
        $request->session()->forget('url.intended');

        Log::info('User logged in', [
            'email' => $user->email,
            'role' => $user->role,
            'user_type' => $user->user_type,
            'admin_type' => $user->admin_type,
            'selected_tab' => $selectedTab,
            'is_super_admin' => $isSuperAdmin,
            'is_co_admin' => $isCoAdmin,
        ]);

        // ====== REDIRECT FINAL (PAKSA, TANPA intended) ======
        if ($isSuperAdmin) {
            return redirect()->route('admin.dashboard');
        }

        if ($isCoAdmin) {
            // CO ADMIN kamu memang numpang dashboard pegawai lama
            return redirect()->route('user.dashboard');
        }

        return redirect()->route('masyarakat.dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        $request->session()->forget('url.intended');

        return redirect()->route('login')
            ->with('success', 'Anda telah berhasil logout.');
    }
}