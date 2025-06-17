<?php

namespace App\Http\Controllers;

use App\Models\PontoAtendimento;
use Illuminate\Http\Request;

class PontoAtendimentoController extends Controller
{
    public function index()
    {
        $pontosAtendimento = PontoAtendimento::orderBy('nome')->get();
        return view('pontos-atendimento.index', compact('pontosAtendimento'));
    }

    public function create()
    {
        return view('pontos-atendimento.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'descricao' => 'nullable|string',
            'ativo' => 'boolean',
        ]);

        $validated['ativo'] = $request->has('ativo');

        PontoAtendimento::create($validated);

        return redirect()->route('pontos-atendimento.index')
            ->with('success', 'Ponto de atendimento criado com sucesso!');
    }

    public function show(PontoAtendimento $pontoAtendimento)
    {
        return view('pontos-atendimento.show', compact('pontoAtendimento'));
    }

    public function edit(PontoAtendimento $pontoAtendimento)
    {
        return view('pontos-atendimento.edit', compact('pontoAtendimento'));
    }

    public function update(Request $request, PontoAtendimento $pontoAtendimento)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'descricao' => 'nullable|string',
            'ativo' => 'boolean',
        ]);

        $validated['ativo'] = $request->has('ativo');

        $pontoAtendimento->update($validated);

        return redirect()->route('pontos-atendimento.index')
            ->with('success', 'Ponto de atendimento atualizado com sucesso!');
    }

    public function destroy(PontoAtendimento $pontoAtendimento)
    {
        $pontoAtendimento->delete();
        return redirect()->route('pontos-atendimento.index')
            ->with('success', 'Ponto de atendimento removido com sucesso!');
    }
}
