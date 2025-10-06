@extends('layouts.app')

@section('title', $news->title)

@push('head')
<style>
  .reveal {
    opacity: 0;
    transform: translateY(16px) scale(.985);
    transition: opacity .6s cubic-bezier(.22,1,.36,1),
                transform .6s cubic-bezier(.22,1,.36,1);
    will-change: opacity, transform;
  }
  .reveal.is-visible {
    opacity: 1;
    transform: none;
  }

  .accent-underline { position: relative; display:inline-block; }
  .accent-underline::after{
    content:""; position:absolute; left:0; right:0; bottom:-8px; height:3px; border-radius:9999px;
    background: linear-gradient(90deg,#84CC16, #22d3ee, #a78bfa);
    transform: scaleX(0); transform-origin: left;
    transition: transform .6s cubic-bezier(.22,1,.36,1);
  }
  .accent-underline.in-view::after { transform: scaleX(1); }

  #readProgress {
    position: fixed; top: 0; left: 0; right: 0; height: 3px; z-index: 50;
    background: linear-gradient(90deg, #84CC16, #22d3ee, #a78bfa);
    transform: scaleX(0); transform-origin: left; opacity: .85;
    transition: transform .08s linear;
  }

  .prose :where(img){ border-radius: .75rem; border: 1px solid rgba(255,255,255,.06); }
  .prose :where(blockquote){
    border-left: 4px solid rgba(132,204,22,.6);
    background: rgba(255,255,255,.03);
    padding: .75rem 1rem; border-radius: .5rem;
  }
  .prose :where(code){
    background: rgba(255,255,255,.06);
    padding: .15rem .4rem; border-radius: .35rem;
  }
  .prose :where(pre code){
    background: transparent; padding: 0; border-radius: 0;
  }

  @media (prefers-reduced-motion: reduce) {
    .reveal, .accent-underline::after, #readProgress { transition: none !important; animation: none !important; }
  }
</style>
@endpush

@section('content')
  <div id="readProgress"></div>

  @if(!empty($news->preview_image ?? null))
    <section class="relative h-[36vh] sm:h-[44vh] overflow-hidden">
      <div class="absolute inset-0"
           style="
             background-image:
               linear-gradient(to bottom, rgba(0,0,0,.45), rgba(0,0,0,.55)),
               url('{{ $news->preview_image }}');
             background-size: cover;
             background-position: center;
           ">
      </div>
      <div class="relative h-full max-w-5xl mx-auto px-4 flex items-end">
        <div class="pb-6 sm:pb-10 reveal">
          <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-white drop-shadow">
            {{ $news->title }}
          </h1>
          <div class="mt-3 flex flex-wrap items-center gap-4 text-sm text-[#F3F4F6]/80">
            <time datetime="{{ $news->created_at->toIso8601String() }}" class="inline-flex items-center gap-2">
              <svg width="16" height="16" fill="none" viewBox="0 0 24 24" class="opacity-80">
                <path d="M8 2v3M16 2v3M3.5 9h17M5 12h14v9H5z" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
              Publicēts: {{ $news->created_at->format('Y-m-d H:i') }}
            </time>
            <span class="inline-flex items-center gap-2" id="readTime">
              <svg width="16" height="16" fill="none" viewBox="0 0 24 24" class="opacity-80">
                <path d="M12 6v6l4 2" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
              —
            </span>
          </div>
        </div>
      </div>
    </section>
  @endif

  <article class="max-w-5xl mx-auto px-4 pt-8 pb-16 space-y-8">
    @if(empty($news->preview_image ?? null))
      <header class="space-y-3 reveal">
        <h1 class="text-4xl font-extrabold text-white accent-underline">{{ $news->title }}</h1>
        <div class="flex flex-wrap items-center gap-4 text-sm text-[#F3F4F6]/70">
          <time datetime="{{ $news->created_at->toIso8601String() }}" class="inline-flex items-center gap-2">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" class="opacity-80">
              <path d="M8 2v3M16 2v3M3.5 9h17M5 12h14v9H5z" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            Publicēts: {{ $news->created_at->format('Y-m-d H:i') }}
          </time>
          <span class="inline-flex items-center gap-2" id="readTimeTop">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" class="opacity-80">
              <path d="M12 6v6l4 2" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            —
          </span>
        </div>
      </header>
    @endif

    <div class="reveal">
      <div class="flex flex-wrap items-center gap-3 text-sm">
        <button
          onclick="copyLink()"
          class="px-3 py-1.5 rounded-lg bg-white/10 text-white hover:bg-white/20 border border-white/10 transition">
          Kopēt saiti
        </button>
        <button
          onclick="handleBack()"
          class="px-3 py-1.5 rounded-lg bg-[#84CC16] text-[#111827] font-semibold hover:bg-[#a6e23a] transition">
          ⬅ Atpakaļ
        </button>
      </div>
    </div>

    <div id="articleBody" class="prose prose-invert max-w-none reveal">
      {!! $news->clean_content !!}
    </div>

    <footer class="reveal">
      <div class="pt-6 border-t border-white/10 flex flex-wrap gap-3">
        <button
          onclick="handleBack()"
          class="px-5 py-2.5 rounded-full bg-white/10 text-white hover:bg-white/20 border border-white/10 transition">
          ← Atgriezties
        </button>
        <button
          onclick="copyLink()"
          class="px-5 py-2.5 rounded-full bg-[#84CC16] text-[#111827] font-semibold hover:bg-[#a6e23a] transition">
          Kopēt raksta saiti
        </button>
      </div>
    </footer>
  </article>
@endsection

@push('scripts')
<script>
  function handleBack() {
    if (document.referrer && document.referrer !== window.location.href) {
      window.history.back();
    } else {
      window.location.href = "{{ route('lbs.home') }}";
    }
  }

  function copyLink(){
    const url = window.location.href;
    if (navigator.clipboard && window.isSecureContext) {
      navigator.clipboard.writeText(url).then(() => toast('Saite nokopēta!'));
    } else {
      const ta = document.createElement('textarea');
      ta.value = url; document.body.appendChild(ta);
      ta.select(); document.execCommand('copy'); ta.remove();
      toast('Saite nokopēta!');
    }
  }

  function toast(msg){
    const t = document.createElement('div');
    t.textContent = msg;
    t.className = "fixed left-1/2 -translate-x-1/2 bottom-6 z-50 px-4 py-2 rounded-lg bg-white/10 text-white border border-white/10 backdrop-blur";
    document.body.appendChild(t);
    setTimeout(()=>{ t.style.transition="opacity .4s"; t.style.opacity="0"; }, 1400);
    setTimeout(()=> t.remove(), 1850);
  }

  (function(){
    const reveals = document.querySelectorAll('.reveal');
    const headings = document.querySelectorAll('.accent-underline');

    if (!('IntersectionObserver' in window)) {
      reveals.forEach(el => el.classList.add('is-visible'));
      headings.forEach(h => h.classList.add('in-view'));
      return;
    }

    const obs = new IntersectionObserver((entries,o)=>{
      entries.forEach(e=>{
        if (e.isIntersecting){
          e.target.classList.add('is-visible');
          if (e.target.classList.contains('accent-underline')) e.target.classList.add('in-view');
          o.unobserve(e.target);
        }
      });
    }, { rootMargin: '0px 0px -10% 0px', threshold: 0.12 });

    reveals.forEach(el => obs.observe(el));
    headings.forEach(h => obs.observe(h));
  })();

  (function(){
    const bar = document.getElementById('readProgress');
    const article = document.getElementById('articleBody');
    if (!bar || !article) return;

    const calc = () => {
      const rect = article.getBoundingClientRect();
      const scrollTop = window.scrollY || document.documentElement.scrollTop;
      const top = article.offsetTop;
      const height = article.offsetHeight - window.innerHeight;
      const prog = Math.max(0, Math.min(1, (scrollTop - top) / Math.max(1, height)));
      bar.style.transform = `scaleX(${prog})`;
    };
    calc();
    window.addEventListener('scroll', calc, { passive: true });
    window.addEventListener('resize', calc);

    const text = article.innerText || '';
    const words = text.trim().split(/\s+/).filter(Boolean).length;
    const mins = Math.max(1, Math.round(words / 200));

    const a = document.getElementById('readTime');
    const b = document.getElementById('readTimeTop');
    if (a) a.innerHTML = a.innerHTML.replace('—', `${mins} min lasījums`);
    if (b) b.innerHTML = b.innerHTML.replace('—', `${mins} min lasījums`);
  })();
</script>
@endpush
