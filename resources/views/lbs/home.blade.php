<!DOCTYPE html>
<html lang="lv" class="scroll-smooth">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>LBS – Home</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    /* smooth reveal */
    .fade-in-section { transition: opacity 0.6s ease-out, transform 0.6s ease-out; }

    /* nav states */
    .nav-transparent { background-color: transparent; }
    .nav-solid { background-color: rgba(17, 24, 39, 0.85); } /* #111827 at ~85% */
  </style>
</head>
<body class="antialiased text-[#F3F4F6] bg-[#111827]">

 <x-lbs-navbar :parentLeagues="$parentLeagues" />

  <main class="pt-16"><!-- hero will slide under the 64px navbar -->

    <!-- HERO -->
    @if($heroImage)
      <section
        id="hero"
        class="relative -mt-16 w-full h-[75vh] sm:h-[80vh] lg:h-screen bg-fixed bg-cover bg-center"
        style="background-image: url('{{ Storage::url($heroImage->image_path) }}');"
      >
        {{-- dark overlay --}}
        <div class="absolute inset-0 bg-black/60"></div>

        {{-- centered content --}}
        <div class="relative z-10 flex h-full items-center justify-center px-6 text-center">
          <div class="max-w-3xl space-y-6 fade-in-section opacity-0 translate-y-6">
            @if($heroImage->title)
              <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-extrabold text-white drop-shadow-lg">
                {{ $heroImage->title }}
              </h1>
            @endif
            <div class="flex items-center justify-center gap-4">
              <a href="#news"
                 class="inline-flex items-center gap-2 px-8 py-3 rounded-full bg-[#84CC16] text-[#111827] font-semibold tracking-wide hover:bg-[#a6e23a] transition">
                Skatīt jaunākās ziņas
                <span class="translate-y-[1px]">↓</span>
              </a>
            </div>
          </div>
        </div>
      </section>
    @endif

    <!-- FEATURES -->
    <section class="py-12 bg-[#111827]">
      <div class="max-w-7xl mx-auto px-4 text-center space-y-8">
        <h2 class="text-3xl font-bold text-white fade-in-section opacity-0 translate-y-6">
          Kāpēc izvēlēties LBS?
        </h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
          @foreach([
            ['icon'=>'M12 2L2 22h20L12 2z','title'=>'Live Rezultāti','text'=>'Ik sekundi atjaunināti rezultāti un statistika.'],
            ['icon'=>'M12 2A10 10 0 1 1 2 12 10 10 0 0 1 12 2','title'=>'Ekspertu Analīze','text'=>'Padziļinātas spēļu analīzes un komandu pārskati.'],
            ['icon'=>'M4 4h16v16H4z','title'=>'Mobilā Lietotne','text'=>'Sekojiet līdzi tiešraidēm jebkurā ierīcē.'],
            ['icon'=>'M12 2L22 22H2L12 2z','title'=>'Kopiena','text'=>'Pievienojies fanu forumiem un dalies viedokļos.'],
          ] as $feature)
            <div class="space-y-4 fade-in-section opacity-0 translate-y-6">
              <svg class="mx-auto h-12 w-12 text-[#84CC16]" fill="currentColor" viewBox="0 0 24 24">
                <path d="{{ $feature['icon'] }}"/>
              </svg>
              <h3 class="text-xl font-semibold text-white">{{ $feature['title'] }}</h3>
              <p class="text-[#F3F4F6]/80">{{ $feature['text'] }}</p>
            </div>
          @endforeach
        </div>
      </div>
    </section>

    <!-- NEWS GRID -->
    <section id="news" class="py-16 bg-[#111827]">
      <div class="max-w-7xl mx-auto px-4 space-y-12">
        <h2 class="text-3xl font-bold text-white text-center fade-in-section opacity-0 translate-y-6">
          Jaunākās Ziņas
        </h2>

        <!-- Secondary Panels (two big cards, fixed image height) -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          @foreach(['secondary-1','secondary-2'] as $slot)
            @if($bySlot[$slot] ?? false)
              <article
                class="group bg-[#0f172a] rounded-2xl overflow-hidden shadow-lg border border-[#1f2937]/60
                       flex flex-col hover:shadow-2xl fade-in-section opacity-0 translate-y-6 transition">
                <!-- fixed image area with object-contain -->
                <div class="relative w-full h-[260px] bg-[#0b1220]">
                  <img
                    loading="lazy"
                    src="{{ $bySlot[$slot]->preview_image }}"
                    alt="{{ $bySlot[$slot]->title }}"
                    class="absolute inset-0 m-auto max-h-full max-w-full object-contain"
                  />
                  <div class="absolute inset-0 bg-gradient-to-t from-[#0b1220] via-transparent to-transparent"></div>
                </div>

                <div class="p-6 flex flex-col flex-1">
                  <h3 class="text-2xl font-semibold text-white mb-2">
                    {{ $bySlot[$slot]->title }}
                  </h3>
                  <p class="flex-1 text-[#F3F4F6]/90 line-clamp-3">{{ $bySlot[$slot]->excerpt }}</p>
                  <div class="mt-4 flex items-center justify-between">
                    <time class="text-sm text-[#F3F4F6]/60">
                      {{ $bySlot[$slot]->created_at->format('Y-m-d') }}
                    </time>
                    <a href="{{ route('lbs.news.show', $bySlot[$slot]->id) }}"
                       class="inline-flex items-center gap-2 text-[#84CC16] font-medium hover:underline text-2xl">
                      Lasīt vairāk
                      <span>→</span>
                    </a>
                  </div>
                </div>
              </article>
            @endif
          @endforeach
        </div>

        <!-- Three Small Cards (fixed image height) -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
          @foreach(['slot-1','slot-2','slot-3'] as $slot)
            @if($bySlot[$slot] ?? false)
              <article
                class="group bg-[#0f172a] rounded-2xl overflow-hidden shadow-lg border border-[#1f2937]/60
                       flex flex-col hover:shadow-2xl fade-in-section opacity-0 translate-y-6 transition">
                <!-- fixed image area with object-contain -->
                <div class="relative w-full h-[220px] bg-[#0b1220]">
                  <img
                    loading="lazy"
                    src="{{ $bySlot[$slot]->preview_image }}"
                    alt="{{ $bySlot[$slot]->title }}"
                    class="absolute inset-0 m-auto max-h-full max-w-full object-contain"
                  />
                  <div class="absolute inset-0 bg-gradient-to-t from-[#0b1220] via-transparent to-transparent"></div>
                </div>

                <div class="p-5 flex flex-col flex-1">
                  <h4 class="text-lg font-semibold text-white mb-1">
                    {{ $bySlot[$slot]->title }}
                  </h4>
                  <p class="flex-1 text-[#F3F4F6]/90 line-clamp-2">{{ $bySlot[$slot]->excerpt }}</p>
                  <div class="mt-3 flex items-center justify-between">
                    <time class="text-xs text-[#F3F4F6]/60">
                      {{ $bySlot[$slot]->created_at->format('Y-m-d') }}
                    </time>
                    <a href="{{ route('lbs.news.show', $bySlot[$slot]->id) }}"
                       class="text-[#84CC16] font-medium hover:underline text-2xl inline-flex items-center gap-1">
                      Lasīt <span>→</span>
                    </a>
                  </div>
                </div>
              </article>
            @endif
          @endforeach
        </div>
      </div>
    </section>

    <!-- CTA BANNER -->
    <section class="py-16 bg-[#F97316] fade-in-section opacity-0 translate-y-6">
      <div class="max-w-7xl mx-auto text-center px-4">
        <h2 class="text-3xl md:text-4xl font-bold text-white">
          Pievienojies LBS kopienai!
        </h2>
        <p class="mt-4 text-white/90">
          Abonē mūsu jaunumu vēstuli, lai nekad nepalaistu garām svarīgāko.
        </p>
        <form class="mt-6 flex flex-col sm:flex-row justify-center gap-4">
          <input type="email"
                 placeholder="Tavs e-pasts"
                 class="w-full sm:w-auto px-4 py-2 rounded-full border-0 focus:ring-2 focus:ring-[#84CC16]"
                 required/>
          <button type="submit"
                  class="px-6 py-2 rounded-full bg-[#111827] text-[#F3F4F6] font-semibold hover:bg-[#1f2937] transition">
            Abonēt
          </button>
        </form>
      </div>
    </section>

    <!-- FOOTER -->
    <footer class="py-8 bg-[#111827] text-[#F3F4F6]/70 text-center text-sm fade-in-section opacity-0 translate-y-6">
      &copy; {{ date('Y') }} LBS. Visas tiesības aizsargātas.
    </footer>

  </main>

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

    // Navbar: transparent at top, solid after scroll
    (function() {
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
