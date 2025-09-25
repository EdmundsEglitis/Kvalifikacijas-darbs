<!DOCTYPE html>
<html lang="lv" class="scroll-smooth">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $parent->name }} – Līgas</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    .fade-in-section { transition: opacity .6s ease-out, transform .6s ease-out; }
    .nav-transparent { background-color: transparent; }
    .nav-solid { background-color: rgba(17, 24, 39, .85); } /* #111827 @ ~85% */
  </style>
</head>
<body class="antialiased text-[#F3F4F6] bg-[#111827]">

  <!-- NAVBAR (transparent on top, solid after scroll) -->
  <nav id="site-nav" class="fixed inset-x-0 top-0 z-50 nav-transparent backdrop-blur-md transition-colors duration-300">
    <div class="max-w-7xl mx-auto flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">
      {{-- LEFT: Home + LBS Logo --}}
      <div class="flex items-center space-x-3">
        <a href="{{ route('home') }}" class="block" aria-label="Home">
          <img src="{{ asset('home-icon-silhouette-svgrepo-com.svg') }}" alt="" class="h-8 w-8 filter invert transition"/>
        </a>
        <a href="{{ route('lbs.home') }}" class="block" aria-label="LBS">
          <img src="{{ asset('415986933_1338154883529529_7481933183149808416_n.jpg') }}" alt="LBS Logo" class="h-10 rounded"/>
        </a>
      </div>

      {{-- Desktop links --}}
      <div class="hidden md:flex space-x-8">
        @foreach(\App\Models\League::whereNull('parent_id')->get() as $league)
          <a href="{{ route('lbs.league.show', $league->id) }}"
             class="font-medium hover:text-[#84CC16] transition">
            {{ $league->name }}
          </a>
        @endforeach
      </div>

      {{-- Mobile button --}}
      <button id="menu-btn" class="md:hidden focus:outline-none" aria-label="Menu">
        <img src="{{ asset('burger-menu-svgrepo-com.svg') }}" alt="" class="h-8 w-8 filter invert transition hover:opacity-80"/>
      </button>
    </div>

    {{-- Mobile menu --}}
    <div id="mobile-menu" class="hidden md:hidden bg-[#111827]/90 backdrop-blur-lg">
      <div class="px-4 py-4 space-y-2">
        @foreach(\App\Models\League::whereNull('parent_id')->get() as $league)
          <a href="{{ route('lbs.league.show', $league->id) }}"
             class="block font-medium hover:text-[#84CC16] transition">
            {{ $league->name }}
          </a>
        @endforeach
      </div>
    </div>
  </nav>

  <main class="pt-16"><!-- hero slides under the fixed navbar -->

    <!-- HERO -->
    @if($heroImage)
      <section
        id="hero"
        class="relative -mt-16 w-full h-[60vh] sm:h-[70vh] lg:h-[75vh] bg-cover bg-center"
        style="background-image: url('{{ Storage::url($heroImage->image_path) }}');"
      >
        <div class="absolute inset-0 bg-black/60"></div>

        <div class="relative z-10 flex h-full items-center justify-center px-6 text-center">
          <div class="max-w-3xl space-y-6 fade-in-section opacity-0 translate-y-6">
            <h1 class="text-3xl sm:text-4xl md:text-5xl font-extrabold text-white drop-shadow-lg">
              {{ $heroImage->title ?? $parent->name }}
            </h1>
            <a href="#news"
               class="inline-flex items-center gap-2 justify-center mt-2 px-8 py-3 rounded-full bg-[#84CC16] text-[#111827]
                      font-semibold tracking-wide hover:bg-[#a6e23a] transition">
              Skatīt jaunākās ziņas <span>↓</span>
            </a>
          </div>
        </div>
      </section>
    @endif

    <!-- Sub-League List -->
    <section class="py-16 max-w-7xl mx-auto px-4">
      <h2 class="text-4xl font-extrabold text-white tracking-tight">{{ $parent->name }}</h2>
      <p class="mt-3 text-lg text-[#F3F4F6]/80">Izvēlieties apakšlīgu:</p>

      @if($subLeagues->isEmpty())
        <p class="text-[#F3F4F6]/60 mt-6 italic">Nav atrasta neviena apakšlīga.</p>
      @else
        <ul class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mt-10">
          @foreach($subLeagues as $sub)
            <li>
              <a href="{{ route('lbs.subleague.news', $sub->id) }}"
                 class="block w-full text-center px-6 py-4 rounded-xl
                        bg-[#84CC16] text-[#111827] font-semibold text-lg uppercase
                        shadow-md transition duration-300
                        hover:bg-[#a3e635] hover:shadow-xl hover:scale-105">
                {{ $sub->name }}
              </a>
            </li>
          @endforeach
        </ul>
      @endif
    </section>

    <!-- NEWS GRID -->
    @if($news->isNotEmpty())
      <section id="news" class="py-12 max-w-7xl mx-auto px-4">
        <h2 class="text-3xl font-bold text-white mb-6">Jaunumi no {{ $parent->name }}</h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
          @foreach($news as $item)
            <article
              class="group bg-[#0f172a] rounded-2xl overflow-hidden shadow-lg border border-[#1f2937]/60
                     flex flex-col hover:shadow-2xl fade-in-section opacity-0 translate-y-6 transition"
            >
              <!-- Fixed image area that accepts any aspect ratio -->
              <div class="relative w-full h-[220px] bg-[#0b1220]">
                @if($item->preview_image)
                  <img
                    loading="lazy"
                    src="{{ $item->preview_image }}"
                    alt="{{ $item->title }}"
                    class="absolute inset-0 m-auto max-h-full max-w-full object-contain"
                  />
                @endif
                <div class="absolute inset-0 bg-gradient-to-t from-[#0b1220] via-transparent to-transparent"></div>
              </div>

              <div class="p-5 flex flex-col flex-1">
                <h3 class="text-lg font-semibold text-white mb-1">
                  {{ $item->title }}
                </h3>
                <p class="flex-1 text-[#F3F4F6]/90 line-clamp-3">{{ $item->excerpt }}</p>
                <div class="mt-3 flex items-center justify-between">
                  <time class="text-xs text-[#F3F4F6]/60">
                    {{ $item->created_at->format('Y-m-d') }}
                  </time>
                  <a href="{{ route('news.show', $item->id) }}"
                     class="text-[#84CC16] font-medium hover:underline text-sm inline-flex items-center gap-1">
                    Lasīt <span>→</span>
                  </a>
                </div>
              </div>
            </article>
          @endforeach
        </div>
      </section>
    @endif

    <!-- FOOTER -->
    <footer class="py-8 bg-[#111827] text-[#F3F4F6]/70 text-center text-sm fade-in-section opacity-0 translate-y-6">
      &copy; {{ date('Y') }} LBS. Visas tiesības aizsargātas.
    </footer>
  </main>

  <!-- Scripts: mobile menu, fade-in on scroll, transparent->solid nav -->
  <script>
    // Mobile menu toggle
    document.getElementById("menu-btn").addEventListener("click", function () {
      document.getElementById("mobile-menu").classList.toggle("hidden");
    });

    // Fade-in on scroll
    document.addEventListener("DOMContentLoaded", function () {
      const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            entry.target.classList.remove('opacity-0', 'translate-y-6');
            entry.target.classList.add('opacity-100', 'translate-y-0');
            observer.unobserve(entry.target);
          }
        });
      }, { threshold: 0.1 });

      document.querySelectorAll('.fade-in-section').forEach((el) => observer.observe(el));
    });

    // Navbar transparency
    (function () {
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
      window.addEventListener('scroll', update);
    })();
  </script>
</body>
</html>
