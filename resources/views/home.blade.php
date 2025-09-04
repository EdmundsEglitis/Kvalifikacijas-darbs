<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NBA Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans">

    <!-- Navbar -->
    <nav class="bg-white shadow-md">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('home') }}" class="text-xl font-bold text-gray-800">NBA Dashboard</a>
                    <a href="{{ route('players') }}" class="text-gray-600 hover:text-gray-900">All Players</a>
                    <a href="{{ route('games') }}" class="text-gray-600 hover:text-gray-900">Upcoming Games</a>
                    <a href="#" class="text-gray-600 hover:text-gray-900">Teams</a>
                    <a href="#" class="text-gray-600 hover:text-gray-900">Stats</a>
                </div>

            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <header class="bg-gradient-to-r from-blue-500 to-indigo-600 text-white py-20">
        <div class="max-w-7xl mx-auto text-center">
            <h1 class="text-4xl md:text-5xl font-bold mb-4">Welcome to the NBA Dashboard</h1>
            <p class="text-lg md:text-xl mb-6">Your one-stop hub for NBA players, teams, schedules, and stats.</p>
            <a href="{{ route('players') }}" class="px-6 py-3 bg-white text-blue-600 font-semibold rounded shadow hover:bg-gray-100">View All Players</a>
        </div>
    </header>

    <!-- Features / Sections -->
    <section class="py-16">
        <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-8 text-center">
            <div class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition">
                <h2 class="text-xl font-bold mb-2">Players</h2>
                <p>Explore all NBA players with detailed stats and profiles.</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition">
                <h2 class="text-xl font-bold mb-2">Games</h2>
                <p>Check upcoming games, schedules, and matchups easily.</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition">
                <h2 class="text-xl font-bold mb-2">Teams</h2>
                <p>Get information about every NBA team, rosters, and standings.</p>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-6 mt-12">
        <div class="max-w-7xl mx-auto text-center">
            <p>&copy; {{ date('Y') }} NBA Dashboard. All rights reserved.</p>
        </div>
    </footer>

</body>
</html>
