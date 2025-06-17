@extends('layouts.app')

@section('title', 'Dashboard - Sistema Cólera Angola')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <h2 class="text-3xl font-bold text-gray-900">Dashboard</h2>
        <div class="text-sm text-gray-500">
            Última atualização: {{ now()->format('d/m/Y H:i:s') }}
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-users text-blue-600 text-2xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total de Pacientes</dt>
                            <dd class="text-3xl font-bold text-gray-900">{{ $totalPacientes }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-triangle text-red-600 text-2xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Casos Alto Risco</dt>
                            <dd class="text-3xl font-bold text-red-600">{{ $casosAltoRisco }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-hospital text-green-600 text-2xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Estabelecimentos</dt>
                            <dd class="text-3xl font-bold text-gray-900">{{ $totalEstabelecimentos }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-building text-purple-600 text-2xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Gabinetes</dt>
                            <dd class="text-3xl font-bold text-gray-900">{{ $totalGabinetes }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Casos por Província -->
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold mb-4">Casos por Província</h3>
            <canvas id="casosPorProvinciaChart"></canvas>
        </div>

        <!-- Evolução Temporal -->
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold mb-4">Evolução Temporal (30 dias)</h3>
            <canvas id="evolucaoTemporalChart"></canvas>
        </div>

        <!-- Distribuição por Sexo -->
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold mb-4">Distribuição por Sexo</h3>
            <canvas id="distribuicaoSexoChart"></canvas>
        </div>

        <!-- Distribuição por Risco -->
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold mb-4">Distribuição por Risco</h3>
            <canvas id="distribuicaoRiscoChart"></canvas>
        </div>
    </div>
</div>

<script>
// Casos por Província
const ctxProvincia = document.getElementById('casosPorProvinciaChart').getContext('2d');
new Chart(ctxProvincia, {
    type: 'bar',
    data: {
        labels: {!! json_encode($casosPorProvincia->pluck('nome')) !!},
        datasets: [{
            label: 'Casos',
            data: {!! json_encode($casosPorProvincia->pluck('total')) !!},
            backgroundColor: 'rgba(59, 130, 246, 0.8)',
            borderColor: 'rgba(59, 130, 246, 1)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// Evolução Temporal
const ctxTemporal = document.getElementById('evolucaoTemporalChart').getContext('2d');
new Chart(ctxTemporal, {
    type: 'line',
    data: {
        labels: {!! json_encode($evolucaoTemporal->pluck('data')) !!},
        datasets: [{
            label: 'Casos por Dia',
            data: {!! json_encode($evolucaoTemporal->pluck('casos')) !!},
            borderColor: 'rgba(16, 185, 129, 1)',
            backgroundColor: 'rgba(16, 185, 129, 0.1)',
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// Distribuição por Sexo
const ctxSexo = document.getElementById('distribuicaoSexoChart').getContext('2d');
new Chart(ctxSexo, {
    type: 'doughnut',
    data: {
        labels: {!! json_encode($distribuicaoSexo->pluck('sexo')) !!},
        datasets: [{
            data: {!! json_encode($distribuicaoSexo->pluck('total')) !!},
            backgroundColor: ['rgba(59, 130, 246, 0.8)', 'rgba(236, 72, 153, 0.8)']
        }]
    },
    options: {
        responsive: true
    }
});

// Distribuição por Risco
const ctxRisco = document.getElementById('distribuicaoRiscoChart').getContext('2d');
new Chart(ctxRisco, {
    type: 'pie',
    data: {
        labels: {!! json_encode($distribuicaoRisco->pluck('risco')) !!},
        datasets: [{
            data: {!! json_encode($distribuicaoRisco->pluck('total')) !!},
            backgroundColor: [
                'rgba(34, 197, 94, 0.8)',  // baixo - verde
                'rgba(251, 191, 36, 0.8)', // medio - amarelo
                'rgba(239, 68, 68, 0.8)'   // alto - vermelho
            ]
        }]
    },
    options: {
        responsive: true
    }
});
</script>
@endsection