<?php

namespace App\Http\Controllers;

use App\Models\Gabinete;
use Illuminate\Http\Request;

class GabineteController extends Controller
{
    public function index()
    {
        $gabinetes = Gabinete::with('estabelecimentos')->orderBy('nome')->get();
        return view('gabinetes.index', compact('gabinetes'));
    }

    public function create()
    {
        return view('gabinetes.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'tipo' => 'required|in:provincial,municipal',
            'endereco' => 'nullable|string',
            'telefone' => 'nullable|string|max:20',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        Gabinete::create($validated);

        return redirect()->route('gabinetes.index')
            ->with('success', 'Gabinete criado com sucesso!');
    }

    public function show(Gabinete $gabinete)
    {
        $gabinete->load('estabelecimentos');
        return view('gabinetes.show', compact('gabinete'));
    }

    public function edit(Gabinete $gabinete)
    {
        return view('gabinetes.edit', compact('gabinete'));
    }

    public function update(Request $request, Gabinete $gabinete)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'tipo' => 'required|in:provincial,municipal',
            'endereco' => 'nullable|string',
            'telefone' => 'nullable|string|max:20',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        $gabinete->update($validated);

        return redirect()->route('gabinetes.index')
            ->with('success', 'Gabinete atualizado com sucesso!');
    }

    public function destroy(Gabinete $gabinete)
    {
        try {
            $gabinete->delete();
            return redirect()->route('gabinetes.index')
                ->with('success', 'Gabinete removido com sucesso!');
        } catch (\Exception $e) {
            return redirect()->route('gabinetes.index')
                ->with('error', 'Erro ao remover gabinete. Verifique se não há estabelecimentos vinculados.');
        }
    }
}
