<nav class="fixed inset-x-0 top-0 z-50 bg-[#111827]/95 backdrop-blur-md shadow">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">
            
            <!-- LEFT: Home + NBA Logo -->
            <div class="flex items-center space-x-4">
                <!-- Home Button -->
                <a href="{{ route('home') }}" class="flex items-center">
                    <img src="{{ asset('home-icon-silhouette-svgrepo-com.svg') }}" 
                         alt="Home" class="h-8 w-8 filter invert hover:opacity-80 transition">
                </a>

                <!-- NBA Logo -->
                <a href="{{ route('nba.home') }}" class="flex items-center">
                    <img src="{{ asset('nba-logo-png-transparent.png') }}" 
                         alt="NBA Logo" class="h-10 w-auto drop-shadow-lg">
                </a>
            </div>

            <!-- DESKTOP NAV -->
            <div class="hidden md:flex space-x-8 text-sm font-medium">
                <a href="{{ route('nba.players') }}" class="text-[#F3F4F6] hover:text-[#84CC16] transition">Players</a>
                <a href="{{ route('nba.games.upcoming') }}" class="text-[#F3F4F6] hover:text-[#84CC16] transition">Upcoming Games</a>
                <a href="{{ route('nba.teams') }}" class="text-[#F3F4F6] hover:text-[#84CC16] transition">Teams</a>
                <a href="{{ route('nba.standings.explorer') }}" class="text-[#F3F4F6] hover:text-[#84CC16] transition">Compare teams</a>
                <a href="{{ route('nba.compare') }}" class="text-[#F3F4F6] hover:text-[#84CC16] transition">Compare players</a>
            </div>

            <!-- MOBILE BUTTON -->
            <div class="md:hidden flex items-center">
                <button id="menu-btn" class="focus:outline-none">
                    <img src="{{ asset('burger-menu-svgrepo-com.svg') }}" alt="Menu" 
                         class="h-8 w-8 filter invert hover:opacity-80 transition">
                </button>
            </div>
        </div>
    </div>

    <!-- MOBILE MENU -->
    <div id="mobile-menu" class="hidden md:hidden bg-[#1f2937] shadow-lg">
        <div class="space-y-2 px-4 py-3 text-sm font-medium">
            <a href="{{ route('nba.players') }}" class="block text-[#F3F4F6] hover:text-[#84CC16] transition">Players</a>
            <a href="{{ route('nba.games.upcoming') }}" class="block text-[#F3F4F6] hover:text-[#84CC16] transition">Upcoming Games</a>
            <a href="{{ route('nba.games.all') }}" class="block text-[#F3F4F6] hover:text-[#84CC16] transition">All Games</a>
            <a href="{{ route('nba.teams') }}" class="block text-[#F3F4F6] hover:text-[#84CC16] transition">Teams</a>
            <a href="{{ route('nba.stats') }}" class="block text-[#F3F4F6] hover:text-[#84CC16] transition">Stats</a>
        </div>
    </div>
</nav>

<script>
    // Toggle mobile menu
    document.getElementById('menu-btn').addEventListener('click', () => {
        document.getElementById('mobile-menu').classList.toggle('hidden');
    });
</script>
