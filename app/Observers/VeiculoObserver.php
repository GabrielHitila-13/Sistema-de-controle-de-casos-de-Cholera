<?php

namespace App\Observers;

use App\Models\Veiculo;
use App\Services\DashboardUpdateService;

class VeiculoObserver
{
    protected $updateService;

    public function __construct(DashboardUpdateService $updateService)
    {
        $this->updateService = $updateService;
    }

    /**
     * Handle the Veiculo "created" event.
     */
    public function created(Veiculo $veiculo): void
    {
        $this->updateService->handleVehicleUpdate($veiculo, 'created');
    }

    /**
     * Handle the Veiculo "updated" event.
     */
    public function updated(Veiculo $veiculo): void
    {
        $this->updateService->handleVehicleUpdate($veiculo, 'updated');
    }

    /**
     * Handle the Veiculo "deleted" event.
     */
    public function deleted(Veiculo $veiculo): void
    {
        $this->updateService->handleVehicleUpdate($veiculo, 'deleted');
    }
}
