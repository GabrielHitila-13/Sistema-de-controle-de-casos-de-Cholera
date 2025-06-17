<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Relatório de Pacientes - Sistema Cólera Angola</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .logo { font-size: 18px; font-weight: bold; color: #1f2937; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f3f4f6; font-weight: bold; }
        .risco-alto { background-color: #fee2e2; color: #991b1b; }
        .risco-medio { background-color: #fef3c7; color: #92400e; }
        .risco-baixo { background-color: #d1fae5; color: #065f46; }
        .footer { margin-top: 20px; text-align: center; font-size: 10px; color: #6b7280; }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">Sistema de Gestão - Cólera Angola</div>
        <h2>Relatório de Pacientes</h2>
        <p>Gerado em: {{ date('d/m/Y H:i:s') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Nome</th>
                <th>BI</th>
                <th>Sexo</th>
                <th>Idade</th>
                <th>Risco</th>
                <th>Estabelecimento</th>
                <th>Data Triagem</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pacientes as $paciente)
            <tr>
                <td>{{ $paciente->nome }}</td>
                <td>{{ $paciente->bi }}</td>
                <td>{{ ucfirst($paciente->sexo) }}</td>
                <td>{{ $paciente->data_nascimento->age }} anos</td>
                <td class="risco-{{ $paciente->risco }}">{{ ucfirst($paciente->risco) }}</td>
                <td>{{ $paciente->estabelecimento->nome ?? 'N/A' }}</td>
                <td>{{ $paciente->data_triagem?->format('d/m/Y H:i') ?? 'N/A' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Total de pacientes: {{ $pacientes->count() }}</p>
        <p>Sistema de Gestão - Cólera Angola | Ministério da Saúde</p>
    </div>
</body>
</html>
