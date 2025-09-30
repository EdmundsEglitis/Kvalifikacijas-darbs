@props(['class' => ''])

@php
  $menuId = 'nba-mobile-' . uniqid();
  $link = fn($name, $label, $active = null) =>
    '<a href="'.e(route($name)).'" class="'.
    (request()->routeIs($active ?? $name) ? 'text-[#84CC16]' : 'text-[#F3F4F6] hover:text-[#84CC16]').
    ' transition">'.$label.'</a>';
@endphp

<nav {{ $attributes->merge(['class' => "w-full $class"]) }}>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between h-16 items-center">

      {{-- LEFT: Home + NBA --}}
      <div class="flex items-center space-x-4">
        <a href="{{ route('home') }}" class="flex items-center">
          <img src="{{ asset('home-icon-silhouette-svgrepo-com.svg') }}" alt="Home"
               class="h-8 w-8 filter invert hover:opacity-80 transition">
        </a>
        <a href="{{ route('nba.home') }}" class="flex items-center">
          <img src="{{ asset('nba-logo-png-transparent.png') }}" alt="NBA Logo"
               class="h-10 w-auto drop-shadow-lg">
        </a>
      </div>

      {{-- DESKTOP NAV --}}
      <div class="hidden md:flex items-center gap-8 text-sm font-medium">
        {!! $link('nba.players', 'Players') !!}
        {!! $link('nba.games.upcoming', 'Upcoming Games', 'nba.games.*') !!}
        @if(Route::has('nba.games.all')) {!! $link('nba.games.all', 'All Games') !!} @endif
        {!! $link('nba.teams', 'Teams') !!}
        {!! $link('nba.standings.explorer', 'Compare teams') !!}
        {!! $link('nba.compare', 'Compare players') !!}
      </div>

      {{-- MOBILE BUTTON --}}
      <button
        type="button"
        data-mobile-btn
        aria-controls="{{ $menuId }}"
        aria-expanded="false"
        aria-label="Toggle menu"
        class="md:hidden inline-flex items-center justify-center rounded p-1 focus:outline-none focus:ring-2 focus:ring-[#84CC16]/60">
        <img src="{{ asset('burger-menu-svgrepo-com.svg') }}" alt="Menu"
             class="h-8 w-8 filter invert hover:opacity-80 transition">
      </button>
    </div>
  </div>

  {{-- MOBILE MENU (hidden on md+) --}}
  <div id="{{ $menuId }}" class="hidden md:hidden bg-[#111827] shadow-lg">
    <div class="space-y-2 px-4 py-3 text-sm font-medium">
      <a href="{{ route('nba.players') }}" class="block text-[#F3F4F6] hover:text-[#84CC16] transition">Players</a>
      <a href="{{ route('nba.games.upcoming') }}" class="block text-[#F3F4F6] hover:text-[#84CC16] transition">Upcoming Games</a>
      @if(Route::has('nba.games.all'))
        <a href="{{ route('nba.games.all') }}" class="block text-[#F3F4F6] hover:text-[#84CC16] transition">All Games</a>
      @endif
      <a href="{{ route('nba.teams') }}" class="block text-[#F3F4F6] hover:text-[#84CC16] transition">Teams</a>
      <a href="{{ route('nba.standings.explorer') }}" class="block text-[#F3F4F6] hover:text-[#84CC16] transition">Compare teams</a>
      <a href="{{ route('nba.compare') }}" class="block text-[#F3F4F6] hover:text-[#84CC16] transition">Compare players</a>
    </div>
  </div>
</nav>
