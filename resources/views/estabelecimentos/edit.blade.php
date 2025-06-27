
@extends('layouts.app')

@section('title', 'Editar Estabelecimento: ' . $estabelecimento->nome)

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="bg-white shadow-md rounded-lg p-6">
        <h2 class="text-2xl font-bold mb-4">Editar Estabelecimento</h2>
        <form method="POST" action="{{ route('estabelecimentos.update', $estabelecimento) }}">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-1">Nome</label>
                <input type="text" name="nome" value="{{ old('nome', $estabelecimento->nome) }}" required maxlength="255"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('nome') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-1">Gabinete</label>
                <select name="gabinete_id" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Selecione...</option>
                    @foreach($gabinetes as $gabinete)
                        <option value="{{ $gabinete->id }}" {{ old('gabinete_id', $estabelecimento->gabinete_id) == $gabinete->id ? 'selected' : '' }}>{{ $gabinete->nome }}</option>
                    @endforeach
                </select>
                @error('gabinete_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-1">Categoria</label>
                <select name="categoria" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Selecione...</option>
                    <option value="geral" {{ old('categoria', $estabelecimento->categoria) == 'geral' ? 'selected' : '' }}>Geral</option>
                    <option value="municipal" {{ old('categoria', $estabelecimento->categoria) == 'municipal' ? 'selected' : '' }}>Municipal</option>
                    <option value="centro" {{ old('categoria', $estabelecimento->categoria) == 'centro' ? 'selected' : '' }}>Centro</option>
                    <option value="posto" {{ old('categoria', $estabelecimento->categoria) == 'posto' ? 'selected' : '' }}>Posto</option>
                    <option value="clinica" {{ old('categoria', $estabelecimento->categoria) == 'clinica' ? 'selected' : '' }}>Clínica</option>
                    <option value="outros" {{ old('categoria', $estabelecimento->categoria) == 'outros' ? 'selected' : '' }}>Outros</option>
                </select>
                @error('categoria') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-1">Endereço</label>
                <input type="text" name="endereco" value="{{ old('endereco', $estabelecimento->endereco) }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('endereco') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-1">Telefone</label>
                <input type="text" name="telefone" value="{{ old('telefone', $estabelecimento->telefone) }}" maxlength="20"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('telefone') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-1">Capacidade</label>
                <input type="number" name="capacidade" value="{{ old('capacidade', $estabelecimento->capacidade) }}" min="0"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('capacidade') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div class="flex justify-end space-x-2 mt-6">
                <a href="{{ route('estabelecimentos.index') }}" class="btn-secondary">Cancelar</a>
                <button type="submit" class="btn-primary">Salvar Alterações</button>
            </div>
        </form>
    </div>
</div>
@endsection
