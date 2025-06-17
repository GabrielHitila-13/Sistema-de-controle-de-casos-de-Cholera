<?php

namespace App\Http\Controllers;

use App\Models\Estabelecimento;
use App\Models\Gabinete;
use Illuminate\Http\Request;

class EstabelecimentoController extends Controller
{
    public function index()
    {
        $estabelecimentos = Estabelecimento::with('gabinete')->orderBy('nome')->get();
        return view('estabelecimentos.index', compact('estabelecimentos'));
    }

    public function create()
    {
        $gabinetes = Gabinete::orderBy('nome')->get();
        return view('estabelecimentos.create', compact('gabinetes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'gabinete_id' => 'required|exists:gabinetes,id',
            'categoria' => 'required|in:geral,municipal,centro,posto,clinica,outros',
            'endereco' => 'nullable|string',
            'telefone' => 'nullable|string|max:20',
            'capacidade' => 'nullable|integer|min:0',
        ]);

        Estabelecimento::create($validated);

        return redirect()->route('estabelecimentos.index')
            ->with('success', 'Estabelecimento criado com sucesso!');
    }

    public function show(Estabelecimento $estabelecimento)
    {
        $estabelecimento->load(['gabinete', 'pacientes', 'usuarios']);
        return view('estabelecimentos.show', compact('estabelecimento'));
    }

    public function edit(Estabelecimento $estabelecimento)
    {
        $gabinetes = Gabinete::orderBy('nome')->get();
        return view('estabelecimentos.edit', compact('estabelecimento', 'gabinetes'));
    }

    public function update(Request $request, Estabelecimento $estabelecimento)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'gabinete_id' => 'required|exists:gabinetes,id',
            'categoria' => 'required|in:geral,municipal,centro,posto,clinica,outros',
            'endereco' => 'nullable|string',
            'telefone' => 'nullable|string|max:20',
            'capacidade' => 'nullable|integer|min:0',
        ]);

        $estabelecimento->update($validated);

        return redirect()->route('estabelecimentos.index')
            ->with('success', 'Estabelecimento atualizado com sucesso!');
    }

    public function destroy(Estabelecimento $estabelecimento)
    {
        try {
            $estabelecimento->delete();
            return redirect()->route('estabelecimentos.index')
                ->with('success', 'Estabelecimento removido com sucesso!');
        } catch (\Exception $e) {
            return redirect()->route('estabelecimentos.index')
                ->with('error', 'Erro ao remover estabelecimento. Verifique se não há pacientes vinculados.');
        }
    }
}
