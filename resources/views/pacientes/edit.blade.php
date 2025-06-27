
@extends('layouts.app')

@section('title', 'Editar Paciente: ' . $paciente->nome)

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="bg-white shadow-md rounded-lg p-6">
        <h2 class="text-2xl font-bold mb-4">Editar Paciente</h2>
        <form method="POST" action="{{ route('pacientes.update', $paciente) }}">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-1">Nome</label>
                <input type="text" name="nome" value="{{ old('nome', $paciente->nome) }}" required maxlength="255"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('nome') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-1">BI</label>
                <input type="text" name="bi" value="{{ old('bi', $paciente->bi) }}" required maxlength="255"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('bi') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-1">Telefone</label>
                <input type="text" name="telefone" value="{{ old('telefone', $paciente->telefone) }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('telefone') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-1">Data de Nascimento</label>
                <input type="date" name="data_nascimento" value="{{ old('data_nascimento', $paciente->data_nascimento ? $paciente->data_nascimento->format('Y-m-d') : '') }}" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('data_nascimento') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-1">Sexo</label>
                <select name="sexo" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="masculino" {{ old('sexo', $paciente->sexo) == 'masculino' ? 'selected' : '' }}>Masculino</option>
                    <option value="feminino" {{ old('sexo', $paciente->sexo) == 'feminino' ? 'selected' : '' }}>Feminino</option>
                </select>
                @error('sexo') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-1">Estabelecimento</label>
                <select name="estabelecimento_id" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Selecione...</option>
                    @foreach($estabelecimentos as $estabelecimento)
                        <option value="{{ $estabelecimento->id }}" {{ old('estabelecimento_id', $paciente->estabelecimento_id) == $estabelecimento->id ? 'selected' : '' }}>
                            {{ $estabelecimento->nome }}
                            @if($estabelecimento->gabinete)
                                ({{ $estabelecimento->gabinete->nome }})
                            @endif
                        </option>
                    @endforeach
                </select>
                @error('estabelecimento_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-1">Sintomas</label>
                <div class="flex flex-wrap gap-2">
                    @php
                        $sintomasPadrao = ['Diarreia aquosa', 'Vómito', 'Desidratação', 'Febre', 'Dor abdominal', 'Fraqueza'];
                        $sintomasPaciente = collect(explode(',', $paciente->sintomas))->map(fn($s) => trim(strtolower($s)))->toArray();
                    @endphp
                    @foreach($sintomasPadrao as $sintoma)
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="sintomas[]" value="{{ $sintoma }}"
                                {{ in_array(strtolower($sintoma), $sintomasPaciente) ? 'checked' : '' }}
                                class="form-checkbox text-blue-600">
                            <span class="ml-2">{{ $sintoma }}</span>
                        </label>
                    @endforeach
                </div>
                <input type="text" name="sintomas_outros" placeholder="Outros sintomas..." value="{{ old('sintomas_outros') }}"
                    class="mt-2 w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('sintomas') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                @error('sintomas_outros') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div class="flex justify-end space-x-2 mt-6">
                <a href="{{ route('pacientes.index') }}" class="btn-secondary">Cancelar</a>
                <button type="submit" class="btn-primary">Salvar Alterações</button>
            </div>
        </form>
    </div>
</div>
@endsection
