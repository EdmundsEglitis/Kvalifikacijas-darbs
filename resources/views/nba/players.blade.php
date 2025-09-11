<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NBA - Spēlētāji</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const menuBtn = document.getElementById('menu-btn');
            const mobileMenu = document.getElementById('mobile-menu');
            if(menuBtn){
                menuBtn.addEventListener('click', () => {
                    mobileMenu.classList.toggle('hidden');
                });
            }
        });
    </script>
</head>
<body class="bg-gray-100">
    <!-- Navbar -->
    <nav class="bg-white shadow-md fixed w-full top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('home') }}">
                        <img src="{{ asset('home-icon-silhouette-svgrepo-com.svg') }}" class="h-8 w-8 hover:opacity-80">
                    </a>
                    <a href="{{ route('nba.home') }}" class="text-gray-800 font-bold text-lg hover:text-blue-600">
                        NBA
                    </a>
                </div>
                <div class="hidden md:flex space-x-6">
                    <a href="{{ route('nba.players') }}" class="text-gray-700 hover:text-blue-600 font-medium">Spēlētāji</a>
                    <a href="{{ route('nba.games.upcoming') }}" class="text-gray-700 hover:text-blue-600 font-medium">Spēles</a>
                    <a href="{{ route('nba.teams') }}" class="text-gray-700 hover:text-blue-600 font-medium">Komandas</a>
                    <a href="{{ route('nba.stats') }}" class="text-gray-700 hover:text-blue-600 font-medium">Statistika</a>
                </div>
                <div class="md:hidden flex items-center">
                    <button id="menu-btn" class="focus:outline-none">
                        <img src="{{ asset('burger-menu-svgrepo-com.svg') }}" alt="Menu" class="h-8 w-8">
                    </button>
                </div>
            </div>
        </div>
        <div id="mobile-menu" class="hidden md:hidden bg-white shadow-lg">
            <div class="space-y-2 px-4 py-3">
                <a href="{{ route('nba.players') }}" class="block text-gray-700 hover:text-blue-600">Spēlētāji</a>
                <a href="{{ route('nba.games.upcoming') }}" class="block text-gray-700 hover:text-blue-600">Spēles</a>
                <a href="{{ route('nba.teams') }}" class="block text-gray-700 hover:text-blue-600">Komandas</a>
                <a href="{{ route('nba.stats') }}" class="block text-gray-700 hover:text-blue-600">Statistika</a>
            </div>
        </div>
    </nav>

    <!-- Page Content -->
    <main class="pt-20 max-w-7xl mx-auto px-4">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">NBA Spēlētāji</h1>

        @if($players && count($players))
            <div class="overflow-x-auto bg-white shadow rounded-lg">
                <table class="min-w-full text-left text-sm">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-4 py-2 font-semibold text-gray-700">Vārds</th>
                            <th class="px-4 py-2 font-semibold text-gray-700">Komanda</th>
                            <th class="px-4 py-2 font-semibold text-gray-700">Pozīcija</th>
                            <th class="px-4 py-2 font-semibold text-gray-700">Augums</th>
                            <th class="px-4 py-2 font-semibold text-gray-700">Svars</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($players as $player)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-2">
                                    {{ $player['firstname'] }} {{ $player['lastname'] }}
                                </td>
                                <td class="px-4 py-2">
                                    {{ $player['team']['name'] ?? 'N/A' }}
                                </td>
                                <td class="px-4 py-2">
                                    {{ $player['position'] ?? 'N/A' }}
                                </td>
                                <td class="px-4 py-2">
                                    {{ $player['height']['feets'] ?? '-' }}' {{ $player['height']['inches'] ?? '' }}
                                </td>
                                <td class="px-4 py-2">
                                    {{ $player['weight']['pounds'] ?? '-' }} lbs
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-gray-600 mt-4">Nav atrasti spēlētāji.</p>
        @endif
    </main>
</body>
</html>
