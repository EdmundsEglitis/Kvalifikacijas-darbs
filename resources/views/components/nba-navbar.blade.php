<nav class="bg-white shadow-md fixed w-full top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">
            <div class="flex items-center space-x-4">
                <!-- Home Button -->
                <a href="{{ route('home') }}" class="flex items-center">
                    <img src="{{ asset('home-icon-silhouette-svgrepo-com.svg') }}" 
                         alt="Home" class="h-8 w-8 hover:opacity-80">
                </a>

                <!-- NBA Logo -->
                <a href="{{ route('nba.home') }}" class="flex items-center">
                    <img src="{{ asset('nba-logo-png-transparent.png') }}" 
                         alt="NBA Logo" class="h-10 w-auto">
                </a>
            </div>

            <!-- Desktop Nav -->
            <div class="hidden md:flex space-x-8">
                <a href="{{ route('nba.players') }}" class="text-gray-700 hover:text-blue-600">Players</a>
                <a href="{{ route('nba.games.upcoming') }}" class="text-gray-700 hover:text-blue-600">Upcoming Games</a>
                <a href="{{ route('nba.games.all') }}" class="text-gray-700 hover:text-blue-600">All Games</a>
                <a href="{{ route('nba.teams') }}" class="text-gray-700 hover:text-blue-600">Teams</a>
                <a href="{{ route('nba.stats') }}" class="text-gray-700 hover:text-blue-600">Stats</a>
            </div>

            <!-- Mobile Menu Button -->
            <div class="md:hidden flex items-center">
                <button id="menu-btn" class="focus:outline-none">
                    <img src="{{ asset('burger-menu-svgrepo-com.svg') }}" alt="Menu" class="h-8 w-8">
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Nav -->
    <div id="mobile-menu" class="hidden md:hidden bg-white shadow-lg">
        <div class="space-y-2 px-4 py-3">
            <a href="{{ route('nba.players') }}" class="block text-gray-700 hover:text-blue-600">Players</a>
            <a href="{{ route('nba.games.upcoming') }}" class="block text-gray-700 hover:text-blue-600">Upcoming Games</a>
            <a href="{{ route('nba.games.all') }}" class="block text-gray-700 hover:text-blue-600">All Games</a>
            <a href="{{ route('nba.teams') }}" class="block text-gray-700 hover:text-blue-600">Teams</a>
            <a href="{{ route('nba.stats') }}" class="block text-gray-700 hover:text-blue-600">Stats</a>
        </div>
    </div>
</nav>
