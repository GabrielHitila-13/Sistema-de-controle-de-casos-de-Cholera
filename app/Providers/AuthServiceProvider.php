<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [];

    public function boot(): void
    {
        // Administrador tem acesso a tudo
        Gate::before(function (User $user, $ability) {
            if ($user->papel === 'administrador') {
                return true;
            }
        });

        // Gestão de usuários
        Gate::define('view-users', function (User $user) {
            return in_array($user->papel, ['administrador', 'gestor']);
        });

        Gate::define('create-users', function (User $user) {
            return $user->papel === 'administrador';
        });

        Gate::define('edit-users', function (User $user) {
            return $user->papel === 'administrador';
        });

        Gate::define('delete-users', function (User $user) {
            return $user->papel === 'administrador';
        });

        Gate::define('manage-users', function (User $user) {
            return $user->papel === 'administrador';
        });

        // Gestão de gabinetes
        Gate::define('manage-gabinetes', function (User $user) {
            return in_array($user->papel, ['administrador', 'gestor']);
        });

        // Gestão de estabelecimentos
        Gate::define('manage-estabelecimentos', function (User $user) {
            return in_array($user->papel, ['administrador', 'gestor']);
        });

        // Gestão de pacientes
        Gate::define('view-pacientes', function (User $user) {
            return in_array($user->papel, ['administrador', 'gestor', 'medico', 'tecnico', 'enfermeiro']);
        });

        Gate::define('create-pacientes', function (User $user) {
            return in_array($user->papel, ['administrador', 'medico', 'tecnico', 'enfermeiro']);
        });

        Gate::define('edit-pacientes', function (User $user) {
            return in_array($user->papel, ['administrador', 'medico', 'tecnico', 'enfermeiro']);
        });

        // Gestão de veículos
        Gate::define('manage-veiculos', function (User $user) {
            return in_array($user->papel, ['administrador', 'tecnico']);
        });

        // Pontos de atendimento
        Gate::define('manage-pontos-atendimento', function (User $user) {
            return in_array($user->papel, ['administrador', 'medico', 'tecnico']);
        });

        // Relatórios
        Gate::define('view-relatorios', function (User $user) {
            return in_array($user->papel, ['administrador', 'gestor', 'medico']);
        });
    }
}
