<?php

namespace App\Http\Controllers;

use App\Models\Paciente;
use App\Models\Estabelecimento;
use App\Models\Gabinete;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Estatísticas gerais
        $totalPacientes = Paciente::count();
        $casosAltoRisco = Paciente::where('risco', 'alto')->count();
        $totalEstabelecimentos = Estabelecimento::count();
        $totalGabinetes = Gabinete::count();

        // Casos por província (gabinetes provinciais)
        $casosPorProvincia = DB::table('pacientes')
            ->join('estabelecimentos', 'pacientes.estabelecimento_id', '=', 'estabelecimentos.id')
            ->join('gabinetes', 'estabelecimentos.gabinete_id', '=', 'gabinetes.id')
            ->where('gabinetes.tipo', 'provincial')
            ->select('gabinetes.nome', DB::raw('count(*) as total'))
            ->groupBy('gabinetes.nome')
            ->get();

        // Evolução temporal (últimos 30 dias)
        $evolucaoTemporal = DB::table('pacientes')
            ->select(DB::raw('DATE(created_at) as data'), DB::raw('count(*) as casos'))
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('data')
            ->get();

        // Distribuição por sexo
        $distribuicaoSexo = Paciente::select('sexo', DB::raw('count(*) as total'))
            ->groupBy('sexo')
            ->get();

        // Distribuição por risco
        $distribuicaoRisco = Paciente::select('risco', DB::raw('count(*) as total'))
            ->groupBy('risco')
            ->get();

        return view('dashboard', compact(
            'totalPacientes',
            'casosAltoRisco',
            'totalEstabelecimentos',
            'totalGabinetes',
            'casosPorProvincia',
            'evolucaoTemporal',
            'distribuicaoSexo',
            'distribuicaoRisco'
        ));
    }
}