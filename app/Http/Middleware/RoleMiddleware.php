<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role, ?string $userType = null): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Cek Role (admin/user)
        if ($user->role !== $role) {
            abort(403, 'Unauthorized action.');
        }

        if ($userType && $user->user_type !== $userType) {
            if ($user->user_type === 'pegawai') {
                return redirect()->route('user.dashboard');
            } elseif ($user->user_type === 'masyarakat_umum') {
                return redirect()->route('masyarakat.dashboard');
            }
        }

        return $next($request);
    }
}