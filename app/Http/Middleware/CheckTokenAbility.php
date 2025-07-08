<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckTokenAbility
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$abilities): Response
    {
        if (!$request->user()) {
            return response()->json([
                'success' => false,
                'message' => 'Token de acesso inválido'
            ], 401);
        }

        $token = $request->user()->currentAccessToken();
        
        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Token não encontrado'
            ], 401);
        }

        // Verificar se o token tem acesso total
        if ($token->can('*')) {
            return $next($request);
        }

        // Verificar se o token tem alguma das habilidades necessárias
        foreach ($abilities as $ability) {
            if ($token->can($ability)) {
                return $next($request);
            }
        }

        return response()->json([
            'success' => false,
            'message' => 'Token não possui permissões suficientes',
            'required_abilities' => $abilities,
            'token_abilities' => $token->abilities
        ], 403);
    }
}
