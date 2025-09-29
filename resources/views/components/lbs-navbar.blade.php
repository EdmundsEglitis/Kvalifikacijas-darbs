@props(['parentLeagues' => []])

<nav id="lbs-nav"
     class="fixed inset-x-0 top-0 z-50 bg-transparent border-transparent backdrop-blur-md transition-colors duration-300">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center h-16">
      {{-- Left: Home + LBS --}}
      <div class="flex items-center gap-4">
        <a href="{{ route('home') }}" class="inline-flex items-center rounded focus:outline-none focus:ring-2 focus:ring-[#84CC16]/60">
          <img src="{{ asset('home-icon-silhouette-svgrepo-com.svg') }}" alt="Home"
               class="h-8 w-8 filter invert hover:opacity-80 transition" />
        </a>
        <a href="{{ route('lbs.home') }}" class="inline-flex items-center rounded focus:outline-none focus:ring-2 focus:ring-[#84CC16]/60">
          <img src="{{ asset('415986933_1338154883529529_7481933183149808416_n.jpg') }}"
               alt="LBS Logo" class="h-10 rounded" />
        </a>
      </div>

      {{-- Desktop links --}}
      <div class="hidden md:flex items-center gap-8">
        @foreach($parentLeagues as $league)
          @php $active = request()->routeIs('lbs.league.show') && (request()->route('id') == $league->id); @endphp
          <a href="{{ route('lbs.league.show', $league->id) }}"
             class="relative font-medium transition group
                    {{ $active ? 'text-[#84CC16]' : 'text-[#F3F4F6]/90 hover:text-[#84CC16]' }}">
            {{ $league->name }}
            <span class="pointer-events-none absolute left-0 -bottom-1 h-[2px] w-0 bg-[#84CC16] transition-all group-hover:w-full {{ $active ? 'w-full' : '' }}"></span>
          </a>
        @endforeach
      </div>

      {{-- Mobile menu button --}}
      <button id="menu-btn"
              class="md:hidden inline-flex items-center justify-center rounded p-1 focus:outline-none focus:ring-2 focus:ring-[#84CC16]/60"
              aria-expanded="false" aria-controls="mobile-menu" aria-label="Toggle menu">
        <img src="{{ asset('burger-menu-svgrepo-com.svg') }}" alt="" class="h-8 w-8 filter invert" />
      </button>
    </div>
  </div>

  {{-- Mobile drawer --}}
  <div id="mobile-menu" class="hidden md:hidden bg-[#111827]/95 backdrop-blur-lg border-t border-[#374151]">
    <div class="px-4 py-3 space-y-2">
      @foreach($parentLeagues as $league)
        <a href="{{ route('lbs.league.show', $league->id) }}"
           class="block rounded px-3 py-2 font-medium transition text-[#F3F4F6]/90 hover:text-[#111827] hover:bg-[#84CC16]">
          {{ $league->name }}
        </a>
      @endforeach
    </div>
  </div>
</nav>
