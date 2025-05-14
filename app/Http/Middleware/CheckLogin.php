<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Session\Middleware\StartSession;

class CheckLogin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        // // Cek apakah session 'user' ada
        // if (!$request->session()->has('id')) {
        //     // Kalau tidak ada, redirect ke halaman login
        //     // return redirect('/login');
        //     return redirect()->route('login');
        // }

        // // Kalau ada, lanjut akses halaman
        // return $next($request);

        return app(StartSession::class)->handle($request, function ($request) use ($next) {

            if (!$request->session()->has('id')) {
                // Kalau tidak ada, redirect ke halaman login
                // return redirect('/login');
                return redirect()->route('login');
            }

            return $next($request);
        });
    }
}
