<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LBS - Home</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {},
            },
            plugins: [tailwindTypography],
        }
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const menuBtn = document.getElementById('menu-btn');
            const mobileMenu = document.getElementById('mobile-menu');
            menuBtn.addEventListener('click', () => {
                mobileMenu.classList.toggle('hidden');
            });
        });
    </script>
</head>
<body class="bg-gray-100">
    <!-- Navbar -->
    <nav class="bg-white shadow-md fixed w-full top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('home') }}">
                        <img src="{{ asset('home-icon-silhouette-svgrepo-com.svg') }}" class="h-8 w-8 hover:opacity-80">
                    </a>
                    <a href="{{ route('lbs.home') }}">
                        <img src="{{ asset('415986933_1338154883529529_7481933183149808416_n.jpg') }}" class="h-10 w-auto">
                    </a>
                </div>
                <div class="hidden md:flex space-x-6">
                    @foreach($parentLeagues as $league)
                        <a href="{{ route('lbs.league.show', $league->id) }}" class="text-gray-700 hover:text-blue-600 font-medium">
                            {{ $league->name }}
                        </a>
                    @endforeach
                </div>
                <div class="md:hidden flex items-center">
                    <button id="menu-btn" class="focus:outline-none">
                        <img src="{{ asset('burger-menu-svgrepo-com.svg') }}" alt="Menu" class="h-8 w-8">
                    </button>
                </div>
            </div>
        </div>
        <div id="mobile-menu" class="hidden md:hidden bg-white shadow-lg">
            <div class="space-y-2 px-4 py-3">
                @foreach($parentLeagues as $league)
                    <a href="{{ route('lbs.league.show', $league->id) }}" class="block text-gray-700 hover:text-blue-600">
                        {{ $league->name }}
                    </a>
                @endforeach
            </div>
        </div>
    </nav>

    <!-- Page content -->
    <main class="pt-20 max-w-7xl mx-auto px-4">
        <h1 class="text-3xl font-bold text-gray-800">Laipni lūdzam LBS mājaslapā!</h1>
        <p class="mt-4 text-gray-600">Izvēlieties turnīru vai līgu no navigācijas augšā.</p>

        <!-- News Grid -->
        <section class="mt-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Jaunākās ziņas</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($news as $item)
    <div class="bg-white shadow rounded-lg p-4 flex flex-col">
        <h3 class="font-bold text-xl">{{ $item->title }}</h3>

        <div class="mt-2 prose max-w-full">
            @if($item->preview_image)
                <img src="{{ $item->preview_image }}" class="w-32 h-auto rounded-md mb-2" />
            @endif

            {!! $item->excerpt !!}
        </div>

        <a href="{{ route('news.show', $item->id) }}" class="mt-auto text-blue-600 hover:underline">
            Lasīt vairāk
        </a>
        <p class="text-gray-400 text-sm mt-2">
            Publicēts: {{ $item->created_at->format('Y-m-d H:i') }}
        </p>
    </div>
@endforeach

            </div>
        </section>
    </main>
</body>
</html>
