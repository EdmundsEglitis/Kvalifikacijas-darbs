<!DOCTYPE html>
<html>
<head>
    <title>NBA API Status</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">

    <div class="bg-white p-6 rounded-xl shadow-lg w-full max-w-md">
        <h1 class="text-2xl font-bold mb-4">NBA API Status</h1>

        @if(isset($data['response']))
            <div class="space-y-2">
                <p><strong>Plan:</strong> {{ $data['response']['subscription']['plan'] ?? 'N/A' }}</p>
                <p><strong>Active:</strong> {{ $data['response']['subscription']['active'] ? 'Yes' : 'No' }}</p>
                <p><strong>Requests Today:</strong> {{ $data['response']['requests']['current'] ?? 0 }} / {{ $data['response']['requests']['limit_day'] ?? 0 }}</p>
            </div>
        @else
            <p class="text-red-500">❌ Could not fetch API status.</p>
        @endif
    </div>

</body>
</html>
