@extends('layouts.base')

@section('page_title', $student->user->name ?? 'Profil élève')

@section('breadcrumb')
<span class="text-slate-300 dark:text-slate-600 font-light select-none">/</span>
<a href="{{ route('students.index') }}"
    class="text-slate-500 dark:text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
    Élèves
</a>
@endsection

@section('page_header')
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div class="flex items-center gap-3">
        <a href="{{ route('students.index') }}" class="w-9 h-9 rounded-xl flex items-center justify-center
                  bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700
                  text-slate-500 hover:text-blue-600 hover:border-blue-300 dark:hover:border-blue-600
                  transition-all duration-200">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-800 dark:text-slate-100">
                Profil de l'élève
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                {{ $student->user->name ?? 'Élève' }}
                &bull; {{ $student->classe->name ?? 'Classe non définie' }}
            </p>
        </div>
    </div>

    <div class="flex flex-wrap items-center gap-2">
        <a href="{{ route('attendance.create') }}?student_id={{ $student->id }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-medium
                  bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700
                  text-slate-700 dark:text-slate-300
                  hover:bg-slate-50 dark:hover:bg-slate-700 transition-all duration-200">
            <i class="bi bi-calendar-check"></i>
            <span class="hidden sm:inline">Présence</span>
        </a>

        <a href="{{ route('grades.create') }}?student_id={{ $student->id }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-medium
                  bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700
                  text-slate-700 dark:text-slate-300
                  hover:bg-slate-50 dark:hover:bg-slate-700 transition-all duration-200">
            <i class="bi bi-star-fill text-amber-500"></i>
            <span class="hidden sm:inline">Saisir note</span>
        </a>

        <a href="{{ route('students.edit', $student) }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-medium
                  bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700
                  text-slate-700 dark:text-slate-300
                  hover:bg-slate-50 dark:hover:bg-slate-700 transition-all duration-200">
            <i class="bi bi-pencil-square"></i>
            <span class="hidden sm:inline">Modifier</span>
        </a>

        <form id="delete-student-form" action="{{ route('students.destroy', $student) }}" method="POST">
            @csrf
            @method('DELETE')
            <button id="delete-student-btn" type="button" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold
                           bg-red-600 hover:bg-red-700 text-white
                           shadow-sm hover:shadow-red-500/20 transition-all duration-200">
                <i class="bi bi-trash3"></i>
                <span class="hidden sm:inline">Supprimer</span>
            </button>
        </form>
    </div>
</div>
@endsection

@section('content')
@php
$attendances = $student->attendances ?? collect();
$grades = $student->grades ?? collect();
$payments = $student->payments ?? collect();
$reportCards = $student->reportCards ?? collect();
$parents = $student->parents ?? collect();
$lastAttendance = $attendances->sortByDesc('date')->first();

// Stats présences
$totalAttendance = $attendances->count();
$presentCount = $attendances->where('status', 'present')->count();
$absentCount = $attendances->where('status', 'absent')->count();
$lateCount = $attendances->where('status', 'late')->count();
$attendanceRate = $totalAttendance > 0
? round(($presentCount / $totalAttendance) * 100)
: 0;

// Stats notes
$totalGrades = $grades->count();
$avgGrade = $totalGrades > 0 ? round($grades->avg('score'), 2) : null;
$bestGrade = $totalGrades > 0 ? $grades->max('score') : null;

// Stats paiements
$totalPayments = $payments->count();
$paidPayments = $payments->where('status', 'paid')->count();

// Tabs
$tabs = [
'overview' => ['label' => 'Aperçu', 'icon' => 'bi-grid-fill'],
'grades' => ['label' => 'Notes', 'icon' => 'bi-star-fill'],
'attendance' => ['label' => 'Présences', 'icon' => 'bi-calendar-check-fill'],
'payments' => ['label' => 'Paiements', 'icon' => 'bi-cash-coin'],
'reports' => ['label' => 'Bulletins', 'icon' => 'bi-file-earmark-text-fill'],
];
@endphp

{{-- ── Hero Profil ─────────────────────────────────────────────── --}}
<div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200
            dark:border-slate-700 shadow-sm overflow-hidden mb-6">

    {{-- Cover --}}
    <div class="h-24 sm:h-32 bg-linear-to-r from-blue-600 via-indigo-500 to-emerald-500 relative overflow-hidden">
        <div class="absolute inset-0 opacity-10"
            style="background-image: repeating-linear-gradient(45deg, white 0, white 1px, transparent 0, transparent 50%); background-size: 20px 20px;">
        </div>
    </div>

    <div class="px-5 sm:px-8 pb-6 sm:pb-8">
        <div class="flex flex-col sm:flex-row sm:items-end gap-4 -mt-10 sm:-mt-14">

            {{-- Avatar --}}
            @if($student->user?->avatar)
            <img src="{{ asset('storage/' . $student->user->avatar) }}" alt="{{ $student->user->name }}" class="w-20 h-20 sm:w-24 sm:h-24 rounded-2xl object-cover
                            ring-4 ring-white dark:ring-slate-800 shadow-xl shrink-0">
            @else
            <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-2xl shrink-0
                            bg-linear-to-br from-blue-600 to-emerald-500
                            flex items-center justify-center
                            text-white text-2xl font-black
                            ring-4 ring-white dark:ring-slate-800 shadow-xl">
                {{ strtoupper(substr($student->user->name ?? 'E', 0, 2)) }}
            </div>
            @endif

            {{-- Identité --}}
            <div class="flex-1 min-w-0 pt-2 sm:pt-0 sm:pb-1">
                <div class="flex flex-wrap items-center gap-2 mb-1.5">
                    <h2 class="text-xl sm:text-2xl font-bold text-slate-800 dark:text-slate-100">
                        {{ $student->user->name ?? 'Élève' }}
                    </h2>
                    @if($student->gender ?? null)
                    <span
                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium
                                 {{ $student->gender === 'male'
                                     ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800'
                                     : 'bg-pink-50 dark:bg-pink-900/20 text-pink-700 dark:text-pink-300 border border-pink-200 dark:border-pink-800' }}">
                        <i class="bi {{ $student->gender === 'male' ? 'bi-gender-male' : 'bi-gender-female' }}"></i>
                        {{ $student->gender === 'male' ? 'Masculin' : 'Féminin' }}
                    </span>
                    @endif
                    @if($lastAttendance)
                    <span
                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium
                                 {{ strtolower($lastAttendance->status) === 'present'
                                     ? 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800'
                                     : 'bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-300 border border-red-200 dark:border-red-800' }}">
                        <span
                            class="w-1.5 h-1.5 rounded-full {{ strtolower($lastAttendance->status) === 'present' ? 'bg-emerald-500' : 'bg-red-500' }}"></span>
                        Dernière :
                        {{ strtolower($lastAttendance->status) === 'present' ? 'Présent' : (strtolower($lastAttendance->status) === 'absent' ? 'Absent' : 'Retard') }}
                    </span>
                    @endif
                </div>

                <div class="flex flex-wrap gap-3 sm:gap-5 text-xs text-slate-500 dark:text-slate-400">
                    <span class="inline-flex items-center gap-1.5">
                        <i class="bi bi-collection text-slate-400"></i>
                        {{ $student->classe->name ?? 'Classe non définie' }}
                    </span>
                    <span class="inline-flex items-center gap-1.5">
                        <i class="bi bi-hash text-slate-400"></i>
                        Mat. {{ $student->admission_number ?? $student->matricule ?? '—' }}
                    </span>
                    <span class="inline-flex items-center gap-1.5">
                        <i class="bi bi-envelope text-slate-400"></i>
                        {{ $student->user->email ?? 'Aucun email' }}
                    </span>
                    @if($student->birth_date ?? null)
                    <span class="inline-flex items-center gap-1.5">
                        <i class="bi bi-cake text-slate-400"></i>
                        {{ \Carbon\Carbon::parse($student->birth_date)->format('d/m/Y') }}
                    </span>
                    @endif
                    <span class="inline-flex items-center gap-1.5">
                        <i class="bi bi-calendar-plus text-slate-400"></i>
                        Inscrit {{ $student->created_at->diffForHumans() }}
                    </span>
                </div>
            </div>
        </div>
    </div>
    <div class="h-1 w-full bg-linear-to-r from-blue-600 via-indigo-500 to-emerald-500"></div>
</div>

{{-- ── Stats rapides ────────────────────────────────────────────── --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    @foreach([
    [
    'label' => 'Présence',
    'value' => $attendanceRate.'%',
    'sub' => $presentCount.' / '.$totalAttendance.' séances',
    'icon' => 'bi-calendar-check-fill',
    'color' => $attendanceRate >= 75 ? 'emerald' : ($attendanceRate >= 50 ? 'amber' : 'red'),
    ],
    [
    'label' => 'Moyenne',
    'value' => $avgGrade ? $avgGrade.'/20' : '—',
    'sub' => $totalGrades.' notes saisies',
    'icon' => 'bi-star-fill',
    'color' => $avgGrade >= 12 ? 'emerald' : ($avgGrade >= 10 ? 'amber' : 'red'),
    ],
    [
    'label' => 'Paiements',
    'value' => $paidPayments.'/'.$totalPayments,
    'sub' => 'Frais réglés',
    'icon' => 'bi-cash-coin',
    'color' => 'blue',
    ],
    [
    'label' => 'Bulletins',
    'value' => $reportCards->count(),
    'sub' => 'Générés',
    'icon' => 'bi-file-earmark-text-fill',
    'color' => 'cyan',
    ],
    ] as $s)
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200
                dark:border-slate-700 p-5 shadow-sm">
        <div class="flex items-start justify-between gap-2">
            <div class="min-w-0">
                <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">
                    {{ $s['label'] }}
                </p>
                <p class="text-2xl font-bold mt-1.5
                    {{ $s['color'] === 'emerald' ? 'text-emerald-600 dark:text-emerald-400' : '' }}
                    {{ $s['color'] === 'amber'   ? 'text-amber-600 dark:text-amber-400' : '' }}
                    {{ $s['color'] === 'red'     ? 'text-red-600 dark:text-red-400' : '' }}
                    {{ $s['color'] === 'blue'    ? 'text-slate-800 dark:text-slate-100' : '' }}
                    {{ $s['color'] === 'cyan'    ? 'text-slate-800 dark:text-slate-100' : '' }}">
                    {{ $s['value'] }}
                </p>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">{{ $s['sub'] }}</p>
            </div>
            <div
                class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0
                {{ $s['color'] === 'emerald' ? 'bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400' : '' }}
                {{ $s['color'] === 'amber'   ? 'bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400' : '' }}
                {{ $s['color'] === 'red'     ? 'bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400' : '' }}
                {{ $s['color'] === 'blue'    ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400' : '' }}
                {{ $s['color'] === 'cyan'    ? 'bg-cyan-50 dark:bg-cyan-900/30 text-cyan-600 dark:text-cyan-400' : '' }}">
                <i class="bi {{ $s['icon'] }} text-lg"></i>
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- ── Tabs ─────────────────────────────────────────────────────── --}}
<div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200
            dark:border-slate-700 shadow-sm overflow-hidden">

    {{-- Tab nav --}}
    <div class="border-b border-slate-200 dark:border-slate-700 overflow-x-auto">
        <nav class="flex gap-0 min-w-max" id="student-tabs">
            @foreach($tabs as $key => $tab)
            <button type="button" data-tab="{{ $key }}"
                class="tab-btn flex items-center gap-2 px-5 py-4 text-sm font-medium
                           border-b-2 transition-all duration-200 whitespace-nowrap
                           {{ $loop->first
                               ? 'border-blue-600 text-blue-600 dark:text-blue-400'
                               : 'border-transparent text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:border-slate-300 dark:hover:border-slate-600' }}">
                <i class="bi {{ $tab['icon'] }} text-sm"></i>
                {{ $tab['label'] }}
            </button>
            @endforeach
        </nav>
    </div>

    {{-- ─────── Aperçu ─────────────────────────────────────────── --}}
    <div id="tab-overview" class="tab-panel p-5 sm:p-6">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- Infos personnelles --}}
            <div>
                <h4 class="text-sm font-semibold text-slate-800 dark:text-slate-100
                           flex items-center gap-2 mb-4">
                    <i class="bi bi-person-lines-fill text-blue-500"></i>
                    Informations personnelles
                </h4>
                <div class="space-y-3">
                    @foreach([
                    ['icon' => 'bi-person', 'label' => 'Nom complet', 'value' => $student->user->name ?? '—'],
                    ['icon' => 'bi-envelope', 'label' => 'Email', 'value' => $student->user->email ?? '—'],
                    ['icon' => 'bi-collection', 'label' => 'Classe', 'value' => $student->classe->name ?? '—'],
                    ['icon' => 'bi-hash', 'label' => 'Matricule', 'value' => $student->admission_number ??
                    $student->matricule ?? '—'],
                    ['icon' => 'bi-cake', 'label' => 'Date de naissance', 'value' => $student->birth_date ?
                    \Carbon\Carbon::parse($student->birth_date)->format('d/m/Y') : '—'],
                    ['icon' => 'bi-geo-alt', 'label' => 'Adresse', 'value' => $student->address ?? '—'],
                    ['icon' => 'bi-telephone', 'label' => 'Téléphone', 'value' => $student->phone ?? '—'],
                    ] as $info)
                    <div class="flex items-start gap-3 py-2.5
                                border-b border-slate-100 dark:border-slate-700/50 last:border-0">
                        <div class="w-8 h-8 rounded-lg bg-slate-100 dark:bg-slate-700
                                    flex items-center justify-center shrink-0
                                    text-slate-500 dark:text-slate-400">
                            <i class="bi {{ $info['icon'] }} text-sm"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-[10px] uppercase tracking-wider font-semibold text-slate-400">
                                {{ $info['label'] }}
                            </p>
                            <p class="text-sm font-medium text-slate-800 dark:text-slate-200 truncate">
                                {{ $info['value'] }}
                            </p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Parents & Taux présence --}}
            <div class="space-y-6">

                {{-- Taux présence --}}
                <div>
                    <h4 class="text-sm font-semibold text-slate-800 dark:text-slate-100
                               flex items-center gap-2 mb-4">
                        <i class="bi bi-pie-chart-fill text-emerald-500"></i>
                        Assiduité
                    </h4>
                    <div class="rounded-xl border border-slate-200 dark:border-slate-700
                                bg-slate-50 dark:bg-slate-900/50 p-4 space-y-3">
                        @foreach([
                        ['label' => 'Présent', 'count' => $presentCount, 'color' => 'bg-emerald-500', 'text' =>
                        'text-emerald-700 dark:text-emerald-300'],
                        ['label' => 'Absent', 'count' => $absentCount, 'color' => 'bg-red-500', 'text' => 'text-red-700
                        dark:text-red-300'],
                        ['label' => 'Retard', 'count' => $lateCount, 'color' => 'bg-amber-500', 'text' =>
                        'text-amber-700 dark:text-amber-300'],
                        ] as $bar)
                        @php $pct = $totalAttendance > 0 ? round(($bar['count'] / $totalAttendance) * 100) : 0; @endphp
                        <div>
                            <div class="flex justify-between text-xs mb-1.5">
                                <span class="font-medium text-slate-600 dark:text-slate-300">{{ $bar['label'] }}</span>
                                <span class="font-bold {{ $bar['text'] }}">{{ $bar['count'] }} ({{ $pct }}%)</span>
                            </div>
                            <div
                                class="h-2 bg-white dark:bg-slate-700 rounded-full overflow-hidden border border-slate-200 dark:border-slate-600">
                                <div class="h-full {{ $bar['color'] }} rounded-full" style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Parents --}}
                <div>
                    <h4 class="text-sm font-semibold text-slate-800 dark:text-slate-100
                               flex items-center gap-2 mb-4">
                        <i class="bi bi-people-fill text-blue-500"></i>
                        Parents / Tuteurs ({{ $parents->count() }})
                    </h4>
                    @forelse($parents as $parent)
                    <div class="flex items-center gap-3 p-3 rounded-xl
                                border border-slate-200 dark:border-slate-700
                                hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors mb-2 last:mb-0">
                        <div class="w-9 h-9 rounded-xl bg-blue-50 dark:bg-blue-900/20
                                    flex items-center justify-center text-blue-600 dark:text-blue-400
                                    font-bold text-sm shrink-0">
                            {{ strtoupper(substr($parent->user->name ?? 'P', 0, 1)) }}
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-medium text-slate-800 dark:text-slate-200 truncate">
                                {{ $parent->user->name ?? 'Parent' }}
                            </p>
                            <p class="text-xs text-slate-500 dark:text-slate-400 truncate">
                                {{ $parent->relationship ?? 'Tuteur' }}
                                @if($parent->user->email ?? null) &bull; {{ $parent->user->email }} @endif
                            </p>
                        </div>
                        <a href="{{ route('parents.show', $parent) }}" class="w-7 h-7 rounded-lg border border-slate-200 dark:border-slate-600
                                  flex items-center justify-center text-slate-400
                                  hover:text-blue-600 hover:border-blue-300 transition-all">
                            <i class="bi bi-eye text-xs"></i>
                        </a>
                    </div>
                    @empty
                    <div class="py-6 text-center rounded-xl border border-dashed
                                border-slate-300 dark:border-slate-600">
                        <i class="bi bi-people text-xl text-slate-300 dark:text-slate-600"></i>
                        <p class="text-xs text-slate-400 mt-1">Aucun parent associé</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- ─────── Notes ───────────────────────────────────────────── --}}
    <div id="tab-grades" class="tab-panel hidden p-5 sm:p-6">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h4 class="text-sm font-semibold text-slate-800 dark:text-slate-100">
                    Notes de l'élève
                </h4>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                    {{ $totalGrades }} note(s) &bull;
                    Moyenne : {{ $avgGrade ? $avgGrade.'/20' : '—' }}
                </p>
            </div>
            <a href="{{ route('grades.create') }}?student_id={{ $student->id }}" class="inline-flex items-center gap-2 px-3 py-2 rounded-xl text-xs font-semibold
                      bg-amber-600 hover:bg-amber-700 text-white transition-all">
                <i class="bi bi-plus-lg"></i>
                Saisir une note
            </a>
        </div>

        @if($grades->count())
        <div class="hidden sm:block overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="text-[11px] uppercase font-semibold tracking-wider
                              text-slate-400 dark:text-slate-500 bg-slate-50 dark:bg-slate-900/50">
                    <tr>
                        <th class="px-4 py-3 text-left">Matière</th>
                        <th class="px-4 py-3 text-left">Type</th>
                        <th class="px-4 py-3 text-left">Trimestre</th>
                        <th class="px-4 py-3 text-left">Note</th>
                        <th class="px-4 py-3 text-left">Performance</th>
                        <th class="px-4 py-3 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50">
                    @foreach($grades->sortByDesc('created_at') as $grade)
                    @php
                    $gScore = (float) ($grade->score ?? 0);
                    $gPct = max(0, min(100, round(($gScore / 20) * 100)));
                    $gColor = $gScore >= 12
                    ? 'text-emerald-600 dark:text-emerald-400'
                    : ($gScore >= 10 ? 'text-amber-600 dark:text-amber-400' : 'text-red-600 dark:text-red-400');
                    $gBar = $gScore >= 12 ? 'bg-emerald-500' : ($gScore >= 10 ? 'bg-amber-500' : 'bg-red-500');
                    @endphp
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                        <td class="px-4 py-3 font-medium text-slate-800 dark:text-slate-200">
                            {{ $grade->subject->name ?? '—' }}
                        </td>
                        <td class="px-4 py-3 text-slate-500 dark:text-slate-400">
                            {{ $grade->type ?? '—' }}
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 rounded-full text-xs
                                         bg-slate-100 dark:bg-slate-700
                                         text-slate-600 dark:text-slate-300">
                                {{ $grade->term->name ?? '—' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 font-bold {{ $gColor }}">
                            {{ number_format($gScore, 2) }}/20
                        </td>
                        <td class="px-4 py-3 min-w-40">
                            <div class="flex items-center gap-3">
                                <div class="flex-1 h-2 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
                                    <div class="h-full {{ $gBar }}" style="width: {{ $gPct }}%"></div>
                                </div>
                                <span class="text-xs text-slate-500 w-8 text-right">{{ $gPct }}%</span>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('grades.show', $grade) }}" class="inline-flex w-7 h-7 items-center justify-center rounded-lg
                                      border border-slate-200 dark:border-slate-600
                                      text-slate-500 hover:text-blue-600
                                      hover:border-blue-300 dark:hover:border-blue-500 transition-all">
                                <i class="bi bi-eye text-xs"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="sm:hidden space-y-2">
            @foreach($grades->sortByDesc('created_at') as $grade)
            @php
            $gScore = (float) ($grade->score ?? 0);
            $gColor = $gScore >= 12
            ? 'text-emerald-600 dark:text-emerald-400'
            : ($gScore >= 10 ? 'text-amber-600 dark:text-amber-400' : 'text-red-600 dark:text-red-400');
            @endphp
            <div class="flex items-center gap-3 p-4 rounded-xl
                        border border-slate-200 dark:border-slate-700
                        hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-slate-800 dark:text-slate-200 truncate">
                        {{ $grade->subject->name ?? '—' }}
                    </p>
                    <p class="text-xs text-slate-500">
                        {{ $grade->type ?? '—' }} &bull; {{ $grade->term->name ?? '—' }}
                    </p>
                </div>
                <p class="text-sm font-bold {{ $gColor }}">
                    {{ number_format($gScore, 2) }}/20
                </p>
                <a href="{{ route('grades.show', $grade) }}" class="w-8 h-8 rounded-xl bg-slate-50 dark:bg-slate-700
                          flex items-center justify-center text-slate-400
                          hover:text-blue-600 transition-colors">
                    <i class="bi bi-chevron-right text-xs"></i>
                </a>
            </div>
            @endforeach
        </div>
        @else
        <div class="py-16 text-center">
            <div class="w-12 h-12 mx-auto rounded-2xl bg-slate-100 dark:bg-slate-700
                        flex items-center justify-center mb-3">
                <i class="bi bi-star text-xl text-slate-400"></i>
            </div>
            <p class="text-sm font-medium text-slate-600 dark:text-slate-300">Aucune note</p>
            <p class="text-xs text-slate-400 mt-1">Les évaluations de cet élève apparaîtront ici.</p>
        </div>
        @endif
    </div>

    {{-- ─────── Présences ───────────────────────────────────────── --}}
    <div id="tab-attendance" class="tab-panel hidden p-5 sm:p-6">
        <div class="flex items-center justify-between mb-5">
            <h4 class="text-sm font-semibold text-slate-800 dark:text-slate-100">
                Historique des présences
                <span class="text-xs font-normal text-slate-500 ml-1">({{ $totalAttendance }})</span>
            </h4>
            <a href="{{ route('attendance.create') }}?student_id={{ $student->id }}" class="inline-flex items-center gap-2 px-3 py-2 rounded-xl text-xs font-semibold
                      bg-emerald-600 hover:bg-emerald-700 text-white transition-all">
                <i class="bi bi-plus-lg"></i>
                Marquer
            </a>
        </div>

        @if($attendances->count())
        <div class="space-y-2">
            @foreach($attendances->sortByDesc('date')->take(15) as $att)
            @php
            $attStatus = strtolower($att->status ?? 'present');
            $attCfg = match($attStatus) {
            'present' => ['icon' => 'bi-check-circle-fill', 'text' => 'text-emerald-600 dark:text-emerald-400', 'bg' =>
            'bg-emerald-50 dark:bg-emerald-900/20', 'label' => 'Présent'],
            'absent' => ['icon' => 'bi-x-circle-fill', 'text' => 'text-red-600 dark:text-red-400', 'bg' => 'bg-red-50
            dark:bg-red-900/20', 'label' => 'Absent'],
            'late' => ['icon' => 'bi-clock-fill', 'text' => 'text-amber-600 dark:text-amber-400', 'bg' => 'bg-amber-50
            dark:bg-amber-900/20', 'label' => 'Retard'],
            default => ['icon' => 'bi-question-circle', 'text' => 'text-slate-500', 'bg' => 'bg-slate-50
            dark:bg-slate-900/50', 'label' => ucfirst($attStatus)],
            };
            $attDate = $att->date ? \Carbon\Carbon::parse($att->date) : $att->created_at;
            @endphp
            <div class="flex items-center gap-3 p-4 rounded-xl
                        border border-slate-200 dark:border-slate-700
                        hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                <div class="w-9 h-9 rounded-xl {{ $attCfg['bg'] }} flex items-center justify-center shrink-0">
                    <i class="bi {{ $attCfg['icon'] }} {{ $attCfg['text'] }}"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-slate-800 dark:text-slate-200">
                        {{ $attDate->locale('fr')->translatedFormat('l d M Y') }}
                    </p>
                    @if($att->remarks ?? $att->note ?? null)
                    <p class="text-xs text-slate-500 truncate">{{ $att->remarks ?? $att->note }}</p>
                    @endif
                </div>
                <span class="text-xs font-semibold {{ $attCfg['text'] }}">{{ $attCfg['label'] }}</span>
                <a href="{{ route('attendance.show', $att) }}" class="w-7 h-7 rounded-lg border border-slate-200 dark:border-slate-600
                          flex items-center justify-center text-slate-400
                          hover:text-blue-600 transition-all">
                    <i class="bi bi-eye text-xs"></i>
                </a>
            </div>
            @endforeach
        </div>
        @else
        <div class="py-16 text-center">
            <div class="w-12 h-12 mx-auto rounded-2xl bg-slate-100 dark:bg-slate-700
                        flex items-center justify-center mb-3">
                <i class="bi bi-calendar-x text-xl text-slate-400"></i>
            </div>
            <p class="text-sm font-medium text-slate-600 dark:text-slate-300">Aucune présence</p>
            <p class="text-xs text-slate-400 mt-1">L'historique des présences apparaîtra ici.</p>
        </div>
        @endif
    </div>

    {{-- ─────── Paiements ───────────────────────────────────────── --}}
    <div id="tab-payments" class="tab-panel hidden p-5 sm:p-6">
        <div class="flex items-center justify-between mb-5">
            <h4 class="text-sm font-semibold text-slate-800 dark:text-slate-100">
                Paiements de l'élève
                <span class="text-xs font-normal text-slate-500 ml-1">({{ $totalPayments }})</span>
            </h4>
            <a href="{{ route('payments.create') }}?student_id={{ $student->id }}" class="inline-flex items-center gap-2 px-3 py-2 rounded-xl text-xs font-semibold
                      bg-blue-600 hover:bg-blue-700 text-white transition-all">
                <i class="bi bi-plus-lg"></i>
                Enregistrer
            </a>
        </div>

        @if($payments->count())
        <div class="space-y-2">
            @foreach($payments->sortByDesc('created_at') as $payment)
            @php
            $pStatus = strtolower($payment->status ?? 'pending');
            $pCfg = match($pStatus) {
            'paid' => ['text' => 'text-emerald-600 dark:text-emerald-400', 'label' => 'Payé', 'icon' =>
            'bi-check-circle-fill'],
            'overdue' => ['text' => 'text-red-600 dark:text-red-400', 'label' => 'En retard', 'icon' =>
            'bi-exclamation-triangle-fill'],
            default => ['text' => 'text-amber-600 dark:text-amber-400', 'label' => 'En attente','icon' =>
            'bi-hourglass-split'],
            };
            @endphp
            <div class="flex items-center gap-3 p-4 rounded-xl
                        border border-slate-200 dark:border-slate-700
                        hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                <div class="w-9 h-9 rounded-xl bg-slate-100 dark:bg-slate-700
                            flex items-center justify-center shrink-0">
                    <i class="bi bi-receipt text-slate-500 dark:text-slate-400"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-slate-800 dark:text-slate-200 truncate">
                        {{ $payment->feeType->name ?? 'Frais' }}
                    </p>
                    <p class="text-xs text-slate-500">
                        {{ $payment->created_at->format('d/m/Y') }}
                        &bull; {{ ucfirst($payment->payment_method ?? 'Espèces') }}
                    </p>
                </div>
                <div class="text-right shrink-0">
                    <p class="text-sm font-bold text-slate-800 dark:text-slate-100">
                        {{ number_format($payment->amount ?? 0, 0, ',', ' ') }}
                        <span class="text-[10px] font-normal text-slate-400">FCFA</span>
                    </p>
                    <p class="text-xs {{ $pCfg['text'] }} font-semibold">{{ $pCfg['label'] }}</p>
                </div>
                <a href="{{ route('payments.show', $payment) }}" class="w-7 h-7 rounded-lg border border-slate-200 dark:border-slate-600
                          flex items-center justify-center text-slate-400
                          hover:text-blue-600 transition-all">
                    <i class="bi bi-eye text-xs"></i>
                </a>
            </div>
            @endforeach
        </div>
        @else
        <div class="py-16 text-center">
            <div class="w-12 h-12 mx-auto rounded-2xl bg-slate-100 dark:bg-slate-700
                        flex items-center justify-center mb-3">
                <i class="bi bi-cash-coin text-xl text-slate-400"></i>
            </div>
            <p class="text-sm font-medium text-slate-600 dark:text-slate-300">Aucun paiement</p>
            <p class="text-xs text-slate-400 mt-1">Les paiements de cet élève apparaîtront ici.</p>
        </div>
        @endif
    </div>

    {{-- ─────── Bulletins ───────────────────────────────────────── --}}
    <div id="tab-reports" class="tab-panel hidden p-5 sm:p-6">
        <div class="flex items-center justify-between mb-5">
            <h4 class="text-sm font-semibold text-slate-800 dark:text-slate-100">
                Bulletins scolaires
                <span class="text-xs font-normal text-slate-500 ml-1">({{ $reportCards->count() }})</span>
            </h4>
        </div>

        @if($reportCards->count())
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @foreach($reportCards->sortByDesc('created_at') as $rc)
            @php
            $rcAvg = (float) ($rc->average ?? 0);
            $rcColor = $rcAvg >= 12
            ? 'emerald' : ($rcAvg >= 10 ? 'amber' : 'red');
            $rcColors = [
            'emerald' => 'text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-900/20 border-emerald-200
            dark:border-emerald-800',
            'amber' => 'text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-900/20 border-amber-200
            dark:border-amber-800',
            'red' => 'text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/20 border-red-200 dark:border-red-800',
            ];
            @endphp
            <div class="p-4 rounded-2xl border border-slate-200 dark:border-slate-700
                        bg-white dark:bg-slate-800 shadow-sm
                        hover:shadow-md hover:border-blue-200 dark:hover:border-blue-800 transition-all">
                <div class="flex items-start gap-3 mb-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-900/20
                                flex items-center justify-center shrink-0">
                        <i class="bi bi-file-earmark-text text-blue-600 dark:text-blue-400"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-slate-800 dark:text-slate-100 truncate">
                            {{ $rc->term->name ?? 'Trimestre' }}
                        </p>
                        <p class="text-xs text-slate-500">
                            {{ $rc->term->academicYear->name ?? '—' }}
                        </p>
                    </div>
                    <span
                        class="text-sm font-black {{ str_contains($rcColors[$rcColor], 'text-') ? explode(' ', $rcColors[$rcColor])[0] : '' }}">
                        {{ number_format($rcAvg, 2) }}/20
                    </span>
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2 text-xs text-slate-500">
                        @if($rc->rank ?? null)
                        <span class="inline-flex items-center gap-1">
                            <i class="bi bi-trophy-fill text-amber-500"></i>
                            Rang {{ $rc->rank }}
                        </span>
                        @endif
                        <span>{{ $rc->created_at->format('d/m/Y') }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('report-cards.show', $rc) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium
                                  bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300
                                  hover:bg-blue-50 dark:hover:bg-blue-900/20 hover:text-blue-600 transition-all">
                            <i class="bi bi-eye"></i> Voir
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="py-16 text-center">
            <div class="w-12 h-12 mx-auto rounded-2xl bg-slate-100 dark:bg-slate-700
                        flex items-center justify-center mb-3">
                <i class="bi bi-file-earmark-x text-xl text-slate-400"></i>
            </div>
            <p class="text-sm font-medium text-slate-600 dark:text-slate-300">Aucun bulletin</p>
            <p class="text-xs text-slate-400 mt-1">Les bulletins générés apparaîtront ici.</p>
        </div>
        @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {

    // ── Gestion des onglets ──────────────────────────────────
    const tabBtns = document.querySelectorAll('.tab-btn');
    const tabPanels = document.querySelectorAll('.tab-panel');

    function switchTab(target) {
        tabBtns.forEach(btn => {
            const isActive = btn.dataset.tab === target;
            btn.classList.toggle('border-blue-600', isActive);
            btn.classList.toggle('text-blue-600', isActive);
            btn.classList.toggle('dark:text-blue-400', isActive);
            btn.classList.toggle('border-transparent', !isActive);
            btn.classList.toggle('text-slate-500', !isActive);
            btn.classList.toggle('dark:text-slate-400', !isActive);
        });
        tabPanels.forEach(panel => {
            panel.classList.toggle('hidden', panel.id !== 'tab-' + target);
        });
    }

    tabBtns.forEach(btn => {
        btn.addEventListener('click', () => switchTab(btn.dataset.tab));
    });

    // Restaurer l'onglet depuis l'URL hash
    const hash = window.location.hash.replace('#', '');
    if (hash && document.getElementById('tab-' + hash)) {
        switchTab(hash);
    }

    // ── Suppression ──────────────────────────────────────────
    const deleteBtn = document.getElementById('delete-student-btn');
    const deleteForm = document.getElementById('delete-student-form');

    deleteBtn?.addEventListener('click', () => {
        if (confirm(
                'Supprimer définitivement cet élève ?\n\n' +
                'Toutes ses notes, présences et paiements seront également supprimés.'
            )) {
            deleteForm.submit();
        }
    });
});
</script>
@endsection