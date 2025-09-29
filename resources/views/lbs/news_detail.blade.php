<!DOCTYPE html>
<html lang="lv" class="scroll-smooth">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>{{ $news->title }}</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="antialiased text-[#F3F4F6] bg-[#111827]">

<x-lbs-navbar :parentLeagues="$parentLeagues" />

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