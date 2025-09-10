<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subLeague->name }} - Jaunumi</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

<!-- Main Navbar -->
<nav class="bg-white shadow-md fixed w-full top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">
            <div class="flex items-center space-x-4">
                <a href="{{ route('home') }}">
                    <img src="{{ asset('home-icon-silhouette-svgrepo-com.svg') }}" alt="Home" class="h-8 w-8">
                </a>
                <a href="{{ route('lbs.home') }}">
                    <img src="{{ asset('415986933_1338154883529529_7481933183149808416_n.jpg') }}" alt="LBS Logo" class="h-10">
                </a>
            </div>
            <div class="hidden md:flex space-x-6">
                @foreach($parentLeagues as $league)
                    <a href="{{ route('lbs.league.show', $league->id) }}" class="text-gray-700 hover:text-blue-600">
                        {{ $league->name }}
                    </a>
                @endforeach
            </div>
            <div class="md:hidden flex items-center">
                <button id="menu-btn">
                    <img src="{{ asset('burger-menu-svgrepo-com.svg') }}" alt="Menu" class="h-8 w-8">
                </button>
            </div>
        </div>
    </div>
    <div id="mobile-menu" class="hidden md:hidden bg-white shadow-lg">
        <div class="space-y-2 px-4 py-3">
            @foreach($parentLeagues as $league)
                <a href="{{ route('lbs.league.show', $league->id) }}" class="block text-gray-700 hover:text-blue-600">
                    {{ $league->name }}
                </a>
            @endforeach
        </div>
    </div>
</nav>

<!-- Sub-League Tabs Navbar -->
<nav class="bg-gray-50 shadow-inner fixed top-16 w-full z-40">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex space-x-6 py-3">
            <a href="{{ route('lbs.subleague.news', $subLeague->id) }}" class="text-blue-600 font-bold">JAUNUMI</a>
            <a href="{{ route('lbs.subleague.calendar', $subLeague->id) }}" class="text-gray-700 hover:text-blue-600">KALENDARS</a>
            <a href="{{ route('lbs.subleague.teams', $subLeague->id) }}" class="text-gray-700 hover:text-blue-600">KOMANDAS</a>
            <a href="{{ route('lbs.subleague.stats', $subLeague->id) }}" class="text-gray-700 hover:text-blue-600">STATISTIKA</a>
        </div>
    </div>
</nav>

<!-- Page Content -->
<main class="pt-32 max-w-7xl mx-auto px-4">
    <h1 class="text-3xl font-bold text-gray-800">{{ $subLeague->name }} - Jaunumi</h1>
    <p class="mt-4 text-gray-600">Šeit tiks parādīti jaunumi par šo apakšlīgu.</p>
    
    <!-- Example news -->
    <div class="mt-6 space-y-4">
        @forelse($news as $item)
            <div class="p-4 bg-white shadow rounded-lg">
                <h2 class="font-semibold text-lg">{{ $item->title }}</h2>
                <p class="text-gray-600 mt-1">{{ $item->content }}</p>
                <p class="text-gray-400 text-sm mt-1">{{ $item->created_at->format('Y-m-d') }}</p>
            </div>
        @empty
            <p class="text-gray-500">Jaunumu nav.</p>
        @endforelse
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('menu-btn').addEventListener('click', () => {
        document.getElementById('mobile-menu').classList.toggle('hidden');
    });
});
</script>

</body>
</html>
