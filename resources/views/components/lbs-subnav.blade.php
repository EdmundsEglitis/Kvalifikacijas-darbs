@props(['subLeague','class'=>''])

@php
  $tabs = [
    ['route'=>'lbs.subleague.news',     'label'=>'JAUNUMI'],
    ['route'=>'lbs.subleague.calendar', 'label'=>'KALENDĀRS'],
    ['route'=>'lbs.subleague.teams',    'label'=>'KOMANDAS'],
    ['route'=>'lbs.subleague.stats',    'label'=>'STATISTIKA'],
  ];
@endphp

<nav {{ $attributes->merge(['class'=>"w-full bg-transparent $class"]) }}
     role="navigation" aria-label="Sub-league">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex items-center gap-2 sm:gap-3 overflow-x-auto py-2">
      @foreach ($tabs as $tab)
        @php
          $isActive = request()->routeIs($tab['route']);
          $base = 'whitespace-nowrap px-4 py-2 rounded-md text-sm font-semibold transition';
          $state = $isActive
            ? 'bg-[#84CC16] text-[#111827]'
            : 'text-[#F3F4F6]/85 hover:text-[#84CC16] hover:bg-[#1f2937]/70';
        @endphp
        <a href="{{ route($tab['route'], $subLeague->id) }}"
           class="{{ $base }} {{ $state }}"
           aria-current="{{ $isActive ? 'page' : 'false' }}">
          {{ $tab['label'] }}
        </a>
      @endforeach
    </div>
  </div>
</nav>
