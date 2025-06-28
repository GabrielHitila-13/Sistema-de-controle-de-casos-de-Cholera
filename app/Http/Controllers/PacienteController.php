<?php

namespace App\Http\Controllers;

use App\Models\Paciente;
use App\Models\Estabelecimento;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Barryvdh\DomPDF\Facade\Pdf;

class PacienteController extends Controller
{
    public function index(Request $request)
    {
        $query = Paciente::with('estabelecimento.gabinete');

        // Filtros
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nome', 'like', "%{$search}%")
                  ->orWhere('bi', 'like', "%{$search}%");
            });
        }

        if ($request->filled('risco')) {
            $query->where('risco', $request->risco);
        }

        if ($request->filled('estabelecimento_id')) {
            $query->where('estabelecimento_id', $request->estabelecimento_id);
        }

        $pacientes = $query->orderBy('created_at', 'desc')->paginate(20);
        $estabelecimentos = Estabelecimento::orderBy('nome')->get();

        return view('pacientes.index', compact('pacientes', 'estabelecimentos'));
    }

    public function create()
    {
        $estabelecimentos = Estabelecimento::with('gabinete')->orderBy('nome')->get();
        return view('pacientes.create', compact('estabelecimentos'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'bi' => 'required|string|unique:pacientes,bi',
            'telefone' => 'nullable|string',
            'data_nascimento' => 'required|date',
            'sexo' => 'required|in:masculino,feminino',
            'estabelecimento_id' => 'required|exists:estabelecimentos,id',
            'sintomas' => 'array',
            'sintomas_outros' => 'nullable|string',
        ]);

        // Processar sintomas
        $sintomasArray = $validated['sintomas'] ?? [];
        if (!empty($validated['sintomas_outros'])) {
            $sintomasArray[] = $validated['sintomas_outros'];
        }
        $sintomasTexto = implode(', ', $sintomasArray);

        $paciente = new Paciente([
            'nome' => $validated['nome'],
            'bi' => $validated['bi'],
            'data_nascimento' => $validated['data_nascimento'],
            'sexo' => $validated['sexo'],
            'estabelecimento_id' => $validated['estabelecimento_id'],
            'sintomas' => $sintomasTexto,
        ]);

        // Criptografar telefone se fornecido
        if (!empty($validated['telefone'])) {
            $paciente->telefone = $validated['telefone'];
        }

        // Calcular risco baseado em sintomas
        $paciente->risco = $this->calcularRisco($sintomasTexto);
        $paciente->data_triagem = now();
        
        $paciente->save();

        // Gerar QR Code
        $this->gerarQrCode($paciente);

        return redirect()->route('pacientes.index')
            ->with('success', 'Paciente cadastrado com sucesso!');
    }

    public function show(Paciente $paciente)
    {
        return view('pacientes.show', compact('paciente'));
    }

    public function edit(Paciente $paciente)
    {
        $estabelecimentos = Estabelecimento::with('gabinete')->orderBy('nome')->get();
            $sintomas = is_array($paciente->sintomas) 
        ? $paciente->sintomas 
        : json_decode($paciente->sintomas, true) ?? [];

        return view('pacientes.edit', compact('paciente', 'estabelecimentos'));
    }

   public function update(Request $request, Paciente $paciente)
{
    $validated = $request->validate([
        'nome' => 'required|string|max:255',
        'bi' => 'required|string|unique:pacientes,bi,' . $paciente->id,
        'telefone' => 'nullable|string',
        'data_nascimento' => 'required|date',
        'sexo' => 'required|in:masculino,feminino',
        'estabelecimento_id' => 'required|exists:estabelecimentos,id',
        'sintomas' => 'required|array',
        'sintomas.*' => 'string|max:255',
        'sintomas_outros' => 'nullable|string',
        'risco' => 'sometimes|required|in:Baixo,Médio,Alto',
        'data_triagem' => 'sometimes|nullable|date'
    ]);

    // Processar sintomas
    $sintomasProcessados = $validated['sintomas'];
    
    if (!empty($validated['sintomas_outros'])) {
        $sintomasProcessados[] = $validated['sintomas_outros'];
    }

    // Atualizar apenas se os sintomas mudaram
    if ($paciente->sintomas != $sintomasProcessados) {
        $validated['sintomas'] = $sintomasProcessados;
        $validated['risco'] = $this->calcularRisco($sintomasProcessados); // Método deve existir
        $validated['data_triagem'] = now();
    }

    // Atualizar telefone separadamente se necessário
    if (array_key_exists('telefone', $validated)) {
        $paciente->telefone = encrypt($validated['telefone']); // Exemplo de criptografia
    }

    $paciente->update($validated);

    // Gerar QR code se dados críticos mudaram
    if ($paciente->wasChanged(['nome', 'bi', 'estabelecimento_id'])) {
        $this->gerarQrCode($paciente); // Método deve existir
    }

    return redirect()->route('pacientes.index')
        ->with('success', 'Paciente atualizado com sucesso!');
}

    public function destroy(Paciente $paciente)
    {
        $paciente->delete();
        return redirect()->route('pacientes.index')
            ->with('success', 'Paciente removido com sucesso!');
    }

    public function exportPdf(Request $request)
    {
        $query = Paciente::with('estabelecimento.gabinete');

        // Aplicar mesmos filtros da listagem
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nome', 'like', "%{$search}%")
                  ->orWhere('bi', 'like', "%{$search}%");
            });
        }

        if ($request->filled('risco')) {
            $query->where('risco', $request->risco);
        }

        $pacientes = $query->orderBy('created_at', 'desc')->get();

        $pdf = Pdf::loadView('relatorios.pacientes-pdf', compact('pacientes'));
        
        return $pdf->download('relatorio-pacientes-' . date('Y-m-d') . '.pdf');
    }

    private function calcularRisco($sintomas)
    {
         if (is_array($sintomas)) {
        $sintomas = implode(' ', $sintomas);
    }
        $sintomas = strtolower($sintomas ?? '');
        $pontuacao = 0;

        // Sintomas de alto risco
        if (str_contains($sintomas, 'diarreia aquosa')) $pontuacao += 3;
        if (str_contains($sintomas, 'vomito')) $pontuacao += 2;
        if (str_contains($sintomas, 'desidratacao')) $pontuacao += 3;
        if (str_contains($sintomas, 'febre')) $pontuacao += 1;
        if (str_contains($sintomas, 'dor abdominal')) $pontuacao += 1;
        if (str_contains($sintomas, 'fraqueza')) $pontuacao += 1;

        if ($pontuacao >= 5) return 'alto';
        if ($pontuacao >= 3) return 'medio';
        return 'baixo';
    }

    private function gerarQrCode($paciente)
    {
        $qrData = json_encode([
            'id' => $paciente->id,
            'nome' => $paciente->nome,
            'bi' => $paciente->bi,
            'telefone' => $paciente->telefone,
            'risco' => $paciente->risco,
            'data_triagem' => $paciente->data_triagem?->format('Y-m-d H:i:s'),
            'estabelecimento' => $paciente->estabelecimento->nome ?? null,
        ]);

        $qrCode = QrCode::size(200)->generate($qrData);
        $paciente->update(['qr_code' => base64_encode($qrCode)]);
    }
}
