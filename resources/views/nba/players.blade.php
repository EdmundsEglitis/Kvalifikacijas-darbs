<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NBA - Spēlētāji</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
<x-nba-navbar />


    <main class="pt-20 max-w-7xl mx-auto px-4">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">NBA Spēlētāji</h1>

        {{-- Controls: search + per page --}}
        <form method="GET" class="mb-4 flex flex-col md:flex-row md:items-center md:space-x-4 space-y-3 md:space-y-0">
            <div class="flex-1">
                <input
                    type="text"
                    name="q"
                    value="{{ request('q') }}"
                    placeholder="Meklēt pēc vārda vai komandas..."
                    class="w-full border rounded-md px-3 py-2 focus:outline-none focus:ring focus:border-blue-300"
                />
            </div>

            <div>
                <label class="mr-2 text-sm text-gray-600">Rindu skaits:</label>
                <select name="perPage" onchange="this.form.submit()" class="border rounded-md px-3 py-2">
                    @foreach([10,25,50,100,200] as $pp)
                        <option value="{{ $pp }}" @selected((int)request('perPage', 50) === $pp)>{{ $pp }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Preserve sort/dir on search submit --}}
            <input type="hidden" name="sort" value="{{ request('sort','name') }}">
            <input type="hidden" name="dir" value="{{ request('dir','asc') }}">

            <div>
                <button
                    class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700"
                    type="submit"
                >Meklēt</button>
            </div>
        </form>

        @php
            $sort = request('sort','name');
            $dir  = request('dir','asc') === 'desc' ? 'desc' : 'asc';
            $nextDir = fn($col) => ($sort === $col && $dir === 'asc') ? 'desc' : 'asc';
            $arrow = fn($col) => $sort === $col ? ($dir === 'asc' ? '▲' : '▼') : '';
            $sortUrl = function($col) use ($nextDir) {
                return request()->fullUrlWithQuery([
                    'sort' => $col,
                    'dir'  => $nextDir($col),
                    'page' => 1, // reset to first page when sorting
                ]);
            };
        @endphp

        @if($players->count())
            <div class="overflow-x-auto bg-white shadow rounded-lg">
                <table class="min-w-full text-left text-sm">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-4 py-2">
                                <a href="{{ $sortUrl('name') }}" class="flex items-center space-x-2 hover:text-blue-600">
                                    <span>Vārds</span>
                                    <span class="text-xs">{{ $arrow('name') }}</span>
                                </a>
                            </th>
                            <th class="px-4 py-2">
                                <a href="{{ $sortUrl('team') }}" class="flex items-center space-x-2 hover:text-blue-600">
                                    <span>Komanda</span>
                                    <span class="text-xs">{{ $arrow('team') }}</span>
                                </a>
                            </th>
                            <th class="px-4 py-2">
                                <a href="{{ $sortUrl('height') }}" class="flex items-center space-x-2 hover:text-blue-600">
                                    <span>Augums</span>
                                    <span class="text-xs">{{ $arrow('height') }}</span>
                                </a>
                            </th>
                            <th class="px-4 py-2">
                                <a href="{{ $sortUrl('weight') }}" class="flex items-center space-x-2 hover:text-blue-600">
                                    <span>Svars</span>
                                    <span class="text-xs">{{ $arrow('weight') }}</span>
                                </a>
                            </th>
                            <th class="px-4 py-2">Foto</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($players as $player)
                            <tr class="hover:bg-gray-50">
                                
                                <td class="px-4 py-2">
                                    <a href="{{ route('nba.player.show', $player['id']) }}" class="text-blue-600 hover:underline">
                                        {{ $player['firstName'] ?? '' }} {{ $player['lastName'] ?? '' }}
                                    </a>
                                </td>
                                
                                <td class="px-4 py-2">
                                    <a href="{{ route('nba.team.show', $player['teamId']) }}" class="text-blue-600 hover:underline">
                                        {{ $player['teamName'] ?? 'N/A' }}
                                    </a>
                                </td>

                                <td class="px-4 py-2">{{ $player['displayHeight'] ?? '-' }}</td>
                                <td class="px-4 py-2">{{ $player['displayWeight'] ?? '-' }}</td>
                                <td class="px-4 py-2">
                                    @if(!empty($player['image']))
                                        <img src="{{ $player['image'] }}" alt="Photo" class="h-10 w-10 rounded-full" loading="lazy">
                                    @else
                                        <span class="text-gray-400">No photo</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {!! $players->appends(request()->query())->links() !!}
            </div>
        @else
            <p class="text-gray-600 mt-4">Nav atrasti spēlētāji.</p>
        @endif
    </main>
</body>
</html>
