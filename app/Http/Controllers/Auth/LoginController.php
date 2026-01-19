<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle a login request to the application.
     */
    public function login(Request $request)
    {
        // Tetap menggunakan validasi asli milikmu
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
                'g-recaptcha-response.captcha' => 'Verifikasi reCAPTCHA gagal. Silakan coba lagi.',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()
                ->withErrors($e->errors())
                ->withInput($request->only('email'));
        }

        $credentials = $request->only('email', 'password');
        $remember = $request->filled('remember');
        
        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            $user = Auth::user();
            
            Log::info('User logged in', [
                'email' => $user->email,
                'role' => $user->role,
                'user_type' => $user->user_type ?? 'not_set'
            ]);

            // Cek verifikasi email
            if (!$user->hasVerifiedEmail()) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                
                return redirect()->route('login')
                    ->with('unverified', 'Akun Anda belum terverifikasi. Silakan cek email Anda.')
                    ->withInput($request->only('email'));
            }

            // Logic Pengecekan Tab Login (Admin vs Pengguna)
            $selectedTab = $request->input('user_type', 'user');
            $isAdmin = $user->role === 'admin';

            if (($selectedTab === 'admin' && !$isAdmin) || ($selectedTab === 'user' && $isAdmin)) {
                $message = $isAdmin 
                    ? 'Anda adalah Admin. Silakan gunakan tab Admin untuk login.' 
                    : 'Anda adalah Pengguna. Silakan gunakan tab Pengguna untuk login.';
                
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                
                return redirect()->route('login')
                    ->with('error', $message)
                    ->withInput($request->only('email'));
            }

            // --- REDIRECT BERDASARKAN ROLE & USER_TYPE (FIXED) ---
            if ($isAdmin) {
                return redirect()->intended(route('admin.dashboard'));
            }
            
            // Pegawai menggunakan folder 'user' maka routenya 'user.dashboard'
            if ($user->user_type === 'pegawai') {
                return redirect()->intended(route('user.dashboard'));
            } 
            
            // Masyarakat umum menggunakan route 'masyarakat.dashboard'
            return redirect()->intended(route('masyarakat.dashboard'));
        }

        Log::warning('Login failed', [
            'email' => $request->email,
            'ip' => $request->ip()
        ]);

        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => 'Email atau password salah.']);
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Anda telah berhasil logout.');
    }
}