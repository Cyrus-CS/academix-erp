<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <style>
    body {
        font-family: DejaVu Sans, sans-serif;
        font-size: 11px;
        color: #1e293b;
    }

    h1 {
        font-size: 16px;
        margin-bottom: 4px;
    }

    .subtitle {
        color: #64748b;
        margin-bottom: 16px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    th,
    td {
        border: 1px solid #cbd5e1;
        padding: 6px 8px;
        text-align: left;
    }

    th {
        background-color: #2563EB;
        color: #fff;
    }

    tr:nth-child(even) {
        background-color: #f8fafc;
    }
    </style>
</head>

<body>
    <h1>Emploi du temps — {{ $selectedClass->name }}</h1>
    <p class="subtitle">Année académique : {{ $activeYear?->name ?? 'N/A' }}</p>

    @foreach($days as $index => $day)
    @php $daySchedules = $schedules->get($index) ?? collect(); @endphp
    @if($daySchedules->isNotEmpty())
    <h3>{{ $day }}</h3>
    <table>
        <thead>
            <tr>
                <th>Horaire</th>
                <th>Matière</th>
                <th>Enseignant</th>
            </tr>
        </thead>
        <tbody>
            @foreach($daySchedules->sortBy('start_time') as $schedule)
            <tr>
                <td>{{ $schedule->start_time }} - {{ $schedule->end_time }}</td>
                <td>{{ $schedule->subject->name }}</td>
                <td>{{ $schedule->teacher->user->name ?? 'N/A' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <br>
    @endif
    @endforeach
</body>

</html>