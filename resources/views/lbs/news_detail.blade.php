<!DOCTYPE html>
<html lang="lv" class="scroll-smooth">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>{{ $news->title }}</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="antialiased text-[#F3F4F6] bg-[#111827]">

  <!-- NAVBAR -->
  <nav class="fixed inset-x-0 top-0 z-50 bg-[#111827]/80 backdrop-blur-md">
    <div class="max-w-7xl mx-auto flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">
      <div class="flex items-center space-x-3">
        <a href="{{ route('home') }}">
          <img src="{{ asset('home-icon-silhouette-svgrepo-com.svg') }}"
               alt="Home"
               class="h-8 w-8 filter invert transition hover:opacity-80"/>
        </a>
        <a href="{{ route('lbs.home') }}">
          <img src="{{ asset('415986933_1338154883529529_7481933183149808416_n.jpg') }}"
               alt="LBS Logo"
               class="h-10 w-auto transition hover:opacity-80"/>
        </a>
      </div>
      <div class="hidden md:flex space-x-8">
        @foreach($parentLeagues as $league)
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
        @foreach($parentLeagues as $league)
          <a href="{{ route('lbs.league.show', $league->id) }}"
             class="block font-medium hover:text-[#84CC16] transition">
            {{ $league->name }}
          </a>
        @endforeach
      </div>
    </div>
  </nav>

  <!-- MAIN CONTENT -->
  <main class="pt-20 pb-16 max-w-4xl mx-auto px-4 space-y-8">
    <article class="space-y-6">

      <header class="space-y-2">
        <h1 class="text-4xl font-extrabold text-white">
          {{ $news->title }}
        </h1>
        <div class="flex items-center space-x-4 text-sm text-[#F3F4F6]/70">
          <time>Publicēts: {{ $news->created_at->format('Y-m-d H:i') }}</time>
        </div>
      </header>

      <div class="prose prose-invert max-w-none">
        {!! $news->clean_content !!}
      </div>

      <footer>
  <button onclick="handleBack()"
          class="inline-block mt-4 px-6 py-3 rounded-full bg-[#84CC16] text-[#111827]
                 font-semibold hover:bg-[#a6e23a] transition">
    ⬅ Atpakaļ
  </button>
</footer>

<script>
  function handleBack() {
    if (document.referrer && document.referrer !== window.location.href) {
      // Go back to the previous page if it exists
      window.history.back();
    } else {
      // Fallback: go home if there's no history
      window.location.href = "{{ route('lbs.home') }}";
    }
  }
</script>


    </article>
  </main>

  <!-- MOBILE MENU TOGGLE -->
  <script>
    document.getElementById('menu-btn')
      .addEventListener('click', () => {
        document.getElementById('mobile-menu').classList.toggle('hidden');
      });
  </script>
</body>
</html>