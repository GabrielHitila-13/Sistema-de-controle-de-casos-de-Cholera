<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Administrador tem acesso a tudo
        Gate::before(function (User $user, $ability) {
            if ($user->papel === 'administrador') {
                return true;
            }
        });

        // Dashboard
        Gate::define('view-dashboard', function (User $user) {
            return true; // Todos podem ver o dashboard
        });

        // Gestão de usuários
        Gate::define('view-users', function (User $user) {
            return in_array('view-users', $user->permissoes);
        });

        Gate::define('create-users', function (User $user) {
            return in_array('create-users', $user->permissoes);
        });

        Gate::define('edit-users', function (User $user) {
            return in_array('edit-users', $user->permissoes);
        });

        Gate::define('delete-users', function (User $user) {
            return in_array('delete-users', $user->permissoes);
        });

        Gate::define('manage-users', function (User $user) {
            return $user->temAlgumPapel(['administrador', 'gestor']);
        });

        // Gestão de gabinetes
        Gate::define('manage-gabinetes', function (User $user) {
            return in_array($user->papel, ['administrador', 'gestor']);
        });

        // Gestão de estabelecimentos
        Gate::define('view-estabelecimentos', function (User $user) {
            return in_array('view-estabelecimentos', $user->permissoes);
        });

        Gate::define('create-estabelecimentos', function (User $user) {
            return in_array('create-estabelecimentos', $user->permissoes);
        });

        Gate::define('edit-estabelecimentos', function (User $user) {
            return in_array('edit-estabelecimentos', $user->permissoes);
        });

        Gate::define('delete-estabelecimentos', function (User $user) {
            return in_array('delete-estabelecimentos', $user->permissoes);
        });

        Gate::define('manage-estabelecimentos', function (User $user) {
            return in_array($user->papel, ['administrador', 'gestor']);
        });

        // Gestão de pacientes
        Gate::define('view-pacientes', function (User $user) {
            return in_array('view-pacientes', $user->permissoes);
        });

        Gate::define('create-pacientes', function (User $user) {
            return in_array('create-pacientes', $user->permissoes);
        });

        Gate::define('edit-pacientes', function (User $user) {
            return in_array('edit-pacientes', $user->permissoes);
        });

        Gate::define('delete-pacientes', function (User $user) {
            return in_array('delete-pacientes', $user->permissoes);
        });

        Gate::define('fazer-triagem', function (User $user) {
            return in_array('fazer-triagem', $user->permissoes);
        });

        Gate::define('can-clinical-access', function (User $user) {
            return $user->temAlgumPapel(['medico', 'tecnico', 'enfermeiro']);
        });

        // Gestão de veículos
        Gate::define('manage-veiculos', function (User $user) {
            return in_array($user->papel, ['administrador', 'gestor', 'tecnico']);
        });

        Gate::define('view-veiculos', function (User $user) {
            return in_array('view-veiculos', $user->permissoes);
        });

        Gate::define('create-veiculos', function (User $user) {
            return in_array('create-veiculos', $user->permissoes);
        });

        Gate::define('edit-veiculos', function (User $user) {
            return in_array('edit-veiculos', $user->permissoes);
        });

        Gate::define('delete-veiculos', function (User $user) {
            return in_array('delete-veiculos', $user->permissoes);
        });

        Gate::define('edit-veiculo-proprio', function (User $user) {
            return in_array('edit-veiculo-proprio', $user->permissoes) && $user->veiculo_id;
        });

        Gate::define('can-operational-access', function (User $user) {
            return $user->temAlgumPapel(['administrador', 'gestor', 'tecnico']);
        });

        // Pontos de atendimento
        Gate::define('manage-pontos-atendimento', function (User $user) {
            return in_array($user->papel, ['administrador', 'gestor', 'medico', 'tecnico']);
        });

        // Relatórios
        Gate::define('view-relatorios', function (User $user) {
            return in_array('view-relatorios', $user->permissoes);
        });

        Gate::define('view-relatorios-basicos', function (User $user) {
            return !in_array($user->papel, []); // Todos podem ver relatórios básicos
        });

        Gate::define('export-relatorios', function (User $user) {
            return in_array('export-relatorios', $user->permissoes);
        });

        // Dashboards
        Gate::define('view-dashboards', function (User $user) {
            return !in_array($user->papel, []); // Todos podem ver dashboards
        });

        // Sistema
        Gate::define('manage-system', function (User $user) {
            return in_array('manage-system', $user->permissoes);
        });

        // Permissões específicas por papel
        Gate::define('is-administrador', function (User $user) {
            return $user->papel === 'administrador';
        });

        Gate::define('is-gestor', function (User $user) {
            return $user->papel === 'gestor';
        });

        Gate::define('is-medico', function (User $user) {
            return $user->papel === 'medico';
        });

        Gate::define('is-tecnico', function (User $user) {
            return $user->papel === 'tecnico';
        });

        Gate::define('is-enfermeiro', function (User $user) {
            return $user->papel === 'enfermeiro';
        });

        Gate::define('is-condutor', function (User $user) {
            return $user->papel === 'condutor';
        });

        Gate::define('view-missoes', function (User $user) {
            return in_array('view-missoes', $user->permissoes);
        });

        Gate::define('acesso-dados-nacionais', function (User $user) {
            return $user->papel === 'administrador';
        });

        Gate::define('acesso-dados-provinciais', function (User $user) {
            return in_array($user->papel, ['administrador', 'gestor']);
        });

        Gate::define('fichas-clinicas', function (User $user) {
            return in_array($user->papel, ['administrador', 'medico', 'enfermeiro']);
        });

        Gate::define('diagnosticos', function (User $user) {
            return in_array($user->papel, ['administrador', 'medico']);
        });

        Gate::define('qr-codes', function (User $user) {
            return in_array($user->papel, ['administrador', 'medico', 'tecnico', 'enfermeiro']);
        });

        Gate::define('receber-missoes', function (User $user) {
            return in_array($user->papel, ['administrador', 'condutor', 'tecnico']);
        });

        Gate::define('ver-rotas', function (User $user) {
            return in_array($user->papel, ['administrador', 'condutor', 'tecnico']);
        });

        Gate::define('cuidados-enfermagem', function (User $user) {
            return in_array($user->papel, ['administrador', 'medico', 'enfermeiro']);
        });
    }
}
