@extends('layouts.app')

@section('title', 'Triagem Inteligente - Sistema Cólera Angola')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-3xl font-bold">Triagem Inteligente</h2>
                <p class="text-blue-100 mt-2">Sistema automatizado de avaliação de risco e encaminhamento para cólera</p>
            </div>
            <div class="text-right">
                <div class="bg-white bg-opacity-20 rounded-lg px-4 py-2">
                    <p class="text-blue-100 text-sm">Triagens Hoje</p>
                    <p class="text-2xl font-bold">{{ \App\Models\Paciente::whereDate('created_at', today())->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Funcionalidades -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Nova Triagem -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-stethoscope text-blue-600 text-xl"></i>
                </div>
                <span class="text-sm text-gray-500">Principal</span>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Nova Triagem</h3>
            <p class="text-gray-600 text-sm mb-4">Iniciar avaliação completa de sintomas com encaminhamento automático</p>
            <a href="{{ route('triagem.create') }}" class="btn-primary w-full justify-center">
                <i class="fas fa-plus mr-2"></i>
                Iniciar Triagem
            </a>
        </div>

        <!-- Geolocalização -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-map-marker-alt text-green-600 text-xl"></i>
                </div>
                <span class="text-sm text-gray-500">Automático</span>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Encaminhamento Inteligente</h3>
            <p class="text-gray-600 text-sm mb-4">Localização automática do hospital mais próximo usando GPS</p>
            <div class="flex items-center text-green-600 text-sm">
                <i class="fas fa-check-circle mr-2"></i>
                <span>Google Maps integrado</span>
            </div>
        </div>

        <!-- QR Code -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-qrcode text-purple-600 text-xl"></i>
                </div>
                <span class="text-sm text-gray-500">Automático</span>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">QR Code Automático</h3>
            <p class="text-gray-600 text-sm mb-4">Geração automática de QR Code com dados do paciente</p>
            <div class="flex items-center text-purple-600 text-sm">
                <i class="fas fa-check-circle mr-2"></i>
                <span>Ficha digital completa</span>
            </div>
        </div>
    </div>

    <!-- Estatísticas de Triagem -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Estatísticas de Triagem</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="text-center p-4 bg-red-50 rounded-lg">
                <div class="text-2xl font-bold text-red-600">{{ \App\Models\Paciente::where('risco', 'alto')->count() }}</div>
                <div class="text-sm text-red-700">Alto Risco</div>
            </div>
            <div class="text-center p-4 bg-yellow-50 rounded-lg">
                <div class="text-2xl font-bold text-yellow-600">{{ \App\Models\Paciente::where('risco', 'medio')->count() }}</div>
                <div class="text-sm text-yellow-700">Médio Risco</div>
            </div>
            <div class="text-center p-4 bg-green-50 rounded-lg">
                <div class="text-2xl font-bold text-green-600">{{ \App\Models\Paciente::where('risco', 'baixo')->count() }}</div>
                <div class="text-sm text-green-700">Baixo Risco</div>
            </div>
            <div class="text-center p-4 bg-blue-50 rounded-lg">
                <div class="text-2xl font-bold text-blue-600">{{ \App\Models\Paciente::count() }}</div>
                <div class="text-sm text-blue-700">Total</div>
            </div>
        </div>

        <!-- Últimas Triagens -->
        <div>
            <h4 class="font-medium text-gray-900 mb-3">Últimas Triagens</h4>
            <div class="space-y-3">
                @forelse(\App\Models\Paciente::with('estabelecimento')->latest()->limit(5)->get() as $paciente)
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                            <i class="fas fa-user text-blue-600 text-sm"></i>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">{{ $paciente->nome }}</p>
                            <p class="text-sm text-gray-600">{{ $paciente->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="badge-{{ $paciente->risco }}">{{ ucfirst($paciente->risco) }}</span>
                        <a href="{{ route('pacientes.show', $paciente) }}" class="text-blue-600 hover:text-blue-700">
                            <i class="fas fa-eye"></i>
                        </a>
                    </div>
                </div>
                @empty
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-clipboard-list text-3xl mb-2"></i>
                    <p>Nenhuma triagem realizada ainda</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Como Funciona -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Como Funciona a Triagem Inteligente</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="text-center">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <span class="text-2xl font-bold text-blue-600">1</span>
                </div>
                <h4 class="font-medium text-gray-900 mb-2">Dados do Paciente</h4>
                <p class="text-sm text-gray-600">Inserir informações básicas e obter localização</p>
            </div>
            
            <div class="text-center">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <span class="text-2xl font-bold text-green-600">2</span>
                </div>
                <h4 class="font-medium text-gray-900 mb-2">Avaliação de Sintomas</h4>
                <p class="text-sm text-gray-600">Sistema analisa sintomas e calcula risco automaticamente</p>
            </div>
            
            <div class="text-center">
                <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <span class="text-2xl font-bold text-yellow-600">3</span>
                </div>
                <h4 class="font-medium text-gray-900 mb-2">Hospital Mais Próximo</h4>
                <p class="text-sm text-gray-600">Localização automática do melhor hospital para encaminhamento</p>
            </div>
            
            <div class="text-center">
                <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <span class="text-2xl font-bold text-purple-600">4</span>
                </div>
                <h4 class="font-medium text-gray-900 mb-2">QR Code e Ficha</h4>
                <p class="text-sm text-gray-600">Geração automática de QR Code e ficha para impressão</p>
            </div>
        </div>
    </div>
</div>
@endsection
