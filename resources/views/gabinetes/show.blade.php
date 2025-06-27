
@extends('layouts.app')

@section('title', 'Gabinete: ' . $gabinete->nome)

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="bg-white shadow-md rounded-lg p-6">
        <div class="flex justify-between items-start">
            <div class="flex-1">
                <h2 class="text-3xl font-bold text-gray-900 mb-2">{{ $gabinete->nome }}</h2>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="font-medium text-gray-600">Tipo:</span> {{ ucfirst($gabinete->tipo) }}
                    </div>
                    <div>
                        <span class="font-medium text-gray-600">Telefone:</span> {{ $gabinete->telefone ?? 'N/A' }}
                    </div>
                    <div>
                        <span class="font-medium text-gray-600">Endereço:</span> {{ $gabinete->endereco ?? 'N/A' }}
                    </div>
                    <div>
                        <span class="font-medium text-gray-600">Latitude:</span> {{ $gabinete->latitude ?? 'N/A' }}
                    </div>
                    <div>
                        <span class="font-medium text-gray-600">Longitude:</span> {{ $gabinete->longitude ?? 'N/A' }}
                    </div>
                </div>
            </div>
            <div class="ml-6 text-center">
                <div class="bg-blue-100 p-4 border-2 border-blue-300 rounded-lg">
                    <i class="fas fa-building text-blue-600 text-5xl"></i>
                </div>
                <p class="text-xs text-gray-500 mt-2">Gabinete de Saúde</p>
            </div>
        </div>
    </div>

    <div class="bg-white shadow-md rounded-lg p-6">
        <h3 class="text-xl font-semibold mb-4">Estabelecimentos Vinculados</h3>
        @if($gabinete->estabelecimentos && count($gabinete->estabelecimentos))
            <ul class="divide-y divide-gray-200">
                @foreach($gabinete->estabelecimentos as $estabelecimento)
                    <li class="py-2 flex justify-between items-center">
                        <div>
                            <span class="font-medium">{{ $estabelecimento->nome }}</span>
                            <span class="text-xs text-gray-500 ml-2">({{ ucfirst($estabelecimento->categoria) }})</span>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            Capacidade: {{ $estabelecimento->capacidade ?? 'N/A' }}
                        </span>
                    </li>
                @endforeach
            </ul>
        @else
            <p class="text-gray-500">Nenhum estabelecimento vinculado a este gabinete.</p>
        @endif
    </div>

    <div class="bg-white shadow-md rounded-lg p-6">
        <h3 class="text-xl font-semibold mb-4">Ações</h3>
        <div class="flex space-x-4">
            <a href="{{ route('gabinetes.edit', $gabinete) }}" class="btn-primary">
                <i class="fas fa-edit mr-2"></i>Editar Gabinete
            </a>
            <a href="{{ route('gabinetes.index') }}" class="btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i>Voltar à Lista
            </a>
        </div>
    </div>
</div>
@endsection
