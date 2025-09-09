<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Basketbola Portāls</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col items-center justify-center">

    <h1 class="text-3xl font-bold mb-10">Izvēlies sadaļu</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 w-full max-w-4xl px-6">
        <!-- NBA card -->
        <a href="{{ route('nba.home') }}"
           class="bg-white rounded-2xl shadow-lg hover:shadow-2xl transition p-10 flex flex-col items-center text-center">
           <img src="{{ asset('nba-logo-png-transparent.png') }}" 
                 alt="NBA Logo" class="w-28 mb-6">
            <h2 class="text-2xl font-semibold">NBA</h2>
            <p class="mt-2 text-gray-600">Amerikas basketbola līga</p>
        </a>

        <!-- LBS card -->
        <a href="{{ route('lbs.home') }}"
           class="bg-white rounded-2xl shadow-lg hover:shadow-2xl transition p-10 flex flex-col items-center text-center">
            <img src="415986933_1338154883529529_7481933183149808416_n.jpg" 
                 alt="LBS Logo" class="w-24 mb-6">
            <h2 class="text-2xl font-semibold">LBS</h2>
            <p class="mt-2 text-gray-600">Latvijas Basketbola Savienība</p>
        </a>
    </div>

</body>
</html>
