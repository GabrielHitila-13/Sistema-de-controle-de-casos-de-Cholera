<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!$request->user()) {
            return response()->json([
                'success' => false,
                'message' => 'Usuário não autenticado'
            ], 401);
        }

        $user = $request->user();
        
        // Administrador tem acesso total
        if ($user->isAdmin()) {
            return $next($request);
        }

        // Verificar se o usuário tem algum dos papéis necessários
        if (!$user->temAlgumPapel($roles)) {
            return response()->json([
                'success' => false,
                'message' => 'Acesso negado. Você não tem permissão para acessar este recurso.',
                'required_roles' => $roles,
                'user_role' => $user->papel
            ], 403);
        }

        return $next($request);
    }
}
