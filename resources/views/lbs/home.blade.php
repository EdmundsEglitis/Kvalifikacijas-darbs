@extends('layouts.app')
@section('title','LBS — Home')

@push('head')
<style>
    .shine {
    position: relative;
    overflow: hidden;
  }
  .shine::after{
    content:"";
    position:absolute; inset:-40% -120% auto auto;
    width:60%; height:200%;
    background: linear-gradient(120deg, transparent 0%, rgba(255,255,255,.12) 40%, rgba(255,255,255,.02) 60%, transparent 100%);
    transform: translateX(-120%) rotate(12deg);
    transition: transform .6s cubic-bezier(.22,1,.36,1);
    pointer-events:none;
  }
  .shine:hover::after{ transform: translateX(40%) rotate(12deg); }
  .reveal{opacity:0;transform:translateY(18px) scale(.98);filter:saturate(.9);
    transition:opacity .6s cubic-bezier(.22,1,.36,1),transform .6s cubic-bezier(.22,1,.36,1),filter .6s ease;will-change:transform,opacity,filter}
  .reveal.is-visible{opacity:1;transform:translateY(0) scale(1);filter:saturate(1)}
  [data-stagger]>*{opacity:0;transform:translateY(24px) scale(.98)}
  [data-stagger].is-visible>*{animation:rise .7s cubic-bezier(.22,1,.36,1) forwards}
  [data-stagger].is-visible>*:nth-child(1){animation-delay:.04s}
  [data-stagger].is-visible>*:nth-child(2){animation-delay:.10s}
  [data-stagger].is-visible>*:nth-child(3){animation-delay:.16s}
  [data-stagger].is-visible>*:nth-child(4){animation-delay:.22s}
  [data-stagger].is-visible>*:nth-child(5){animation-delay:.28s}
  [data-stagger].is-visible>*:nth-child(6){animation-delay:.34s}
  @keyframes rise{to{opacity:1;transform:none}}

  .accent-underline{position:relative;display:inline-block}
  .accent-underline::after{content:"";position:absolute;left:0;right:0;bottom:-6px;height:3px;border-radius:9999px;
    background:linear-gradient(90deg,#84CC16,#22d3ee,#a78bfa);filter:drop-shadow(0 2px 6px rgba(132,204,22,.45));
    transform-origin:left;transform:scaleX(0);transition:transform .6s cubic-bezier(.22,1,.36,1)}
  .accent-underline.in-view::after{transform:scaleX(1)}

  .tilt{transform:perspective(900px) rotateX(0) rotateY(0) translateY(0);
    transition:transform .2s ease, box-shadow .2s ease, border-color .2s ease, filter .2s ease;will-change:transform}
  .tilt:hover{transform:perspective(900px) rotateX(2deg) rotateY(.8deg) translateY(-4px);box-shadow:0 18px 50px rgba(0,0,0,.35);filter:saturate(1.05)}

  .parallax{transform:translateY(0);will-change:transform;transition:transform .12s linear}

  .ring-glow{box-shadow:0 0 0 0 rgba(132,204,22,0);transition:box-shadow .25s ease}
  .ring-glow:hover{box-shadow:0 0 0 6px rgba(132,204,22,.15)}

  :target{scroll-margin-top:96px}

  @media (prefers-reduced-motion:reduce){
    .reveal,[data-stagger]>*,.tilt,.parallax,.accent-underline::after{transition:none!important;animation:none!important}
  }
    .hero-wrap{ height: clamp(48vh, 64vh, 76vh); }

.hero-img{
  position:absolute; inset:0; width:100%; height:100%;
  object-fit:cover;
  object-position:center var(--hero-pos-y, 35%);
  transform: translateZ(0);
  will-change: transform;
}

.parallax{ transition: transform .12s linear; will-change: transform; }

.hero-overlay-top{
  position:absolute; inset:0;
  background: linear-gradient(to bottom, rgba(0,0,0,.60), rgba(0,0,0,.25) 55%, transparent);
  pointer-events:none;
}
.hero-overlay-bottom{
  position:absolute; inset:0;
  background: linear-gradient(to top, rgba(0,0,0,.50), transparent 40%);
  pointer-events:none;
}

:target{ scroll-margin-top: 96px; }
</style>
@endpush

@section('content')
@if($heroImage)
  <section class="relative w-full overflow-hidden pt-20 hero-wrap">
    <picture>
      <img
        id="heroImgLbs"
        src="{{ Storage::url($heroImage->image_path) }}"
        alt="{{ $heroImage->title ?? 'LBS hero' }}"
        class="hero-img parallax"
        decoding="async"
        fetchpriority="high"
      />
    </picture>

    <div class="hero-overlay-top"></div>
    <div class="hero-overlay-bottom"></div>

    <div class="relative z-10 max-w-7xl mx-auto h-full flex items-center px-4">
      <div class="space-y-6 reveal">
        <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-white drop-shadow">
          {{ $heroImage->title ?? 'LBS Hub' }}
        </h1>
        <p class="text-[#F3F4F6]/90 max-w-xl">
          Spēles, komandas, statistika un ziņas — viss vienuviet.
        </p>
        <div class="flex flex-wrap gap-3">
          <a href="{{ url('/lbs') }}#news"
             class="px-6 py-3 rounded-full bg-[#84CC16] text-[#111827] font-semibold hover:bg-[#a6e23a] transition">
            Skatīt ziņas
          </a>
          <a href="{{ url('/lbs/compare/teams') }}"
             class="px-6 py-3 rounded-full bg-white/10 text-white border border-white/20 hover:bg-white/20 transition">
            Salīdzināt komandas
          </a>
        </div>
      </div>
    </div>
  </section>
@else
  <div class="pt-20"></div>
@endif


  <div class="max-w-7xl mx-auto px-4 space-y-16 pt-8">

    <section class="reveal" data-stagger>
      <h2 class="text-2xl font-bold text-white mb-4">
        <span class="accent-underline">Ātrās saites</span>
      </h2>
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <a href="{{ url('/lbs#news') }}" class="group rounded-2xl p-6 bg-[#0f172a] border border-[#1f2937]/70 hover:border-[#84CC16] transition shadow tilt">
          <div class="text-sm text-[#9CA3AF]">Ziņas</div>
          <div class="mt-2 text-2xl font-bold text-white">Jaunākās</div>
          <div class="mt-3 text-[#F3F4F6]/80">Aktualitātes no Latvijas basketbola.</div>
          <div class="mt-4 text-[#84CC16] font-semibold">Skatīt →</div>
        </a>

        <a href="{{ url('/lbs/league/1') }}" class="group rounded-2xl p-6 bg-[#0f172a] border border-[#1f2937]/70 hover:border-[#84CC16] transition shadow tilt">
          <div class="text-sm text-[#9CA3AF]">Līga</div>
          <div class="mt-2 text-2xl font-bold text-white">Līga #1</div>
          <div class="mt-3 text-[#F3F4F6]/80">Sadaļa par konkrēto līgu.</div>
          <div class="mt-4 text-[#84CC16] font-semibold">Atvērt →</div>
        </a>

        <a href="{{ url('/lbs/compare/teams') }}" class="group rounded-2xl p-6 bg-[#0f172a] border border-[#1f2937]/70 hover:border-[#84CC16] transition shadow tilt">
          <div class="text-sm text-[#9CA3AF]">Komandas</div>
          <div class="mt-2 text-2xl font-bold text-white">Salīdzināt</div>
          <div class="mt-3 text-[#F3F4F6]/80">Salīdzini sezonas un metrikas.</div>
          <div class="mt-4 text-[#84CC16] font-semibold">Atvērt →</div>
        </a>

        <a href="{{ url('/lbs/compare/players') }}" class="group rounded-2xl p-6 bg-[#0f172a] border border-[#1f2937]/70 hover:border-[#84CC16] transition shadow tilt">
          <div class="text-sm text-[#9CA3AF]">Spēlētāji</div>
          <div class="mt-2 text-2xl font-bold text-white">Salīdzināt</div>
          <div class="mt-3 text-[#F3F4F6]/80">Salīdzini spēlētāju statistiku.</div>
          <div class="mt-4 text-[#84CC16] font-semibold">Atvērt →</div>
        </a>
      </div>
    </section>

    @if(!empty($upcomingGames) && $upcomingGames->isNotEmpty())
      <section class="reveal">
        <h2 class="text-2xl font-bold text-white mb-4">
          <span class="accent-underline">Upcoming Games</span>
        </h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6" data-stagger>
          @foreach($upcomingGames as $g)
            <article class="bg-[#0f172a] border border-[#1f2937]/70 rounded-2xl p-5 shadow hover:shadow-xl transition tilt">
              <div class="text-sm text-[#9CA3AF]">
                {{ \Carbon\Carbon::parse($g->tipoff)->format('M d, Y H:i') }}
              </div>

              <div class="mt-3 flex items-center justify-between gap-3">
                <div class="flex items-center gap-2 min-w-0">
                  <div class="h-8 w-8 rounded bg-white/90 grid place-items-center overflow-hidden p-1 ring-glow">
                    @if($g->home_team_logo)
                      <img src="{{ $g->home_team_logo }}" class="h-full w-full object-contain" alt="">
                    @endif
                  </div>
                  <div class="truncate">{{ $g->home_team_name ?? 'Home' }}</div>
                </div>

                <div class="text-[#9CA3AF]">vs</div>

                <div class="flex items-center gap-2 min-w-0 justify-end">
                  <div class="h-8 w-8 rounded bg-white/90 grid place-items-center overflow-hidden p-1 ring-glow">
                    @if($g->away_team_logo)
                      <img src="{{ $g->away_team_logo }}" class="h-full w-full object-contain" alt="">
                    @endif
                  </div>
                  <div class="truncate text-right">{{ $g->away_team_name ?? 'Away' }}</div>
                </div>
              </div>

              <a href="{{ route('lbs.game.detail', $g->id) }}"
                 class="mt-4 inline-flex items-center justify-center w-full px-4 py-2 rounded-lg bg-[#84CC16] text-[#111827] font-semibold hover:bg-[#a3e635] transition tilt">
                Spēles detaļas
              </a>
            </article>
          @endforeach
        </div>
      </section>
    @endif

<section id="news" class="reveal">
  <h2 class="text-2xl sm:text-3xl font-bold text-white text-center mb-8 accent-underline">
    Jaunākās ziņas
  </h2>

  <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8" data-stagger>
    @foreach (['secondary-1','secondary-2'] as $slot)
      @if ($bySlot[$slot] ?? false)
        @php($item = $bySlot[$slot])

        <a href="{{ route('lbs.news.show', $item->id) }}"
           class="group block bg-[#0f172a] border border-[#1f2937]/60 rounded-2xl overflow-hidden
                  shadow-lg hover:shadow-2xl transition-all duration-300 shine
                  hover:-translate-y-1 hover:border-[#84CC16]/60 focus:outline-none focus-visible:ring-2
                  focus-visible:ring-[#84CC16]/50">
          <div class="relative w-full h-[260px] bg-[#0b1220]">
            @if (!empty($item->preview_image))
              <img
                loading="lazy"
                src="{{ $item->preview_image }}"
                alt="{{ $item->title }}"
                class="absolute inset-0 m-auto max-h-full max-w-full object-contain
                       transition-transform duration-500 ease-out
                       group-hover:scale-[1.03]"
              >
            @endif

            {{-- gradient intensifies on hover for readability --}}
            <div class="absolute inset-0 bg-gradient-to-t from-[#0b1220] via-transparent to-transparent
                        transition-opacity duration-300 group-hover:opacity-90 pointer-events-none"></div>
          </div>

          <div class="p-6 flex flex-col flex-1">
            <h3 class="text-2xl font-semibold text-white mb-2
                       transition-colors group-hover:text-[#a7f36c]">
              {{ $item->title }}
            </h3>

            <p class="flex-1 text-[#F3F4F6]/90 line-clamp-3">
              {{ $item->excerpt }}
            </p>

            <div class="mt-4 flex items-center justify-between">
              <time class="text-sm text-[#F3F4F6]/60">
                {{ optional($item->created_at)->format('Y-m-d') }}
              </time>

              <span class="inline-flex items-center gap-2 text-[#84CC16] font-medium">
                Lasīt vairāk
                <svg class="transition-transform duration-300 group-hover:translate-x-1" width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                  <path d="M9 18l6-6-6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </span>
            </div>
          </div>
        </a>
      @endif
    @endforeach
  </div>
</section>
<div class="grid grid-cols-1 sm:grid-cols-3 gap-6" data-stagger>
  @foreach (['slot-1','slot-2','slot-3'] as $slot)
    @if ($bySlot[$slot] ?? false)
      @php($item = $bySlot[$slot])

      <a href="{{ route('lbs.news.show', $item->id) }}"
         class="group block bg-[#0f172a] border border-[#1f2937]/60 rounded-2xl overflow-hidden
                shadow-lg hover:shadow-2xl transition-all duration-300 shine
                hover:-translate-y-1 hover:border-[#84CC16]/60 focus:outline-none
                focus-visible:ring-2 focus-visible:ring-[#84CC16]/50">

        <div class="relative w-full h-[200px] bg-[#0b1220]">
          @if (!empty($item->preview_image))
            <img
              loading="lazy"
              src="{{ $item->preview_image }}"
              alt="{{ $item->title }}"
              class="absolute inset-0 m-auto max-h-full max-w-full object-contain
                     transition-transform duration-500 ease-out group-hover:scale-[1.03]">
          @endif
          <div class="absolute inset-0 bg-gradient-to-t from-[#0b1220] via-transparent to-transparent
                      transition-opacity duration-300 group-hover:opacity-90 pointer-events-none"></div>
        </div>

        <div class="p-5 flex flex-col">
          <h4 class="text-lg font-semibold text-white mb-1 transition-colors group-hover:text-[#a7f36c]">
            {{ $item->title }}
          </h4>

          <p class="text-[#F3F4F6]/90 line-clamp-2">
            {{ $item->excerpt }}
          </p>

          <div class="mt-3 flex items-center justify-between">
            <time class="text-xs text-[#F3F4F6]/60">
              {{ optional($item->created_at)->format('Y-m-d') }}
            </time>
            <span class="text-[#84CC16] font-medium inline-flex items-center gap-1">
              Lasīt
              <svg class="transition-transform duration-300 group-hover:translate-x-1"
                   width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M9 18l6-6-6-6" stroke="currentColor" stroke-width="2"
                      stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </span>
          </div>
        </div>
      </a>
    @endif
  @endforeach
</div>



    <footer class="py-10 text-center text-sm text-[#F3F4F6]/60">&copy; {{ date('Y') }} LBS.</footer>
  </div>
@endsection

@push('scripts')
<script>
  // Subtle parallax for hero (no layout shift)
  (function(){
    const layer=document.getElementById('heroLayerLbs'); if(!layer) return;
    const onScroll=()=>{ const t=Math.min(60,Math.max(-60, window.scrollY*0.05)); layer.style.transform=`translateY(${t}px)` };
    onScroll(); window.addEventListener('scroll',onScroll,{passive:true});
  })();

  // Reveal / stagger / underline activation
  (function(){
    const watchers=document.querySelectorAll('.reveal,[data-stagger],.accent-underline');
    if(!('IntersectionObserver' in window)){ watchers.forEach(el=>el.classList.add('is-visible','in-view')); return; }
    const obs=new IntersectionObserver((ents,o)=>{
      ents.forEach(e=>{
        if(!e.isIntersecting) return;
        e.target.classList.add('is-visible');
        if(e.target.classList.contains('accent-underline')) e.target.classList.add('in-view');
        o.unobserve(e.target);
      });
    },{rootMargin:'0px 0px -10% 0px',threshold:.12});
    watchers.forEach(el=>obs.observe(el));
  })();
</script>
@endpush
