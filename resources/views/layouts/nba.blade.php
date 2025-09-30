<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>@yield('title','NBA — Hub')</title>
  <script src="https://cdn.tailwindcss.com"></script>
  @stack('head')
</head>
<body class="antialiased text-[#F3F4F6] bg-[#0B1220]">

  {{-- One blurred wrapper for the navbar (prevents seams) --}}
  <header class="fixed inset-x-0 top-0 z-50">
    <div class="bg-[#0B1220]/60 backdrop-blur-md">
      <x-nba-navbar class="bg-transparent border-0 backdrop-blur-0"/>
    </div>
  </header>

  <main class="pt-16">
    @yield('content')
  </main>

  @stack('scripts')

  {{-- Global burger handler (no hardcoded IDs) --}}
  <script>
    document.addEventListener('click', function (e) {
      const btn = e.target.closest('[data-mobile-btn]');
      if (!btn) return;
      const menuId = btn.getAttribute('aria-controls');
      const menu   = document.getElementById(menuId);
      if (!menu) return;
      const willOpen = menu.classList.contains('hidden');
      menu.classList.toggle('hidden');
      btn.setAttribute('aria-expanded', String(willOpen));
    });
  </script>
</body>
</html>
