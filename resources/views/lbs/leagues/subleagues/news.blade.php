@extends('layouts.app')
@section('title', $subLeague->name . ' – Jaunumi')

@push('head')
  <style>.fade-in-section{transition:opacity .6s ease-out, transform .6s ease-out;}</style>
@endpush

{{-- Sub-league tabs bar just under the main navbar --}}
@section('subnav')
  <x-lbs-subnav :subLeague="$subLeague" />
@endsection

@section('content')
  {{-- HERO --}}
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
          <a href="#news"
             class="inline-block mt-4 px-8 py-3 rounded-full bg-[#84CC16] text-[#111827]
                    font-semibold uppercase tracking-wide hover:bg-[#a6e23a] transition">
            Skatīt jaunākās ziņas
          </a>
        </div>
      </div>
    </section>
  @endif

  {{-- NEWS GRID --}}
  <section id="news" class="py-16 bg-[#111827]">
    <div class="max-w-7xl mx-auto px-4 space-y-12">
      <h2 class="text-3xl font-bold text-white text-center fade-in-section opacity-0 translate-y-6">
        {{ $subLeague->name }} – Jaunākās ziņas
      </h2>

      {{-- Secondary Panels --}}
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @foreach(['secondary-1','secondary-2'] as $slot)
          @if(($bySlot[$slot] ?? null) && ($bySlot[$slot]->preview_image ?? null))
            <article
              class="group bg-[#F3F4F6] rounded-xl overflow-hidden shadow-lg border-t-4 border-[#F97316]
                     flex flex-col transform transition-transform duration-300 ease-in-out
                     hover:scale-105 hover:shadow-2xl fade-in-section opacity-0 translate-y-6"
            >
              <img loading="lazy" src="{{ $bySlot[$slot]->preview_image }}" alt="{{ $bySlot[$slot]->title }}"
                   class="w-full h-60 object-cover transition-transform duration-300 group-hover:scale-110" />
              <div class="p-6 flex flex-col flex-1">
                <h3 class="text-2xl font-semibold text-[#111827] mb-2">{{ $bySlot[$slot]->title }}</h3>
                @if(!empty($bySlot[$slot]->excerpt))
                  <p class="flex-1 text-[#111827]/90">{{ $bySlot[$slot]->excerpt }}</p>
                @endif
                <div class="mt-4 flex items-center justify-between">
                  <time class="text-sm text-[#111827]/70">
                    {{ optional($bySlot[$slot]->created_at)->format('Y-m-d') }}
                  </time>
                  <a href="{{ route('lbs.news.show', $bySlot[$slot]->id) }}"
                     class="text-[#84CC16] font-medium hover:underline">
                    Lasīt vairāk →
                  </a>
                </div>
              </div>
            </article>
          @endif
        @endforeach
      </div>

      {{-- Three Small Cards --}}
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        @foreach(['slot-1','slot-2','slot-3'] as $slot)
          @if(($bySlot[$slot] ?? null) && ($bySlot[$slot]->preview_image ?? null))
            <article
              class="group bg-[#F3F4F6] rounded-xl overflow-hidden shadow-lg border-t-4 border-[#F97316]
                     flex flex-col transform transition-transform duration-300 ease-in-out
                     hover:scale-105 hover:shadow-2xl fade-in-section opacity-0 translate-y-6"
            >
              <img loading="lazy" src="{{ $bySlot[$slot]->preview_image }}" alt="{{ $bySlot[$slot]->title }}"
                   class="w-full h-48 object-cover transition-transform duration-300 group-hover:scale-110" />
              <div class="p-4 flex flex-col flex-1">
                <h4 class="text-lg font-semibold text-[#111827] mb-1">{{ $bySlot[$slot]->title }}</h4>
                @if(!empty($bySlot[$slot]->excerpt))
                  <p class="flex-1 text-[#111827]/90">{{ $bySlot[$slot]->excerpt }}</p>
                @endif
                <div class="mt-3 flex items-center justify-between">
                  <time class="text-xs text-[#111827]/70">
                    {{ optional($bySlot[$slot]->created_at)->format('Y-m-d') }}
                  </time>
                  <a href="{{ route('lbs.news.show', $bySlot[$slot]->id) }}"
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

  {{-- FOOTER --}}
  <footer class="py-8 bg-[#111827] text-[#F3F4F6]/70 text-center text-sm fade-in-section opacity-0 translate-y-6">
    &copy; {{ date('Y') }} LBS. Visas tiesības aizsargātas.
  </footer>
@endsection

@push('scripts')
<script>
  // Fade-in on scroll
  document.addEventListener('DOMContentLoaded', () => {
    const els = document.querySelectorAll('.fade-in-section');
    if (!('IntersectionObserver' in window)) {
      els.forEach(el => { el.style.opacity = 1; el.style.transform = 'none'; });
      return;
    }
    const obs = new IntersectionObserver((entries) => {
      entries.forEach(e => {
        if (e.isIntersecting) {
          e.target.classList.remove('opacity-0','translate-y-6');
          e.target.classList.add('opacity-100','translate-y-0');
          obs.unobserve(e.target);
        }
      });
    }, { threshold: 0.15 });
    els.forEach(el => obs.observe(el));
  });
</script>
@endpush
