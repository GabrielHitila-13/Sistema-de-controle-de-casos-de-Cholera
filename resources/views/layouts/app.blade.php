<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistema de Gestão - Cólera Angola')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <!-- Navigation -->
    <nav class="bg-blue-800 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-4">
                    <h1 class="text-xl font-bold">Sistema Cólera Angola</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <span>{{ auth()->user()->name ?? 'Usuário' }}</span>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="bg-red-600 hover:bg-red-700 px-3 py-1 rounded">
                            Sair
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Sidebar -->
    <div class="flex">
        <aside class="w-64 bg-white shadow-md min-h-screen">
            <nav class="mt-8">
                <div class="px-4 space-y-2">
                    <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-2 text-gray-700 hover:bg-blue-50 rounded-lg">
                        <i class="fas fa-chart-dashboard mr-3"></i>
                        Dashboard
                    </a>
                    <a href="{{ route('pacientes.index') }}" class="flex items-center px-4 py-2 text-gray-700 hover:bg-blue-50 rounded-lg">
                        <i class="fas fa-user-injured mr-3"></i>
                        Pacientes
                    </a>
                    <a href="{{ route('estabelecimentos.index') }}" class="flex items-center px-4 py-2 text-gray-700 hover:bg-blue-50 rounded-lg">
                        <i class="fas fa-hospital mr-3"></i>
                        Estabelecimentos
                    </a>
                    <a href="{{ route('gabinetes.index') }}" class="flex items-center px-4 py-2 text-gray-700 hover:bg-blue-50 rounded-lg">
                        <i class="fas fa-building mr-3"></i>
                        Gabinetes
                    </a>
                    <a href="{{ route('veiculos.index') }}" class="flex items-center px-4 py-2 text-gray-700 hover:bg-blue-50 rounded-lg">
                        <i class="fas fa-ambulance mr-3"></i>
                        Veículos
                    </a>
                    <a href="{{ route('pontos-atendimento.index') }}" class="flex items-center px-4 py-2 text-gray-700 hover:bg-blue-50 rounded-lg">
                        <i class="fas fa-map-marker-alt mr-3"></i>
                        Pontos de Atendimento
                    </a>
                </div>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-8">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</body>
</html>