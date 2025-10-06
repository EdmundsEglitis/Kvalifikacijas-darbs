@extends('layouts.app')
@section('title', $parent->name . ' – Līgas')

@push('head')
  <style>
    .fade-in-section { transition: opacity .6s ease-out, transform .6s ease-out; }
  </style>
@endpush

@section('content')
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
             class="inline-flex items-center gap-2 justify-center mt-2 px-8 py-3 rounded-full bg-[#84CC16] text-[#111827]
                    font-semibold tracking-wide hover:bg-[#a6e23a] transition">
            Skatīt jaunākās ziņas <span>↓</span>
          </a>
        </div>
      </div>
    </section>
  @endif

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

  @if($news->isNotEmpty())
    <section id="news" class="py-12 max-w-7xl mx-auto px-4">
      <h2 class="text-3xl font-bold text-white mb-6">Jaunumi no {{ $parent->name }}</h2>

      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($news as $item)
          <article
            class="group bg-[#0f172a] rounded-2xl overflow-hidden shadow-lg border border-[#1f2937]/60
                   flex flex-col hover:shadow-2xl fade-in-section opacity-0 translate-y-6 transition"
          >
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
                <a href="{{ route('lbs.news.show', $item->id) }}"
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
    els.forEach(el => obs.observe(el));
  });
</script>
@endpush
