<?php

namespace App\Http\Middleware;

use App\Helpers\ResponseFormatter;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // validasi user telah terautentikasi
        if (!$request->user()) {
            return ResponseFormatter::error('Unauthenticated.', 401);
        }

        $userRole = $request->user()->role;

        // cek apakah role user termasuk dalam daftar role yang diiizinkan
        if (!in_array($userRole, $roles)) {
            return ResponseFormatter::error(
                'Forbidden. Required role: ' . implode(' or ', $roles) . '. Your role: ' . $userRole,
                403
            );
        }
        return $next($request);
    }
}
