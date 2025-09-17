<!DOCTYPE html>
<html lang="lv" class="scroll-smooth">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $subLeague->name }} - Komandas</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const menuBtn = document.getElementById('menu-btn');
      const mobileMenu = document.getElementById('mobile-menu');
      if (menuBtn) {
        menuBtn.addEventListener('click', () => {
          mobileMenu.classList.toggle('hidden');
        });
      }
    });
  </script>
</head>
<body class="antialiased bg-[#111827] text-[#F3F4F6]">

  <!-- Main Navbar -->
  <nav class="bg-[#111827]/80 backdrop-blur-md fixed w-full top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between h-16 items-center">
        <div class="flex items-center space-x-4">
          <a href="{{ route('home') }}">
            <img src="{{ asset('home-icon-silhouette-svgrepo-com.svg') }}" 
                 alt="Home" class="h-8 w-8 filter invert hover:opacity-80 transition">
          </a>
          <a href="{{ route('lbs.home') }}">
            <img src="{{ asset('415986933_1338154883529529_7481933183149808416_n.jpg') }}" 
                 alt="LBS Logo" class="h-10">
          </a>
        </div>

        <!-- Desktop Nav -->
        <div class="hidden md:flex space-x-6">
          @isset($parentLeagues)
            @foreach($parentLeagues as $league)
              <a href="{{ route('lbs.league.show', $league->id) }}" 
                 class="font-medium hover:text-[#84CC16] transition">
                {{ $league->name }}
              </a>
            @endforeach
          @endisset
        </div>

        <!-- Mobile Menu Button -->
        <div class="md:hidden flex items-center">
          <button id="menu-btn" class="focus:outline-none">
            <img src="{{ asset('burger-menu-svgrepo-com.svg') }}" alt="Menu" class="h-8 w-8 filter invert">
          </button>
        </div>
      </div>
    </div>

    <!-- Mobile Nav -->
    <div id="mobile-menu" class="hidden md:hidden bg-[#111827]/90 backdrop-blur-lg">
      <div class="space-y-2 px-4 py-3">
        @isset($parentLeagues)
          @foreach($parentLeagues as $league)
            <a href="{{ route('lbs.league.show', $league->id) }}" 
               class="block font-medium hover:text-[#84CC16] transition">
              {{ $league->name }}
            </a>
          @endforeach
        @endisset
      </div>
    </div>
  </nav>

  <!-- Sub-League Tabs Navbar -->
  <nav class="bg-[#0f172a]/80 backdrop-blur border-b border-white/10 fixed top-16 w-full z-40">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex space-x-6 py-3 text-sm sm:text-base">
        <a href="{{ route('lbs.subleague.news', $subLeague->id) }}" 
           class="hover:text-[#84CC16] transition {{ request()->routeIs('lbs.subleague.news') ? 'text-[#84CC16] font-bold' : 'text-[#F3F4F6]/80' }}">
          JAUNUMI
        </a>
        <a href="{{ route('lbs.subleague.calendar', $subLeague->id) }}" 
           class="hover:text-[#84CC16] transition text-[#F3F4F6]/80">
          KALENDĀRS
        </a>
        <a href="{{ route('lbs.subleague.teams', $subLeague->id) }}" 
           class="text-[#84CC16] font-bold">
          KOMANDAS
        </a>
        <a href="{{ route('lbs.subleague.stats', $subLeague->id) }}" 
           class="hover:text-[#84CC16] transition text-[#F3F4F6]/80">
          STATISTIKA
        </a>
      </div>
    </div>
  </nav>

  <!-- Page Content -->
  <main class="pt-32 max-w-6xl mx-auto px-4 space-y-8">
    <h1 class="text-3xl font-bold text-white">{{ $subLeague->name }} - Komandas</h1>

    @if($teams->isEmpty())
      <p class="mt-4 text-[#F3F4F6]/70">Šai līgai vēl nav komandu.</p>
    @else
      <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        @foreach($teams as $team)
          <div class="bg-[#1f2937] shadow-md rounded-lg p-4 flex flex-col justify-between border border-[#374151] hover:border-[#84CC16] hover:shadow-xl transition">
            <h2 class="text-lg font-semibold text-white">{{ $team->name }}</h2>
            @if($team->logo)
              <img src="{{ asset('storage/' . $team->logo) }}" alt="{{ $team->name }}" class="mt-2 h-24 w-auto object-contain">
            @endif
            <a href="{{ route('lbs.team.overview', $team->id) }}" 
               class="mt-4 text-center bg-[#84CC16] text-[#111827] py-2 rounded font-semibold hover:bg-[#a6e23a] transition">
              Skatīt komandu
            </a>
          </div>
        @endforeach
      </div>
    @endif
  </main>

</body>
</html>
