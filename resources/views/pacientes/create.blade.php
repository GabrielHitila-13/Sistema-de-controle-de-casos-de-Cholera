@extends('layouts.app')

@section('title', 'Novo Paciente - Sistema Cólera Angola')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white shadow-md rounded-lg p-6">
        <h2 class="text-2xl font-bold mb-6">Novo Paciente</h2>
        
        <form method="POST" action="{{ route('pacientes.store') }}">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="nome" class="form-label">Nome Completo</label>
                    <input type="text" id="nome" name="nome" value="{{ old('nome') }}" 
                           class="form-input" required>
                    @error('nome')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="bi" class="form-label">Bilhete de Identidade</label>
                    <input type="text" id="bi" name="bi" value="{{ old('bi') }}" 
                           class="form-input" required>
                    @error('bi')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="telefone" class="form-label">Telefone</label>
                    <input type="text" id="telefone" name="telefone" value="{{ old('telefone') }}" 
                           class="form-input">
                    @error('telefone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="data_nascimento" class="form-label">Data de Nascimento</label>
                    <input type="date" id="data_nascimento" name="data_nascimento" value="{{ old('data_nascimento') }}" 
                           class="form-input" required>
                    @error('data_nascimento')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="sexo" class="form-label">Sexo</label>
                    <select id="sexo" name="sexo" class="form-input" required>
                        <option value="">Selecione o sexo</option>
                        <option value="masculino" {{ old('sexo') == 'masculino' ? 'selected' : '' }}>Masculino</option>
                        <option value="feminino" {{ old('sexo') == 'feminino' ? 'selected' : '' }}>Feminino</option>
                    </select>
                    @error('sexo')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="estabelecimento_id" class="form-label">Estabelecimento</label>
                    <select id="estabelecimento_id" name="estabelecimento_id" class="form-input" required>
                        <option value="">Selecione o estabelecimento</option>
                        @foreach($estabelecimentos ?? [] as $estabelecimento)
                            <option value="{{ $estabelecimento->id }}" {{ old('estabelecimento_id') == $estabelecimento->id ? 'selected' : '' }}>
                                {{ $estabelecimento->nome }}
                            </option>
                        @endforeach
                    </select>
                    @error('estabelecimento_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Seção de Triagem -->
            <div class="mt-8 border-t pt-6">
                <h3 class="text-lg font-semibold mb-4 text-red-600">
                    <i class="fas fa-stethoscope mr-2"></i>
                    Triagem de Sintomas - Cólera
                </h3>
                
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                    <p class="text-sm text-yellow-800">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        Marque todos os sintomas apresentados pelo paciente. O sistema calculará automaticamente o nível de risco.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-3">
                        <h4 class="font-medium text-gray-900">Sintomas Principais:</h4>
                        <label class="flex items-center">
                            <input type="checkbox" name="sintomas[]" value="diarreia aquosa" class="mr-2 symptom-checkbox" data-score="3">
                            <span>Diarreia aquosa abundante</span>
                            <span class="ml-2 text-red-600 font-bold">(Alto risco)</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="sintomas[]" value="vomito" class="mr-2 symptom-checkbox" data-score="2">
                            <span>Vômitos</span>
                            <span class="ml-2 text-yellow-600 font-bold">(Médio risco)</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="sintomas[]" value="desidratacao" class="mr-2 symptom-checkbox" data-score="3">
                            <span>Sinais de desidratação</span>
                            <span class="ml-2 text-red-600 font-bold">(Alto risco)</span>
                        </label>
                    </div>

                    <div class="space-y-3">
                        <h4 class="font-medium text-gray-900">Sintomas Secundários:</h4>
                        <label class="flex items-center">
                            <input type="checkbox" name="sintomas[]" value="febre" class="mr-2 symptom-checkbox" data-score="1">
                            <span>Febre</span>
                            <span class="ml-2 text-green-600 font-bold">(Baixo risco)</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="sintomas[]" value="dor abdominal" class="mr-2 symptom-checkbox" data-score="1">
                            <span>Dor abdominal</span>
                            <span class="ml-2 text-green-600 font-bold">(Baixo risco)</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="sintomas[]" value="fraqueza" class="mr-2 symptom-checkbox" data-score="1">
                            <span>Fraqueza/Prostração</span>
                            <span class="ml-2 text-green-600 font-bold">(Baixo risco)</span>
                        </label>
                    </div>
                </div>

                <div class="mt-4">
                    <label for="sintomas_outros" class="form-label">Outros sintomas (descreva):</label>
                    <textarea id="sintomas_outros" name="sintomas_outros" rows="3" class="form-input" 
                              placeholder="Descreva outros sintomas observados...">{{ old('sintomas_outros') }}</textarea>
                </div>

                <!-- Resultado da Triagem -->
                <div class="mt-6 p-4 border rounded-lg" id="triagem-resultado">
                    <h4 class="font-semibold mb-2">Resultado da Triagem:</h4>
                    <div id="risco-display" class="text-lg font-bold">
                        <span class="badge-baixo">Baixo Risco</span>
                    </div>
                    <p id="risco-descricao" class="text-sm text-gray-600 mt-2">
                        Paciente apresenta sintomas leves. Monitoramento recomendado.
                    </p>
                </div>
            </div>

            <div class="flex justify-end space-x-4 mt-6">
                <a href="{{ route('pacientes.index') }}" class="btn-secondary">Cancelar</a>
                <button type="submit" class="btn-primary">Salvar Paciente</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('.symptom-checkbox');
    const riscoDisplay = document.getElementById('risco-display');
    const riscoDescricao = document.getElementById('risco-descricao');

    function calcularRisco() {
        let pontuacao = 0;
        checkboxes.forEach(checkbox => {
            if (checkbox.checked) {
                pontuacao += parseInt(checkbox.dataset.score);
            }
        });

        let risco, descricao, classe;
        if (pontuacao >= 5) {
            risco = 'Alto Risco';
            classe = 'badge-alto';
            descricao = 'URGENTE: Paciente apresenta sintomas graves de cólera. Requer atenção médica imediata e isolamento.';
        } else if (pontuacao >= 3) {
            risco = 'Médio Risco';
            classe = 'badge-medio';
            descricao = 'ATENÇÃO: Paciente apresenta sintomas moderados. Monitoramento próximo e tratamento recomendados.';
        } else {
            risco = 'Baixo Risco';
            classe = 'badge-baixo';
            descricao = 'Paciente apresenta sintomas leves. Monitoramento recomendado e orientações preventivas.';
        }

        riscoDisplay.innerHTML = `<span class="${classe}">${risco}</span>`;
        riscoDescricao.textContent = descricao;
    }

    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', calcularRisco);
    });
});
</script>
@endsection
