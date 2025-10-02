@extends('layouts.app')
@section('title', 'LBS – Home')

@push('head')
  <style>
    .fade-in-section { transition: opacity .6s ease-out, transform .6s ease-out; }
  </style>
@endpush

@section('content')
  @if($heroImage)
    <section
      id="hero"
      class="relative w-full h-[75vh] sm:h-[80vh] lg:h-screen bg-fixed bg-cover bg-center"
      style="background-image: url('{{ Storage::url($heroImage->image_path) }}');"
    >
      <div class="absolute inset-0 bg-black/60"></div>
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

  {{-- Inspired "Explore" band (hardcoded links) --}}
<section class="py-14 bg-gradient-to-b from-[#0b1220] to-[#111827]">
  <div class="max-w-7xl mx-auto px-4 space-y-8">
    <h2 class="text-2xl sm:text-3xl font-bold text-white text-center">Izpēti LBS sadaļas</h2>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

      {{-- Jaunākās ziņas (anchor on LBS home) --}}
      <a href="{{ url('/lbs') }}#news"
         class="group rounded-2xl p-6 bg-[#0f172a] border border-[#1f2937]/70 hover:border-[#84CC16] transition shadow">
        <div class="text-sm text-[#9CA3AF]">Ziņas</div>
        <div class="mt-2 text-2xl font-bold text-white">Jaunākās</div>
        <div class="mt-3 text-[#F3F4F6]/80">Aktualitātes no Latvijas basketbola.</div>
        <div class="mt-4 text-[#84CC16] font-semibold">Skatīt →</div>
      </a>

      {{-- Konkrēta līga (ID = 1) --}}
      <a href="{{ url('/lbs/league/1') }}"
         class="group rounded-2xl p-6 bg-[#0f172a] border border-[#1f2937]/70 hover:border-[#84CC16] transition shadow">
        <div class="text-sm text-[#9CA3AF]">Līga</div>
        <div class="mt-2 text-2xl font-bold text-white">Līga #1</div>
        <div class="mt-3 text-[#F3F4F6]/80">Pamatinformācija un saturs par līgu.</div>
        <div class="mt-4 text-[#84CC16] font-semibold">Atvērt →</div>
      </a>

      {{-- Komandu salīdzinātājs --}}
      <a href="{{ url('/lbs/compare/teams') }}"
         class="group rounded-2xl p-6 bg-[#0f172a] border border-[#1f2937]/70 hover:border-[#84CC16] transition shadow">
        <div class="text-sm text-[#9CA3AF]">Komandas</div>
        <div class="mt-2 text-2xl font-bold text-white">Salīdzināt</div>
        <div class="mt-3 text-[#F3F4F6]/80">Salīdzini komandas pēc sezonām un metrikām.</div>
        <div class="mt-4 text-[#84CC16] font-semibold">Atvērt →</div>
      </a>

      {{-- Spēlētāju salīdzinātājs --}}
      <a href="{{ url('/lbs/compare/players') }}"
         class="group rounded-2xl p-6 bg-[#0f172a] border border-[#1f2937]/70 hover:border-[#84CC16] transition shadow">
        <div class="text-sm text-[#9CA3AF]">Spēlētāji</div>
        <div class="mt-2 text-2xl font-bold text-white">Salīdzināt</div>
        <div class="mt-3 text-[#F3F4F6]/80">Salīdzini spēlētāju statistiku.</div>
        <div class="mt-4 text-[#84CC16] font-semibold">Atvērt →</div>
      </a>

    </div>
  </div>
</section>

  <section id="news" class="py-16 bg-[#111827]">
    <div class="max-w-7xl mx-auto px-4 space-y-12">
      <h2 class="text-3xl font-bold text-white text-center fade-in-section opacity-0 translate-y-6">
        Jaunākās Ziņas
      </h2>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @foreach(['secondary-1','secondary-2'] as $slot)
          @if($bySlot[$slot] ?? false)
            <article
              class="group bg-[#0f172a] rounded-2xl overflow-hidden shadow-lg border border-[#1f2937]/60
                     flex flex-col hover:shadow-2xl fade-in-section opacity-0 translate-y-6 transition">
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

      <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        @foreach(['slot-1','slot-2','slot-3'] as $slot)
          @if($bySlot[$slot] ?? false)
            <article
              class="group bg-[#0f172a] rounded-2xl overflow-hidden shadow-lg border border-[#1f2937]/60
                     flex flex-col hover:shadow-2xl fade-in-section opacity-0 translate-y-6 transition">
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

  <footer class="py-8 bg-[#111827] text-[#F3F4F6]/70 text-center text-sm fade-in-section opacity-0 translate-y-6">
    &copy; {{ date('Y') }} LBS. Visas tiesības aizsargātas.
  </footer>
@endsection

@push('scripts')
<script>
  document.addEventListener("DOMContentLoaded", function () {
    const els = document.querySelectorAll('.fade-in-section');
    if (!('IntersectionObserver' in window)) {
      els.forEach(el => { el.style.opacity = 1; el.style.transform = 'none'; });
      return;
    }
    const obs = new IntersectionObserver((entries) => {
      entries.forEach((e) => {
        if (e.isIntersecting) {
          e.target.classList.remove('opacity-0','translate-y-6');
          e.target.classList.add('opacity-100','translate-y-0');
          obs.unobserve(e.target);
        }
      });
    }, { threshold: 0.1 });
    els.forEach((el) => obs.observe(el));
  });
</script>
@endpush
