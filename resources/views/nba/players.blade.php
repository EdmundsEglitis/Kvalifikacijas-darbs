<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>NBA Players</title>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
</head>
<body class="p-6">

<h1>All NBA Players</h1>

<table id="players-table" class="display">
    <thead>
        <tr>
            <th>Image</th>
            <th>Name</th>
            <th>Height</th>
            <th>Weight</th>
            <th>Age</th>
            <th>Salary</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($players as $player)
            <tr>
                <td>
                    @if(isset($player['image']))
                        <img src="{{ $player['image'] }}" alt="{{ $player['fullName'] }}" width="50">
                    @endif
                </td>
                <td>{{ $player['fullName'] ?? 'N/A' }}</td>
                <td>{{ $player['displayHeight'] ?? 'N/A' }}</td>
                <td>{{ $player['displayWeight'] ?? 'N/A' }}</td>
                <td>{{ $player['age'] ?? 'N/A' }}</td>
                <td>${{ number_format($player['salary'] ?? 0) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<script>
    $(document).ready(function() {
        $('#players-table').DataTable({
            "order": [[ 1, "asc" ]] // default sort by name
        });
    });
</script>

</body>
</html>
