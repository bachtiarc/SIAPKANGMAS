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
        // If user is already verified, redirect to dashboard
        if ($request->user() && $request->user()->hasVerifiedEmail()) {
            $role = $request->user()->role;
            return redirect()->route($role === 'admin' ? 'admin.dashboard' : 'user.dashboard');
        }

        return view('auth.verify-email');
    }

    /**
     * Mark the authenticated user's email address as verified.
     * 
     * THIS IS THE CRITICAL METHOD THAT FIXES THE NULL TIMESTAMP ISSUE!
     */
    public function verify(Request $request, $id, $hash)
    {
        // Find user by ID
        $user = User::findOrFail($id);

        // Log for debugging
        Log::info('Verification attempt', [
            'user_id' => $id,
            'hash' => $hash,
            'expected_hash' => sha1($user->getEmailForVerification()),
            'current_verified_at' => $user->email_verified_at
        ]);

        // Verify hash matches
        if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            Log::error('Invalid verification hash', ['user_id' => $id]);
            return redirect()->route('verification.notice')
                ->with('error', 'Link verifikasi tidak valid.');
        }

        // Check if already verified
        if ($user->hasVerifiedEmail()) {
            Log::info('User already verified', ['user_id' => $id]);
            return redirect()->route('login')
                ->with('info', 'Email sudah terverifikasi sebelumnya. Silakan login.');
        }

        // MARK EMAIL AS VERIFIED - THIS SETS THE TIMESTAMP!
        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
            Log::info('Email verified successfully', [
                'user_id' => $id,
                'verified_at' => $user->fresh()->email_verified_at
            ]);
        }

        // Redirect to login with success toast message
        return redirect()->route('login')
            ->with('verified', 'Akun sudah terverifikasi, silakan Login!');
    }

    /**
     * Resend the email verification notification.
     */
    public function resend(Request $request)
    {
        // Check if already verified
        if ($request->user()->hasVerifiedEmail()) {
            $role = $request->user()->role;
            return redirect()->route($role === 'admin' ? 'admin.dashboard' : 'user.dashboard');
        }

        // Send verification notification
        $request->user()->sendEmailVerificationNotification();

        Log::info('Verification email resent', ['user_id' => $request->user()->id]);

        return back()->with('success', 'Link verifikasi baru telah dikirim ke email Anda!');
    }
}