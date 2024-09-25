<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        if(auth()->user()->is_active == 0){
            Auth::guard('web')->logout();
            return redirect()->route('login')->with('message', 'Akun anda tidak aktif')->with('color', 'red');
        }

        // if(auth()->user()->profile == 'trial' && auth()->user()->created_at->diffInDays(now()) >= 60){
        //     Auth::guard('web')->logout();
        //     return redirect()->route('login')->with('message', 'Maaf, Masa Trial Anda sudah expired')->with('color', 'red');
        // }

        

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
