
@extends('layouts.app')

@section('title', 'Estabelecimento: ' . $estabelecimento->nome)

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="bg-white shadow-md rounded-lg p-6">
        <div class="flex justify-between items-start">
            <div class="flex-1">
                <h2 class="text-3xl font-bold text-gray-900 mb-2">{{ $estabelecimento->nome }}</h2>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="font-medium text-gray-600">Categoria:</span> {{ ucfirst($estabelecimento->categoria) }}
                    </div>
                    <div>
                        <span class="font-medium text-gray-600">Gabinete:</span> {{ $estabelecimento->gabinete->nome ?? 'N/A' }}
                    </div>
                    <div>
                        <span class="font-medium text-gray-600">Endereço:</span> {{ $estabelecimento->endereco ?? 'N/A' }}
                    </div>
                    <div>
                        <span class="font-medium text-gray-600">Telefone:</span> {{ $estabelecimento->telefone ?? 'N/A' }}
                    </div>
                    <div>
                        <span class="font-medium text-gray-600">Capacidade:</span> {{ $estabelecimento->capacidade ?? 'N/A' }}
                    </div>
                </div>
            </div>
            <div class="ml-6 text-center">
                <div class="bg-green-100 p-4 border-2 border-green-300 rounded-lg">
                    <i class="fas fa-hospital text-green-600 text-5xl"></i>
                </div>
                <p class="text-xs text-gray-500 mt-2">Estabelecimento de Saúde</p>
            </div>
        </div>
    </div>

    <div class="bg-white shadow-md rounded-lg p-6">
        <h3 class="text-xl font-semibold mb-4">Pacientes Vinculados</h3>
        @if($estabelecimento->pacientes && count($estabelecimento->pacientes))
            <ul class="divide-y divide-gray-200">
                @foreach($estabelecimento->pacientes as $paciente)
                    <li class="py-2 flex justify-between items-center">
                        <div>
                            <span class="font-medium">{{ $paciente->nome }}</span>
                            <span class="text-xs text-gray-500 ml-2">({{ $paciente->bi }})</span>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            @if($paciente->risco == 'alto') bg-red-100 text-red-800
                            @elseif($paciente->risco == 'medio') bg-yellow-100 text-yellow-800
                            @else bg-green-100 text-green-800 @endif">
                            {{ ucfirst($paciente->risco) }}
                        </span>
                    </li>
                @endforeach
            </ul>
        @else
            <p class="text-gray-500">Nenhum paciente vinculado a este estabelecimento.</p>
        @endif
    </div>

    <div class="bg-white shadow-md rounded-lg p-6">
        <h3 class="text-xl font-semibold mb-4">Usuários Vinculados</h3>
        @if($estabelecimento->usuarios && count($estabelecimento->usuarios))
            <ul class="divide-y divide-gray-200">
                @foreach($estabelecimento->usuarios as $usuario)
                    <li class="py-2 flex justify-between items-center">
                        <div>
                            <span class="font-medium">{{ $usuario->name }}</span>
                            <span class="text-xs text-gray-500 ml-2">({{ $usuario->email }})</span>
                        </div>
                    </li>
                @endforeach
            </ul>
        @else
            <p class="text-gray-500">Nenhum usuário vinculado a este estabelecimento.</p>
        @endif
    </div>

    <div class="bg-white shadow-md rounded-lg p-6">
        <h3 class="text-xl font-semibold mb-4">Ações</h3>
        <div class="flex space-x-4">
            <a href="{{ route('estabelecimentos.edit', $estabelecimento) }}" class="btn-primary">
                <i class="fas fa-edit mr-2"></i>Editar Estabelecimento
            </a>
            <a href="{{ route('estabelecimentos.index') }}" class="btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i>Voltar à Lista
            </a>
        </div>
    </div>
</div>
@endsection
