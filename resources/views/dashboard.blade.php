@extends('layouts.app')

@section('title', 'Dashboard - Sistema de Triagem')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
                    <p class="mt-1 text-sm text-gray-600">Sistema de Triagem e Monitoramento de Cólera</p>
                </div>
                <div class="flex items-center space-x-4">
                    <!-- Update Indicator -->
                    <div data-component="update-indicator" class="hidden flex items-center text-sm text-blue-600">
                        <svg class="animate-spin w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Atualizando...
                    </div>
                    
                    <!-- Auto-refresh Toggle -->
                    <label class="flex items-center text-sm text-gray-600">
                        <input type="checkbox" id="auto-refresh-toggle" checked class="mr-2 rounded">
                        Atualização automática
                    </label>
                    
                    <!-- Manual Refresh Button -->
                    <button data-action="refresh-dashboard" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 transform hover:scale-105">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Atualizar
                    </button>
                    
                    <!-- Last Update Time -->
                    <div data-component="last-update" class="text-sm text-gray-500">
                        Última atualização: {{ now()->format('H:i') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Cards de Estatísticas -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total de Pacientes -->
            <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 border-l-4 border-blue-500">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 uppercase tracking-wide">Total de Pacientes</p>
                            <p data-stat="pacientes-total" class="text-3xl font-bold text-gray-900 mt-2 updating-number">{{ number_format($stats['pacientes_total']) }}</p>
                            <div class="flex items-center mt-2">
                                <svg class="w-4 h-4 text-green-500 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="text-sm text-green-600 font-medium">+12% este mês</span>
                            </div>
                        </div>
                        <div class="p-3 bg-blue-100 rounded-full">
                            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cólera Confirmada -->
            <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 border-l-4 border-red-500">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 uppercase tracking-wide">Cólera Confirmada</p>
                            <p data-stat="colera-confirmada" class="text-3xl font-bold text-red-600 mt-2 updating-number">{{ number_format($stats['colera_confirmada']) }}</p>
                            <div class="flex items-center mt-2">
                                <svg class="w-4 h-4 text-red-500 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 12.586V5a1 1 0 012 0v7.586l2.293-2.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="text-sm text-red-600 font-medium">Crítico</span>
                            </div>
                        </div>
                        <div class="p-3 bg-red-100 rounded-full">
                            <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Alto Risco -->
            <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 border-l-4 border-yellow-500">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 uppercase tracking-wide">Alto Risco</p>
                            <p data-stat="alto-risco" class="text-3xl font-bold text-yellow-600 mt-2 updating-number">{{ number_format($stats['pacientes_alto_risco']) }}</p>
                            <div class="flex items-center mt-2">
                                <svg class="w-4 h-4 text-yellow-500 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="text-sm text-yellow-600 font-medium">Atenção</span>
                            </div>
                        </div>
                        <div class="p-3 bg-yellow-100 rounded-full">
                            <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ambulâncias Disponíveis -->
            @if(auth()->user()->podeGerenciarVeiculos())
            <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 border-l-4 border-green-500">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 uppercase tracking-wide">Ambulâncias Disponíveis</p>
                            <p data-stat="veiculos-disponiveis" class="text-3xl font-bold text-green-600 mt-2 updating-number">{{ number_format($stats['veiculos_disponiveis']) }}</p>
                            <div class="flex items-center mt-2">
                                <svg class="w-4 h-4 text-green-500 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="text-sm text-green-600 font-medium">Operacional</span>
                            </div>
                        </div>
                        <div class="p-3 bg-green-100 rounded-full">
                            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.031 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Gráficos -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
            <!-- Evolução de Casos -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <svg class="w-5 h-5 text-indigo-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            Evolução de Casos de Cólera (30 dias)
                        </h3>
                        <div class="flex space-x-4 text-sm">
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-red-500 rounded-full mr-2"></div>
                                <span class="text-gray-600">Confirmados</span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-yellow-500 rounded-full mr-2"></div>
                                <span class="text-gray-600">Prováveis</span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-blue-500 rounded-full mr-2"></div>
                                <span class="text-gray-600">Suspeitos</span>
                            </div>
                        </div>
                    </div>
                    <div class="h-80">
                        <canvas id="evolutionChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Distribuição por Diagnóstico -->
            <div>
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                        <svg class="w-5 h-5 text-indigo-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path>
                        </svg>
                        Diagnósticos de Cólera
                    </h3>
                    <div class="h-64 mb-4">
                        <canvas id="diagnosisChart"></canvas>
                    </div>
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-red-500 rounded-full mr-2"></div>
                                <span class="text-sm text-gray-600">Confirmado</span>
                            </div>
                            <span data-diagnosis="confirmado" class="text-sm font-medium text-gray-900">{{ $stats['colera_confirmada'] }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-yellow-500 rounded-full mr-2"></div>
                                <span class="text-sm text-gray-600">Provável</span>
                            </div>
                            <span data-diagnosis="provavel" class="text-sm font-medium text-gray-900">{{ $stats['colera_provavel'] }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-blue-500 rounded-full mr-2"></div>
                                <span class="text-sm text-gray-600">Suspeito</span>
                            </div>
                            <span data-diagnosis="suspeito" class="text-sm font-medium text-gray-900">{{ $stats['colera_suspeita'] }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-green-500 rounded-full mr-2"></div>
                                <span class="text-sm text-gray-600">Descartado</span>
                            </div>
                            <span data-diagnosis="descartado" class="text-sm font-medium text-gray-900">{{ $stats['colera_descartada'] }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-gray-500 rounded-full mr-2"></div>
                                <span class="text-sm text-gray-600">Pendente</span>
                            </div>
                            <span data-diagnosis="pendente" class="text-sm font-medium text-gray-900">{{ $stats['colera_pendente'] }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabelas de Dados -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Casos Recentes -->
            <div class="bg-white rounded-xl shadow-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 text-indigo-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Casos Recentes (24h)
                    </h3>
                </div>
                <div class="p-6">
                    <div data-component="recent-patients" class="space-y-4">
                        @forelse($pacientesRecentes as $paciente)
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors duration-200">
                            <div class="flex-1">
                                <h4 class="font-medium text-gray-900">{{ $paciente->nome }}</h4>
                                <p class="text-sm text-gray-600">{{ $paciente->estabelecimento->nome ?? 'N/A' }}</p>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $paciente->risco === 'alto' ? 'bg-red-100 text-red-800' : 
                                       ($paciente->risco === 'medio' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') }}">
                                    {{ $paciente->risco_formatado }}
                                </span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $paciente->diagnostico_colera === 'confirmado' ? 'bg-red-100 text-red-800' : 
                                       ($paciente->diagnostico_colera === 'provavel' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800') }}">
                                    {{ $paciente->diagnostico_colera_formatado }}
                                </span>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <p class="mt-2 text-sm text-gray-500">Nenhum caso recente</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Status das Ambulâncias -->
            @if(auth()->user()->podeGerenciarVeiculos())
            <div class="bg-white rounded-xl shadow-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 text-indigo-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.031 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                        Status das Ambulâncias
                    </h3>
                </div>
                <div class="p-6">
                    <div class="h-64 mb-4">
                        <canvas id="ambulanceChart"></canvas>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="text-center p-3 bg-green-50 rounded-lg">
                            <div data-ambulance="disponivel" class="text-2xl font-bold text-green-600">{{ $ambulanceData['disponivel'] }}</div>
                            <div class="text-sm text-green-700">Disponível</div>
                        </div>
                        <div class="text-center p-3 bg-blue-50 rounded-lg">
                            <div data-ambulance="em-atendimento" class="text-2xl font-bold text-blue-600">{{ $ambulanceData['em_atendimento'] }}</div>
                            <div class="text-sm text-blue-700">Em Atendimento</div>
                        </div>
                        <div class="text-center p-3 bg-yellow-50 rounded-lg">
                            <div data-ambulance="manutencao" class="text-2xl font-bold text-yellow-600">{{ $ambulanceData['manutencao'] }}</div>
                            <div class="text-sm text-yellow-700">Manutenção</div>
                        </div>
                        <div class="text-center p-3 bg-red-50 rounded-lg">
                            <div data-ambulance="indisponivel" class="text-2xl font-bold text-red-600">{{ $ambulanceData['indisponivel'] }}</div>
                            <div class="text-sm text-red-700">Indisponível</div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="{{ asset('js/dashboard-realtime.js') }}"></script>
<script>
// Dados para os gráficos
const evolutionData = @json($evolutionData);
const ambulanceData = @json($ambulanceData);
const stats = @json($stats);

// Configuração comum dos gráficos
Chart.defaults.font.family = 'Inter, system-ui, sans-serif';
Chart.defaults.color = '#6B7280';

// Initialize real-time dashboard
const dashboardRealTime = new DashboardRealTime();

// Gráfico de Evolução
const evolutionCtx = document.getElementById('evolutionChart').getContext('2d');
const evolutionChart = new Chart(evolutionCtx, {
    type: 'line',
    data: {
        labels: evolutionData.labels,
        datasets: [{
            label: 'Confirmados',
            data: evolutionData.confirmados,
            borderColor: '#EF4444',
            backgroundColor: 'rgba(239, 68, 68, 0.1)',
            borderWidth: 3,
            fill: true,
            tension: 0.4,
            pointBackgroundColor: '#EF4444',
            pointBorderColor: '#ffffff',
            pointBorderWidth: 2,
            pointRadius: 6,
            pointHoverRadius: 8
        }, {
            label: 'Prováveis',
            data: evolutionData.provaveis,
            borderColor: '#F59E0B',
            backgroundColor: 'rgba(245, 158, 11, 0.1)',
            borderWidth: 3,
            fill: true,
            tension: 0.4,
            pointBackgroundColor: '#F59E0B',
            pointBorderColor: '#ffffff',
            pointBorderWidth: 2,
            pointRadius: 6,
            pointHoverRadius: 8
        }, {
            label: 'Suspeitos',
            data: evolutionData.suspeitos,
            borderColor: '#3B82F6',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            borderWidth: 3,
            fill: true,
            tension: 0.4,
            pointBackgroundColor: '#3B82F6',
            pointBorderColor: '#ffffff',
            pointBorderWidth: 2,
            pointRadius: 6,
            pointHoverRadius: 8
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                backgroundColor: 'rgba(17, 24, 39, 0.9)',
                titleColor: '#ffffff',
                bodyColor: '#ffffff',
                borderColor: '#374151',
                borderWidth: 1,
                cornerRadius: 8,
                displayColors: true,
                intersect: false,
                mode: 'index'
            }
        },
        scales: {
            x: {
                grid: {
                    display: false
                },
                border: {
                    display: false
                }
            },
            y: {
                beginAtZero: true,
                grid: {
                    color: '#F3F4F6'
                },
                border: {
                    display: false
                }
            }
        },
        interaction: {
            intersect: false,
            mode: 'index'
        }
    }
});

// Register chart with real-time service
dashboardRealTime.registerChart('evolution', evolutionChart);

// Gráfico de Diagnósticos
const diagnosisCtx = document.getElementById('diagnosisChart').getContext('2d');
const diagnosisChart = new Chart(diagnosisCtx, {
    type: 'doughnut',
    data: {
        labels: ['Confirmado', 'Provável', 'Suspeito', 'Descartado', 'Pendente'],
        datasets: [{
            data: [
                stats.colera_confirmada,
                stats.colera_provavel,
                stats.colera_suspeita,
                stats.colera_descartada,
                stats.colera_pendente
            ],
            backgroundColor: [
                '#EF4444',
                '#F59E0B',
                '#3B82F6',
                '#10B981',
                '#6B7280'
            ],
            borderWidth: 0,
            hoverOffset: 4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                backgroundColor: 'rgba(17, 24, 39, 0.9)',
                titleColor: '#ffffff',
                bodyColor: '#ffffff',
                borderColor: '#374151',
                borderWidth: 1,
                cornerRadius: 8
            }
        },
        cutout: '60%'
    }
});

// Register chart with real-time service
dashboardRealTime.registerChart('diagnosis', diagnosisChart);

@if(auth()->user()->podeGerenciarVeiculos())
// Gráfico de Ambulâncias
const ambulanceCtx = document.getElementById('ambulanceChart').getContext('2d');
const ambulanceChart = new Chart(ambulanceCtx, {
    type: 'doughnut',
    data: {
        labels: ['Disponível', 'Em Atendimento', 'Manutenção', 'Indisponível'],
        datasets: [{
            data: [
                ambulanceData.disponivel,
                ambulanceData.em_atendimento,
                ambulanceData.manutencao,
                ambulanceData.indisponivel
            ],
            backgroundColor: [
                '#10B981',
                '#3B82F6',
                '#F59E0B',
                '#EF4444'
            ],
            borderWidth: 0,
            hoverOffset: 4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                backgroundColor: 'rgba(17, 24, 39, 0.9)',
                titleColor: '#ffffff',
                bodyColor: '#ffffff',
                borderColor: '#374151',
                borderWidth: 1,
                cornerRadius: 8
            }
        },
        cutout: '60%'
    }
});

// Register chart with real-time service
dashboardRealTime.registerChart('ambulance', ambulanceChart);
@endif

// CSS for updating animations
const style = document.createElement('style');
style.textContent = `
    .updating-number.updating {
        color: #3B82F6;
        transform: scale(1.05);
        transition: all 0.3s ease;
    }
    
    .chart-updating {
        opacity: 0.7;
        transition: opacity 0.3s ease;
    }
`;
document.head.appendChild(style);

// Animações de entrada
document.addEventListener('DOMContentLoaded', function() {
    const cards = document.querySelectorAll('.grid > div');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        setTimeout(() => {
            card.style.transition = 'all 0.5s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });
});
</script>
@endpush
@endsection
