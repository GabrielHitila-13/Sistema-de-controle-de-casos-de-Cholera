@extends('layouts.app')

@section('title', 'Pontos de Atendimento - Sistema Cólera Angola')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h2 class="text-3xl font-bold text-gray-900">Pontos de Atendimento de Emergência</h2>
        <a href="{{ route('pontos-atendimento.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center">
            <i class="fas fa-plus mr-2"></i>
            Novo Ponto
        </a>
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-md">
        <ul class="divide-y divide-gray-200">
            @forelse($pontosAtendimento ?? [] as $ponto)
            <li>
                <div class="px-4 py-4 flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-10 w-10">
                            <div class="h-10 w-10 rounded-full {{ $ponto->ativo ? 'bg-green-100' : 'bg-gray-100' }} flex items-center justify-center">
                                <i class="fas fa-map-marker-alt {{ $ponto->ativo ? 'text-green-600' : 'text-gray-600' }}"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <div class="flex items-center">
                                <div class="text-sm font-medium text-gray-900">{{ $ponto->nome }}</div>
                                <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $ponto->ativo ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $ponto->ativo ? 'Ativo' : 'Inativo' }}
                                </span>
                            </div>
                            <div class="text-sm text-gray-500">
                                Lat: {{ $ponto->latitude }}, Lng: {{ $ponto->longitude }}
                            </div>
                            @if($ponto->descricao)
                            <div class="text-xs text-gray-400 mt-1">
                                {{ Str::limit($ponto->descricao, 100) }}
                            </div>
                            @endif
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <a href="https://maps.google.com/?q={{ $ponto->latitude }},{{ $ponto->longitude }}" target="_blank" class="text-blue-600 hover:text-blue-900" title="Ver no Mapa">
                            <i class="fas fa-map"></i>
                        </a>
                        <a href="{{ route('pontos-atendimento.edit', $ponto) }}" class="text-green-600 hover:text-green-900">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form method="POST" action="{{ route('pontos-atendimento.destroy', $ponto) }}" class="inline" onsubmit="return confirm('Tem certeza?')">
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
                Nenhum ponto de atendimento encontrado.
            </li>
            @endforelse
        </ul>
    </div>
</div>
@endsection
