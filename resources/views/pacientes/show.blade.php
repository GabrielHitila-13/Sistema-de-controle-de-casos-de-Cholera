@extends('layouts.app')

@section('title', 'Paciente: ' . $paciente->nome)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header com QR Code -->
    <div class="bg-white shadow-md rounded-lg p-6">
        <div class="flex justify-between items-start">
            <div class="flex-1">
                <h2 class="text-3xl font-bold text-gray-900 mb-2">{{ $paciente->nome }}</h2>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="font-medium text-gray-600">BI:</span> {{ $paciente->bi }}
                    </div>
                    <div>
                        <span class="font-medium text-gray-600">Telefone:</span> {{ $paciente->telefone ?? 'N/A' }}
                    </div>
                    <div>
                        <span class="font-medium text-gray-600">Sexo:</span> {{ ucfirst($paciente->sexo) }}
                    </div>
                    <div>
                        <span class="font-medium text-gray-600">Idade:</span> {{ $paciente->data_nascimento->age }} anos
                    </div>
                    <div>
                        <span class="font-medium text-gray-600">Estabelecimento:</span> {{ $paciente->estabelecimento->nome ?? 'N/A' }}
                    </div>
                    <div>
                        <span class="font-medium text-gray-600">Data Triagem:</span> {{ $paciente->data_triagem?->format('d/m/Y H:i') ?? 'N/A' }}
                    </div>
                </div>
            </div>
            
            <!-- QR Code -->
            @if($paciente->qr_code)
            <div class="ml-6 text-center">
                <div class="bg-white p-4 border-2 border-gray-300 rounded-lg">
                    <img src="data:image/svg+xml;base64,{{ $paciente->qr_code }}" alt="QR Code" class="w-32 h-32">
                </div>
                <p class="text-xs text-gray-500 mt-2">QR Code do Paciente</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Status de Risco -->
    <div class="bg-white shadow-md rounded-lg p-6">
        <h3 class="text-xl font-semibold mb-4">Status de Triagem</h3>
        <div class="flex items-center space-x-4">
            <span class="text-lg font-medium">Nível de Risco:</span>
            <span class="
                @if($paciente->risco == 'alto') badge-alto text-lg px-4 py-2
                @elseif($paciente->risco == 'medio') badge-medio text-lg px-4 py-2
                @else badge-baixo text-lg px-4 py-2 @endif
            ">
                {{ ucfirst($paciente->risco) }} Risco
            </span>
            @if($paciente->risco == 'alto')
                <i class="fas fa-exclamation-triangle text-red-500 text-xl animate-pulse"></i>
            @endif
        </div>
        
        @if($paciente->sintomas)
        <div class="mt-4">
            <h4 class="font-medium text-gray-900 mb-2">Sintomas Relatados:</h4>
            <div class="bg-gray-50 p-3 rounded-md">
                <p class="text-gray-700">{{ $paciente->sintomas }}</p>
            </div>
        </div>
        @endif
    </div>

    <!-- Ações -->
    <div class="bg-white shadow-md rounded-lg p-6">
        <h3 class="text-xl font-semibold mb-4">Ações</h3>
        <div class="flex space-x-4">
            <a href="{{ route('pacientes.edit', $paciente) }}" class="btn-primary">
                <i class="fas fa-edit mr-2"></i>Editar Paciente
            </a>
            <button onclick="window.print()" class="btn-secondary">
                <i class="fas fa-print mr-2"></i>Imprimir Ficha
            </button>
            <a href="{{ route('pacientes.index') }}" class="btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i>Voltar à Lista
            </a>
        </div>
    </div>
</div>

<style>
@media print {
    .no-print { display: none !important; }
    body { font-size: 12pt; }
    .bg-white { background: white !important; }
    .shadow-md { box-shadow: none !important; }
}
</style>
@endsection
