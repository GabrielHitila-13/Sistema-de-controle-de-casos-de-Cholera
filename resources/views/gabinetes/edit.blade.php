@extends('layouts.app')

@section('title', 'Editar Gabinete - Sistema Cólera Angola')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white shadow-md rounded-lg p-6">
        <h2 class="text-2xl font-bold mb-6">Editar Gabinete: {{ $gabinete->nome }}</h2>
        
        <form method="POST" action="{{ route('gabinetes.update', $gabinete) }}">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 gap-6">
                <div>
                    <label for="nome" class="form-label">Nome do Gabinete</label>
                    <input type="text" id="nome" name="nome" value="{{ old('nome', $gabinete->nome) }}" 
                           class="form-input" required>
                    @error('nome')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="tipo" class="form-label">Tipo</label>
                    <select id="tipo" name="tipo" class="form-input" required>
                        <option value="">Selecione o tipo</option>
                        <option value="provincial" {{ old('tipo', $gabinete->tipo) == 'provincial' ? 'selected' : '' }}>Provincial</option>
                        <option value="municipal" {{ old('tipo', $gabinete->tipo) == 'municipal' ? 'selected' : '' }}>Municipal</option>
                    </select>
                    @error('tipo')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="endereco" class="form-label">Endereço</label>
                    <textarea id="endereco" name="endereco" rows="3" class="form-input">{{ old('endereco', $gabinete->endereco) }}</textarea>
                    @error('endereco')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="telefone" class="form-label">Telefone</label>
                    <input type="text" id="telefone" name="telefone" value="{{ old('telefone', $gabinete->telefone) }}" 
                           class="form-input">
                    @error('telefone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="latitude" class="form-label">Latitude</label>
                        <input type="number" step="any" id="latitude" name="latitude" value="{{ old('latitude', $gabinete->latitude) }}" 
                               class="form-input">
                        @error('latitude')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="longitude" class="form-label">Longitude</label>
                        <input type="number" step="any" id="longitude" name="longitude" value="{{ old('longitude', $gabinete->longitude) }}" 
                               class="form-input">
                        @error('longitude')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="flex justify-end space-x-4 mt-6">
                <a href="{{ route('gabinetes.index') }}" class="btn-secondary">Cancelar</a>
                <button type="submit" class="btn-primary">Atualizar</button>
            </div>
        </form>
    </div>
</div>
@endsection
