<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckUserActive
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && !Auth::user()->ativo) {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Sua conta foi desativada. Entre em contato com o administrador.');
        }

        return $next($request);
    }
}
