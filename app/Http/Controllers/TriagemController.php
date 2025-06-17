<?php

namespace App\Http\Controllers;

use App\Models\Paciente;
use App\Models\Estabelecimento;
use App\Services\TriagemService;
use App\Services\GeolocationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TriagemController extends Controller
{
    protected $triagemService;
    protected $geolocationService;

    public function __construct(TriagemService $triagemService, GeolocationService $geolocationService)
    {
        $this->triagemService = $triagemService;
        $this->geolocationService = $geolocationService;
    }

    public function index()
    {
        return view('triagem.index');
    }

    public function create()
    {
        $estabelecimentos = Estabelecimento::with('gabinete')->orderBy('nome')->get();
        return view('triagem.create', compact('estabelecimentos'));
    }

    public function avaliarSintomas(Request $request)
    {
        $sintomas = $request->input('sintomas', []);
        $sintomasTexto = $request->input('sintomas_outros', '');
        
        $resultado = $this->triagemService->avaliarRisco($sintomas, $sintomasTexto);
        
        return response()->json($resultado);
    }

    public function buscarHospitalProximo(Request $request)
    {
        $latitude = $request->input('latitude');
        $longitude = $request->input('longitude');
        
        if (!$latitude || !$longitude) {
            return response()->json(['error' => 'Coordenadas não fornecidas'], 400);
        }

        $hospitaisProximos = $this->geolocationService->buscarHospitaisProximos($latitude, $longitude);
        
        return response()->json($hospitaisProximos);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'bi' => 'required|string|unique:pacientes,bi',
            'telefone' => 'nullable|string',
            'data_nascimento' => 'required|date',
            'sexo' => 'required|in:masculino,feminino',
            'sintomas' => 'array',
            'sintomas_outros' => 'nullable|string',
            'latitude_paciente' => 'nullable|numeric',
            'longitude_paciente' => 'nullable|numeric',
            'estabelecimento_sugerido_id' => 'nullable|exists:estabelecimentos,id',
        ]);

        DB::beginTransaction();
        
        try {
            // Processar sintomas
            $sintomasArray = $validated['sintomas'] ?? [];
            if (!empty($validated['sintomas_outros'])) {
                $sintomasArray[] = $validated['sintomas_outros'];
            }
            $sintomasTexto = implode(', ', $sintomasArray);

            // Avaliar risco
            $resultadoTriagem = $this->triagemService->avaliarRisco($validated['sintomas'] ?? [], $validated['sintomas_outros'] ?? '');

            // Buscar hospital mais próximo se coordenadas fornecidas
            $estabelecimentoId = $validated['estabelecimento_sugerido_id'];
            if (!$estabelecimentoId && isset($validated['latitude_paciente']) && isset($validated['longitude_paciente'])) {
                $hospitaisProximos = $this->geolocationService->buscarHospitaisProximos(
                    $validated['latitude_paciente'], 
                    $validated['longitude_paciente']
                );
                
                if (!empty($hospitaisProximos)) {
                    $estabelecimentoId = $hospitaisProximos[0]['id'];
                }
            }

            // Criar paciente
            $paciente = Paciente::create([
                'nome' => $validated['nome'],
                'bi' => $validated['bi'],
                'telefone' => $validated['telefone'],
                'data_nascimento' => $validated['data_nascimento'],
                'sexo' => $validated['sexo'],
                'estabelecimento_id' => $estabelecimentoId,
                'sintomas' => $sintomasTexto,
                'risco' => $resultadoTriagem['nivel'],
                'data_triagem' => now(),
                'latitude_triagem' => $validated['latitude_paciente'],
                'longitude_triagem' => $validated['longitude_paciente'],
            ]);

            // Gerar QR Code
            $this->triagemService->gerarQrCode($paciente);

            DB::commit();

            return redirect()->route('triagem.resultado', $paciente->id)
                ->with('success', 'Triagem realizada com sucesso!');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Erro ao processar triagem: ' . $e->getMessage()]);
        }
    }

    public function resultado(Paciente $paciente)
    {
        $paciente->load('estabelecimento.gabinete');
        
        // Buscar rota para o hospital se temos coordenadas
        $rotaHospital = null;
        if ($paciente->latitude_triagem && $paciente->longitude_triagem && $paciente->estabelecimento) {
            $rotaHospital = $this->geolocationService->obterRota(
                $paciente->latitude_triagem,
                $paciente->longitude_triagem,
                $paciente->estabelecimento
            );
        }

        return view('triagem.resultado', compact('paciente', 'rotaHospital'));
    }

    public function imprimirFicha(Paciente $paciente)
    {
        $paciente->load('estabelecimento.gabinete');
        return view('triagem.ficha-impressao', compact('paciente'));
    }
}
