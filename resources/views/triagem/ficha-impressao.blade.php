<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ficha de Triagem - {{ $paciente->nome }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .logo {
            font-size: 18px;
            font-weight: bold;
            color: #1f2937;
        }
        .subtitle {
            color: #666;
            margin-top: 5px;
        }
        .section {
            margin-bottom: 20px;
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 5px;
        }
        .section-title {
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 10px;
            color: #333;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }
        .info-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }
        .label {
            font-weight: bold;
            color: #555;
        }
        .value {
            color: #333;
        }
        .risco-alto {
            background-color: #fee2e2;
            color: #991b1b;
            padding: 5px 10px;
            border-radius: 5px;
            font-weight: bold;
        }
        .risco-medio {
            background-color: #fef3c7;
            color: #92400e;
            padding: 5px 10px;
            border-radius: 5px;
            font-weight: bold;
        }
        .risco-baixo {
            background-color: #d1fae5;
            color: #065f46;
            padding: 5px 10px;
            border-radius: 5px;
            font-weight: bold;
        }
        .qr-code {
            text-align: center;
            margin: 20px 0;
        }
        .qr-code img {
            width: 150px;
            height: 150px;
            border: 2px solid #333;
            padding: 10px;
        }
        .recomendacoes {
            background-color: #f9fafb;
            padding: 10px;
            border-left: 4px solid #3b82f6;
        }
        .recomendacoes ul {
            margin: 0;
            padding-left: 20px;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        @media print {
            body { margin: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">Sistema de Gestão de Surto de Cólera - Angola</div>
        <div class="subtitle">Ministério da Saúde Pública</div>
        <div class="subtitle">Ficha de Triagem</div>
    </div>

    <!-- Dados do Paciente -->
    <div class="section">
        <div class="section-title">DADOS DO PACIENTE</div>
        <div class="info-grid">
            <div class="info-item">
                <span class="label">Nome:</span>
                <span class="value">{{ $paciente->nome }}</span>
            </div>
            <div class="info-item">
                <span class="label">BI:</span>
                <span class="value">{{ $paciente->bi }}</span>
            </div>
            <div class="info-item">
                <span class="label">Telefone:</span>
                <span class="value">{{ $paciente->telefone ?? 'N/A' }}</span>
            </div>
            <div class="info-item">
                <span class="label">Data de Nascimento:</span>
                <span class="value">{{ $paciente->data_nascimento->format('d/m/Y') }}</span>
            </div>
            <div class="info-item">
                <span class="label">Idade:</span>
                <span class="value">{{ $paciente->data_nascimento->age }} anos</span>
            </div>
            <div class="info-item">
                <span class="label">Sexo:</span>
                <span class="value">{{ ucfirst($paciente->sexo) }}</span>
            </div>
        </div>
    </div>

    <!-- Resultado da Triagem -->
    <div class="section">
        <div class="section-title">RESULTADO DA TRIAGEM</div>
        <div class="info-grid">
            <div class="info-item">
                <span class="label">Data/Hora da Triagem:</span>
                <span class="value">{{ $paciente->data_triagem->format('d/m/Y H:i') }}</span>
            </div>
            <div class="info-item">
                <span class="label">Nível de Risco:</span>
                <span class="risco-{{ $paciente->risco }}">{{ strtoupper($paciente->risco) }} RISCO</span>
            </div>
        </div>
        
        @if($paciente->sintomas)
        <div style="margin-top: 15px;">
            <div class="label">Sintomas Relatados:</div>
            <div style="background-color: #f9fafb; padding: 10px; border-radius: 5px; margin-top: 5px;">
                {{ $paciente->sintomas }}
            </div>
        </div>
        @endif
    </div>

    <!-- Hospital Recomendado -->
    @if($paciente->estabelecimento)
    <div class="section">
        <div class="section-title">HOSPITAL RECOMENDADO</div>
        <div class="info-grid">
            <div class="info-item">
                <span class="label">Nome:</span>
                <span class="value">{{ $paciente->estabelecimento->nome }}</span>
            </div>
            <div class="info-item">
                <span class="label">Categoria:</span>
                <span class="value">{{ ucfirst($paciente->estabelecimento->categoria) }}</span>
            </div>
            <div class="info-item">
                <span class="label">Endereço:</span>
                <span class="value">{{ $paciente->estabelecimento->endereco }}</span>
            </div>
            <div class="info-item">
                <span class="label">Telefone:</span>
                <span class="value">{{ $paciente->estabelecimento->telefone ?? 'N/A' }}</span>
            </div>
            <div class="info-item">
                <span class="label">Gabinete:</span>
                <span class="value">{{ $paciente->estabelecimento->gabinete->nome ?? 'N/A' }}</span>
            </div>
        </div>
    </div>
    @endif

    <!-- Recomendações -->
    <div class="section">
        <div class="section-title">RECOMENDAÇÕES MÉDICAS</div>
        <div class="recomendacoes">
            @if($paciente->risco == 'alto')
                <ul>
                    <li><strong>URGENTE:</strong> Encaminhar imediatamente para hospital</li>
                    <li>Iniciar hidratação oral ou endovenosa</li>
                    <li>Monitoramento contínuo dos sinais vitais</li>
                    <li>Isolamento do paciente</li>
                    <li>Notificação às autoridades de saúde</li>
                </ul>
            @elseif($paciente->risco == 'medio')
                <ul>
                    <li>Encaminhar para avaliação médica</li>
                    <li>Iniciar hidratação oral</li>
                    <li>Monitoramento de sintomas</li>
                    <li>Orientações sobre higiene</li>
                    <li>Retorno em 24h se piora</li>
                </ul>
            @else
                <ul>
                    <li>Monitoramento domiciliar</li>
                    <li>Hidratação oral abundante</li>
                    <li>Orientações sobre prevenção</li>
                    <li>Retorno se surgir febre ou diarreia</li>
                    <li>Manter isolamento preventivo</li>
                </ul>
            @endif
        </div>
    </div>

    <!-- QR Code -->
    @if($paciente->qr_code)
    <div class="qr-code">
        <div class="section-title">QR CODE DO PACIENTE</div>
        <img src="data:image/svg+xml;base64,{{ $paciente->qr_code }}" alt="QR Code">
        <div style="margin-top: 10px; font-size: 10px; color: #666;">
            Escaneie para acessar informações digitais do paciente
        </div>
    </div>
    @endif

    <div class="footer">
        <p>Sistema de Gestão de Surto de Cólera - Angola | Ministério da Saúde</p>
        <p>Gerado em: {{ now()->format('d/m/Y H:i:s') }}</p>
        <p>ID do Paciente: {{ $paciente->id }} | BI: {{ $paciente->bi }}</p>
    </div>

    <script>
        // Auto-imprimir quando a página carregar
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
