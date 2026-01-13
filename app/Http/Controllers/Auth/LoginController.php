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
        // Validate the form data
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
            // Validation failed, redirect back with errors
            return back()
                ->withErrors($e->errors())
                ->withInput($request->only('email'));
        }

        // Attempt to log the user in
        $credentials = $request->only('email', 'password');
        
        // Remember Me: if checkbox is checked, remember for 30 days
        $remember = $request->filled('remember');
        
        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            $user = Auth::user();
            
            // Log successful login for debugging
            Log::info('User logged in', [
                'email' => $user->email,
                'role' => $user->role,
                'verified' => $user->hasVerifiedEmail(),
                'remember' => $remember
            ]);

            // Check if email is verified
            if (!$user->hasVerifiedEmail()) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                
                return redirect()->route('login')
                    ->with('unverified', 'Akun Anda belum terverifikasi. Silakan cek email Anda.')
                    ->withInput($request->only('email'));
            }

            // Check user role matches selected tab (optional, for better UX)
            $userType = $request->input('user_type', 'user');
            $isAdmin = $user->role === 'admin';

            // If user selected "Admin" tab but is not admin, or vice versa
            if (($userType === 'admin' && !$isAdmin) || ($userType === 'user' && $isAdmin)) {
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

            // Redirect based on role
            if ($isAdmin) {
                return redirect()->intended(route('admin.dashboard'));
            }
            
            return redirect()->intended(route('user.dashboard'));
        }

        // If login attempt failed
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