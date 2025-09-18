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

{{-- Sub-League Tabs --}}
<nav class="fixed top-16 inset-x-0 z-40 bg-[#1f2937] border-b border-[#374151]">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex space-x-4 overflow-x-auto py-2">
      @foreach([
        'news'     => 'JAUNUMI',
        'calendar' => 'KALENDĀRS',
        'teams'    => 'KOMANDAS',
        'stats'    => 'STATISTIKA',
      ] as $route => $label)
        <a href="{{ route("lbs.subleague.{$route}", $subLeague->id) }}"
           class="whitespace-nowrap px-4 py-2 rounded-md text-sm font-semibold transition
                  {{ request()->routeIs("lbs.subleague.{$route}") 
                      ? 'bg-[#84CC16] text-[#111827]' 
                      : 'text-gray-300 hover:bg-[#374151] hover:text-[#84CC16]' }}">
          {{ $label }}
        </a>
      @endforeach
    </div>
  </div>
</nav>
