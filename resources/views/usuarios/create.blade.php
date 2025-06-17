@extends('layouts.app')

@section('title', 'Novo Usuário - Sistema Cólera Angola')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white shadow-md rounded-lg p-6">
        <h2 class="text-2xl font-bold mb-6">Novo Usuário</h2>
        
        <form method="POST" action="{{ route('usuarios.store') }}">
            @csrf
            
            <div class="grid grid-cols-1 gap-6">
                <div>
                    <label for="name" class="form-label">Nome Completo</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" 
                           class="form-input" required>
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="form-label">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" 
                           class="form-input" required>
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="papel" class="form-label">Papel/Função</label>
                    <select id="papel" name="papel" class="form-input" required>
                        <option value="">Selecione o papel</option>
                        <option value="administrador" {{ old('papel') == 'administrador' ? 'selected' : '' }}>Administrador</option>
                        <option value="gestor" {{ old('papel') == 'gestor' ? 'selected' : '' }}>Gestor</option>
                        <option value="medico" {{ old('papel') == 'medico' ? 'selected' : '' }}>Médico</option>
                        <option value="tecnico" {{ old('papel') == 'tecnico' ? 'selected' : '' }}>Técnico</option>
                        <option value="enfermeiro" {{ old('papel') == 'enfermeiro' ? 'selected' : '' }}>Enfermeiro</option>
                    </select>
                    @error('papel')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="form-label">Senha</label>
                    <input type="password" id="password" name="password" class="form-input" required>
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="form-label">Confirmar Senha</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-input" required>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" id="ativo" name="ativo" value="1" 
                           {{ old('ativo', true) ? 'checked' : '' }}
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="ativo" class="ml-2 block text-sm text-gray-900">
                        Usuário ativo
                    </label>
                </div>
            </div>

            <!-- Informações sobre permissões -->
            <div class="mt-6 p-4 bg-blue-50 rounded-lg">
                <h4 class="font-medium text-blue-900 mb-2">Permissões por Papel:</h4>
                <ul class="text-sm text-blue-800 space-y-1">
                    <li><strong>Administrador:</strong> Acesso total ao sistema</li>
                    <li><strong>Gestor:</strong> Relatórios e gestão de gabinetes/estabelecimentos</li>
                    <li><strong>Médico:</strong> Fichas clínicas, pacientes e pontos de atendimento</li>
                    <li><strong>Técnico:</strong> Pacientes, veículos e pontos de atendimento</li>
                    <li><strong>Enfermeiro:</strong> Fichas clínicas e pacientes</li>
                </ul>
            </div>

            <div class="flex justify-end space-x-4 mt-6">
                <a href="{{ route('usuarios.index') }}" class="btn-secondary">Cancelar</a>
                <button type="submit" class="btn-primary">Criar Usuário</button>
            </div>
        </form>
    </div>
</div>
@endsection
