<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Session\TokenMismatchException;

class TokenExpiredMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            // Lanjutkan permintaan jika tidak ada masalah
            return $next($request);
        } catch (TokenMismatchException $e) {
            // Jika token mismatch (419), redirect ke login dengan pesan
            return redirect()->route('login')->with('message', 'Session expired, please log in again.');
        }
    }
}
