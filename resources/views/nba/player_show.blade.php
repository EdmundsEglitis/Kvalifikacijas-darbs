<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $player['fullName'] }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <x-nba-navbar />

    <main class="pt-24 px-6 max-w-6xl mx-auto">
        {{-- Player Header --}}
        <div class="bg-white shadow rounded p-6 mb-8 flex items-center space-x-6">
            <img src="{{ $player['headshot']['href'] ?? 'https://via.placeholder.com/120' }}"
                 alt="{{ $player['fullName'] }}"
                 class="h-28 w-28 rounded-full object-cover">
            <div>
                <h1 class="text-3xl font-bold">{{ $player['fullName'] }}</h1>
                <p class="text-gray-600">
                    {{ $player['position']['displayName'] ?? '' }} |
                    <a href="{{ route('nba.team.show', $player['team']['id']) }}"
                       class="text-blue-600 hover:underline">
                        {{ $player['team']['displayName'] ?? '' }}
                    </a>
                </p>
                <p class="text-gray-500">Jersey: {{ $player['displayJersey'] ?? '-' }}</p>
            </div>
        </div>

        {{-- Bio Info --}}
        <div class="bg-white shadow rounded p-6 mb-8">
            <h2 class="text-2xl font-semibold mb-4">Bio</h2>
            <ul class="grid grid-cols-2 gap-4 text-sm">
                <li><strong>Height:</strong> {{ $player['displayHeight'] ?? '-' }}</li>
                <li><strong>Weight:</strong> {{ $player['displayWeight'] ?? '-' }}</li>
                <li><strong>Age:</strong> {{ $player['age'] ?? '-' }}</li>
                <li><strong>DOB:</strong> {{ $player['displayDOB'] ?? '-' }}</li>
                <li><strong>Birthplace:</strong> {{ $player['displayBirthPlace'] ?? '-' }}</li>
                <li><strong>Experience:</strong> {{ $player['displayExperience'] ?? '-' }}</li>
                <li><strong>Draft:</strong> {{ $player['displayDraft'] ?? '-' }}</li>
                <li><strong>Status:</strong> {{ $player['status']['name'] ?? '-' }}</li>
            </ul>
        </div>

        {{-- External Links --}}
        <div class="bg-white shadow rounded p-6 mb-8">
            <h2 class="text-2xl font-semibold mb-4">More</h2>
            <ul class="list-disc pl-5 space-y-2 text-blue-600">
                @foreach($player['links'] ?? [] as $link)
                    <li>
                        <a href="{{ $link['href'] }}" target="_blank" class="hover:underline">
                            {{ $link['text'] }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>

        @php
            $labels = $gamelog['labels'] ?? [];
            $eventsMeta = $gamelog['events'] ?? [];
        @endphp

        {{-- Season Types --}}
        @foreach($gamelog['seasonTypes'] ?? [] as $season)
            <h2 class="text-2xl font-semibold mb-4">{{ $season['displayName'] }}</h2>

            @foreach($season['categories'] ?? [] as $category)
                {{-- Per-game logs --}}
                @if($category['type'] === 'event' && !empty($category['events']))
                    <h3 class="text-xl font-bold mb-2">{{ $category['displayName'] }}</h3>
                    <div class="overflow-x-auto bg-white shadow rounded-lg mb-8">
                        <table class="min-w-full text-left text-sm">
                            <thead class="bg-gray-50 border-b sticky top-0">
                                <tr>
                                    <th class="px-4 py-2">Date</th>
                                    <th class="px-4 py-2">Opponent</th>
                                    <th class="px-4 py-2">Result</th>
                                    <th class="px-4 py-2">Score</th>
                                    @foreach($labels as $label)
                                        <th class="px-4 py-2">{{ $label }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($category['events'] as $event)
                                    @php
                                        $meta = $eventsMeta[$event['eventId']] ?? null;
                                    @endphp
                                    <tr class="odd:bg-white even:bg-gray-50 hover:bg-gray-100">
                                        <td class="px-4 py-2">
                                            {{ $meta ? \Carbon\Carbon::parse($meta['gameDate'])->format('M d, Y') : $event['eventId'] }}
                                        </td>
                                        <td class="px-4 py-2 flex items-center space-x-2">
                                            @if($meta && !empty($meta['opponent']['logo']))
                                                <img src="{{ $meta['opponent']['logo'] }}" class="h-6 w-6 rounded-full">
                                            @endif
                                            {{ $meta['opponent']['displayName'] ?? '-' }}
                                        </td>
                                        <td class="px-4 py-2">{{ $meta['gameResult'] ?? '-' }}</td>
                                        <td class="px-4 py-2">{{ $meta['score'] ?? '-' }}</td>
                                        @foreach($event['stats'] as $value)
                                            <td class="px-4 py-2">{{ $value }}</td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

                {{-- Totals & Averages --}}
                @if(in_array($category['type'], ['total','avg']))
                    <h3 class="text-xl font-bold mb-2">{{ $category['displayName'] }}</h3>
                    <div class="overflow-x-auto bg-white shadow rounded-lg mb-8">
                        <table class="min-w-full text-left text-sm">
                            <thead class="bg-gray-50 border-b sticky top-0">
                                <tr>
                                    @foreach($labels as $label)
                                        <th class="px-4 py-2">{{ $label }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="odd:bg-white even:bg-gray-50">
                                    @foreach($category['totals'] ?? $category['stats'] ?? [] as $value)
                                        <td class="px-4 py-2">{{ $value }}</td>
                                    @endforeach
                                </tr>
                            </tbody>
                        </table>
                    </div>
                @endif
            @endforeach
        @endforeach
    </main>
</body>
</html>
