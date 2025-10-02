<!DOCTYPE html>
<html lang="lv">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Basketbola Portāls</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    .fade-up { opacity:0; transform:translateY(8px); animation:fadeup .45s ease-out forwards; }
    @keyframes fadeup { to { opacity:1; transform:none; } }

    .fade-in-section{
    opacity:0;
    transform:translateY(12px);
    transition:opacity .5s ease, transform .5s ease;
  }
  .fade-in-section.is-visible{
    opacity:1;
    transform:none;
  }
  </style>
</head>
<body class="bg-[#0B1220] text-[#F3F4F6] min-h-screen">

  <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-14 space-y-10">

    <header class="space-y-2 fade-up" style="animation-delay:60ms">
      <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight">Izvēlies sadaļu</h1>
      <p class="text-sm text-gray-400">Ātrā piekļuve NBA un LBS, jaunākās ziņas un šodienas formā spēlētāji.</p>
    </header>

    {{-- League cards --}}
    <section class="grid grid-cols-1 sm:grid-cols-2 gap-5 fade-up" style="animation-delay:100ms">
    {{-- NBA card --}}
<a href="{{ route('nba.home') }}"
   class="bg-white/5 border border-white/10 rounded-2xl p-6 hover:bg-white/10 transition block">
  <div class="flex items-center gap-4">
    <img src="{{ asset('nba-logo-png-transparent.png') }}" class="w-10 h-10" alt="">
    <div>
      <h2 class="text-2xl font-semibold text-white">NBA</h2>
      <p class="text-gray-400">Amerikas basketbola līga</p>
    </div>
  </div>

  @if($nba)
    <div class="mt-5 rounded-xl bg-[#0f172a] border border-[#1f2937] p-4">
      <div class="text-xs text-gray-400 mb-2">
        Pēdējā spēle — {{ \Carbon\Carbon::parse($nba['date'])->format('Y-m-d') }}
      </div>
      <div class="flex items-center justify-between gap-3">
        <div class="flex items-center gap-2 min-w-0">
          @if($nba['team1']['logo'])
            <img src="{{ $nba['team1']['logo'] }}" class="h-7 w-7 object-contain rounded bg-white p-[2px]" />
          @endif
          <div class="truncate">{{ $nba['team1']['name'] }}</div>
        </div>
        <div class="text-xl font-bold tabular-nums">
          {{ $nba['score1'] }}–{{ $nba['score2'] }}
        </div>
        <div class="flex items-center gap-2 min-w-0 justify-end">
          @if($nba['team2']['logo'])
            <img src="{{ $nba['team2']['logo'] }}" class="h-7 w-7 object-contain rounded bg-white p-[2px]" />
          @endif
          <div class="truncate text-right">{{ $nba['team2']['name'] }}</div>
        </div>
      </div>
    </div>
  @else
    <p class="mt-4 text-sm text-gray-400">Pēdējās spēles informācija tiks pievienota.</p>
  @endif
</a>

{{-- LBS card --}}
<a href="{{ route('lbs.home') }}"
   class="bg-white/5 border border-white/10 rounded-2xl p-6 hover:bg-white/10 transition block">
  <div class="flex items-center gap-4">
    <img src="{{ asset('415986933_1338154883529529_7481933183149808416_n.jpg') }}" class="w-10 h-10 rounded" alt="">
    <div>
      <h2 class="text-2xl font-semibold text-white">LBS</h2>
      <p class="text-gray-400">Latvijas Basketbola Savienība</p>
    </div>
  </div>

  @if($lbs)
    <div class="mt-5 rounded-xl bg-[#0f172a] border border-[#1f2937] p-4">
      <div class="text-xs text-gray-400 mb-1">
        Pēdējā spēle — {{ \Carbon\Carbon::parse($lbs['date'])->format('Y-m-d') }}
      </div>
      <div class="text-[11px] text-gray-500 mb-3">
        Līga: <span class="text-gray-300">{{ $lbs['league'] }}</span>
      </div>

      <div class="flex items-center justify-between gap-3">
        <div class="flex items-center gap-2 min-w-0">
          @if($lbs['team1']['logo'])
            <img src="{{ $lbs['team1']['logo'] }}" class="h-7 w-7 object-contain rounded bg-white p-[2px]" />
          @endif
          <div class="truncate">{{ $lbs['team1']['name'] }}</div>
        </div>
        <div class="text-xl font-bold tabular-nums">
          {{ $lbs['score1'] }}–{{ $lbs['score2'] }}
        </div>
        <div class="flex items-center gap-2 min-w-0 justify-end">
          @if($lbs['team2']['logo'])
            <img src="{{ $lbs['team2']['logo'] }}" class="h-7 w-7 object-contain rounded bg-white p-[2px]" />
          @endif
          <div class="truncate text-right">{{ $lbs['team2']['name'] }}</div>
        </div>
      </div>
    </div>
  @else
    <p class="mt-4 text-sm text-gray-400">Pēdējās spēles informācija tiks pievienota.</p>
  @endif
</a>

    </section>

<!-- NEWS GRID -->
<section id="news" class="py-16 bg-[#111827]">
  <div class="max-w-7xl mx-auto px-4 space-y-12">
    <h2 class="text-3xl font-bold text-white text-center fade-in-section opacity-0 translate-y-6">
      Jaunākās Ziņas
    </h2>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      @foreach(['secondary-1','secondary-2'] as $slot)
        @if(!empty($bySlot[$slot]))
          @php($item = $bySlot[$slot])
          <article
            class="group bg-[#0f172a] rounded-2xl overflow-hidden shadow-lg border border-[#1f2937]/60
                   flex flex-col hover:shadow-2xl fade-in-section opacity-0 translate-y-6 transition">
            <!-- fixed image area with object-contain -->
            <div class="relative w-full h-[260px] bg-[#0b1220]">
              @if(!empty($item->preview_image))
                <img
                  loading="lazy"
                  src="{{ $item->preview_image }}"
                  alt="{{ $item->title }}"
                  class="absolute inset-0 m-auto max-h-full max-w-full object-contain"
                />
              @endif
              <div class="absolute inset-0 bg-gradient-to-t from-[#0b1220] via-transparent to-transparent"></div>
            </div>

            <div class="p-6 flex flex-col flex-1">
              <h3 class="text-2xl font-semibold text-white mb-2 line-clamp-2">
                {{ $item->title }}
              </h3>
              <p class="flex-1 text-[#F3F4F6]/90 line-clamp-3">{{ $item->excerpt }}</p>
              <div class="mt-4 flex items-center justify-between">
                <time class="text-sm text-[#F3F4F6]/60">
                  {{ optional($item->created_at)->format('Y-m-d') }}
                </time>
                <a href="{{ route('lbs.news.show', $item->id) }}"
                   class="inline-flex items-center gap-2 text-[#84CC16] font-medium hover:underline text-2xl">
                  Lasīt vairāk <span>→</span>
                </a>
              </div>
            </div>
          </article>
        @endif
      @endforeach
    </div>
  </div>
</section>

@if($bestOverall && $worstOverall)
  <section class="fade-up" style="animation-delay:240ms">
    <h3 class="text-xl font-semibold mb-3">🏆 Labākais vs sliktākais (kopējais rādītājs)</h3>
    <div class="bg-[#111827] border border-[#1f2937] rounded-2xl p-4 sm:p-6">
      <div class="grid grid-cols-1 sm:grid-cols-3 items-center gap-6">
        {{-- Best overall --}}
        <div class="flex items-center gap-4">
          <a
            href="{{ isset($bestOverall->player_id)
                      ? route('nba.player.show', $bestOverall->player_id)
                      : (isset($bestOverall->id) ? route('nba.player.show', $bestOverall->id) : '#') }}"
            class="shrink-0"
          >
            @if(!empty($bestOverall->headshot))
              <img src="{{ $bestOverall->headshot }}" class="h-14 w-14 rounded-full object-cover ring-1 ring-white/10" alt="">
            @else
              <div class="h-14 w-14 rounded-full bg-white/10"></div>
            @endif
          </a>
          <div>
            <div class="text-xs text-gray-400">Labākais Overall</div>
            <a
              href="{{ isset($bestOverall->player_id)
                        ? route('nba.player.show', $bestOverall->player_id)
                        : (isset($bestOverall->id) ? route('nba.player.show', $bestOverall->id) : '#') }}"
              class="font-semibold hover:text-[#84CC16]"
            >
              {{ $bestOverall->name }}
            </a>
            <div class="text-sm text-gray-400">{{ $bestOverall->team }}</div>
            <div class="text-[#84CC16] font-bold">{{ number_format($bestOverall->overall, 1) }}</div>
            <div class="text-[11px] text-gray-400">PTS + 2*REB + 2*AST + 1.5*STL + 1.5*BLK − 1.5*TOV</div>
          </div>
        </div>

        <div class="hidden sm:flex items-center justify-center">
          <span class="px-3 py-1 rounded-full bg-white/10 border border-white/10 text-sm">VS</span>
        </div>

        {{-- Worst overall --}}
        <div class="flex items-center gap-4 sm:justify-end">
          <a
            href="{{ isset($worstOverall->player_id)
                      ? route('nba.player.show', $worstOverall->player_id)
                      : (isset($worstOverall->id) ? route('nba.player.show', $worstOverall->id) : '#') }}"
            class="shrink-0"
          >
            @if(!empty($worstOverall->headshot))
              <img src="{{ $worstOverall->headshot }}" class="h-14 w-14 rounded-full object-cover ring-1 ring-white/10" alt="">
            @else
              <div class="h-14 w-14 rounded-full bg-white/10"></div>
            @endif
          </a>
          <div class="text-right">
            <div class="text-xs text-gray-400">Zemākais Overall*</div>
            <a
              href="{{ isset($worstOverall->player_id)
                        ? route('nba.player.show', $worstOverall->player_id)
                        : (isset($worstOverall->id) ? route('nba.player.show', $worstOverall->id) : '#') }}"
              class="font-semibold hover:text-[#84CC16]"
            >
              {{ $worstOverall->name }}
            </a>
            <div class="text-sm text-gray-400">{{ $worstOverall->team }}</div>
            <div class="text-[#F97316] font-bold">{{ number_format($worstOverall->overall, 1) }}</div>
          </div>
        </div>
      </div>
      <div class="mt-3 text-xs text-gray-400">* Vismaz 10 spēles {{ date('Y') }}. sezonā.</div>
    </div>
  </section>
@endif



  </main>
  <script>
  (function(){
    const els = document.querySelectorAll('.fade-in-section');
    if (!('IntersectionObserver' in window)) {
      els.forEach(el => el.classList.add('is-visible'));
      return;
    }
    const obs = new IntersectionObserver((entries, o) => {
      entries.forEach(e => {
        if (e.isIntersecting){
          e.target.classList.add('is-visible');
          o.unobserve(e.target);
        }
      });
    }, {rootMargin: '0px 0px -10% 0px', threshold: 0.1});
    els.forEach(el => obs.observe(el));
  })();
</script>
</body>
</html>
