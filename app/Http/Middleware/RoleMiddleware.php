<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (! $request->user()) {
            return redirect()->route('login');
        }

        // Cek jika user memiliki salah satu role yang diizinkan
        if (! in_array($request->user()->role, $roles)) {
            abort(403, 'Akses Ditolak: Anda tidak memiliki hak akses ke halaman ini.');
        }

        return $next($request);
    }
}
