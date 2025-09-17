<!DOCTYPE html>
<html lang="lv" class="scroll-smooth">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $parent->name }} – Līgas</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="antialiased text-[#F3F4F6] bg-[#111827]">

  <!-- NAVBAR -->
  <nav class="fixed inset-x-0 top-0 z-50 bg-[#111827]/80 backdrop-blur-md">
  <div class="max-w-7xl mx-auto flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">
    {{-- LEFT: Home + LBS Logo --}}
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
      <div class="hidden md:flex space-x-8">
        @foreach(\App\Models\League::whereNull('parent_id')->get() as $league)
          <a href="{{ route('lbs.league.show', $league->id) }}"
             class="font-medium hover:text-[#84CC16] transition">
            {{ $league->name }}
          </a>
        @endforeach
      </div>
      <button id="menu-btn" class="md:hidden focus:outline-none">
        <img src="{{ asset('burger-menu-svgrepo-com.svg') }}"
             alt="Menu"
             class="h-8 w-8 filter invert transition hover:opacity-80"/>
      </button>
    </div>
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

  <main class="pt-24">

    <!-- HERO -->
    @if($heroImage)
      <section
        id="hero"
        class="relative w-full h-[60vh] sm:h-[70vh] lg:h-[75vh] bg-cover bg-center"
        style="background-image: url('{{ Storage::url($heroImage->image_path) }}');"
      >
        <div class="absolute inset-0 bg-black/60"></div>

        <div class="relative z-10 flex h-full items-center justify-center px-6 text-center">
          <div class="max-w-3xl space-y-6 fade-in-section opacity-0 translate-y-6">
            <h1 class="text-3xl sm:text-4xl md:text-5xl font-extrabold text-white drop-shadow-lg">
              {{ $heroImage->title ?? $parent->name }}
            </h1>
            <a href="#news"
               class="inline-block mt-4 px-8 py-3 rounded-full bg-[#84CC16] text-[#111827]
                      font-semibold uppercase tracking-wide hover:bg-[#a6e23a] transition">
              Skatīt jaunākās ziņas
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



    <!-- News Grid -->
    @if($news->isNotEmpty())
      <section id="news" class="py-12 max-w-7xl mx-auto px-4">
        <h2 class="text-3xl font-bold text-white mb-6">Jaunumi no {{ $parent->name }}</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
          @foreach($news as $item)
            <article
              class="group bg-[#F3F4F6] rounded-xl overflow-hidden shadow-lg border-t-4 border-[#F97316]
                     flex flex-col transform transition duration-300 ease-in-out
                     hover:scale-105 hover:shadow-2xl fade-in-section opacity-0 translate-y-6"
            >
              @if($item->preview_image)
                <img loading="lazy"
                     src="{{ $item->preview_image }}"
                     alt="{{ $item->title }}"
                     class="w-full h-48 object-cover transition-transform duration-300 group-hover:scale-110"/>
              @endif
              <div class="p-4 flex flex-col flex-1">
                <h3 class="text-lg font-semibold text-[#111827] mb-1">
                  {{ $item->title }}
                </h3>
                <p class="flex-1 text-[#111827]/90">{{ $item->excerpt }}</p>
                <div class="mt-3 flex items-center justify-between">
                  <time class="text-xs text-[#111827]/70">
                    {{ $item->created_at->format('Y-m-d') }}
                  </time>
                  <a href="{{ route('news.show', $item->id) }}"
                     class="text-[#84CC16] font-medium hover:underline text-sm">
                    Lasīt →
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

  <!-- JS: Mobile menu + fade-in -->
  <script>
    document.getElementById("menu-btn").addEventListener("click", () => {
      document.getElementById("mobile-menu").classList.toggle("hidden");
    });

    document.addEventListener("DOMContentLoaded", () => {
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
  </script>
</body>
</html>
