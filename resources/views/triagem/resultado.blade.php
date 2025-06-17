@extends('layouts.app')

@section('title', 'Resultado da Triagem - Sistema Cólera Angola')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header com Status -->
    <div class="bg-white shadow-lg rounded-lg overflow-hidden">
        <div class="bg-gradient-to-r from-{{ $paciente->risco == 'alto' ? 'red' : ($paciente->risco == 'medio' ? 'yellow' : 'green') }}-600 to-{{ $paciente->risco == 'alto' ? 'red' : ($paciente->risco == 'medio' ? 'yellow' : 'green') }}-700 px-6 py-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-white">Triagem Concluída</h2>
                    <p class="text-{{ $paciente->risco == 'alto' ? 'red' : ($paciente->risco == 'medio' ? 'yellow' : 'green') }}-100">Paciente: {{ $paciente->nome }}</p>
                </div>
                <div class="text-right">
                    <div class="bg-white bg-opacity-20 rounded-lg px-4 py-2">
                        <p class="text-white text-sm">Nível de Risco</p>
                        <p class="text-2xl font-bold text-white">{{ ucfirst($paciente->risco) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Dados do Paciente -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Dados do Paciente</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Nome:</span>
                            <span class="font-medium">{{ $paciente->nome }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">BI:</span>
                            <span class="font-medium">{{ $paciente->bi }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Telefone:</span>
                            <span class="font-medium">{{ $paciente->telefone ?? 'N/A' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Idade:</span>
                            <span class="font-medium">{{ $paciente->data_nascimento->age }} anos</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Sexo:</span>
                            <span class="font-medium">{{ ucfirst($paciente->sexo) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Data da Triagem:</span>
                            <span class="font-medium">{{ $paciente->data_triagem->format('d/m/Y H:i') }}</span>
                        </div>
                    </div>
                </div>

                <!-- QR Code -->
                <div class="text-center">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">QR Code do Paciente</h3>
                    @if($paciente->qr_code)
                        <div class="inline-block bg-white p-4 border-2 border-gray-300 rounded-lg shadow-sm">
                            <img src="data:image/svg+xml;base64,{{ $paciente->qr_code }}" alt="QR Code" class="w-48 h-48 mx-auto">
                        </div>
                        <p class="text-sm text-gray-600 mt-2">Escaneie para acessar informações do paciente</p>
                    @else
                        <div class="bg-gray-100 p-8 rounded-lg">
                            <i class="fas fa-qrcode text-gray-400 text-4xl mb-2"></i>
                            <p class="text-gray-500">QR Code não disponível</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Sintomas e Avaliação -->
    <div class="bg-white shadow-lg rounded-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Sintomas Relatados</h3>
        @if($paciente->sintomas)
            <div class="bg-gray-50 p-4 rounded-lg">
                <p class="text-gray-700">{{ $paciente->sintomas }}</p>
            </div>
        @else
            <p class="text-gray-500">Nenhum sintoma específico relatado.</p>
        @endif

        <div class="mt-6">
            <h4 class="font-medium text-gray-900 mb-3">Recomendações Médicas</h4>
            <div class="space-y-2">
                @if($paciente->risco == 'alto')
                    <div class="flex items-start bg-red-50 p-3 rounded-lg">
                        <i class="fas fa-exclamation-triangle text-red-600 mr-2 mt-1"></i>
                        <span class="text-red-800">URGENTE: Encaminhar imediatamente para hospital</span>
                    </div>
                    <div class="flex items-start bg-red-50 p-3 rounded-lg">
                        <i class="fas fa-tint text-red-600 mr-2 mt-1"></i>
                        <span class="text-red-800">Iniciar hidratação oral ou endovenosa</span>
                    </div>
                    <div class="flex items-start bg-red-50 p-3 rounded-lg">
                        <i class="fas fa-heartbeat text-red-600 mr-2 mt-1"></i>
                        <span class="text-red-800">Monitoramento contínuo dos sinais vitais</span>
                    </div>
                @elseif($paciente->risco == 'medio')
                    <div class="flex items-start bg-yellow-50 p-3 rounded-lg">
                        <i class="fas fa-hospital text-yellow-600 mr-2 mt-1"></i>
                        <span class="text-yellow-800">Encaminhar para avaliação médica</span>
                    </div>
                    <div class="flex items-start bg-yellow-50 p-3 rounded-lg">
                        <i class="fas fa-tint text-yellow-600 mr-2 mt-1"></i>
                        <span class="text-yellow-800">Iniciar hidratação oral</span>
                    </div>
                @else
                    <div class="flex items-start bg-green-50 p-3 rounded-lg">
                        <i class="fas fa-home text-green-600 mr-2 mt-1"></i>
                        <span class="text-green-800">Monitoramento domiciliar</span>
                    </div>
                    <div class="flex items-start bg-green-50 p-3 rounded-lg">
                        <i class="fas fa-tint text-green-600 mr-2 mt-1"></i>
                        <span class="text-green-800">Hidratação oral abundante</span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Hospital Recomendado -->
    @if($paciente->estabelecimento)
    <div class="bg-white shadow-lg rounded-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
            <i class="fas fa-hospital mr-2 text-blue-600"></i>
            Hospital Recomendado
        </h3>
        
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <h4 class="font-semibold text-blue-900">{{ $paciente->estabelecimento->nome }}</h4>
                    <p class="text-blue-700">{{ $paciente->estabelecimento->endereco }}</p>
                    <p class="text-sm text-blue-600 mt-1">{{ $paciente->estabelecimento->gabinete->nome ?? '' }}</p>
                    
                    @if($paciente->estabelecimento->telefone)
                    <div class="flex items-center mt-2">
                        <i class="fas fa-phone text-blue-600 mr-2"></i>
                        <span class="text-blue-700">{{ $paciente->estabelecimento->telefone }}</span>
                    </div>
                    @endif

                    @if($rotaHospital)
                    <div class="flex items-center mt-2 space-x-4">
                        <span class="text-sm text-blue-600">
                            <i class="fas fa-route mr-1"></i>
                            {{ $rotaHospital['distancia'] }}
                        </span>
                        <span class="text-sm text-blue-600">
                            <i class="fas fa-clock mr-1"></i>
                            {{ $rotaHospital['duracao'] }}
                        </span>
                    </div>
                    @endif
                </div>
                
                <div class="flex flex-col space-y-2">
                    @if($rotaHospital)
                    <a href="{{ $rotaHospital['url_maps'] }}" target="_blank" class="btn-primary text-sm">
                        <i class="fas fa-directions mr-1"></i>
                        Ver Rota
                    </a>
                    @endif
                    
                    @if($paciente->estabelecimento->telefone)
                    <a href="tel:{{ $paciente->estabelecimento->telefone }}" class="btn-secondary text-sm">
                        <i class="fas fa-phone mr-1"></i>
                        Ligar
                    </a>
                    @endif
                </div>
            </div>
        </div>

        @if($rotaHospital && isset($rotaHospital['instrucoes']))
        <div class="mt-4">
            <h5 class="font-medium text-gray-900 mb-2">Instruções de Rota</h5>
            <div class="bg-gray-50 p-3 rounded-lg max-h-32 overflow-y-auto">
                <ol class="list-decimal list-inside space-y-1 text-sm text-gray-700">
                    @foreach(array_slice($rotaHospital['instrucoes'], 0, 5) as $instrucao)
                    <li>{{ $instrucao }}</li>
                    @endforeach
                </ol>
            </div>
        </div>
        @endif
    </div>
    @endif

    <!-- Ações -->
    <div class="bg-white shadow-lg rounded-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Próximas Ações</h3>
        <div class="flex flex-wrap gap-4">
            <a href="{{ route('triagem.imprimir-ficha', $paciente) }}" target="_blank" class="btn-primary">
                <i class="fas fa-print mr-2"></i>
                Imprimir Ficha
            </a>
            
            <a href="{{ route('pacientes.show', $paciente) }}" class="btn-secondary">
                <i class="fas fa-eye mr-2"></i>
                Ver Ficha Completa
            </a>
            
            <a href="{{ route('triagem.create') }}" class="btn-secondary">
                <i class="fas fa-plus mr-2"></i>
                Nova Triagem
            </a>
            
            <a href="{{ route('dashboard') }}" class="btn-secondary">
                <i class="fas fa-home mr-2"></i>
                Voltar ao Dashboard
            </a>
        </div>
    </div>
</div>
@endsection
