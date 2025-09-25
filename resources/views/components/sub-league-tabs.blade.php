{{-- LBS Main Navbar --}}
<nav id="lbs-nav" class="fixed inset-x-0 top-0 z-50 bg-transparent backdrop-blur-md transition-colors duration-300">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between h-16 items-center">
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

      {{-- Desktop Nav --}}
      <div class="hidden md:flex items-center gap-6">
        @isset($parentLeagues)
          @foreach($parentLeagues as $league)
            @php $active = request()->routeIs('lbs.league.show') && (request()->route('id') == $league->id); @endphp
            <a href="{{ route('lbs.league.show', $league->id) }}"
               class="relative font-medium transition group
                      {{ $active ? 'text-[#84CC16]' : 'text-[#F3F4F6]/90 hover:text-[#84CC16]' }}">
              {{ $league->name }}
              <span class="pointer-events-none absolute left-0 -bottom-1 h-[2px] w-0 bg-[#84CC16] transition-all group-hover:w-full {{ $active ? 'w-full' : '' }}"></span>
            </a>
          @endforeach
        @endisset
      </div>

      {{-- Mobile Menu Button --}}
      <button id="menu-btn"
              class="md:hidden inline-flex items-center justify-center rounded p-1 focus:outline-none focus:ring-2 focus:ring-[#84CC16]/60"
              aria-expanded="false" aria-controls="mobile-menu" aria-label="Toggle menu">
        <img src="{{ asset('burger-menu-svgrepo-com.svg') }}" alt="" class="h-8 w-8 filter invert" />
      </button>
    </div>
  </div>

  {{-- Mobile Nav --}}
  <div id="mobile-menu" class="hidden md:hidden bg-[#111827]/95 backdrop-blur-lg border-t border-[#374151]">
    <div class="px-4 py-3 space-y-2">
      @isset($parentLeagues)
        @foreach($parentLeagues as $league)
          <a href="{{ route('lbs.league.show', $league->id) }}"
             class="block rounded px-3 py-2 font-medium transition
                    text-[#F3F4F6]/90 hover:text-[#111827] hover:bg-[#84CC16]">
            {{ $league->name }}
          </a>
        @endforeach
      @endisset
    </div>
  </div>
</nav>

{{-- Sub-League Tabs (only if a sub-league context exists) --}}
{{-- Sub-League Tabs (transparent -> solid on scroll) --}}
@isset($subLeague)
  <nav id="lbs-subnav"
       class="fixed top-16 inset-x-0 z-40 bg-transparent backdrop-blur-md border-b border-transparent transition-colors duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex gap-2 sm:gap-3 overflow-x-auto py-2 scrollbar-thin scrollbar-thumb-[#374151]">
        @foreach([
          'news'     => 'JAUNUMI',
          'calendar' => 'KALENDĀRS',
          'teams'    => 'KOMANDAS',
          'stats'    => 'STATISTIKA',
        ] as $route => $label)
          @php $active = request()->routeIs("lbs.subleague.$route"); @endphp
          <a href="{{ route("lbs.subleague.$route", $subLeague->id) }}"
             class="whitespace-nowrap px-4 py-2 rounded-md text-sm font-semibold transition
                    {{ $active
                        ? 'bg-[#84CC16] text-[#111827]'
                        : 'text-[#F3F4F6]/85 hover:text-[#84CC16] hover:bg-[#1f2937]/70' }}">
            {{ $label }}
          </a>
        @endforeach
      </div>
    </div>
  </nav>
@endisset

{{-- Scroll behavior for BOTH navbars --}}
<script>
  (function () {
    const main = document.getElementById('lbs-nav');       // main navbar (keep your existing id)
    const sub  = document.getElementById('lbs-subnav');    // secondary tabs bar

    function setSolid(el, solid) {
      if (!el) return;
      if (solid) {
        el.classList.remove('bg-transparent', 'border-transparent');
        el.classList.add('bg-[#111827]/85', 'border-[#1f2937]');
      } else {
        el.classList.add('bg-transparent', 'border-transparent');
        el.classList.remove('bg-[#111827]/85', 'border-[#1f2937]');
      }
    }

    function onScroll() {
      const y = window.scrollY || 0;
      // threshold where both bars become solid
      const threshold = 10;
      setSolid(main, y > threshold);
      setSolid(sub,  y > threshold);
    }

    onScroll();
    window.addEventListener('scroll', onScroll, { passive: true });

    // Mobile toggle (unchanged)
    const btn  = document.getElementById('menu-btn');
    const menu = document.getElementById('mobile-menu');
    if (btn && menu) {
      btn.addEventListener('click', () => {
        const open = !menu.classList.contains('hidden');
        menu.classList.toggle('hidden');
        btn.setAttribute('aria-expanded', String(!open));
      });
    }
  })();
</script>
