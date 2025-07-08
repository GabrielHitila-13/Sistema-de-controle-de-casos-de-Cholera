<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Paciente;
use App\Models\Veiculo;
use App\Observers\PacienteObserver;
use App\Observers\VeiculoObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register model observers for real-time updates
        Paciente::observe(PacienteObserver::class);
        Veiculo::observe(VeiculoObserver::class);
    }
}
