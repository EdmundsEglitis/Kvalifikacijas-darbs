
@extends('layouts.nba')
@section('title','All players')

@section('content')
    <main class="pt-20 max-w-7xl mx-auto px-4">
        <h1 class="text-3xl font-bold text-white mb-6">NBA Spēlētāji</h1>

        <form method="GET"
              class="mb-6 flex flex-col md:flex-row md:items-center md:space-x-4 space-y-3 md:space-y-0 bg-[#1f2937] p-4 rounded-lg shadow">
            <div class="flex-1">
                <input
                    type="text"
                    name="q"
                    value="{{ request('q') }}"
                    placeholder="Meklēt pēc vārda vai komandas..."
                    class="w-full rounded-md px-3 py-2 bg-[#111827] text-white placeholder-gray-400 border border-[#374151] focus:outline-none focus:ring-2 focus:ring-[#84CC16]"
                />
            </div>

            <div>
                <label class="mr-2 text-sm text-gray-300">Rindu skaits:</label>
                <select name="perPage" onchange="this.form.submit()"
                        class="rounded-md px-3 py-2 bg-[#111827] text-white border border-[#374151] focus:ring-2 focus:ring-[#84CC16]">
                    @foreach([10,25,50,100,200] as $pp)
                        <option value="{{ $pp }}" @selected((int)request('perPage', 50) === $pp)>{{ $pp }}</option>
                    @endforeach
                </select>
            </div>

            <input type="hidden" name="sort" value="{{ request('sort','name') }}">
            <input type="hidden" name="dir" value="{{ request('dir','asc') }}">

            <div>
                <button
                    class="bg-[#84CC16] text-[#111827] px-4 py-2 rounded-md font-semibold hover:bg-[#a3e635] transition"
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
                    'page' => 1,
                ]);
            };
        @endphp

        @if($players->count())
            <div class="overflow-x-auto bg-[#1f2937] shadow rounded-lg">
                <table class="min-w-full text-left text-sm">
                    <thead class="bg-[#111827] border-b border-[#374151] text-gray-400">
                        <tr>
                            <th class="px-4 py-2">
                                <a href="{{ $sortUrl('name') }}" class="flex items-center space-x-2 hover:text-[#84CC16]">
                                    <span>Vārds</span>
                                    <span class="text-xs">{{ $arrow('name') }}</span>
                                </a>
                            </th>
                            <th class="px-4 py-2">
                                <a href="{{ $sortUrl('team') }}" class="flex items-center space-x-2 hover:text-[#84CC16]">
                                    <span>Komanda</span>
                                    <span class="text-xs">{{ $arrow('team') }}</span>
                                </a>
                            </th>
                            <th class="px-4 py-2">
                                <a href="{{ $sortUrl('height') }}" class="flex items-center space-x-2 hover:text-[#84CC16]">
                                    <span>Augums</span>
                                    <span class="text-xs">{{ $arrow('height') }}</span>
                                </a>
                            </th>
                            <th class="px-4 py-2">
                                <a href="{{ $sortUrl('weight') }}" class="flex items-center space-x-2 hover:text-[#84CC16]">
                                    <span>Svars</span>
                                    <span class="text-xs">{{ $arrow('weight') }}</span>
                                </a>
                            </th>
                            <th class="px-4 py-2">Foto</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#374151] text-[#F3F4F6]">
                        @foreach($players as $player)
                            <tr class="odd:bg-[#1f2937] even:bg-[#111827] hover:bg-[#374151] transition">
                                <td class="px-4 py-2">
                                    <a href="{{ route('nba.player.show', $player->external_id) }}"
                                       class="text-[#84CC16] hover:underline font-medium">
                                        {{ $player->full_name }}
                                    </a>
                                </td>
                                <td class="px-4 py-2">
                                    @if($player->team_id)
                                        <a href="{{ route('nba.team.show', $player->team_id) }}"
                                           class="text-[#84CC16] hover:underline">
                                            {{ $player->team_name ?? 'N/A' }}
                                        </a>
                                    @else
                                        <span class="text-gray-400">Brīvais aģents</span>
                                    @endif
                                </td>
                                <td class="px-4 py-2">{{ $player->display_height ?? '-' }}</td>
                                <td class="px-4 py-2">{{ $player->display_weight ?? '-' }}</td>
                                <td class="px-4 py-2">
                                    @if($player->image)
                                        <img src="{{ $player->image }}" alt="Photo"
                                             class="h-10 w-10 rounded-full object-cover ring-2 ring-[#84CC16]" loading="lazy">
                                    @else
                                        <span class="text-gray-500">No photo</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-6 flex justify-center">
                {{ $players->appends(request()->query())->links('vendor.pagination.custom-dark') }}
            </div>
        @else
            <p class="text-gray-400 mt-4">Nav atrasti spēlētāji.</p>
        @endif

    </main>
</body>
</html>
@endsection