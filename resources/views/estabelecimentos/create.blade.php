@extends('layouts.app')

@section('title', 'Estabelecimentos - Sistema Cólera Angola')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h2 class="text-3xl font-bold text-gray-900">Estabelecimentos de Saúde</h2>
        <a href="{{ route('estabelecimentos.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center">
            <i class="fas fa-plus mr-2"></i>
            Novo Estabelecimento
        </a>
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-md">
        <ul class="divide-y divide-gray-200">
            @forelse($estabelecimentos ?? [] as $estabelecimento)
            <li>
                <div class="px-4 py-4 flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-10 w-10">
                            <div class="h-10 w-10 rounded-full bg-green-100 flex items-center justify-center">
                                <i class="fas fa-hospital text-green-600"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <div class="text-sm font-medium text-gray-900">{{ $estabelecimento->nome }}</div>
                            <div class="text-sm text-gray-500">
                                {{ ucfirst($estabelecimento->categoria) }} | 
                                Gabinete: {{ $estabelecimento->gabinete->nome ?? 'N/A' }}
                            </div>
                            <div class="text-xs text-gray-400">
                                Capacidade: {{ $estabelecimento->capacidade }} | 
                                Tel: {{ $estabelecimento->telefone ?? 'N/A' }}
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <a href="{{ route('estabelecimentos.show', $estabelecimento) }}" class="text-blue-600 hover:text-blue-900">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('estabelecimentos.edit', $estabelecimento) }}" class="text-green-600 hover:text-green-900">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form method="POST" action="{{ route('estabelecimentos.destroy', $estabelecimento) }}" class="inline" onsubmit="return confirm('Tem certeza?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </li>
            @empty
            <li class="px-4 py-8 text-center text-gray-500">
                Nenhum estabelecimento encontrado.
            </li>
            @endforelse
        </ul>
    </div>
</div>
@endsection
