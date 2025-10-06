@props(['class' => ''])

@php
  $menuId = 'nba-mobile-' . uniqid();
  $link = function ($name, $label, $active = null) {
      $isActive = request()->routeIs($active ?? $name);
      $textCls  = $isActive ? 'text-[#84CC16]' : 'text-[#F3F4F6]/90 hover:text-[#84CC16]';
      return ''
        . '<a href="'.e(route($name)).'" class="relative font-medium transition group '.$textCls.'">'
        .   e($label)
        .   '<span class="pointer-events-none absolute left-0 -bottom-1 h-[2px] w-0 bg-[#84CC16] transition-all group-hover:w-full '.($isActive ? 'w-full' : '').'"></span>'
        . '</a>';
  };
@endphp

<nav {{ $attributes->merge(['class' => "w-full $class"]) }}>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between h-16 items-center">
      <div class="flex items-center gap-4">
        <a href="{{ route('home') }}" class="inline-flex items-center rounded focus:outline-none focus:ring-2 focus:ring-[#84CC16]/60">
          <img src="{{ asset('home-icon-silhouette-svgrepo-com.svg') }}" alt="Home" class="h-8 w-8 filter invert hover:opacity-80 transition" />
        </a>
        <a href="{{ route('nba.home') }}" class="inline-flex items-center rounded focus:outline-none focus:ring-2 focus:ring-[#84CC16]/60">
          <img src="{{ asset('nba-logo-black-transparent.png') }}" alt="NBA Logo" class="h-10 w-auto drop-shadow-lg" />
        </a>
      </div>

      <div class="hidden md:flex items-center gap-8 text-sm font-medium">
        {!! $link('nba.players', 'Players') !!}
        {!! $link('nba.games.upcoming', 'Upcoming Games', 'nba.games.*') !!}
        @if(Route::has('nba.games.all')) {!! $link('nba.games.all', 'All Games') !!} @endif
        {!! $link('nba.teams', 'Teams') !!}
        {!! $link('nba.standings.explorer', 'Compare teams') !!}
        {!! $link('nba.compare', 'Compare players') !!}
      </div>

      <button
        type="button"
        data-mobile-btn
        data-target="{{ $menuId }}"
        aria-controls="{{ $menuId }}"
        aria-expanded="false"
        aria-label="Toggle menu"
        class="md:hidden inline-flex items-center justify-center rounded p-1 focus:outline-none focus:ring-2 focus:ring-[#84CC16]/60">
        <img src="{{ asset('burger-menu-svgrepo-com.svg') }}" alt="Menu" class="h-8 w-8 filter invert hover:opacity-80 transition" />
      </button>
    </div>
  </div>

  <div id="{{ $menuId }}" class="hidden md:hidden bg-transparent">
    <div class="px-4 py-3 space-y-2 text-sm font-medium">
      <a href="{{ route('nba.players') }}" class="block rounded px-3 py-2 text-[#F3F4F6]/90 hover:text-[#111827] hover:bg-[#84CC16] transition">Players</a>
      <a href="{{ route('nba.games.upcoming') }}" class="block rounded px-3 py-2 text-[#F3F4F6]/90 hover:text-[#111827] hover:bg-[#84CC16] transition">Upcoming Games</a>
      @if(Route::has('nba.games.all'))
        <a href="{{ route('nba.games.all') }}" class="block rounded px-3 py-2 text-[#F3F4F6]/90 hover:text-[#111827] hover:bg-[#84CC16] transition">All Games</a>
      @endif
      <a href="{{ route('nba.teams') }}" class="block rounded px-3 py-2 text-[#F3F4F6]/90 hover:text-[#111827] hover:bg-[#84CC16] transition">Teams</a>
      <a href="{{ route('nba.standings.explorer') }}" class="block rounded px-3 py-2 text-[#F3F4F6]/90 hover:text-[#111827] hover:bg-[#84CC16] transition">Compare teams</a>
      <a href="{{ route('nba.compare') }}" class="block rounded px-3 py-2 text-[#F3F4F6]/90 hover:text-[#111827] hover:bg-[#84CC16] transition">Compare players</a>
    </div>
  </div>
</nav>

<script>
  document.addEventListener('click', function (e) {
    const btn = e.target.closest('[data-mobile-btn]');
    if (!btn) return;

    const id   = btn.getAttribute('data-target');
    const menu = document.getElementById(id);
    if (!menu) return;

    const willOpen = menu.classList.contains('hidden');
    menu.classList.toggle(!willOpen);
    btn.setAttribute('aria-expanded', String(willOpen));

    if (willOpen) {
      const onKey = (ev) => {
        if (ev.key === 'Escape') {
          menu.classList.add('hidden');
          btn.setAttribute('aria-expanded','false');
          cleanup();
        }
      };
      const onAway = (ev) => {
        if (!menu.contains(ev.target) && !btn.contains(ev.target)) {
          menu.classList.add('hidden');
          btn.setAttribute('aria-expanded','false');
          cleanup();
        }
      };
      function cleanup() {
        document.removeEventListener('keydown', onKey);
        document.removeEventListener('click', onAway, true);
      }
      document.addEventListener('keydown', onKey);
      document.addEventListener('click', onAway, true);
    }
  });
</script>
