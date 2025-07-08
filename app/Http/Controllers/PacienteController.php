<?php

namespace App\Http\Controllers;

use App\Models\Paciente;
use App\Models\Estabelecimento;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

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
                  ->orWhere('bi', 'like', "%{$search}%")
                  ->orWhere('numero_caso', 'like', "%{$search}%");
            });
        }

        if ($request->filled('risco')) {
            $query->where('risco', $request->risco);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('diagnostico_colera')) {
            $query->where('diagnostico_colera', $request->diagnostico_colera);
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
        $validator = Validator::make($request->all(), [
            'nome' => 'required|string|max:255',
            'bi' => 'nullable|string|unique:pacientes,bi',
            'telefone' => 'nullable|string|max:20',
            'data_nascimento' => 'required|date|before:today',
            'sexo' => 'required|in:masculino,feminino',
            'endereco' => 'nullable|string|max:500',
            'estabelecimento_id' => 'nullable|exists:estabelecimentos,id',
            'sintomas' => 'array',
            'sintomas_outros' => 'nullable|string|max:1000',
            'observacoes' => 'nullable|string|max:1000',
            'contato_caso_confirmado' => 'boolean',
            'area_surto' => 'boolean',
            'agua_contaminada' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Por favor, corrija os erros abaixo.');
        }

        DB::beginTransaction();
        
        try {
            $validated = $validator->validated();
            
            // Processar sintomas
            $sintomasArray = $validated['sintomas'] ?? [];
            if (!empty($validated['sintomas_outros'])) {
                $sintomasArray[] = $validated['sintomas_outros'];
            }
            $sintomasTexto = implode(', ', $sintomasArray);

            // Calculate age from birth date
            $idade = Carbon::parse($validated['data_nascimento'])->age;

            $pacienteData = [
                'nome' => $validated['nome'],
                'bi' => $validated['bi'] ?? null,
                'telefone' => $validated['telefone'] ?? null,
                'data_nascimento' => $validated['data_nascimento'],
                'idade' => $idade,
                'sexo' => $validated['sexo'],
                'endereco' => $validated['endereco'] ?? '',
                'estabelecimento_id' => $validated['estabelecimento_id'] ?? null,
                'sintomas' => $sintomasTexto,
                'observacoes' => $validated['observacoes'] ?? '',
                'contato_caso_confirmado' => $validated['contato_caso_confirmado'] ?? false,
                'area_surto' => $validated['area_surto'] ?? false,
                'agua_contaminada' => $validated['agua_contaminada'] ?? false,
                'data_triagem' => now(),
            ];

            // Calcular risco baseado em sintomas
            $pacienteData['risco'] = $this->calcularRisco($sintomasTexto, $pacienteData);
            
            // Avaliar probabilidade de cólera
            $avaliacaoColera = $this->avaliarColera($sintomasArray, $pacienteData);
            $pacienteData = array_merge($pacienteData, $avaliacaoColera);

            $paciente = Paciente::create($pacienteData);

            // Gerar QR Code
            $this->gerarQrCode($paciente);

            DB::commit();

            return redirect()->route('pacientes.index')
                ->with('success', 'Paciente cadastrado com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao criar paciente: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'exception' => $e
            ]);
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Erro ao cadastrar paciente. Tente novamente.');
        }
    }

    public function show(Paciente $paciente)
    {
        $paciente->load(['estabelecimento', 'veiculo', 'hospitalDestino']);
        return view('pacientes.show', compact('paciente'));
    }

    public function edit(Paciente $paciente)
    {
        $estabelecimentos = Estabelecimento::with('gabinete')->orderBy('nome')->get();
        return view('pacientes.edit', compact('paciente', 'estabelecimentos'));
    }

    public function update(Request $request, Paciente $paciente)
    {
        $validator = Validator::make($request->all(), [
            'nome' => 'required|string|max:255',
            'bi' => 'nullable|string|unique:pacientes,bi,' . $paciente->id,
            'telefone' => 'nullable|string|max:20',
            'data_nascimento' => 'required|date|before:today',
            'sexo' => 'required|in:masculino,feminino',
            'endereco' => 'nullable|string|max:500',
            'estabelecimento_id' => 'nullable|exists:estabelecimentos,id',
            'sintomas' => 'array',
            'sintomas_outros' => 'nullable|string|max:1000',
            'observacoes' => 'nullable|string|max:1000',
            'status' => 'nullable|in:aguardando,em_atendimento,finalizado,transferido',
            'contato_caso_confirmado' => 'boolean',
            'area_surto' => 'boolean',
            'agua_contaminada' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Por favor, corrija os erros abaixo.');
        }

        DB::beginTransaction();
        
        try {
            $validated = $validator->validated();
            
            // Processar sintomas
            $sintomasArray = $validated['sintomas'] ?? [];
            if (!empty($validated['sintomas_outros'])) {
                $sintomasArray[] = $validated['sintomas_outros'];
            }
            $sintomasTexto = implode(', ', $sintomasArray);

            // Calculate age from birth date
            $idade = Carbon::parse($validated['data_nascimento'])->age;

            $updateData = [
                'nome' => $validated['nome'],
                'bi' => $validated['bi'] ?? null,
                'telefone' => $validated['telefone'] ?? null,
                'data_nascimento' => $validated['data_nascimento'],
                'idade' => $idade,
                'sexo' => $validated['sexo'],
                'endereco' => $validated['endereco'] ?? '',
                'estabelecimento_id' => $validated['estabelecimento_id'] ?? null,
                'sintomas' => $sintomasTexto,
                'observacoes' => $validated['observacoes'] ?? '',
                'status' => $validated['status'] ?? $paciente->status,
                'contato_caso_confirmado' => $validated['contato_caso_confirmado'] ?? false,
                'area_surto' => $validated['area_surto'] ?? false,
                'agua_contaminada' => $validated['agua_contaminada'] ?? false,
            ];

            // Recalcular risco se sintomas mudaram
            if ($paciente->sintomas !== $sintomasTexto) {
                $updateData['risco'] = $this->calcularRisco($sintomasTexto, $updateData);
                $updateData['data_triagem'] = now();
                
                // Reavaliar probabilidade de cólera
                $avaliacaoColera = $this->avaliarColera($sintomasArray, $updateData);
                $updateData = array_merge($updateData, $avaliacaoColera);
            }

            $paciente->update($updateData);

            // Regenerar QR Code se dados importantes mudaram
            $this->gerarQrCode($paciente);

            DB::commit();

            return redirect()->route('pacientes.index')
                ->with('success', 'Paciente atualizado com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao atualizar paciente: ' . $e->getMessage(), [
                'paciente_id' => $paciente->id,
                'request_data' => $request->all(),
                'exception' => $e
            ]);
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Erro ao atualizar paciente. Tente novamente.');
        }
    }

    public function destroy(Paciente $paciente)
    {
        try {
            $paciente->delete();
            return redirect()->route('pacientes.index')
                ->with('success', 'Paciente removido com sucesso!');
        } catch (\Exception $e) {
            Log::error('Erro ao excluir paciente: ' . $e->getMessage(), [
                'paciente_id' => $paciente->id
            ]);
            
            return redirect()->back()
                ->with('error', 'Erro ao remover paciente. Tente novamente.');
        }
    }

    public function exportPdf(Request $request)
    {
        $query = Paciente::with('estabelecimento.gabinete');

        // Aplicar mesmos filtros da listagem
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nome', 'like', "%{$search}%")
                  ->orWhere('bi', 'like', "%{$search}%")
                  ->orWhere('numero_caso', 'like', "%{$search}%");
            });
        }

        if ($request->filled('risco')) {
            $query->where('risco', $request->risco);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('diagnostico_colera')) {
            $query->where('diagnostico_colera', $request->diagnostico_colera);
        }

        $pacientes = $query->orderBy('created_at', 'desc')->get();

        $pdf = Pdf::loadView('relatorios.pacientes-pdf', compact('pacientes'));
        
        return $pdf->download('relatorio-pacientes-' . date('Y-m-d') . '.pdf');
    }

    private function calcularRisco($sintomas, $dadosPaciente)
    {
        $sintomas = strtolower($sintomas ?? '');
        $pontuacao = 0;

        // Sintomas de alto risco
        if (str_contains($sintomas, 'diarreia aquosa')) $pontuacao += 3;
        if (str_contains($sintomas, 'vomito')) $pontuacao += 2;
        if (str_contains($sintomas, 'desidratacao')) $pontuacao += 3;
        if (str_contains($sintomas, 'febre')) $pontuacao += 1;
        if (str_contains($sintomas, 'dor abdominal')) $pontuacao += 1;
        if (str_contains($sintomas, 'fraqueza')) $pontuacao += 1;

        // Fatores de risco adicionais
        if ($dadosPaciente['contato_caso_confirmado'] ?? false) $pontuacao += 2;
        if ($dadosPaciente['area_surto'] ?? false) $pontuacao += 2;
        if ($dadosPaciente['agua_contaminada'] ?? false) $pontuacao += 1;

        // Considerar idade
        $idade = $dadosPaciente['idade'] ?? 0;
        if ($idade < 5 || $idade > 65) $pontuacao += 1;

        if ($pontuacao >= 6) return 'alto';
        if ($pontuacao >= 3) return 'medio';
        return 'baixo';
    }

    private function avaliarColera($sintomas, $dadosPaciente)
    {
        $pontuacaoColera = 0;
        $sintomasColera = [];

        // Sintomas específicos de cólera
        foreach ($sintomas as $sintoma) {
            $sintoma = strtolower($sintoma);
            if (str_contains($sintoma, 'diarreia aquosa')) {
                $pontuacaoColera += 4;
                $sintomasColera[] = 'Diarreia aquosa abundante';
            }
            if (str_contains($sintoma, 'vomito')) {
                $pontuacaoColera += 2;
                $sintomasColera[] = 'Vômitos';
            }
            if (str_contains($sintoma, 'desidratacao')) {
                $pontuacaoColera += 3;
                $sintomasColera[] = 'Desidratação';
            }
        }

        // Fatores de risco epidemiológicos
        if ($dadosPaciente['contato_caso_confirmado'] ?? false) $pontuacaoColera += 3;
        if ($dadosPaciente['area_surto'] ?? false) $pontuacaoColera += 2;
        if ($dadosPaciente['agua_contaminada'] ?? false) $pontuacaoColera += 2;

        // Determinar diagnóstico e probabilidade
        $diagnostico = 'pendente';
        $probabilidade = 0;

        if ($pontuacaoColera >= 8) {
            $diagnostico = 'provavel';
            $probabilidade = min(95, 60 + ($pontuacaoColera - 8) * 5);
        } elseif ($pontuacaoColera >= 5) {
            $diagnostico = 'suspeito';
            $probabilidade = min(60, 30 + ($pontuacaoColera - 5) * 10);
        } elseif ($pontuacaoColera >= 2) {
            $diagnostico = 'suspeito';
            $probabilidade = min(30, $pontuacaoColera * 10);
        }

        return [
            'diagnostico_colera' => $diagnostico,
            'probabilidade_colera' => $probabilidade,
            'sintomas_colera' => $sintomasColera,
            'data_diagnostico' => $diagnostico !== 'pendente' ? now() : null,
        ];
    }

    private function gerarQrCode($paciente)
    {
        try {
            $qrData = json_encode([
                'id' => $paciente->id,
                'nome' => $paciente->nome,
                'bi' => $paciente->bi,
                'telefone' => $paciente->telefone,
                'risco' => $paciente->risco,
                'diagnostico_colera' => $paciente->diagnostico_colera,
                'numero_caso' => $paciente->numero_caso,
                'data_triagem' => $paciente->data_triagem?->format('Y-m-d H:i:s'),
                'estabelecimento' => $paciente->estabelecimento->nome ?? null,
            ]);

            $qrCode = QrCode::size(200)->generate($qrData);
            $paciente->update(['qr_code' => base64_encode($qrCode)]);
        } catch (\Exception $e) {
            Log::error('Erro ao gerar QR Code: ' . $e->getMessage(), [
                'paciente_id' => $paciente->id
            ]);
        }
    }
}
