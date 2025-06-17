@extends('layouts.app')

@section('title', 'Novo Ponto de Atendimento - Sistema Cólera Angola')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white shadow-md rounded-lg p-6">
        <h2 class="text-2xl font-bold mb-6">Novo Ponto de Atendimento</h2>
        
        <form method="POST" action="{{ route('pontos-atendimento.store') }}">
            @csrf
            
            <div class="grid grid-cols-1 gap-6">
                <div>
                    <label for="nome" class="form-label">Nome do Ponto</label>
                    <input type="text" id="nome" name="nome" value="{{ old('nome') }}" 
                           class="form-input" required>
                    @error('nome')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="latitude" class="form-label">Latitude</label>
                        <input type="number" step="any" id="latitude" name="latitude" value="{{ old('latitude') }}" 
                               class="form-input" required placeholder="-8.8383">
                        @error('latitude')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="longitude" class="form-label">Longitude</label>
                        <input type="number" step="any" id="longitude" name="longitude" value="{{ old('longitude') }}" 
                               class="form-input" required placeholder="13.2344">
                        @error('longitude')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="descricao" class="form-label">Descrição</label>
                    <textarea id="descricao" name="descricao" rows="3" class="form-input" 
                              placeholder="Descrição do ponto de atendimento...">{{ old('descricao') }}</textarea>
                    @error('descricao')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center">
                    <input type="checkbox" id="ativo" name="ativo" value="1" 
                           {{ old('ativo', true) ? 'checked' : '' }}
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="ativo" class="ml-2 block text-sm text-gray-900">
                        Ponto ativo
                    </label>
                </div>
            </div>

            <div class="flex justify-end space-x-4 mt-6">
                <a href="{{ route('pontos-atendimento.index') }}" class="btn-secondary">Cancelar</a>
                <button type="submit" class="btn-primary">Salvar</button>
            </div>
        </form>
    </div>
</div>
@endsection
