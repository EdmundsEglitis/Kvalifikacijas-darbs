<!DOCTYPE html>
<html lang="lv" class="scroll-smooth">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>@yield('title','Basketbola Portāls')</title>
  <script src="https://cdn.tailwindcss.com"></script>
  @stack('head')
</head>
<body class="antialiased text-[#F3F4F6] bg-[#111827]">

  {{-- One blurred wrapper for BOTH nav rows (no seam) --}}
  <header class="fixed inset-x-0 top-0 z-50">
    <div class="bg-[#111827]/10 backdrop-blur-md">
      {{-- Row 1: main navbar, rendered transparent inside --}}
      <x-lbs-navbar class="bg-transparent border-0 backdrop-blur-0" :parentLeagues="$parentLeagues ?? []" />

      {{-- Row 2: subnav (optional), also transparent inside --}}
      @hasSection('subnav')
  <div class="border-b border-white/10">
    @yield('subnav')
  </div>
@endif
    </div>
  </header>

  {{-- Top padding depends on whether the subnav exists --}}
  <main class="@hasSection('subnav') pt-28 @else pt-16 @endif">
    @yield('content')
  </main>

  @stack('scripts')

  @stack('scripts')

<script>
  // Works for any navbar instance; no hardcoded IDs.
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
