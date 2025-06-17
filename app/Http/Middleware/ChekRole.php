<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Administrador tem acesso a tudo
        if ($user->papel === 'administrador') {
            return $next($request);
        }

        // Verificar se o usuário tem um dos papéis necessários
        if (in_array($user->papel, $roles)) {
            return $next($request);
        }

        abort(403, 'Acesso negado. Você não tem permissão para acessar esta área.');
    }
}
