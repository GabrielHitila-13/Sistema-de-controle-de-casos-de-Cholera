@extends('layouts.app')

@section('title', 'Editar Usuário - Sistema Cólera Angola')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white shadow-md rounded-lg p-6">
        <h2 class="text-2xl font-bold mb-6">Editar Usuário: {{ $usuario->name }}</h2>
        
        <form method="POST" action="{{ route('usuarios.update', $usuario) }}">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 gap-6">
                <div>
                    <label for="name" class="form-label">Nome Completo</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $usuario->name) }}" 
                           class="form-input" required>
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="form-label">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $usuario->email) }}" 
                           class="form-input" required>
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="papel" class="form-label">Papel/Função</label>
                    <select id="papel" name="papel" class="form-input" required>
                        <option value="">Selecione o papel</option>
                        <option value="administrador" {{ old('papel', $usuario->papel) == 'administrador' ? 'selected' : '' }}>Administrador</option>
                        <option value="gestor" {{ old('papel', $usuario->papel) == 'gestor' ? 'selected' : '' }}>Gestor</option>
                        <option value="medico" {{ old('papel', $usuario->papel) == 'medico' ? 'selected' : '' }}>Médico</option>
                        <option value="tecnico" {{ old('papel', $usuario->papel) == 'tecnico' ? 'selected' : '' }}>Técnico</option>
                        <option value="enfermeiro" {{ old('papel', $usuario->papel) == 'enfermeiro' ? 'selected' : '' }}>Enfermeiro</option>
                    </select>
                    @error('papel')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="form-label">Nova Senha (deixe em branco para manter a atual)</label>
                    <input type="password" id="password" name="password" class="form-input">
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="form-label">Confirmar Nova Senha</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-input">
                </div>

                <div class="flex items-center">
                    <input type="checkbox" id="ativo" name="ativo" value="1" 
                           {{ old('ativo', $usuario->ativo) ? 'checked' : '' }}
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="ativo" class="ml-2 block text-sm text-gray-900">
                        Usuário ativo
                    </label>
                </div>
            </div>

            <div class="flex justify-end space-x-4 mt-6">
                <a href="{{ route('usuarios.index') }}" class="btn-secondary">Cancelar</a>
                <button type="submit" class="btn-primary">Atualizar</button>
            </div>
        </form>
    </div>
</div>
@endsection
