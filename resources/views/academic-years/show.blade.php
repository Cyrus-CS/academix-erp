@extends('layouts.base')

@section('page_title', 'Année académique : ' . $academicYear->name)

@section('breadcrumb')
<span class="text-slate-300 dark:text-slate-600 font-light select-none">/</span>
<a href="{{ route('academic-years.index') }}"
    class="text-slate-500 dark:text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
    Années académiques
</a>
@endsection

@section('page_header')
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div class="flex items-center gap-3">
        <a href="{{ route('academic-years.index') }}" class="w-9 h-9 rounded-xl flex items-center justify-center
                  bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700
                  text-slate-500 hover:text-blue-600 hover:border-blue-300 dark:hover:border-blue-600
                  transition-all duration-200">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-800 dark:text-slate-100">
                {{ $academicYear->name }}
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                Du {{ \Carbon\Carbon::parse($academicYear->start_date)->format('d M Y') }}
                au {{ \Carbon\Carbon::parse($academicYear->end_date)->format('d M Y') }}
            </p>
        </div>
    </div>

    <div class="flex flex-wrap items-center gap-2">
        @if(!$academicYear->is_current)
        <form id="set-current-form" action="{{ route('academic-years.update', $academicYear) }}" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" name="is_current" value="1">
            <input type="hidden" name="name" value="{{ $academicYear->name }}">
            <input type="hidden" name="start_date" value="{{ $academicYear->start_date }}">
            <input type="hidden" name="end_date" value="{{ $academicYear->end_date }}">
            <button id="set-current-btn" type="button" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold
                           bg-emerald-600 hover:bg-emerald-700 text-white
                           shadow-sm hover:shadow-emerald-500/20 transition-all duration-200">
                <i class="bi bi-check-circle-fill"></i>
                <span class="hidden sm:inline">Définir comme active</span>
            </button>
        </form>
        @endif

        <a href="{{ route('academic-years.edit', $academicYear) }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-medium
                  bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700
                  text-slate-700 dark:text-slate-300
                  hover:bg-slate-50 dark:hover:bg-slate-700 transition-all duration-200">
            <i class="bi bi-pencil-square"></i>
            <span class="hidden sm:inline">Modifier</span>
        </a>

        <form id="delete-year-form" action="{{ route('academic-years.destroy', $academicYear) }}" method="POST">
            @csrf
            @method('DELETE')
            <button id="delete-year-btn" type="button" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold
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
$start = \Carbon\Carbon::parse($academicYear->start_date);
$end = \Carbon\Carbon::parse($academicYear->end_date);
$today = now();
$isCurrent = (bool) $academicYear->is_current;

$totalDays = $start->diffInDays($end);
$passedDays = min($start->diffInDays($today), $totalDays);
$progress = $totalDays > 0 ? round(($passedDays / $totalDays) * 100) : 0;
$progress = max(0, min(100, $progress));

$isActive = $today->between($start, $end);
$isPast = $today->isAfter($end);
$isFuture = $today->isBefore($start);

$statusLabel = $isCurrent ? 'Année active' : ($isPast ? 'Terminée' : ($isFuture ? 'À venir' : 'En cours'));
$statusColor = $isCurrent ? 'emerald' : ($isPast ? 'slate' : ($isFuture ? 'blue' : 'cyan'));

// Données liées (chargées si dispo, sinon valeur 0)
$termsCount = $academicYear->terms?->count() ?? 0;
$schedCount = $academicYear->schedules?->count() ?? 0;
$assignCount = $academicYear->teacherAssignments?->count() ?? 0;
@endphp

{{-- ── Banner année active ─────────────────────────────────────── --}}
@if($isCurrent)
<div class="mb-5 flex items-center gap-3 px-4 py-3.5 rounded-xl
            bg-emerald-50 dark:bg-emerald-900/20
            border border-emerald-200 dark:border-emerald-800
            text-emerald-700 dark:text-emerald-300">
    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse shrink-0"></span>
    <p class="text-sm font-semibold">
        C'est l'année académique actuellement active.
        Elle est visible dans toute l'application.
    </p>
</div>
@endif

{{-- ── Hero ─────────────────────────────────────────────────────── --}}
<div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200
            dark:border-slate-700 shadow-sm overflow-hidden mb-6">
    <div class="p-6 sm:p-8">
        <div class="flex flex-col lg:flex-row gap-6">

            {{-- Icône --}}
            <div class="w-16 h-16 rounded-2xl bg-linear-to-br from-blue-600 to-indigo-600
                        flex items-center justify-center shadow-lg shadow-blue-500/20 shrink-0">
                <i class="bi bi-calendar-range-fill text-white text-2xl"></i>
            </div>

            <div class="flex-1 min-w-0">
                {{-- Badges --}}
                <div class="flex flex-wrap gap-2.5 mb-3">
                    @if($isCurrent)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold
                                 bg-emerald-600 text-white shadow-sm">
                        <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span>
                        Année active
                    </span>
                    @endif
                    <span
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold
                        {{ $statusColor === 'emerald' ? 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800' : '' }}
                        {{ $statusColor === 'slate'   ? 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-600' : '' }}
                        {{ $statusColor === 'blue'    ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800' : '' }}
                        {{ $statusColor === 'cyan'    ? 'bg-cyan-50 dark:bg-cyan-900/20 text-cyan-700 dark:text-cyan-300 border border-cyan-200 dark:border-cyan-800' : '' }}">
                        <i class="bi bi-dot text-xl -mx-1"></i>
                        {{ $statusLabel }}
                    </span>
                </div>

                <h2 class="text-2xl font-bold text-slate-800 dark:text-slate-100">
                    {{ $academicYear->name }}
                </h2>

                {{-- Dates --}}
                <div class="flex flex-col sm:flex-row gap-3 mt-5 mb-5">
                    <div class="flex items-center gap-3 p-3.5 rounded-xl flex-1
                                bg-slate-50 dark:bg-slate-900/50
                                border border-slate-200 dark:border-slate-700">
                        <i class="bi bi-calendar-plus text-blue-500 text-lg shrink-0"></i>
                        <div>
                            <p class="text-[10px] uppercase tracking-wider font-semibold text-slate-400">Début</p>
                            <p class="text-sm font-bold text-slate-800 dark:text-slate-100">
                                {{ $start->format('d M Y') }}
                            </p>
                        </div>
                    </div>

                    <div class="hidden sm:flex items-center text-slate-300 dark:text-slate-600">
                        <i class="bi bi-arrow-right text-lg"></i>
                    </div>

                    <div class="flex items-center gap-3 p-3.5 rounded-xl flex-1
                                bg-slate-50 dark:bg-slate-900/50
                                border border-slate-200 dark:border-slate-700">
                        <i class="bi bi-calendar-minus text-red-500 text-lg shrink-0"></i>
                        <div>
                            <p class="text-[10px] uppercase tracking-wider font-semibold text-slate-400">Fin</p>
                            <p class="text-sm font-bold text-slate-800 dark:text-slate-100">
                                {{ $end->format('d M Y') }}
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 p-3.5 rounded-xl flex-1
                                bg-slate-50 dark:bg-slate-900/50
                                border border-slate-200 dark:border-slate-700">
                        <i class="bi bi-hourglass-split text-amber-500 text-lg shrink-0"></i>
                        <div>
                            <p class="text-[10px] uppercase tracking-wider font-semibold text-slate-400">Durée</p>
                            <p class="text-sm font-bold text-slate-800 dark:text-slate-100">
                                {{ $totalDays }} jours
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Progress --}}
                <div class="space-y-1.5">
                    <div class="flex items-center justify-between text-xs">
                        <span class="font-medium text-slate-500 dark:text-slate-400">
                            Progression de l'année
                        </span>
                        <span
                            class="font-bold
                            {{ $progress >= 75 ? 'text-red-500' : ($progress >= 50 ? 'text-amber-500' : 'text-blue-600 dark:text-blue-400') }}">
                            {{ $progress }}%
                        </span>
                    </div>
                    <div class="w-full h-2.5 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
                        <div class="h-full rounded-full transition-all duration-700
                            {{ $progress >= 75 ? 'bg-red-500' : ($progress >= 50 ? 'bg-amber-500' : 'bg-linear-to-r from-blue-600 to-indigo-500') }}"
                            style="width: {{ $progress }}%"></div>
                    </div>
                    <p class="text-[11px] text-slate-400">
                        {{ $passedDays }} / {{ $totalDays }} jours
                        @if($isActive && !$isCurrent)
                        &bull; Se termine dans {{ $today->diffInDays($end) }} jours
                        @elseif($isPast)
                        &bull; Terminée il y a {{ $end->diffForHumans() }}
                        @elseif($isFuture)
                        &bull; Commence dans {{ $today->diffInDays($start) }} jours
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>
    <div class="h-1 w-full bg-linear-to-r from-blue-600 to-indigo-600"></div>
</div>

{{-- ── Stats ─────────────────────────────────────────────────────── --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
    @foreach([
    ['label' => 'Trimestres', 'value' => $termsCount, 'sub' => 'Périodes définies', 'icon' => 'bi-calendar3-week-fill',
    'color' => 'blue', 'route' => 'terms.index', 'param' => 'academic_year_id'],
    ['label' => 'Emplois du temps', 'value' => $schedCount, 'sub' => 'Créneaux planifiés', 'icon' => 'bi-clock-history',
    'color' => 'emerald', 'route' => 'timetables.index', 'param' => 'academic_year_id'],
    ['label' => 'Affectations', 'value' => $assignCount, 'sub' => 'Enseignants assignés', 'icon' =>
    'bi-person-check-fill', 'color' => 'amber', 'route' => 'teacher-assignments.index', 'param' => 'academic_year_id'],
    ] as $s)
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200
                dark:border-slate-700 p-5 shadow-sm group hover:shadow-md transition-shadow">
        <div class="flex items-start justify-between gap-2 mb-4">
            <div class="min-w-0">
                <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">
                    {{ $s['label'] }}
                </p>
                <p class="text-3xl font-black text-slate-800 dark:text-slate-100 mt-1.5">
                    {{ $s['value'] }}
                </p>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">{{ $s['sub'] }}</p>
            </div>
            <div
                class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0
                {{ $s['color'] === 'blue'    ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400' : '' }}
                {{ $s['color'] === 'emerald' ? 'bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400' : '' }}
                {{ $s['color'] === 'amber'   ? 'bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400' : '' }}">
                <i class="bi {{ $s['icon'] }} text-xl"></i>
            </div>
        </div>
        <a href="{{ route($s['route']) }}?{{ $s['param'] }}={{ $academicYear->id }}" class="inline-flex items-center gap-1.5 text-xs font-semibold
                  {{ $s['color'] === 'blue'    ? 'text-blue-600 dark:text-blue-400' : '' }}
                  {{ $s['color'] === 'emerald' ? 'text-emerald-600 dark:text-emerald-400' : '' }}
                  {{ $s['color'] === 'amber'   ? 'text-amber-600 dark:text-amber-400' : '' }}
                  hover:underline transition-all">
            Voir tout
            <i class="bi bi-arrow-right-short text-base"></i>
        </a>
    </div>
    @endforeach
</div>

{{-- ── Trimestres + Sidebar ─────────────────────────────────────── --}}
<div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

    {{-- Trimestres --}}
    <div class="lg:col-span-8 bg-white dark:bg-slate-800 rounded-2xl border
                border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
        <div class="px-5 sm:px-6 py-4 border-b border-slate-200 dark:border-slate-700
                    bg-slate-50/50 dark:bg-slate-800/50 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-100 flex items-center gap-2">
                <i class="bi bi-calendar3-week-fill text-blue-500"></i>
                Trimestres de l'année
            </h3>
            <a href="{{ route('terms.create') }}?academic_year_id={{ $academicYear->id }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold
                      bg-blue-600 hover:bg-blue-700 text-white transition-all">
                <i class="bi bi-plus-lg"></i>
                Ajouter
            </a>
        </div>

        @if($academicYear->terms?->count())
        <div class="divide-y divide-slate-100 dark:divide-slate-700/50">
            @foreach($academicYear->terms->sortBy('start_date') as $i => $term)
            @php
            $tStart = \Carbon\Carbon::parse($term->start_date);
            $tEnd = \Carbon\Carbon::parse($term->end_date);
            $tDays = $tStart->diffInDays($tEnd);
            $tPassed = min($tStart->diffInDays($today), $tDays);
            $tPct = $tDays > 0 ? round(($tPassed / $tDays) * 100) : 0;
            $tPct = max(0, min(100, $tPct));

            $tIsActive = $today->between($tStart, $tEnd);
            $tIsPast = $today->isAfter($tEnd);

            $tStatusLabel = $tIsActive ? 'En cours' : ($tIsPast ? 'Terminé' : 'À venir');
            $tStatusColor = $tIsActive ? 'emerald' : ($tIsPast ? 'slate' : 'blue');
            @endphp
            <div class="p-5 hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                    {{-- Numéro --}}
                    <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-900/20
                                flex items-center justify-center shrink-0 text-blue-600
                                dark:text-blue-400 font-black text-sm">
                        {{ $i + 1 }}
                    </div>

                    <div class="flex-1 min-w-0">
                        <div class="flex flex-wrap items-center gap-2 mb-1.5">
                            <p class="text-sm font-semibold text-slate-800 dark:text-slate-100">
                                {{ $term->name }}
                            </p>
                            <span
                                class="px-2 py-0.5 rounded-full text-[10px] font-semibold
                                {{ $tStatusColor === 'emerald' ? 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800' : '' }}
                                {{ $tStatusColor === 'slate'   ? 'bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400' : '' }}
                                {{ $tStatusColor === 'blue'    ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800' : '' }}">
                                {{ $tStatusLabel }}
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 dark:text-slate-400">
                            {{ $tStart->format('d M Y') }} → {{ $tEnd->format('d M Y') }}
                            &bull; {{ $tDays }} jours
                        </p>

                        {{-- Mini progress --}}
                        <div class="mt-2 flex items-center gap-2">
                            <div class="flex-1 h-1.5 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
                                <div class="h-full rounded-full
                                    {{ $tIsActive ? 'bg-emerald-500' : ($tIsPast ? 'bg-slate-400' : 'bg-blue-500') }}"
                                    style="width: {{ $tPct }}%"></div>
                            </div>
                            <span class="text-[10px] font-semibold text-slate-400 w-8 text-right">
                                {{ $tPct }}%
                            </span>
                        </div>
                    </div>

                    <a href="{{ route('terms.show', $term) }}" class="w-8 h-8 rounded-xl border border-slate-200 dark:border-slate-600
                              flex items-center justify-center text-slate-400
                              hover:text-blue-600 hover:border-blue-300 dark:hover:border-blue-500
                              transition-all shrink-0">
                        <i class="bi bi-eye text-xs"></i>
                    </a>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="py-16 text-center">
            <div class="w-14 h-14 mx-auto rounded-2xl bg-slate-100 dark:bg-slate-700
                        flex items-center justify-center mb-3">
                <i class="bi bi-calendar-x text-2xl text-slate-400"></i>
            </div>
            <p class="text-sm font-medium text-slate-600 dark:text-slate-300">
                Aucun trimestre défini
            </p>
            <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">
                Créez les trimestres pour structurer cette année académique.
            </p>
            <a href="{{ route('terms.create') }}?academic_year_id={{ $academicYear->id }}" class="mt-4 inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold
                      bg-blue-600 hover:bg-blue-700 text-white transition-all">
                <i class="bi bi-plus-circle"></i>
                Créer le premier trimestre
            </a>
        </div>
        @endif
    </div>

    {{-- Sidebar --}}
    <div class="lg:col-span-4 space-y-6">

        {{-- Résumé --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200
                    dark:border-slate-700 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-700
                        bg-slate-50/50 dark:bg-slate-800/50">
                <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-100 flex items-center gap-2">
                    <i class="bi bi-info-circle text-blue-500"></i>
                    Résumé
                </h3>
            </div>
            <div class="p-5 space-y-4">
                @foreach([
                ['icon' => 'bi-calendar-range', 'label' => 'Nom', 'value' => $academicYear->name, 'color' => 'blue'],
                ['icon' => 'bi-calendar-plus', 'label' => 'Début', 'value' => $start->format('d/m/Y'), 'color' =>
                'emerald'],
                ['icon' => 'bi-calendar-minus', 'label' => 'Fin', 'value' => $end->format('d/m/Y'), 'color' => 'red'],
                ['icon' => 'bi-hourglass', 'label' => 'Durée', 'value' => $totalDays.' jours ('.round($totalDays/30).'
                mois)', 'color' => 'amber'],
                ['icon' => 'bi-toggle-on', 'label' => 'Statut', 'value' => $isCurrent ? 'Active' : $statusLabel, 'color'
                => $isCurrent ? 'emerald' : 'slate'],
                ['icon' => 'bi-clock', 'label' => 'Créée', 'value' => $academicYear->created_at->format('d/m/Y'),
                'color' => 'slate'],
                ] as $info)
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0
                        {{ $info['color'] === 'blue'    ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-500' : '' }}
                        {{ $info['color'] === 'emerald' ? 'bg-emerald-50 dark:bg-emerald-900/30 text-emerald-500' : '' }}
                        {{ $info['color'] === 'red'     ? 'bg-red-50 dark:bg-red-900/30 text-red-500' : '' }}
                        {{ $info['color'] === 'amber'   ? 'bg-amber-50 dark:bg-amber-900/30 text-amber-500' : '' }}
                        {{ $info['color'] === 'slate'   ? 'bg-slate-100 dark:bg-slate-700 text-slate-500' : '' }}">
                        <i class="bi {{ $info['icon'] }} text-sm"></i>
                    </div>
                    <div class="min-w-0">
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

        {{-- Liens rapides --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200
                    dark:border-slate-700 shadow-sm p-4 space-y-2">
            <p class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">
                Liens rapides
            </p>
            @foreach([
            ['route' => 'timetables.index', 'param' => ['academic_year_id' => $academicYear->id], 'icon' => 'bi-clock',
            'label' => 'Emplois du temps', 'color' => 'emerald'],
            ['route' => 'teacher-assignments.index', 'param' => ['academic_year_id' => $academicYear->id], 'icon' =>
            'bi-person-check', 'label' => 'Affectations', 'color' => 'blue'],
            ['route' => 'grades.index', 'param' => [], 'icon' => 'bi-star', 'label' => 'Notes de l\'année', 'color' =>
            'amber'],
            ['route' => 'report-cards.index', 'param' => [], 'icon' => 'bi-file-earmark-text', 'label' => 'Bulletins',
            'color' => 'cyan'],
            ] as $link)
            <a href="{{ route($link['route']) }}{{ count($link['param']) ? '?'.http_build_query($link['param']) : '' }}"
                class="flex items-center gap-3 p-3 rounded-xl text-sm
                      border border-slate-100 dark:border-slate-700
                      text-slate-700 dark:text-slate-300
                      hover:bg-blue-50 dark:hover:bg-blue-950/20
                      hover:border-blue-200 dark:hover:border-blue-800
                      hover:text-blue-600 dark:hover:text-blue-400
                      transition-all duration-200 group">
                <i class="bi {{ $link['icon'] }} text-base"></i>
                {{ $link['label'] }}
                <i class="bi bi-arrow-right-short ml-auto opacity-0 group-hover:opacity-100 transition-opacity"></i>
            </a>
            @endforeach
        </div>

        {{-- CTA si pas active --}}
        @if(!$isCurrent && !$isPast)
        <div
            class="bg-linear-to-br from-blue-600 to-indigo-600 rounded-2xl p-5 text-white shadow-lg shadow-blue-500/20">
            <div class="flex items-start gap-3">
                <i class="bi bi-lightbulb-fill text-white/80 text-lg shrink-0 mt-0.5"></i>
                <div>
                    <p class="text-sm font-bold">Activer cette année ?</p>
                    <p class="text-xs mt-1 text-white/80 leading-relaxed">
                        Définissez cette année comme active pour l'utiliser dans toute l'application.
                    </p>
                    <button id="set-current-btn-2" type="button" class="mt-3 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full
                                   text-xs font-bold bg-white/20 hover:bg-white/30 backdrop-blur
                                   text-white transition-all">
                        <i class="bi bi-check-circle-fill"></i>
                        Définir comme active
                    </button>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {

    // ── Suppression ──────────────────────────────────────────
    const deleteBtn = document.getElementById('delete-year-btn');
    const deleteForm = document.getElementById('delete-year-form');

    deleteBtn?.addEventListener('click', () => {
        if (confirm(
                'Supprimer l\'année académique "{{ addslashes($academicYear->name) }}" ?\n\n' +
                'Attention : tous les trimestres, emplois du temps et affectations liés seront supprimés.'
            )) {
            deleteForm.submit();
        }
    });

    // ── Définir comme active ─────────────────────────────────
    const setCurrentForm = document.getElementById('set-current-form');

    const handleSetCurrent = () => {
        if (confirm(
                'Définir "{{ addslashes($academicYear->name) }}" comme année active ?\n\n' +
                'L\'ancienne année active sera automatiquement désactivée.'
            )) {
            setCurrentForm?.submit();
        }
    };

    document.getElementById('set-current-btn')
        ?.addEventListener('click', handleSetCurrent);

    document.getElementById('set-current-btn-2')
        ?.addEventListener('click', handleSetCurrent);
});
</script>
@endsection