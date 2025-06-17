@extends('layouts.app')

@section('title', 'Novo Veículo - Sistema Cólera Angola')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white shadow-md rounded-lg p-6">
        <h2 class="text-2xl font-bold mb-6">Novo Veículo</h2>
        
        <form method="POST" action="{{ route('veiculos.store') }}">
            @csrf
            
            <div class="grid grid-cols-1 gap-6">
                <div>
                    <label for="placa" class="form-label">Placa do Veículo</label>
                    <input type="text" id="placa" name="placa" value="{{ old('placa') }}" 
                           class="form-input" required placeholder="Ex: LD-12-34-AB">
                    @error('placa')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="tipo" class="form-label">Tipo de Veículo</label>
                    <select id="tipo" name="tipo" class="form-input" required>
                        <option value="">Selecione o tipo</option>
                        <option value="ambulancia" {{ old('tipo') == 'ambulancia' ? 'selected' : '' }}>Ambulância</option>
                        <option value="outro" {{ old('tipo') == 'outro' ? 'selected' : '' }}>Outro</option>
                    </select>
                    @error('tipo')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="status" class="form-label">Status</label>
                    <select id="status" name="status" class="form-input" required>
                        <option value="">Selecione o status</option>
                        <option value="disponivel" {{ old('status') == 'disponivel' ? 'selected' : '' }}>Disponível</option>
                        <option value="em_atendimento" {{ old('status') == 'em_atendimento' ? 'selected' : '' }}>Em Atendimento</option>
                        <option value="manutencao" {{ old('status') == 'manutencao' ? 'selected' : '' }}>Manutenção</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="descricao" class="form-label">Descrição</label>
                    <textarea id="descricao" name="descricao" rows="3" class="form-input" 
                              placeholder="Informações adicionais sobre o veículo...">{{ old('descricao') }}</textarea>
                    @error('descricao')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex justify-end space-x-4 mt-6">
                <a href="{{ route('veiculos.index') }}" class="btn-secondary">Cancelar</a>
                <button type="submit" class="btn-primary">Salvar</button>
            </div>
        </form>
    </div>
</div>
@endsection
