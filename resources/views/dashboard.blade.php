@extends('layouts.app')

@section('title', 'Dashboard - Sistema Cólera Angola')
@section('page-title', 'Sistema de Gestão de Surto de Cólera')
@section('page-subtitle', 'Monitoramento em tempo real - República de Angola')

@section('content')
<div class="space-y-6">
    <!-- Métricas principais -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total de Casos -->
        <div class="metric-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total de Casos</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $stats['pacientes_total'] ?? 127 }}</p>
                    <p class="text-sm text-green-600 mt-1">
                        <i class="fas fa-arrow-up mr-1"></i>
                        +12.5% vs. semana anterior
                    </p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-users text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>
        
        <!-- Casos Ativos -->
        <div class="metric-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Casos Ativos</p>
                    <p class="text-3xl font-bold text-gray-900">{{ ($stats['pacientes_alto_risco'] ?? 0) + ($stats['pacientes_medio_risco'] ?? 0) ?: 89 }}</p>
                    <p class="text-sm text-green-600 mt-1">
                        <i class="fas fa-arrow-up mr-1"></i>
                        +8.2% vs. semana anterior
                    </p>
                </div>
                <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-heartbeat text-orange-600 text-xl"></i>
                </div>
            </div>
        </div>
        
        <!-- Óbitos -->
        <div class="metric-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Óbitos</p>
                    <p class="text-3xl font-bold text-gray-900">12</p>
                    <p class="text-sm text-red-600 mt-1">
                        <i class="fas fa-exclamation-triangle mr-1"></i>
                        5.1% vs. semana anterior
                    </p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                </div>
            </div>
        </div>
        
        <!-- Recuperados -->
        <div class="metric-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Recuperados</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $stats['pacientes_baixo_risco'] ?? 26 }}</p>
                    <p class="text-sm text-green-600 mt-1">
                        <i class="fas fa-arrow-up mr-1"></i>
                        +15.3% vs. semana anterior
                    </p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-heart text-green-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Gráficos -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Evolução dos Casos -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Evolução dos Casos</h3>
            <div class="h-64">
                <canvas id="evolutionChart"></canvas>
            </div>
        </div>
        
        <!-- Casos por Província -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Casos por Província</h3>
            <div class="h-64">
                <canvas id="provinceChart"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Distribuições -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Distribuição por Faixa Etária -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Distribuição por Faixa Etária</h3>
            <div class="h-64">
                <canvas id="ageChart"></canvas>
            </div>
        </div>
        
        <!-- Distribuição por Sexo -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Distribuição por Sexo</h3>
            <div class="h-64">
                <canvas id="genderChart"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Pacientes Recentes e Alto Risco -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Pacientes de Alto Risco -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-exclamation-triangle text-red-600 mr-2"></i>
                    Pacientes de Alto Risco
                </h3>
                <span class="badge-alto">{{ $stats['pacientes_alto_risco'] ?? 0 }} casos</span>
            </div>
            
            @if(isset($pacientesAltoRisco) && $pacientesAltoRisco->count() > 0)
                <div class="space-y-3">
                    @foreach($pacientesAltoRisco as $paciente)
                    <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg">
                        <div>
                            <p class="font-medium text-gray-900">{{ $paciente->nome }}</p>
                            <p class="text-sm text-gray-600">{{ $paciente->estabelecimento->nome ?? 'N/A' }}</p>
                        </div>
                        <div class="text-right">
                            <span class="badge-alto">Alto</span>
                            <p class="text-xs text-gray-500 mt-1">{{ $paciente->data_triagem?->format('d/m H:i') }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="mt-4">
                    <a href="{{ route('pacientes.index', ['risco' => 'alto']) }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                        Ver todos os casos de alto risco →
                    </a>
                </div>
            @else
                <div class="text-center py-8">
                    <i class="fas fa-check-circle text-green-500 text-3xl mb-2"></i>
                    <p class="text-gray-500">Nenhum paciente de alto risco no momento</p>
                </div>
            @endif
        </div>
        
        <!-- Pacientes Recentes -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-clock text-blue-600 mr-2"></i>
                    Pacientes Recentes (24h)
                </h3>
                <span class="text-sm text-gray-500">Últimas 24 horas</span>
            </div>
            
            @if(isset($pacientesRecentes) && $pacientesRecentes->count() > 0)
                <div class="space-y-3">
                    @foreach($pacientesRecentes->take(5) as $paciente)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div>
                            <p class="font-medium text-gray-900">{{ $paciente->nome }}</p>
                            <p class="text-sm text-gray-600">{{ $paciente->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <span class="badge-{{ $paciente->risco }}">{{ ucfirst($paciente->risco) }}</span>
                    </div>
                    @endforeach
                </div>
                <div class="mt-4">
                    <a href="{{ route('pacientes.index') }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                        Ver todos os pacientes →
                    </a>
                </div>
            @else
                <div class="text-center py-8">
                    <i class="fas fa-calendar-check text-gray-400 text-3xl mb-2"></i>
                    <p class="text-gray-500">Nenhum paciente registrado nas últimas 24 horas</p>
                </div>
            @endif
        </div>
    </div>
    
    <!-- Ações Rápidas -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Ações Rápidas</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <a href="{{ route('pacientes.create') }}" class="btn-primary justify-center">
                <i class="fas fa-plus mr-2"></i>
                Novo Paciente
            </a>
            
            <a href="{{ route('pacientes.index', ['risco' => 'alto']) }}" class="btn-secondary justify-center">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                Alto Risco
            </a>
            
            <a href="{{ route('estabelecimentos.index') }}" class="btn-secondary justify-center">
                <i class="fas fa-hospital mr-2"></i>
                Estabelecimentos
            </a>
            
            <a href="#" class="btn-secondary justify-center">
                <i class="fas fa-file-pdf mr-2"></i>
                Relatório PDF
            </a>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Evolução dos Casos
    const evolutionCtx = document.getElementById('evolutionChart').getContext('2d');
    new Chart(evolutionCtx, {
        type: 'line',
        data: {
            labels: ['01/11', '08/11', '15/11', '22/11', '29/11', '06/12', '13/12', '19/12'],
            datasets: [{
                label: 'Casos Confirmados',
                data: [5, 12, 25, 45, 68, 89, 115, 127],
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.4,
                fill: true
            }, {
                label: 'Óbitos',
                data: [0, 1, 2, 3, 5, 7, 10, 12],
                borderColor: '#ef4444',
                backgroundColor: 'rgba(239, 68, 68, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
    
    // Casos por Província
    const provinceCtx = document.getElementById('provinceChart').getContext('2d');
    new Chart(provinceCtx, {
        type: 'bar',
        data: {
            labels: ['Luanda', 'Bengo', 'Malanje', 'Cuanza Sul'],
            datasets: [{
                label: 'Casos Ativos',
                data: [78, 23, 15, 12],
                backgroundColor: '#3b82f6'
            }, {
                label: 'Óbitos',
                data: [8, 2, 1, 1],
                backgroundColor: '#ef4444'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
    
    // Distribuição por Faixa Etária
    const ageCtx = document.getElementById('ageChart').getContext('2d');
    new Chart(ageCtx, {
        type: 'doughnut',
        data: {
            labels: ['0-18 anos', '19-35 anos', '36-60 anos', '60+ anos'],
            datasets: [{
                data: [25, 45, 35, 22],
                backgroundColor: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
    
    // Distribuição por Sexo
    const genderCtx = document.getElementById('genderChart').getContext('2d');
    new Chart(genderCtx, {
        type: 'pie',
        data: {
            labels: ['Masculino', 'Feminino'],
            datasets: [{
                data: [65, 62],
                backgroundColor: ['#3b82f6', '#ec4899']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
});
</script>
@endsection
