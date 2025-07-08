@extends('layouts.app')

@section('title', 'Novo Paciente - Sistema Cólera Angola')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white shadow-md rounded-lg p-6">
        <h2 class="text-2xl font-bold mb-6">Novo Paciente</h2>
        
        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-circle text-red-400"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">
                            Por favor, corrija os seguintes erros:
                        </h3>
                        <div class="mt-2 text-sm text-red-700">
                            <ul class="list-disc pl-5 space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        
        <form method="POST" action="{{ route('pacientes.store') }}" id="paciente-form">
            @csrf
            
            <!-- Dados Pessoais -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold mb-4 text-blue-600 border-b border-blue-200 pb-2">
                    <i class="fas fa-user mr-2"></i>
                    Dados Pessoais
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="nome" class="form-label">Nome Completo *</label>
                        <input type="text" id="nome" name="nome" value="{{ old('nome') }}" 
                               class="form-input @error('nome') border-red-500 @enderror" required>
                        @error('nome')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="bi" class="form-label">Bilhete de Identidade</label>
                        <input type="text" id="bi" name="bi" value="{{ old('bi') }}" 
                               class="form-input @error('bi') border-red-500 @enderror">
                        @error('bi')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="telefone" class="form-label">Telefone</label>
                        <input type="text" id="telefone" name="telefone" value="{{ old('telefone') }}" 
                               class="form-input @error('telefone') border-red-500 @enderror"
                               placeholder="+244 xxx xxx xxx">
                        @error('telefone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="data_nascimento" class="form-label">Data de Nascimento *</label>
                        <input type="date" id="data_nascimento" name="data_nascimento" value="{{ old('data_nascimento') }}" 
                               class="form-input @error('data_nascimento') border-red-500 @enderror" required>
                        @error('data_nascimento')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="sexo" class="form-label">Sexo *</label>
                        <select id="sexo" name="sexo" class="form-input @error('sexo') border-red-500 @enderror" required>
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
                        <select id="estabelecimento_id" name="estabelecimento_id" class="form-input @error('estabelecimento_id') border-red-500 @enderror">
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

                <div class="mt-4">
                    <label for="endereco" class="form-label">Endereço</label>
                    <textarea id="endereco" name="endereco" rows="2" class="form-input @error('endereco') border-red-500 @enderror" 
                              placeholder="Endereço completo do paciente...">{{ old('endereco') }}</textarea>
                    @error('endereco')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Fatores de Risco Epidemiológicos -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold mb-4 text-orange-600 border-b border-orange-200 pb-2">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Fatores de Risco Epidemiológicos
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <label class="flex items-center p-3 border rounded-lg hover:bg-gray-50">
                        <input type="checkbox" name="contato_caso_confirmado" value="1" 
                               class="mr-3 h-4 w-4 text-orange-600" {{ old('contato_caso_confirmado') ? 'checked' : '' }}>
                        <span class="text-sm">Contato com caso confirmado</span>
                    </label>
                    
                    <label class="flex items-center p-3 border rounded-lg hover:bg-gray-50">
                        <input type="checkbox" name="area_surto" value="1" 
                               class="mr-3 h-4 w-4 text-orange-600" {{ old('area_surto') ? 'checked' : '' }}>
                        <span class="text-sm">Reside em área de surto</span>
                    </label>
                    
                    <label class="flex items-center p-3 border rounded-lg hover:bg-gray-50">
                        <input type="checkbox" name="agua_contaminada" value="1" 
                               class="mr-3 h-4 w-4 text-orange-600" {{ old('agua_contaminada') ? 'checked' : '' }}>
                        <span class="text-sm">Consumo de água contaminada</span>
                    </label>
                </div>
            </div>

            <!-- Seção de Triagem -->
            <div class="mb-8 border-t pt-6">
                <h3 class="text-lg font-semibold mb-4 text-red-600 border-b border-red-200 pb-2">
                    <i class="fas fa-stethoscope mr-2"></i>
                    Triagem de Sintomas - Cólera
                </h3>
                
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                    <p class="text-sm text-yellow-800">
                        <i class="fas fa-info-circle mr-2"></i>
                        Marque todos os sintomas apresentados pelo paciente. O sistema calculará automaticamente o nível de risco e a probabilidade de cólera.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-3">
                        <h4 class="font-medium text-gray-900">Sintomas Principais:</h4>
                        <label class="flex items-center p-2 border rounded hover:bg-red-50">
                            <input type="checkbox" name="sintomas[]" value="diarreia aquosa" class="mr-3 symptom-checkbox" data-score="4">
                            <span>Diarreia aquosa abundante</span>
                            <span class="ml-auto text-red-600 font-bold text-xs">ALTO RISCO</span>
                        </label>
                        <label class="flex items-center p-2 border rounded hover:bg-yellow-50">
                            <input type="checkbox" name="sintomas[]" value="vomito" class="mr-3 symptom-checkbox" data-score="2">
                            <span>Vômitos</span>
                            <span class="ml-auto text-yellow-600 font-bold text-xs">MÉDIO RISCO</span>
                        </label>
                        <label class="flex items-center p-2 border rounded hover:bg-red-50">
                            <input type="checkbox" name="sintomas[]" value="desidratacao" class="mr-3 symptom-checkbox" data-score="3">
                            <span>Sinais de desidratação</span>
                            <span class="ml-auto text-red-600 font-bold text-xs">ALTO RISCO</span>
                        </label>
                    </div>

                    <div class="space-y-3">
                        <h4 class="font-medium text-gray-900">Sintomas Secundários:</h4>
                        <label class="flex items-center p-2 border rounded hover:bg-green-50">
                            <input type="checkbox" name="sintomas[]" value="febre" class="mr-3 symptom-checkbox" data-score="1">
                            <span>Febre</span>
                            <span class="ml-auto text-green-600 font-bold text-xs">BAIXO RISCO</span>
                        </label>
                        <label class="flex items-center p-2 border rounded hover:bg-green-50">
                            <input type="checkbox" name="sintomas[]" value="dor abdominal" class="mr-3 symptom-checkbox" data-score="1">
                            <span>Dor abdominal</span>
                            <span class="ml-auto text-green-600 font-bold text-xs">BAIXO RISCO</span>
                        </label>
                        <label class="flex items-center p-2 border rounded hover:bg-green-50">
                            <input type="checkbox" name="sintomas[]" value="fraqueza" class="mr-3 symptom-checkbox" data-score="1">
                            <span>Fraqueza/Prostração</span>
                            <span class="ml-auto text-green-600 font-bold text-xs">BAIXO RISCO</span>
                        </label>
                    </div>
                </div>

                <div class="mt-6">
                    <label for="sintomas_outros" class="form-label">Outros sintomas (descreva):</label>
                    <textarea id="sintomas_outros" name="sintomas_outros" rows="3" 
                              class="form-input @error('sintomas_outros') border-red-500 @enderror" 
                              placeholder="Descreva outros sintomas observados...">{{ old('sintomas_outros') }}</textarea>
                    @error('sintomas_outros')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mt-6">
                    <label for="observacoes" class="form-label">Observações Gerais:</label>
                    <textarea id="observacoes" name="observacoes" rows="3" 
                              class="form-input @error('observacoes') border-red-500 @enderror" 
                              placeholder="Observações adicionais sobre o paciente...">{{ old('observacoes') }}</textarea>
                    @error('observacoes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Resultado da Triagem -->
                <div class="mt-6 p-4 border rounded-lg bg-gray-50" id="triagem-resultado">
                    <h4 class="font-semibold mb-3">Resultado da Triagem:</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600 mb-1">Nível de Risco:</p>
                            <div id="risco-display" class="text-lg font-bold">
                                <span class="badge-baixo">Baixo Risco</span>
                            </div>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 mb-1">Probabilidade de Cólera:</p>
                            <div id="colera-display" class="text-lg font-bold">
                                <span class="text-gray-500">0%</span>
                            </div>
                        </div>
                    </div>
                    <p id="risco-descricao" class="text-sm text-gray-600 mt-3">
                        Paciente apresenta sintomas leves. Monitoramento recomendado.
                    </p>
                </div>
            </div>

            <div class="flex justify-end space-x-4 mt-8 pt-6 border-t">
                <a href="{{ route('pacientes.index') }}" class="btn-secondary">
                    <i class="fas fa-times mr-2"></i>Cancelar
                </a>
                <button type="submit" class="btn-primary" id="submit-btn">
                    <i class="fas fa-save mr-2"></i>Salvar Paciente
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('.symptom-checkbox');
    const riscoDisplay = document.getElementById('risco-display');
    const coleraDisplay = document.getElementById('colera-display');
    const riscoDescricao = document.getElementById('risco-descricao');
    const submitBtn = document.getElementById('submit-btn');
    const form = document.getElementById('paciente-form');

    // Prevent double submission
    form.addEventListener('submit', function() {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Salvando...';
    });

    function calcularRisco() {
        let pontuacao = 0;
        let pontuacaoColera = 0;
        
        // Calculate symptom scores
        checkboxes.forEach(checkbox => {
            if (checkbox.checked) {
                const score = parseInt(checkbox.dataset.score);
                pontuacao += score;
                pontuacaoColera += score;
            }
        });

        // Add epidemiological risk factors
        const contatoCaso = document.querySelector('input[name="contato_caso_confirmado"]').checked;
        const areaSurto = document.querySelector('input[name="area_surto"]').checked;
        const aguaContaminada = document.querySelector('input[name="agua_contaminada"]').checked;

        if (contatoCaso) {
            pontuacao += 2;
            pontuacaoColera += 3;
        }
        if (areaSurto) {
            pontuacao += 2;
            pontuacaoColera += 2;
        }
        if (aguaContaminada) {
            pontuacao += 1;
            pontuacaoColera += 2;
        }

        // Calculate risk level
        let risco, descricao, classe;
        if (pontuacao >= 6) {
            risco = 'Alto Risco';
            classe = 'badge-alto';
            descricao = 'URGENTE: Paciente apresenta sintomas graves. Requer atenção médica imediata e isolamento.';
        } else if (pontuacao >= 3) {
            risco = 'Médio Risco';
            classe = 'badge-medio';
            descricao = 'ATENÇÃO: Paciente apresenta sintomas moderados. Monitoramento próximo e tratamento recomendados.';
        } else {
            risco = 'Baixo Risco';
            classe = 'badge-baixo';
            descricao = 'Paciente apresenta sintomas leves. Monitoramento recomendado e orientações preventivas.';
        }

        // Calculate cholera probability
        let probabilidadeColera = 0;
        if (pontuacaoColera >= 8) {
            probabilidadeColera = Math.min(95, 60 + (pontuacaoColera - 8) * 5);
        } else if (pontuacaoColera >= 5) {
            probabilidadeColera = Math.min(60, 30 + (pontuacaoColera - 5) * 10);
        } else if (pontuacaoColera >= 2) {
            probabilidadeColera = Math.min(30, pontuacaoColera * 10);
        }

        // Update displays
        riscoDisplay.innerHTML = `<span class="${classe}">${risco}</span>`;
        
        let coleraClasse = 'text-gray-500';
        if (probabilidadeColera >= 60) coleraClasse = 'text-red-600';
        else if (probabilidadeColera >= 30) coleraClasse = 'text-yellow-600';
        else if (probabilidadeColera > 0) coleraClasse = 'text-blue-600';
        
        coleraDisplay.innerHTML = `<span class="${coleraClasse}">${probabilidadeColera}%</span>`;
        riscoDescricao.textContent = descricao;
    }

    // Add event listeners
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', calcularRisco);
    });

    document.querySelector('input[name="contato_caso_confirmado"]').addEventListener('change', calcularRisco);
    document.querySelector('input[name="area_surto"]').addEventListener('change', calcularRisco);
    document.querySelector('input[name="agua_contaminada"]').addEventListener('change', calcularRisco);

    // Initial calculation
    calcularRisco();
});
</script>

<style>
.badge-alto {
    @apply inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800;
}

.badge-medio {
    @apply inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800;
}

.badge-baixo {
    @apply inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800;
}

.form-label {
    @apply block text-sm font-medium text-gray-700 mb-1;
}

.form-input {
    @apply mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500;
}

.btn-primary {
    @apply inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed;
}

.btn-secondary {
    @apply inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500;
}
</style>
@endsection
