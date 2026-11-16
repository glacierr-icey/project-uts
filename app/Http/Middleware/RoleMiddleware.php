<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // 1. Cek apakah pengguna sudah login
        if (!Auth::check()) {
            return redirect()->route('login')->with('warning', 'Silakan login terlebih dahulu untuk mengakses sistem.');
        }

        $user = Auth::user();

        // 2. Cek apakah role pengguna diizinkan
        if (!in_array($user->role, $roles)) {
            abort(403, "Akses Ditolak: Akun Anda memiliki peran '{$user->role}'. Anda tidak memiliki izin untuk membuka halaman ini.");
        }

        return $next($request);
    }
}

