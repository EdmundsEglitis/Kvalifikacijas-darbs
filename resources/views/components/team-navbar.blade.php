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

      <div class="hidden md:flex space-x-6">
        @foreach($parentLeagues as $league)
          <a href="{{ route('lbs.league.show', $league->id) }}"
             class="font-medium hover:text-[#84CC16] transition">
            {{ $league->name }}
          </a>
        @endforeach
      </div>

      <div class="md:hidden flex items-center">
        <button id="menu-btn" class="focus:outline-none">
          <img src="{{ asset('burger-menu-svgrepo-com.svg') }}"
               alt="Menu" class="h-8 w-8 filter invert">
        </button>
      </div>
    </div>
  </div>

  <div id="mobile-menu" class="hidden md:hidden bg-[#111827]/90 backdrop-blur-lg">
    <div class="space-y-2 px-4 py-3">
      @foreach($parentLeagues as $league)
        <a href="{{ route('lbs.league.show', $league->id) }}"
           class="block font-medium hover:text-[#84CC16] transition">
          {{ $league->name }}
        </a>
      @endforeach
    </div>
  </div>
</nav>

<nav class="bg-[#0f172a]/80 backdrop-blur border-b border-white/10 fixed top-16 w-full z-40">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex space-x-6 py-3 text-sm sm:text-base">
      <a href="{{ route('lbs.team.show', $team->id) }}"
         class="{{ request()->routeIs('lbs.team.overview') ? 'text-[#84CC16] font-bold' : 'text-[#F3F4F6]/80 hover:text-[#84CC16]' }}">
        PĀRSKATS
      </a>
      <a href="{{ route('lbs.team.games', $team->id) }}"
         class="{{ request()->routeIs('lbs.team.games') ? 'text-[#84CC16] font-bold' : 'text-[#F3F4F6]/80 hover:text-[#84CC16]' }}">
        SPĒLES
      </a>
      <a href="{{ route('lbs.team.players', $team->id) }}"
         class="{{ request()->routeIs('lbs.team.players') ? 'text-[#84CC16] font-bold' : 'text-[#F3F4F6]/80 hover:text-[#84CC16]' }}">
        SPĒLĒTĀJI
      </a>
      <a href="{{ route('lbs.team.stats', $team->id) }}"
         class="{{ request()->routeIs('lbs.team.stats') ? 'text-[#84CC16] font-bold' : 'text-[#F3F4F6]/80 hover:text-[#84CC16]' }}">
        STATISTIKA
      </a>
    </div>
  </div>
</nav>
