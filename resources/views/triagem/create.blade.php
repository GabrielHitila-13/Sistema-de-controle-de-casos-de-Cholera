@extends('layouts.app')

@section('title', 'Triagem Inteligente - Sistema Cólera Angola')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white shadow-lg rounded-lg overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
            <h2 class="text-2xl font-bold text-white flex items-center">
                <i class="fas fa-stethoscope mr-3"></i>
                Triagem Inteligente para Cólera
            </h2>
            <p class="text-blue-100 mt-1">Sistema de avaliação automática de risco e encaminhamento</p>
        </div>

        <form id="triagemForm" method="POST" action="{{ route('triagem.store') }}" class="p-6">
            @csrf
            
            <!-- Dados do Paciente -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-user mr-2 text-blue-600"></i>
                    Dados do Paciente
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="nome" class="form-label">Nome Completo *</label>
                        <input type="text" id="nome" name="nome" value="{{ old('nome') }}" 
                               class="form-input" required>
                        @error('nome')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="bi" class="form-label">Bilhete de Identidade *</label>
                        <input type="text" id="bi" name="bi" value="{{ old('bi') }}" 
                               class="form-input" required>
                        @error('bi')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="telefone" class="form-label">Telefone</label>
                        <input type="text" id="telefone" name="telefone" value="{{ old('telefone') }}" 
                               class="form-input" placeholder="+244 9XX XXX XXX">
                        @error('telefone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="data_nascimento" class="form-label">Data de Nascimento *</label>
                        <input type="date" id="data_nascimento" name="data_nascimento" value="{{ old('data_nascimento') }}" 
                               class="form-input" required>
                        @error('data_nascimento')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="sexo" class="form-label">Sexo *</label>
                        <select id="sexo" name="sexo" class="form-input" required>
                            <option value="">Selecione o sexo</option>
                            <option value="masculino" {{ old('sexo') == 'masculino' ? 'selected' : '' }}>Masculino</option>
                            <option value="feminino" {{ old('sexo') == 'feminino' ? 'selected' : '' }}>Feminino</option>
                        </select>
                        @error('sexo')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Localização -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-map-marker-alt mr-2 text-blue-600"></i>
                    Localização (para encaminhamento automático)
                </h3>
                
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                    <div class="flex items-center">
                        <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                        <p class="text-blue-800 text-sm">
                            Permita o acesso à localização para encontrarmos o hospital mais próximo automaticamente.
                        </p>
                    </div>
                    <button type="button" id="obterLocalizacao" class="mt-3 btn-primary">
                        <i class="fas fa-crosshairs mr-2"></i>
                        Obter Minha Localização
                    </button>
                </div>

                <div id="localizacaoInfo" class="hidden bg-green-50 border border-green-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-green-600 mr-2"></i>
                        <p class="text-green-800 text-sm">Localização obtida com sucesso!</p>
                    </div>
                    <div id="coordenadas" class="text-xs text-green-600 mt-1"></div>
                </div>

                <input type="hidden" id="latitude_paciente" name="latitude_paciente">
                <input type="hidden" id="longitude_paciente" name="longitude_paciente">
            </div>

            <!-- Avaliação de Sintomas -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-heartbeat mr-2 text-red-600"></i>
                    Avaliação de Sintomas
                </h3>
                
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-triangle text-yellow-600 mr-2"></i>
                        <p class="text-yellow-800 text-sm">
                            Marque todos os sintomas apresentados pelo paciente. O sistema calculará automaticamente o nível de risco.
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Sintomas de Alto Risco -->
                    <div class="space-y-4">
                        <h4 class="font-medium text-red-700 flex items-center">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            Sintomas de Alto Risco
                        </h4>
                        
                        <div class="space-y-3">
                            <label class="flex items-center p-3 border border-red-200 rounded-lg hover:bg-red-50 cursor-pointer">
                                <input type="checkbox" name="sintomas[]" value="diarreia aquosa abundante" class="mr-3 symptom-checkbox" data-score="4">
                                <div class="flex-1">
                                    <span class="font-medium">Diarreia aquosa abundante</span>
                                    <p class="text-sm text-gray-600">Mais de 3 episódios por hora</p>
                                </div>
                                <span class="text-red-600 font-bold text-sm">Alto</span>
                            </label>

                            <label class="flex items-center p-3 border border-red-200 rounded-lg hover:bg-red-50 cursor-pointer">
                                <input type="checkbox" name="sintomas[]" value="desidratacao severa" class="mr-3 symptom-checkbox" data-score="5">
                                <div class="flex-1">
                                    <span class="font-medium">Desidratação severa</span>
                                    <p class="text-sm text-gray-600">Pele ressecada, olhos fundos</p>
                                </div>
                                <span class="text-red-600 font-bold text-sm">Alto</span>
                            </label>

                            <label class="flex items-center p-3 border border-red-200 rounded-lg hover:bg-red-50 cursor-pointer">
                                <input type="checkbox" name="sintomas[]" value="vomito intenso" class="mr-3 symptom-checkbox" data-score="3">
                                <div class="flex-1">
                                    <span class="font-medium">Vômitos intensos</span>
                                    <p class="text-sm text-gray-600">Não consegue reter líquidos</p>
                                </div>
                                <span class="text-red-600 font-bold text-sm">Alto</span>
                            </label>

                            <label class="flex items-center p-3 border border-red-200 rounded-lg hover:bg-red-50 cursor-pointer">
                                <input type="checkbox" name="sintomas[]" value="prostração" class="mr-3 symptom-checkbox" data-score="3">
                                <div class="flex-1">
                                    <span class="font-medium">Prostração</span>
                                    <p class="text-sm text-gray-600">Extrema fraqueza, letargia</p>
                                </div>
                                <span class="text-red-600 font-bold text-sm">Alto</span>
                            </label>
                        </div>
                    </div>

                    <!-- Sintomas de Médio Risco -->
                    <div class="space-y-4">
                        <h4 class="font-medium text-yellow-700 flex items-center">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            Sintomas de Médio Risco
                        </h4>
                        
                        <div class="space-y-3">
                            <label class="flex items-center p-3 border border-yellow-200 rounded-lg hover:bg-yellow-50 cursor-pointer">
                                <input type="checkbox" name="sintomas[]" value="diarreia" class="mr-3 symptom-checkbox" data-score="2">
                                <div class="flex-1">
                                    <span class="font-medium">Diarreia</span>
                                    <p class="text-sm text-gray-600">Fezes líquidas frequentes</p>
                                </div>
                                <span class="text-yellow-600 font-bold text-sm">Médio</span>
                            </label>

                            <label class="flex items-center p-3 border border-yellow-200 rounded-lg hover:bg-yellow-50 cursor-pointer">
                                <input type="checkbox" name="sintomas[]" value="vomito" class="mr-3 symptom-checkbox" data-score="2">
                                <div class="flex-1">
                                    <span class="font-medium">Vômitos</span>
                                    <p class="text-sm text-gray-600">Episódios de vômito</p>
                                </div>
                                <span class="text-yellow-600 font-bold text-sm">Médio</span>
                            </label>

                            <label class="flex items-center p-3 border border-yellow-200 rounded-lg hover:bg-yellow-50 cursor-pointer">
                                <input type="checkbox" name="sintomas[]" value="desidratacao moderada" class="mr-3 symptom-checkbox" data-score="3">
                                <div class="flex-1">
                                    <span class="font-medium">Desidratação moderada</span>
                                    <p class="text-sm text-gray-600">Sede, boca seca</p>
                                </div>
                                <span class="text-yellow-600 font-bold text-sm">Médio</span>
                            </label>

                            <label class="flex items-center p-3 border border-yellow-200 rounded-lg hover:bg-yellow-50 cursor-pointer">
                                <input type="checkbox" name="sintomas[]" value="dor abdominal intensa" class="mr-3 symptom-checkbox" data-score="2">
                                <div class="flex-1">
                                    <span class="font-medium">Dor abdominal intensa</span>
                                    <p class="text-sm text-gray-600">Cólicas fortes</p>
                                </div>
                                <span class="text-yellow-600 font-bold text-sm">Médio</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Sintomas de Baixo Risco -->
                <div class="mt-6">
                    <h4 class="font-medium text-green-700 flex items-center mb-4">
                        <i class="fas fa-info-circle mr-2"></i>
                        Sintomas de Baixo Risco
                    </h4>
                    
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        <label class="flex items-center p-2 border border-green-200 rounded-lg hover:bg-green-50 cursor-pointer">
                            <input type="checkbox" name="sintomas[]" value="febre baixa" class="mr-2 symptom-checkbox" data-score="1">
                            <span class="text-sm">Febre baixa</span>
                        </label>

                        <label class="flex items-center p-2 border border-green-200 rounded-lg hover:bg-green-50 cursor-pointer">
                            <input type="checkbox" name="sintomas[]" value="dor abdominal leve" class="mr-2 symptom-checkbox" data-score="1">
                            <span class="text-sm">Dor abdominal leve</span>
                        </label>

                        <label class="flex items-center p-2 border border-green-200 rounded-lg hover:bg-green-50 cursor-pointer">
                            <input type="checkbox" name="sintomas[]" value="fraqueza" class="mr-2 symptom-checkbox" data-score="1">
                            <span class="text-sm">Fraqueza</span>
                        </label>

                        <label class="flex items-center p-2 border border-green-200 rounded-lg hover:bg-green-50 cursor-pointer">
                            <input type="checkbox" name="sintomas[]" value="mal-estar" class="mr-2 symptom-checkbox" data-score="1">
                            <span class="text-sm">Mal-estar</span>
                        </label>

                        <label class="flex items-center p-2 border border-green-200 rounded-lg hover:bg-green-50 cursor-pointer">
                            <input type="checkbox" name="sintomas[]" value="nauseas" class="mr-2 symptom-checkbox" data-score="1">
                            <span class="text-sm">Náuseas</span>
                        </label>

                        <label class="flex items-center p-2 border border-green-200 rounded-lg hover:bg-green-50 cursor-pointer">
                            <input type="checkbox" name="sintomas[]" value="dor de cabeça" class="mr-2 symptom-checkbox" data-score="1">
                            <span class="text-sm">Dor de cabeça</span>
                        </label>
                    </div>
                </div>

                <!-- Outros Sintomas -->
                <div class="mt-6">
                    <label for="sintomas_outros" class="form-label">Outros sintomas observados</label>
                    <textarea id="sintomas_outros" name="sintomas_outros" rows="3" class="form-input" 
                              placeholder="Descreva outros sintomas não listados acima...">{{ old('sintomas_outros') }}</textarea>
                </div>
            </div>

            <!-- Resultado da Triagem em Tempo Real -->
            <div class="mb-8">
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-6" id="resultadoTriagem">
                    <h4 class="font-semibold mb-4 flex items-center">
                        <i class="fas fa-calculator mr-2 text-blue-600"></i>
                        Resultado da Triagem
                    </h4>
                    <div id="riscoDisplay" class="text-lg font-bold mb-2">
                        <span class="badge-baixo">Baixo Risco</span>
                        <span class="ml-2 text-gray-600">Pontuação: <span id="pontuacao">0</span></span>
                    </div>
                    <div id="recomendacoes" class="text-sm text-gray-600">
                        <p>Selecione os sintomas para ver as recomendações.</p>
                    </div>
                </div>
            </div>

            <!-- Hospital Sugerido -->
            <div class="mb-8" id="hospitalSugerido" style="display: none;">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-hospital mr-2 text-blue-600"></i>
                    Hospital Recomendado
                </h3>
                <div id="hospitalInfo" class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <!-- Informações do hospital serão inseridas aqui via JavaScript -->
                </div>
                <input type="hidden" id="estabelecimento_sugerido_id" name="estabelecimento_sugerido_id">
            </div>

            <!-- Botões de Ação -->
            <div class="flex justify-end space-x-4">
                <a href="{{ route('dashboard') }}" class="btn-secondary">
                    <i class="fas fa-times mr-2"></i>
                    Cancelar
                </a>
                <button type="submit" class="btn-primary" id="submitBtn">
                    <i class="fas fa-save mr-2"></i>
                    Finalizar Triagem
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('.symptom-checkbox');
    const sintomasOutros = document.getElementById('sintomas_outros');
    const riscoDisplay = document.getElementById('riscoDisplay');
    const pontuacaoSpan = document.getElementById('pontuacao');
    const recomendacoesDiv = document.getElementById('recomendacoes');
    const obterLocalizacaoBtn = document.getElementById('obterLocalizacao');
    const localizacaoInfo = document.getElementById('localizacaoInfo');
    const coordenadasDiv = document.getElementById('coordenadas');
    const hospitalSugerido = document.getElementById('hospitalSugerido');
    const hospitalInfo = document.getElementById('hospitalInfo');

    // Avaliação em tempo real
    function avaliarRisco() {
        const sintomas = Array.from(checkboxes)
            .filter(cb => cb.checked)
            .map(cb => cb.value);
        
        const sintomasOutrosTexto = sintomasOutros.value;

        fetch('{{ route("triagem.avaliar-sintomas") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                sintomas: sintomas,
                sintomas_outros: sintomasOutrosTexto
            })
        })
        .then(response => response.json())
        .then(data => {
            atualizarResultado(data);
        })
        .catch(error => {
            console.error('Erro:', error);
        });
    }

    function atualizarResultado(resultado) {
        pontuacaoSpan.textContent = resultado.pontuacao;
        
        const badgeClass = `badge-${resultado.nivel}`;
        const nivelTexto = resultado.nivel.charAt(0).toUpperCase() + resultado.nivel.slice(1);
        
        riscoDisplay.innerHTML = `
            <span class="${badgeClass}">${nivelTexto} Risco</span>
            <span class="ml-2 text-gray-600">Pontuação: <span id="pontuacao">${resultado.pontuacao}</span></span>
        `;

        if (resultado.recomendacoes && resultado.recomendacoes.length > 0) {
            recomendacoesDiv.innerHTML = `
                <div class="space-y-2">
                    ${resultado.recomendacoes.map(rec => `
                        <div class="flex items-start">
                            <i class="${resultado.icone} mr-2 mt-1 text-${resultado.cor}-600"></i>
                            <span>${rec}</span>
                        </div>
                    `).join('')}
                </div>
            `;
        } else {
            recomendacoesDiv.innerHTML = '<p>Selecione os sintomas para ver as recomendações.</p>';
        }
    }

    // Event listeners para sintomas
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', avaliarRisco);
    });

    sintomasOutros.addEventListener('input', debounce(avaliarRisco, 500));

    // Geolocalização
    obterLocalizacaoBtn.addEventListener('click', function() {
        if (navigator.geolocation) {
            obterLocalizacaoBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Obtendo localização...';
            obterLocalizacaoBtn.disabled = true;

            navigator.geolocation.getCurrentPosition(
                function(position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    
                    document.getElementById('latitude_paciente').value = lat;
                    document.getElementById('longitude_paciente').value = lng;
                    
                    coordenadasDiv.textContent = `Lat: ${lat.toFixed(6)}, Lng: ${lng.toFixed(6)}`;
                    localizacaoInfo.style.display = 'block';
                    
                    obterLocalizacaoBtn.innerHTML = '<i class="fas fa-check mr-2"></i>Localização Obtida';
                    
                    // Buscar hospital mais próximo
                    buscarHospitalProximo(lat, lng);
                },
                function(error) {
                    alert('Erro ao obter localização: ' + error.message);
                    obterLocalizacaoBtn.innerHTML = '<i class="fas fa-crosshairs mr-2"></i>Tentar Novamente';
                    obterLocalizacaoBtn.disabled = false;
                }
            );
        } else {
            alert('Geolocalização não é suportada neste navegador.');
        }
    });

    function buscarHospitalProximo(lat, lng) {
        fetch('{{ route("triagem.hospital-proximo") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                latitude: lat,
                longitude: lng
            })
        })
        .then(response => response.json())
        .then(hospitais => {
            if (hospitais.length > 0) {
                const hospital = hospitais[0];
                document.getElementById('estabelecimento_sugerido_id').value = hospital.id;
                
                hospitalInfo.innerHTML = `
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <h4 class="font-semibold text-blue-900">${hospital.nome}</h4>
                            <p class="text-sm text-blue-700">${hospital.endereco}</p>
                            <div class="flex items-center mt-2 space-x-4">
                                <span class="text-sm text-blue-600">
                                    <i class="fas fa-route mr-1"></i>
                                    ${hospital.distancia.toFixed(1)} km
                                </span>
                                <span class="text-sm text-blue-600">
                                    <i class="fas fa-clock mr-1"></i>
                                    ${hospital.tempo_estimado}
                                </span>
                                ${hospital.telefone ? `
                                    <span class="text-sm text-blue-600">
                                        <i class="fas fa-phone mr-1"></i>
                                        ${hospital.telefone}
                                    </span>
                                ` : ''}
                            </div>
                        </div>
                        <a href="https://www.google.com/maps/dir/${lat},${lng}/${hospital.latitude},${hospital.longitude}" 
                           target="_blank" class="btn-primary text-sm">
                            <i class="fas fa-directions mr-1"></i>
                            Rota
                        </a>
                    </div>
                `;
                
                hospitalSugerido.style.display = 'block';
            }
        })
        .catch(error => {
            console.error('Erro ao buscar hospital:', error);
        });
    }

    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
});
</script>
@endsection
