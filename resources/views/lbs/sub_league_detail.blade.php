<!DOCTYPE html>
<html lang="lv" class="scroll-smooth">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>{{ $subLeague->name }}</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    .fade-in-section{transition:opacity .6s ease-out,transform .6s ease-out}
    .nav-transparent{background-color:transparent}
    .nav-solid{background-color:rgba(17,24,39,.85)} /* #111827 @ 85% */
  </style>
</head>
<body class="antialiased text-[#F3F4F6] bg-[#111827]">

  {{-- MAIN NAVBAR (transparent on top, solid after scroll) --}}
  <nav id="site-nav" class="fixed inset-x-0 top-0 z-50 nav-transparent backdrop-blur-md transition-colors duration-300">
    <div class="max-w-7xl mx-auto flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">
      <div class="flex items-center space-x-4">
        <a href="{{ route('home') }}" aria-label="Home">
          <img src="{{ asset('home-icon-silhouette-svgrepo-com.svg') }}" alt="" class="h-8 w-8 filter invert hover:opacity-80"/>
        </a>
        <a href="{{ route('lbs.home') }}" aria-label="LBS">
          <img src="{{ asset('415986933_1338154883529529_7481933183149808416_n.jpg') }}" alt="LBS Logo" class="h-10 rounded"/>
        </a>
      </div>

      <div class="hidden md:flex space-x-8">
        @foreach($parentLeagues as $league)
          <a href="{{ route('lbs.league.show', $league->id) }}"
             class="font-medium hover:text-[#84CC16] transition">
            {{ $league->name }}
          </a>
        @endforeach
      </div>

      <button id="menu-btn" class="md:hidden focus:outline-none" aria-label="Menu">
        <img src="{{ asset('burger-menu-svgrepo-com.svg') }}" alt="" class="h-8 w-8 filter invert"/>
      </button>
    </div>
    <div id="mobile-menu" class="hidden md:hidden bg-[#111827]/90 backdrop-blur-lg">
      <div class="px-4 py-3 space-y-2">
        @foreach($parentLeagues as $league)
          <a href="{{ route('lbs.league.show', $league->id) }}"
             class="block font-medium hover:text-[#84CC16] transition">
            {{ $league->name }}
          </a>
        @endforeach
      </div>
    </div>
  </nav>

  {{-- SUB-LEAGUE TABS (sticky under main nav) --}}
  <nav class="fixed top-16 inset-x-0 z-40 bg-[#0b1220] border-b border-[#1f2937]/60">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex flex-wrap gap-6 py-3 text-sm">
        @foreach([
          'news'     => 'JAUNUMI',
          'calendar' => 'KALENDĀRS',
          'teams'    => 'KOMANDAS',
          'stats'    => 'STATISTIKA',
        ] as $route => $label)
          <a href="{{ route("lbs.subleague.$route", $subLeague->id) }}"
             class="uppercase tracking-wide transition
                    {{ request()->routeIs("lbs.subleague.$route") ? 'text-[#84CC16] font-semibold' : 'text-[#F3F4F6]/80 hover:text-[#84CC16]' }}">
            {{ $label }}
          </a>
        @endforeach
      </div>
    </div>
  </nav>

  <main class="pt-32"><!-- room for fixed navs -->

    {{-- HERO (under nav, no top bar) --}}
    @if($heroImage)
      <section id="hero"
               class="relative -mt-16 w-full h-64 sm:h-80 lg:h-[60vh] bg-cover bg-center"
               style="background-image:url('{{ Storage::url($heroImage->image_path) }}')">
        <div class="absolute inset-0 bg-black/55"></div>
        <div class="relative z-10 flex items-center justify-center h-full px-6 text-center">
          @if($heroImage->title)
            <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-extrabold text-white drop-shadow-lg">
              {{ $heroImage->title }}
            </h1>
          @else
            <h1 class="text-3xl sm:text-4xl md:text-5xl font-extrabold text-white drop-shadow-lg">
              {{ $subLeague->name }}
            </h1>
          @endif
        </div>
      </section>
    @endif

    {{-- NEWS GRID --}}
    <section id="news" class="py-12 max-w-7xl mx-auto px-4 space-y-10">

      {{-- Secondary (2 cols) with fixed image area + object-contain --}}
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @if($item = $bySlot['secondary-1'] ?? null)
          <article class="group bg-[#0f172a] rounded-2xl overflow-hidden shadow-lg border border-[#1f2937]/60 fade-in-section opacity-0 translate-y-6 transition">
            <a href="{{ route('news.show', $item->id) }}" class="block">
              <div class="relative w-full h-[260px] bg-[#0b1220]">
                @if($item->hero_image)
                  <img src="{{ $item->hero_image }}" alt="{{ $item->title }}"
                       class="absolute inset-0 m-auto max-h-full max-w-full object-contain"/>
                @endif
                <div class="absolute inset-0 bg-gradient-to-t from-[#0b1220] via-transparent to-transparent"></div>
              </div>
              <div class="p-6">
                <h2 class="text-2xl font-bold text-white">{{ $item->title }}</h2>
                <p class="mt-2 text-[#F3F4F6]/80">{{ $item->excerpt }}</p>
                <div class="mt-3 text-xs text-[#F3F4F6]/60">{{ $item->created_at->format('Y-m-d') }}</div>
              </div>
            </a>
          </article>
        @endif

        @if($item = $bySlot['secondary-2'] ?? null)
          <article class="group bg-[#0f172a] rounded-2xl overflow-hidden shadow-lg border border-[#1f2937]/60 fade-in-section opacity-0 translate-y-6 transition">
            <a href="{{ route('news.show', $item->id) }}" class="block">
              <div class="relative w-full h-[260px] bg-[#0b1220]">
                @if($item->preview_image)
                  <img src="{{ $item->preview_image }}" alt="{{ $item->title }}"
                       class="absolute inset-0 m-auto max-h-full max-w-full object-contain"/>
                @endif
                <div class="absolute inset-0 bg-gradient-to-t from-[#0b1220] via-transparent to-transparent"></div>
              </div>
              <div class="p-6">
                <h2 class="text-2xl font-bold text-white">{{ $item->title }}</h2>
                <p class="mt-2 text-[#F3F4F6]/80">{{ $item->excerpt }}</p>
                <div class="mt-3 text-xs text-[#F3F4F6]/60">{{ $item->created_at->format('Y-m-d') }}</div>
              </div>
            </a>
          </article>
        @endif
      </div>

      {{-- Small Cards (3 cols) with fixed image area + object-contain --}}
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach(['slot-1','slot-2','slot-3'] as $slot)
          @if($item = $bySlot[$slot] ?? null)
            <article class="group bg-[#0f172a] rounded-2xl overflow-hidden shadow-lg border border-[#1f2937]/60 fade-in-section opacity-0 translate-y-6 transition">
              <a href="{{ route('news.show', $item->id) }}" class="block">
                <div class="relative w-full h-[200px] bg-[#0b1220]">
                  @if($item->preview_image)
                    <img src="{{ $item->preview_image }}" alt="{{ $item->title }}"
                         class="absolute inset-0 m-auto max-h-full max-w-full object-contain"/>
                  @endif
                  <div class="absolute inset-0 bg-gradient-to-t from-[#0b1220] via-transparent to-transparent"></div>
                </div>
                <div class="p-5">
                  <h3 class="text-lg font-semibold text-white">{{ $item->title }}</h3>
                  <p class="mt-2 text-[#F3F4F6]/80 line-clamp-3">{{ $item->excerpt }}</p>
                  <div class="mt-3 text-xs text-[#F3F4F6]/60">{{ $item->created_at->format('Y-m-d') }}</div>
                </div>
              </a>
            </article>
          @endif
        @endforeach
      </div>

    </section>

    {{-- Fallback Page Content --}}
    @unless(isset($bySlot['secondary-1']))
      <section class="max-w-7xl mx-auto px-4 pb-16">
        <h1 class="text-3xl font-bold text-white">{{ $subLeague->name }}</h1>
        @yield('subleague-content')
      </section>
    @endunless

  </main>

  <footer class="py-8 bg-[#111827] text-[#F3F4F6]/70 text-center text-sm">
    &copy; {{ date('Y') }} LBS. Visas tiesības aizsargātas.
  </footer>

  <script>
    // Mobile menu
    document.getElementById('menu-btn').addEventListener('click', () =>
      document.getElementById('mobile-menu').classList.toggle('hidden')
    );

    // Fade-in on scroll
    (function(){
      const obs = new IntersectionObserver((entries)=>{
        entries.forEach(e=>{
          if(e.isIntersecting){
            e.target.classList.remove('opacity-0','translate-y-6');
            e.target.classList.add('opacity-100','translate-y-0');
            obs.unobserve(e.target);
          }
        });
      },{threshold:.1});
      document.querySelectorAll('.fade-in-section').forEach(el=>obs.observe(el));
    })();

    // Transparent -> solid navbar
    (function(){
      const nav = document.getElementById('site-nav');
      const update = () => {
        if (window.scrollY > 10) {
          nav.classList.add('nav-solid');
          nav.classList.remove('nav-transparent');
        } else {
          nav.classList.add('nav-transparent');
          nav.classList.remove('nav-solid');
        }
      };
      update();
      window.addEventListener('scroll', update, {passive:true});
    })();
  </script>
</body>
</html>
