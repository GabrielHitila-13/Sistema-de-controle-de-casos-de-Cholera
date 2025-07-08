<?php

namespace App\Observers;

use App\Models\Paciente;
use App\Services\DashboardUpdateService;

class PacienteObserver
{
    protected $updateService;

    public function __construct(DashboardUpdateService $updateService)
    {
        $this->updateService = $updateService;
    }

    /**
     * Handle the Paciente "created" event.
     */
    public function created(Paciente $paciente): void
    {
        $this->updateService->handlePatientUpdate($paciente, 'created');
    }

    /**
     * Handle the Paciente "updated" event.
     */
    public function updated(Paciente $paciente): void
    {
        $this->updateService->handlePatientUpdate($paciente, 'updated');
    }

    /**
     * Handle the Paciente "deleted" event.
     */
    public function deleted(Paciente $paciente): void
    {
        $this->updateService->handlePatientUpdate($paciente, 'deleted');
    }
}
