<!DOCTYPE html>
<html lang="lv">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>{{ $subLeague->name }}</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const btn = document.getElementById('menu-btn');
      const menu = document.getElementById('mobile-menu');
      btn.addEventListener('click', () => menu.classList.toggle('hidden'));
    });
  </script>
</head>
<body class="bg-gray-100">

  {{-- Main Navbar --}}
  <nav class="fixed inset-x-0 top-0 z-50 bg-white/90 backdrop-blur">
    <div class="max-w-7xl mx-auto flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">
      <div class="flex items-center space-x-4">
        <a href="{{ route('home') }}">
          <img src="{{ asset('home-icon-silhouette-svgrepo-com.svg') }}" alt="Home"
               class="h-8 w-8 hover:opacity-80"/>
        </a>
        <a href="{{ route('lbs.home') }}">
          <img src="{{ asset('415986933_1338154883529529_7481933183149808416_n.jpg') }}"
               alt="LBS Logo" class="h-10 w-auto"/>
        </a>
      </div>

      <div class="hidden md:flex space-x-6">
        @foreach($parentLeagues as $league)
          <a href="{{ route('lbs.league.show', $league->id) }}"
             class="text-gray-700 hover:text-blue-600 font-medium">
            {{ $league->name }}
          </a>
        @endforeach
      </div>

      <button id="menu-btn" class="md:hidden focus:outline-none">
        <img src="{{ asset('burger-menu-svgrepo-com.svg') }}" alt="Menu"
             class="h-8 w-8"/>
      </button>
    </div>
    <div id="mobile-menu" class="hidden md:hidden bg-white shadow-lg">
      <div class="px-4 py-3 space-y-2">
        @foreach($parentLeagues as $league)
          <a href="{{ route('lbs.league.show', $league->id) }}"
             class="block text-gray-700 hover:text-blue-600">
            {{ $league->name }}
          </a>
        @endforeach
      </div>
    </div>
  </nav>

  {{-- Sub-League Tabs --}}
  <nav class="fixed top-16 inset-x-0 z-40 bg-gray-50 shadow-inner">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex space-x-6 py-3">
        @foreach([
          'news'     => 'JAUNUMI',
          'calendar' => 'KALENDĀRS',
          'teams'    => 'KOMANDAS',
          'stats'    => 'STATISTIKA',
        ] as $route => $label)
          <a href="{{ route(\"lbs.subleague.{$route}\", $subLeague->id) }}"
             class="font-medium hover:text-blue-600
                    {{ request()->routeIs(\"lbs.subleague.{$route}\") ? 'text-blue-600 font-bold' : 'text-gray-700' }}">
            {{ $label }}
          </a>
        @endforeach
      </div>
    </div>
  </nav>

  <main class="pt-32 max-w-7xl mx-auto px-4 space-y-12">

    {{-- Hero Banner --}}
    @if($heroImage)
      <section id="hero"
               class="relative w-full h-64 sm:h-80 lg:h-[60vh] bg-cover bg-center"
               style="background-image: url('{{ Storage::url($heroImage->image_path) }}')">
        <div class="absolute inset-0 bg-black/50"></div>
        <div class="relative z-10 flex items-center justify-center h-full px-6 text-center">
          @if($heroImage->title)
            <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-extrabold text-white drop-shadow-lg">
              {{ $heroImage->title }}
            </h1>
          @endif
        </div>
      </section>
    @endif

    {{-- News Grid --}}
    <section id="news" class="space-y-12">

      {{-- Secondary (2 cols) --}}
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @if($item = $bySlot['secondary-1'] ?? null)
          <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <a href="{{ route('news.show', $item) }}">
              <img src="{{ $item->hero_image }}" alt="{{ $item->title }}"
                   class="w-full h-64 object-cover"/>
              <div class="p-6">
                <h2 class="text-2xl font-bold text-gray-800">{{ $item->title }}</h2>
              </div>
            </a>
          </div>
        @endif

        @if($item = $bySlot['secondary-2'] ?? null)
          <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <a href="{{ route('news.show', $item) }}">
              <img src="{{ $item->preview_image }}" alt="{{ $item->title }}"
                   class="w-full h-64 object-cover"/>
              <div class="p-6">
                <h2 class="text-2xl font-bold text-gray-800">{{ $item->title }}</h2>
              </div>
            </a>
          </div>
        @endif
      </div>

      {{-- Small Cards (3 cols) --}}
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach(['slot-1','slot-2','slot-3'] as $slot)
          @if($item = $bySlot[$slot] ?? null)
            <div class="bg-white rounded-lg shadow overflow-hidden">
              <a href="{{ route('news.show', $item) }}">
                <img src="{{ $item->preview_image }}" alt="{{ $item->title }}"
                     class="w-full h-40 object-cover"/>
                <div class="p-4">
                  <h3 class="text-lg font-semibold text-gray-800">{{ $item->title }}</h3>
                  <p class="text-gray-600 text-sm mt-2">{{ $item->excerpt }}</p>
                </div>
              </a>
            </div>
          @endif
        @endforeach
      </div>

    </section>

    {{-- Fallback Page Content --}}
    @unless(isset($bySlot['secondary-1']))
      <h1 class="text-3xl font-bold text-gray-800">{{ $subLeague->name }}</h1>
      @yield('subleague-content')
    @endunless

  </main>
</body>
</html>
