<x-filament::widget>
    <x-filament::card>
        <h2 class="text-lg font-bold mb-4">🚨 System Warnings</h2>

        @if(empty($warnings))
            <div class="p-3 bg-green-100 text-green-800 rounded-lg">
                ✅ All systems look good. No warnings.
            </div>
        @endif

        {{-- Late games --}}
        @if(!empty($warnings['lateGames']))
            <div class="p-3 bg-red-100 text-red-800 rounded-lg mb-4">
                <h3 class="font-semibold mb-1">⚠ Past-due Upcoming Games</h3>
                <ul class="list-disc list-inside text-sm">
                    @foreach($warnings['lateGames'] as $game)
                        <li>
                            {{ $game->name }} ({{ $game->date->format('Y-m-d') }})
                            – <a href="{{ route('filament.admin.resources.games.edit', $game) }}"
                                 class="underline text-blue-700">Fix</a>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Missing stats --}}
        @if(!empty($warnings['missingStats']))
            <div class="p-3 bg-yellow-100 text-yellow-800 rounded-lg">
                <h3 class="font-semibold mb-1">⚠ Completed Games Missing Player Stats</h3>
                <ul class="list-disc list-inside text-sm">
                    @foreach($warnings['missingStats'] as $game)
                        <li>
                            {{ $game->name }} ({{ $game->date->format('Y-m-d') }})
                            – <a href="{{ route('filament.admin.resources.games.edit', $game) }}"
                                 class="underline text-blue-700">Fix</a>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
    </x-filament::card>
</x-filament::widget>
