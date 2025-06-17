@extends('layouts.app')

@section('title', 'Veículos - Sistema Cólera Angola')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h2 class="text-3xl font-bold text-gray-900">Gestão de Veículos</h2>
        <a href="{{ route('veiculos.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center">
            <i class="fas fa-plus mr-2"></i>
            Novo Veículo
        </a>
    </div>

    <!-- Status Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-green-100 p-4 rounded-lg">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-green-600 text-2xl mr-3"></i>
                <div>
                    <p class="text-green-800 font-semibold">Disponíveis</p>
                    <p class="text-2xl font-bold text-green-900">{{ $veiculos->where('status', 'disponivel')->count() ?? 0 }}</p>
                </div>
            </div>
        </div>
        <div class="bg-yellow-100 p-4 rounded-lg">
            <div class="flex items-center">
                <i class="fas fa-ambulance text-yellow-600 text-2xl mr-3"></i>
                <div>
                    <p class="text-yellow-800 font-semibold">Em Atendimento</p>
                    <p class="text-2xl font-bold text-yellow-900">{{ $veiculos->where('status', 'em_atendimento')->count() ?? 0 }}</p>
                </div>
            </div>
        </div>
        <div class="bg-red-100 p-4 rounded-lg">
            <div class="flex items-center">
                <i class="fas fa-wrench text-red-600 text-2xl mr-3"></i>
                <div>
                    <p class="text-red-800 font-semibold">Manutenção</p>
                    <p class="text-2xl font-bold text-red-900">{{ $veiculos->where('status', 'manutencao')->count() ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-md">
        <ul class="divide-y divide-gray-200">
            @forelse($veiculos ?? [] as $veiculo)
            <li>
                <div class="px-4 py-4 flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-10 w-10">
                            <div class="h-10 w-10 rounded-full 
                                @if($veiculo->status == 'disponivel') bg-green-100
                                @elseif($veiculo->status == 'em_atendimento') bg-yellow-100
                                @else bg-red-100 @endif
                                flex items-center justify-center">
                                <i class="fas fa-ambulance 
                                    @if($veiculo->status == 'disponivel') text-green-600
                                    @elseif($veiculo->status == 'em_atendimento') text-yellow-600
                                    @else text-red-600 @endif"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <div class="flex items-center">
                                <div class="text-sm font-medium text-gray-900">{{ $veiculo->placa }}</div>
                                <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($veiculo->status == 'disponivel') bg-green-100 text-green-800
                                    @elseif($veiculo->status == 'em_atendimento') bg-yellow-100 text-yellow-800
                                    @else bg-red-100 text-red-800 @endif">
                                    {{ ucfirst(str_replace('_', ' ', $veiculo->status)) }}
                                </span>
                            </div>
                            <div class="text-sm text-gray-500">
                                Tipo: {{ ucfirst($veiculo->tipo) }}
                            </div>
                            @if($veiculo->descricao)
                            <div class="text-xs text-gray-400 mt-1">
                                {{ Str::limit($veiculo->descricao, 100) }}
                            </div>
                            @endif
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <!-- Botão para alterar status -->
                        <div class="relative">
                            <button onclick="toggleStatusMenu({{ $veiculo->id }})" class="text-blue-600 hover:text-blue-900">
                                <i class="fas fa-exchange-alt"></i>
                            </button>
                            <div id="status-menu-{{ $veiculo->id }}" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-10">
                                <form method="POST" action="{{ route('veiculos.update-status', $veiculo) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" name="status" value="disponivel" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-check-circle text-green-600 mr-2"></i>Disponível
                                    </button>
                                    <button type="submit" name="status" value="em_atendimento" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-ambulance text-yellow-600 mr-2"></i>Em Atendimento
                                    </button>
                                    <button type="submit" name="status" value="manutencao" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-wrench text-red-600 mr-2"></i>Manutenção
                                    </button>
                                </form>
                            </div>
                        </div>
                        <a href="{{ route('veiculos.edit', $veiculo) }}" class="text-green-600 hover:text-green-900">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form method="POST" action="{{ route('veiculos.destroy', $veiculo) }}" class="inline" onsubmit="return confirm('Tem certeza?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </li>
            @empty
            <li class="px-4 py-8 text-center text-gray-500">
                Nenhum veículo encontrado.
            </li>
            @endforelse
        </ul>
    </div>
</div>

<script>
function toggleStatusMenu(id) {
    const menu = document.getElementById(`status-menu-${id}`);
    menu.classList.toggle('hidden');
    
    // Fechar outros menus
    document.querySelectorAll('[id^="status-menu-"]').forEach(otherMenu => {
        if (otherMenu.id !== `status-menu-${id}`) {
            otherMenu.classList.add('hidden');
        }
    });
}

// Fechar menus ao clicar fora
document.addEventListener('click', function(event) {
    if (!event.target.closest('[onclick^="toggleStatusMenu"]')) {
        document.querySelectorAll('[id^="status-menu-"]').forEach(menu => {
            menu.classList.add('hidden');
        });
    }
});
</script>
@endsection
