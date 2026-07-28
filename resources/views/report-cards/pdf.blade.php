{{-- resources/views/report-cards/pdf.blade.php --}}
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Bulletin - {{ $reportCard->student->user->name }}</title>
    <style>
    body {
        font-family: DejaVu Sans, sans-serif;
        font-size: 12px;
        color: #1e293b;
    }

    .header {
        text-align: center;
        margin-bottom: 20px;
    }

    .header h1 {
        font-size: 18px;
        margin: 0;
    }

    .header p {
        margin: 2px 0;
        color: #64748b;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 15px;
    }

    th,
    td {
        border: 1px solid #cbd5e1;
        padding: 6px 10px;
        text-align: left;
    }

    th {
        background-color: #f1f5f9;
        font-size: 11px;
    }

    .summary {
        margin-top: 20px;
    }

    .summary td {
        font-weight: bold;
    }
    </style>
</head>

<body>
    <div class="header">
        <h1>Bulletin Scolaire</h1>
        <p>{{ $reportCard->term->name }} |{{ $reportCard->term->academicYear->name }}</p>
    </div>

    <p>
        <strong>Étudiant :</strong> {{ $reportCard->student->user->name }}<br>
        <strong>Matricule :</strong> {{ $reportCard->student->matricule }}<br>
        <strong>Classe :</strong> {{ $reportCard->student->classe->name }}
    </p>

    <table>
        <thead>
            <tr>
                <th>Matière</th>
                <th>Note</th>
                <th>Coefficient</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reportCard->grades as $grade)
            <tr>
                <td>{{ $grade->subject->name }}</td>
                <td>{{ $grade->score }} / {{ $grade->max_score }}</td>
                <td>{{ $grade->subject->coefficient ?? 1 }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <table class="summary">
        <tr>
            <td>Moyenne générale</td>
            <td>{{ $reportCard->average }} / 20</td>
        </tr>
        <tr>
            <td>Rang</td>
            <td>{{ $reportCard->rank }} / {{ $reportCard->total_students }}</td>
        </tr>
        <tr>
            <td>Appréciation</td>
            <td>{{ $reportCard->appreciation }}</td>
        </tr>
    </table>
</body>

</html>