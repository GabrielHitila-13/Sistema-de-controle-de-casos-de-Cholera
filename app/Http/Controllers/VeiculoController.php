<?php

namespace App\Http\Controllers;

use App\Models\Veiculo;
use Illuminate\Http\Request;

class VeiculoController extends Controller
{
    public function index()
    {
        $veiculos = Veiculo::orderBy('placa')->get();
        return view('veiculos.index', compact('veiculos'));
    }

    public function create()
    {
        return view('veiculos.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'placa' => 'required|string|max:20|unique:veiculos,placa',
            'tipo' => 'required|in:ambulancia,outro',
            'status' => 'required|in:disponivel,em_atendimento,manutencao',
            'descricao' => 'nullable|string',
        ]);

        Veiculo::create($validated);

        return redirect()->route('veiculos.index')
            ->with('success', 'Veículo cadastrado com sucesso!');
    }

    public function show(Veiculo $veiculo)
    {
        return view('veiculos.show', compact('veiculo'));
    }

    public function edit(Veiculo $veiculo)
    {
        return view('veiculos.edit', compact('veiculo'));
    }

    public function update(Request $request, Veiculo $veiculo)
    {
        $validated = $request->validate([
            'placa' => 'required|string|max:20|unique:veiculos,placa,' . $veiculo->id,
            'tipo' => 'required|in:ambulancia,outro',
            'status' => 'required|in:disponivel,em_atendimento,manutencao',
            'descricao' => 'nullable|string',
        ]);

        $veiculo->update($validated);

        return redirect()->route('veiculos.index')
            ->with('success', 'Veículo atualizado com sucesso!');
    }

    public function updateStatus(Request $request, Veiculo $veiculo)
    {
        $validated = $request->validate([
            'status' => 'required|in:disponivel,em_atendimento,manutencao',
        ]);

        $veiculo->update(['status' => $validated['status']]);

        return redirect()->route('veiculos.index')
            ->with('success', 'Status do veículo atualizado com sucesso!');
    }

    public function destroy(Veiculo $veiculo)
    {
        $veiculo->delete();
        return redirect()->route('veiculos.index')
            ->with('success', 'Veículo removido com sucesso!');
    }
}
