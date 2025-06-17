@extends('layouts.app')

@section('title', 'Usuários - Sistema Cólera Angola')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h2 class="text-3xl font-bold text-gray-900">Gestão de Usuários</h2>
        @can('create-users')
        <a href="{{ route('usuarios.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center">
            <i class="fas fa-plus mr-2"></i>
            Novo Usuário
        </a>
        @endcan
    </div>

    <!-- Filtros -->
    <div class="bg-white p-4 rounded-lg shadow">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label for="search" class="form-label">Buscar</label>
                <input type="text" id="search" name="search" value="{{ request('search') }}" 
                       class="form-input" placeholder="Nome ou email...">
            </div>
            <div>
                <label for="papel" class="form-label">Papel</label>
                <select id="papel" name="papel" class="form-input">
                    <option value="">Todos os papéis</option>
                    <option value="administrador" {{ request('papel') == 'administrador' ? 'selected' : '' }}>Administrador</option>
                    <option value="gestor" {{ request('papel') == 'gestor' ? 'selected' : '' }}>Gestor</option>
                    <option value="medico" {{ request('papel') == 'medico' ? 'selected' : '' }}>Médico</option>
                    <option value="tecnico" {{ request('papel') == 'tecnico' ? 'selected' : '' }}>Técnico</option>
                    <option value="enfermeiro" {{ request('papel') == 'enfermeiro' ? 'selected' : '' }}>Enfermeiro</option>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="btn-primary mr-2">Filtrar</button>
                <a href="{{ route('usuarios.index') }}" class="btn-secondary">Limpar</a>
            </div>
        </form>
    </div>

    <!-- Estatísticas -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <div class="bg-blue-100 p-4 rounded-lg">
            <div class="flex items-center">
                <i class="fas fa-users text-blue-600 text-2xl mr-3"></i>
                <div>
                    <p class="text-blue-800 font-semibold">Total</p>
                    <p class="text-2xl font-bold text-blue-900">{{ $users->total() }}</p>
                </div>
            </div>
        </div>
        <div class="bg-red-100 p-4 rounded-lg">
            <div class="flex items-center">
                <i class="fas fa-user-cog text-red-600 text-2xl mr-3"></i>
                <div>
                    <p class="text-red-800 font-semibold">Admins</p>
                    <p class="text-2xl font-bold text-red-900">{{ \App\Models\User::where('papel', 'administrador')->count() }}</p>
                </div>
            </div>
        </div>
        <div class="bg-green-100 p-4 rounded-lg">
            <div class="flex items-center">
                <i class="fas fa-user-md text-green-600 text-2xl mr-3"></i>
                <div>
                    <p class="text-green-800 font-semibold">Médicos</p>
                    <p class="text-2xl font-bold text-green-900">{{ \App\Models\User::where('papel', 'medico')->count() }}</p>
                </div>
            </div>
        </div>
        <div class="bg-yellow-100 p-4 rounded-lg">
            <div class="flex items-center">
                <i class="fas fa-user-nurse text-yellow-600 text-2xl mr-3"></i>
                <div>
                    <p class="text-yellow-800 font-semibold">Enfermeiros</p>
                    <p class="text-2xl font-bold text-yellow-900">{{ \App\Models\User::where('papel', 'enfermeiro')->count() }}</p>
                </div>
            </div>
        </div>
        <div class="bg-purple-100 p-4 rounded-lg">
            <div class="flex items-center">
                <i class="fas fa-user-cog text-purple-600 text-2xl mr-3"></i>
                <div>
                    <p class="text-purple-800 font-semibold">Técnicos</p>
                    <p class="text-2xl font-bold text-purple-900">{{ \App\Models\User::where('papel', 'tecnico')->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de Usuários -->
    <div class="bg-white shadow overflow-hidden sm:rounded-md">
        <ul class="divide-y divide-gray-200">
            @forelse($users as $user)
            <li>
                <div class="px-4 py-4 flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-10 w-10">
                            <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                <i class="fas fa-user text-blue-600"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <div class="flex items-center">
                                <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($user->papel == 'administrador') bg-red-100 text-red-800
                                    @elseif($user->papel == 'gestor') bg-blue-100 text-blue-800
                                    @elseif($user->papel == 'medico') bg-green-100 text-green-800
                                    @elseif($user->papel == 'tecnico') bg-yellow-100 text-yellow-800
                                    @else bg-purple-100 text-purple-800 @endif">
                                    {{ ucfirst($user->papel) }}
                                </span>
                            </div>
                            <div class="text-sm text-gray-500">{{ $user->email }}</div>
                            @if($user->ultimo_acesso)
                            <div class="text-xs text-gray-400">
                                Último acesso: {{ $user->ultimo_acesso->format('d/m/Y H:i') }}
                            </div>
                            @endif
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        @can('view-users')
                        <a href="{{ route('usuarios.show', $user) }}" class="text-blue-600 hover:text-blue-900" title="Ver">
                            <i class="fas fa-eye"></i>
                        </a>
                        @endcan
                        
                        @can('edit-users')
                        <a href="{{ route('usuarios.edit', $user) }}" class="text-green-600 hover:text-green-900" title="Editar">
                            <i class="fas fa-edit"></i>
                        </a>
                        @endcan

                        @can('delete-users')
                        @if($user->id !== auth()->id())
                        <form method="POST" action="{{ route('usuarios.destroy', $user) }}" class="inline" 
                              onsubmit="return confirm('Tem certeza que deseja excluir este usuário?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900" title="Excluir">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                        @endif
                        @endcan
                    </div>
                </div>
            </li>
            @empty
            <li class="px-4 py-8 text-center text-gray-500">
                Nenhum usuário encontrado.
            </li>
            @endforelse
        </ul>
    </div>

    <!-- Paginação -->
    @if($users->hasPages())
    <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
        {{ $users->links() }}
    </div>
    @endif
</div>
@endsection
