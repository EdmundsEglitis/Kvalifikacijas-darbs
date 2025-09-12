<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $team->name }} - Spēlētāji</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

<!-- Main Navbar -->
<nav class="bg-white shadow-md fixed w-full top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">
            <div class="flex items-center space-x-4">
                <a href="{{ route('home') }}">
                    <img src="{{ asset('home-icon-silhouette-svgrepo-com.svg') }}" alt="Home" class="h-8 w-8 hover:opacity-80">
                </a>
                <a href="{{ route('lbs.home') }}">
                    <img src="{{ asset('415986933_1338154883529529_7481933183149808416_n.jpg') }}" alt="LBS Logo" class="h-10 w-auto">
                </a>
            </div>
            <div class="hidden md:flex space-x-6">
                @foreach($parentLeagues as $league)
                    <a href="{{ route('lbs.league.show', $league->id) }}" class="text-gray-700 hover:text-blue-600 font-medium">{{ $league->name }}</a>
                @endforeach
            </div>
            <div class="md:hidden flex items-center">
                <button id="menu-btn" class="focus:outline-none">
                    <img src="{{ asset('burger-menu-svgrepo-com.svg') }}" alt="Menu" class="h-8 w-8">
                </button>
            </div>
        </div>
    </div>
</nav>

<!-- Team Navbar -->
<nav class="bg-gray-50 shadow-inner fixed top-16 w-full z-40">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex space-x-6 py-3">
            <a href="{{ route('lbs.team.overview', $team->id) }}" class="{{ request()->routeIs('lbs.team.overview') ? 'text-blue-600 font-bold' : 'text-gray-700 hover:text-blue-600' }}">PĀRSKATS</a>
            <a href="{{ route('lbs.team.games', $team->id) }}" class="{{ request()->routeIs('lbs.team.games') ? 'text-blue-600 font-bold' : 'text-gray-700 hover:text-blue-600' }}">SPĒLES</a>
            <a href="{{ route('lbs.team.players', $team->id) }}" class="{{ request()->routeIs('lbs.team.players') ? 'text-blue-600 font-bold' : 'text-gray-700 hover:text-blue-600' }}">SPĒLĒTĀJI</a>
            <a href="{{ route('lbs.team.stats', $team->id) }}" class="{{ request()->routeIs('lbs.team.stats') ? 'text-blue-600 font-bold' : 'text-gray-700 hover:text-blue-600' }}">STATISTIKA</a>
        </div>
    </div>
</nav>

<main class="pt-32 max-w-6xl mx-auto px-4">

    <!-- Team Logo + Name -->
    <div class="flex flex-col items-center space-y-4">
        @if($team->logo)
            <img src="{{ asset('storage/' . $team->logo) }}" alt="{{ $team->name }}" class="h-24 w-24 object-contain rounded shadow">
        @endif
        <h1 class="text-3xl font-bold text-gray-800">{{ $team->name }}</h1>
    </div>

    <h2 class="text-2xl font-semibold text-gray-800 mt-8">Spēlētāji</h2>

    @if($team->players->isEmpty())
        <p class="mt-2 text-gray-500 text-center">Šai komandai nav pievienotu spēlētāju.</p>
    @else
        <ul class="mt-4 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach($team->players as $player)
                <li class="p-4 bg-white shadow rounded-lg flex flex-col items-center">
                    @if($player->photo && file_exists(storage_path('app/public/' . $player->photo)))
                        <img src="{{ asset('storage/' . $player->photo) }}" alt="{{ $player->name }}" class="h-20 w-20 object-cover rounded-full mb-2">
                    @elseif($player->photo_url)
                        <img src="{{ $player->photo_url }}" alt="{{ $player->name }}" class="h-20 w-20 object-cover rounded-full mb-2">
                    @else
                        <div class="h-20 w-20 bg-gray-200 rounded-full mb-2 flex items-center justify-center text-gray-400">No Photo</div>
                    @endif
                    <p class="font-medium text-lg">{{ $player->name }}</p>
                    @if($player->jersey_number)
                        <p class="text-gray-600">#{{ $player->jersey_number }}</p>
                    @endif
                    @if($player->height)
                        <p class="text-gray-500 text-sm">{{ $player->height }} cm</p>
                    @endif
                </li>
            @endforeach
        </ul>
    @endif
</main>
</body>
</html>
