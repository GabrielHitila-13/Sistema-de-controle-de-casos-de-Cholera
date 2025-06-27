
@extends('layouts.app')

@section('title', 'Usuário: ' . $usuario->name)

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="bg-white shadow-md rounded-lg p-6">
        <div class="flex items-center space-x-6">
            <div class="flex-shrink-0">
                <div class="h-20 w-20 rounded-full bg-blue-100 flex items-center justify-center">
                    <i class="fas fa-user text-blue-600 text-4xl"></i>
                </div>
            </div>
            <div class="flex-1">
                <h2 class="text-3xl font-bold text-gray-900 mb-2">{{ $usuario->name }}</h2>
                <div class="text-gray-600 mb-1"><i class="fas fa-envelope mr-1"></i> {{ $usuario->email }}</div>
                <div class="mb-1">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                        @if($usuario->papel == 'administrador') bg-red-100 text-red-800
                        @elseif($usuario->papel == 'gestor') bg-blue-100 text-blue-800
                        @elseif($usuario->papel == 'medico') bg-green-100 text-green-800
                        @elseif($usuario->papel == 'tecnico') bg-yellow-100 text-yellow-800
                        @else bg-purple-100 text-purple-800 @endif">
                        {{ ucfirst($usuario->papel) }}
                    </span>
                </div>
                <div class="text-xs text-gray-400">
                    Último acesso:
                    @if($usuario->ultimo_acesso)
                        {{ $usuario->ultimo_acesso->format('d/m/Y H:i') }}
                    @else
                        Nunca
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white shadow-md rounded-lg p-6 flex space-x-4">
        <a href="{{ route('usuarios.edit', $usuario) }}" class="btn-primary">
            <i class="fas fa-edit mr-2"></i>Editar Usuário
        </a>
        <a href="{{ route('usuarios.index') }}" class="btn-secondary">
            <i class="fas fa-arrow-left mr-2"></i>Voltar à Lista
        </a>
    </div>
</div>
@endsection
