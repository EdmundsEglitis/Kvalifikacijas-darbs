<!DOCTYPE html>
<html lang="lv" class="scroll-smooth">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>{{ $subLeague->name }} – Jaunumi</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    .fade-in-section { transition: opacity 0.6s ease-out, transform 0.6s ease-out; }
  </style>
</head>
<body class="antialiased text-[#F3F4F6] bg-[#111827]">

  <!-- NAVBAR (matches home) -->
  <nav class="fixed inset-x-0 top-0 z-50 bg-[#111827]/80 backdrop-blur-md">
    <div class="max-w-7xl mx-auto flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">
      <!-- LEFT: Home + LBS Logo -->
      <div class="flex items-center space-x-3">
        <a href="{{ route('home') }}" class="block">
          <img
            src="{{ asset('home-icon-silhouette-svgrepo-com.svg') }}"
            alt="Home"
            class="h-8 w-8 filter invert transition"
          />
        </a>
        <a href="{{ route('lbs.home') }}" class="block">
          <img
            src="{{ asset('415986933_1338154883529529_7481933183149808416_n.jpg') }}"
            alt="LBS Logo"
            class="h-10"
          />
        </a>
      </div>

      <!-- RIGHT: Leagues (desktop) + Hamburger (mobile) -->
      <div class="flex items-center space-x-4">
        <div class="hidden md:flex space-x-8">
          @foreach($parentLeagues as $league)
            <a
              href="{{ route('lbs.league.show', $league->id) }}"
              class="font-medium hover:text-[#84CC16] transition"
            >
              {{ $league->name }}
            </a>
          @endforeach
        </div>

        <button id="menu-btn" class="md:hidden focus:outline-none">
          <img
            src="{{ asset('burger-menu-svgrepo-com.svg') }}"
            alt="Menu"
            class="h-8 w-8 filter invert transition"
          />
        </button>
      </div>
    </div>

    <!-- MOBILE MENU -->
    <div id="mobile-menu" class="hidden md:hidden bg-[#111827]/90 backdrop-blur-lg">
      <div class="px-4 py-4 space-y-2">
        @foreach($parentLeagues as $league)
          <a
            href="{{ route('lbs.league.show', $league->id) }}"
            class="block font-medium hover:text-[#84CC16] transition"
          >
            {{ $league->name }}
          </a>
        @endforeach
      </div>
    </div>
  </nav>

  <!-- SUB-LEAGUE SECONDARY TABS (dark theme) -->
  <nav class="fixed top-16 inset-x-0 z-40 bg-[#0f172a]/70 backdrop-blur border-b border-white/10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex space-x-6 py-3 text-sm sm:text-base">
        <a href="{{ route('lbs.subleague.news', $subLeague->id) }}"
           class="font-semibold text-[#84CC16]">
          JAUNUMI
        </a>
        <a href="{{ route('lbs.subleague.calendar', $subLeague->id) }}"
           class="text-[#F3F4F6]/80 hover:text-[#84CC16] transition">
          KALENDĀRS
        </a>
        <a href="{{ route('lbs.subleague.teams', $subLeague->id) }}"
           class="text-[#F3F4F6]/80 hover:text-[#84CC16] transition">
          KOMANDAS
        </a>
        <a href="{{ route('lbs.subleague.stats', $subLeague->id) }}"
           class="text-[#F3F4F6]/80 hover:text-[#84CC16] transition">
          STATISTIKA
        </a>
      </div>
    </div>
  </nav>

  <main class="pt-24">

    <!-- HERO (matches home styling; uses sub-league heroImage if provided) -->
    @if(!empty($heroImage))
      <section
        id="hero"
        class="relative w-full h-[60vh] sm:h-[70vh] lg:h-[80vh] bg-fixed bg-cover bg-center"
        style="background-image: url('{{ Storage::url($heroImage->image_path) }}');"
      >
        <div class="absolute inset-0 bg-black/60"></div>
        <div class="relative z-10 flex h-full items-center justify-center px-6 text-center">
          <div class="max-w-3xl space-y-6 fade-in-section opacity-0 translate-y-6">
            @if($heroImage->title)
              <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-extrabold text-white drop-shadow-lg">
                {{ $heroImage->title }}
              </h1>
            @else
              <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-extrabold text-white drop-shadow-lg">
                {{ $subLeague->name }}
              </h1>
            @endif

            <a
              href="#news"
              class="inline-block mt-4 px-8 py-3 rounded-full bg-[#84CC16] text-[#111827]
                     font-semibold uppercase tracking-wide hover:bg-[#a6e23a] transition"
            >
              Skatīt jaunākās ziņas
            </a>
          </div>
        </div>
      </section>
    @endif

    <!-- NEWS GRID (matches home color palette and layout) -->
    <section id="news" class="py-16 bg-[#111827]">
      <div class="max-w-7xl mx-auto px-4 space-y-12">
        <h2 class="text-3xl font-bold text-white text-center fade-in-section opacity-0 translate-y-6">
          {{ $subLeague->name }} – Jaunākās ziņas
        </h2>

        <!-- Secondary Panels -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          @foreach(['secondary-1','secondary-2'] as $slot)
            @if(($bySlot[$slot] ?? null) && ($bySlot[$slot]->preview_image ?? null))
              <article
                class="group bg-[#F3F4F6] rounded-xl overflow-hidden shadow-lg border-t-4 border-[#F97316]
                       flex flex-col transform transition-transform duration-300 ease-in-out
                       hover:scale-105 hover:shadow-2xl fade-in-section opacity-0 translate-y-6"
              >
                <img
                  loading="lazy"
                  src="{{ $bySlot[$slot]->preview_image }}"
                  alt="{{ $bySlot[$slot]->title }}"
                  class="w-full h-60 object-cover transition-transform duration-300 group-hover:scale-110"
                />
                <div class="p-6 flex flex-col flex-1">
                  <h3 class="text-2xl font-semibold text-[#111827] mb-2">
                    {{ $bySlot[$slot]->title }}
                  </h3>
                  @if(!empty($bySlot[$slot]->excerpt))
                    <p class="flex-1 text-[#111827]/90">
                      {{ $bySlot[$slot]->excerpt }}
                    </p>
                  @endif
                  <div class="mt-4 flex items-center justify-between">
                    <time class="text-sm text-[#111827]/70">
                      {{ optional($bySlot[$slot]->created_at)->format('Y-m-d') }}
                    </time>
                    <a href="{{ route('news.show', $bySlot[$slot]->id) }}"
                       class="text-[#84CC16] font-medium hover:underline">
                      Lasīt vairāk →
                    </a>
                  </div>
                </div>
              </article>
            @endif
          @endforeach
        </div>

        <!-- Three Small Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
          @foreach(['slot-1','slot-2','slot-3'] as $slot)
            @if(($bySlot[$slot] ?? null) && ($bySlot[$slot]->preview_image ?? null))
              <article
                class="group bg-[#F3F4F6] rounded-xl overflow-hidden shadow-lg border-t-4 border-[#F97316]
                       flex flex-col transform transition-transform duration-300 ease-in-out
                       hover:scale-105 hover:shadow-2xl fade-in-section opacity-0 translate-y-6"
              >
                <img
                  loading="lazy"
                  src="{{ $bySlot[$slot]->preview_image }}"
                  alt="{{ $bySlot[$slot]->title }}"
                  class="w-full h-48 object-cover transition-transform duration-300 group-hover:scale-110"
                />
                <div class="p-4 flex flex-col flex-1">
                  <h4 class="text-lg font-semibold text-[#111827] mb-1">
                    {{ $bySlot[$slot]->title }}
                  </h4>
                  @if(!empty($bySlot[$slot]->excerpt))
                    <p class="flex-1 text-[#111827]/90">
                      {{ $bySlot[$slot]->excerpt }}
                    </p>
                  @endif
                  <div class="mt-3 flex items-center justify-between">
                    <time class="text-xs text-[#111827]/70">
                      {{ optional($bySlot[$slot]->created_at)->format('Y-m-d') }}
                    </time>
                    <a href="{{ route('news.show', $bySlot[$slot]->id) }}"
                       class="text-[#84CC16] font-medium hover:underline text-sm">
                      Lasīt →
                    </a>
                  </div>
                </div>
              </article>
            @endif
          @endforeach
        </div>

        @if(empty($bySlot['secondary-1']) && empty($bySlot['secondary-2'])
            && empty($bySlot['slot-1']) && empty($bySlot['slot-2']) && empty($bySlot['slot-3']))
          <p class="text-center text-[#F3F4F6]/70">Šeit šobrīd nav jaunumu.</p>
        @endif
      </div>
    </section>
  </main>

  <script>
    // Mobile menu toggle
    document.addEventListener('DOMContentLoaded', () => {
      const btn = document.getElementById('menu-btn');
      const menu = document.getElementById('mobile-menu');
      if (btn && menu) btn.addEventListener('click', () => menu.classList.toggle('hidden'));
    });

    // Fade-in on scroll (matches home behavior)
    const observer = new IntersectionObserver(
      entries => entries.forEach(e => {
        if (e.isIntersecting) {
          e.target.classList.remove('opacity-0','translate-y-6');
          observer.unobserve(e.target);
        }
      }),
      { threshold: 0.15 }
    );
    document.querySelectorAll('.fade-in-section').forEach(el => observer.observe(el));
  </script>

</body>
</html>
