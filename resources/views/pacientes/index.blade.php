@extends('layouts.app')

@section('title', 'Pacientes - Sistema Cólera Angola')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <h2 class="text-3xl font-bold text-gray-900">Gestão de Pacientes</h2>
        <a href="{{ route('pacientes.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center">
            <i class="fas fa-plus mr-2"></i>
            Novo Paciente
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white p-4 rounded-lg shadow">
        <form method="GET" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-64">
                <input type="text" name="search" placeholder="Buscar por nome ou BI..." 
                       value="{{ request('search') }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <select name="risco" class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Todos os Riscos</option>
                    <option value="baixo" {{ request('risco') == 'baixo' ? 'selected' : '' }}>Baixo</option>
                    <option value="medio" {{ request('risco') == 'medio' ? 'selected' : '' }}>Médio</option>
                    <option value="alto" {{ request('risco') == 'alto' ? 'selected' : '' }}>Alto</option>
                </select>
            </div>
            <button type="submit" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md">
                <i class="fas fa-search mr-2"></i>Filtrar
            </button>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white shadow overflow-hidden sm:rounded-md">
        <ul class="divide-y divide-gray-200">
            @forelse($pacientes as $paciente)
            <li>
                <div class="px-4 py-4 flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-10 w-10">
                            <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                <i class="fas fa-user text-gray-600"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <div class="flex items-center">
                                <div class="text-sm font-medium text-gray-900">{{ $paciente->nome }}</div>
                                <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($paciente->risco == 'alto') bg-red-100 text-red-800
                                    @elseif($paciente->risco == 'medio') bg-yellow-100 text-yellow-800
                                    @else bg-green-100 text-green-800 @endif">
                                    {{ ucfirst($paciente->risco) }}
                                </span>
                            </div>
                            <div class="text-sm text-gray-500">
                                BI: {{ $paciente->bi }} | 
                                {{ $paciente->sexo }} | 
                                {{ $paciente->estabelecimento->nome ?? 'N/A' }}
                            </div>
                            @if($paciente->sintomas)
                            <div class="text-xs text-gray-400 mt-1">
                                {{ Str::limit($paciente->sintomas, 100) }}
                            </div>
                            @endif
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <a href="{{ route('pacientes.show', $paciente) }}" 
                           class="text-blue-600 hover:text-blue-900">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('pacientes.edit', $paciente) }}" 
                           class="text-green-600 hover:text-green-900">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form method="POST" action="{{ route('pacientes.destroy', $paciente) }}" 
                              class="inline" onsubmit="return confirm('Tem certeza?')">
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
                Nenhum paciente encontrado.
            </li>
            @endforelse
        </ul>
    </div>

    <!-- Pagination -->
    <div class="flex justify-center">
        {{ $pacientes->links() }}
    </div>
</div>
@endsection