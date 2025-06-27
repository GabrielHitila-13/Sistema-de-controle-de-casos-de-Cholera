@extends('layouts.app')

@section('title', 'Usuários - Sistema Cólera Angola')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h2 class="text-3xl font-bold text-gray-900">Gestão de Usuários</h2>
        <a href="{{ route('usuarios.create') }}"
           class="inline-flex items-center gap-2 bg-blue-700 hover:bg-blue-800 text-white text-lg font-semibold px-6 py-3 rounded-xl shadow-lg transition duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-2">
            <i class="fas fa-user-plus text-xl"></i>
            Novo Usuário
        </a>
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
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nome</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Papel</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Último acesso</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($users as $user)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap flex items-center">
                        <div class="flex-shrink-0 h-10 w-10">
                            <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                <i class="fas fa-user text-blue-600"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-500">{{ $user->email }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            @if($user->papel == 'administrador') bg-red-100 text-red-800
                            @elseif($user->papel == 'gestor') bg-blue-100 text-blue-800
                            @elseif($user->papel == 'medico') bg-green-100 text-green-800
                            @elseif($user->papel == 'tecnico') bg-yellow-100 text-yellow-800
                            @else bg-purple-100 text-purple-800 @endif">
                            {{ ucfirst($user->papel) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($user->ultimo_acesso)
                            <span class="text-xs text-gray-400">{{ $user->ultimo_acesso->format('d/m/Y H:i') }}</span>
                        @else
                            <span class="text-xs text-gray-300">Nunca</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <div class="flex items-center justify-end space-x-2">
                            <a href="{{ route('usuarios.show', $user) }}" class="text-blue-600 hover:text-blue-900" title="Ver">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('usuarios.edit', $user) }}" class="text-green-600 hover:text-green-900" title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                            @if($user->id !== auth()->id())
                            <form method="POST" action="{{ route('usuarios.destroy', $user) }}" class="inline" onsubmit="return confirm('Tem certeza que deseja excluir este usuário?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900" title="Excluir">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-gray-500">Nenhum usuário encontrado.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Paginação -->
    @if($users->hasPages())
    <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
        {{ $users->links() }}
    </div>
    @endif
</div>
@endsection
