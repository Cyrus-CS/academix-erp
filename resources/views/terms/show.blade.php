@extends('layouts.base')

@section('page_title', 'Trimestre : ' . $term->name)

@section('breadcrumb')
<span class="text-slate-300 dark:text-slate-600">/</span>
<a href="{{ route('terms.index') }}"
    class="text-slate-500 dark:text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
    Trimestres
</a>
@endsection

@section('page_header')
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div class="flex items-center gap-3">
        <a href="{{ route('terms.index') }}" class="w-9 h-9 rounded-xl flex items-center justify-center
                  bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700
                  text-slate-500 hover:text-blue-600 hover:border-blue-300
                  dark:hover:border-blue-600 transition-all duration-200">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-800 dark:text-slate-100">
                {{ $term->name }}
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                {{ $term->academicYear->name ?? 'Année non définie' }}
                &bull; du {{ \Carbon\Carbon::parse($term->start_date)->format('d/m/Y') }}
                au {{ \Carbon\Carbon::parse($term->end_date)->format('d/m/Y') }}
            </p>
        </div>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ route('terms.edit', $term) }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-medium
                  bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700
                  text-slate-700 dark:text-slate-300
                  hover:bg-slate-50 dark:hover:bg-slate-700 transition-all">
            <i class="bi bi-pencil-square"></i>
            <span class="hidden sm:inline">Modifier</span>
        </a>
        <form id="delete-term-form" action="{{ route('terms.destroy', $term) }}" method="POST">
            @csrf @method('DELETE')
            <button id="delete-term-btn" type="button" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold
                           bg-red-600 hover:bg-red-700 text-white
                           shadow-sm hover:shadow-red-500/20 transition-all">
                <i class="bi bi-trash3"></i>
                <span class="hidden sm:inline">Supprimer</span>
            </button>
        </form>
    </div>
</div>
@endsection

@section('content')
@php
$start = \Carbon\Carbon::parse($term->start_date);
$end = \Carbon\Carbon::parse($term->end_date);
$today = now();
$totalDays = $start->diffInDays($end);
$passedDays = min($start->diffInDays($today), $totalDays);
$progress = $totalDays > 0 ? round(($passedDays / $totalDays) * 100) : 0;
$isActive = $today->between($start, $end);
$isPast = $today->isAfter($end);
$isFuture = $today->isBefore($start);
$statusLabel = $isActive ? 'En cours' : ($isPast ? 'Terminé' : 'À venir');
$statusColor = $isActive ? 'emerald' : ($isPast ? 'slate' : 'blue');
@endphp

{{-- ── Hero ─────────────────────────────────────────────────────── --}}
<div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700
            shadow-sm overflow-hidden mb-6">
    <div class="p-6 sm:p-8">
        <div class="flex flex-col lg:flex-row gap-6">
            <div class="flex-1">
                <div class="flex flex-wrap items-center gap-3 mb-4">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-blue-600 to-emerald-500
                                flex items-center justify-center shadow-lg shadow-blue-500/20 shrink-0">
                        <i class="bi bi-calendar3-week-fill text-white text-xl"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2.5">
                            <h2 class="text-xl font-bold text-slate-800 dark:text-slate-100">{{ $term->name }}</h2>
                            <span
                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold
                                {{ $statusColor === 'emerald' ? 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800' : '' }}
                                {{ $statusColor === 'slate' ? 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-600' : '' }}
                                {{ $statusColor === 'blue' ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800' : '' }}">
                                @if($isActive)<span
                                    class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>@endif
                                {{ $statusLabel }}
                            </span>
                        </div>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                            {{ $term->academicYear->name ?? 'Année académique' }}
                        </p>
                    </div>
                </div>

                {{-- Dates timeline --}}
                <div class="flex flex-col sm:flex-row gap-3 mb-5">
                    <div class="flex items-center gap-3 p-3.5 rounded-xl
                                bg-slate-50 dark:bg-slate-900/50
                                border border-slate-200 dark:border-slate-700 flex-1">
                        <i class="bi bi-calendar-plus text-blue-500 text-lg"></i>
                        <div>
                            <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">Début</p>
                            <p class="text-sm font-bold text-slate-800 dark:text-slate-100">
                                {{ $start->format('d M Y') }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 p-3.5 rounded-xl
                                bg-slate-50 dark:bg-slate-900/50
                                border border-slate-200 dark:border-slate-700 flex-1">
                        <i class="bi bi-calendar-minus text-red-500 text-lg"></i>
                        <div>
                            <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">Fin</p>
                            <p class="text-sm font-bold text-slate-800 dark:text-slate-100">{{ $end->format('d M Y') }}
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 p-3.5 rounded-xl
                                bg-slate-50 dark:bg-slate-900/50
                                border border-slate-200 dark:border-slate-700 flex-1">
                        <i class="bi bi-hourglass-split text-amber-500 text-lg"></i>
                        <div>
                            <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">Durée</p>
                            <p class="text-sm font-bold text-slate-800 dark:text-slate-100">{{ $totalDays }} jours</p>
                        </div>
                    </div>
                </div>

                {{-- Progress Bar --}}
                <div class="space-y-1.5">
                    <div class="flex items-center justify-between text-xs">
                        <span class="font-medium text-slate-600 dark:text-slate-400">Progression du trimestre</span>
                        <span
                            class="font-bold
                            {{ $progress >= 75 ? 'text-red-500' : ($progress >= 50 ? 'text-amber-500' : 'text-emerald-600') }}">
                            {{ $progress }}%
                        </span>
                    </div>
                    <div class="w-full h-2.5 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
                        <div class="h-full rounded-full transition-all duration-700
                            {{ $progress >= 75 ? 'bg-red-500' : ($progress >= 50 ? 'bg-amber-500' : 'bg-gradient-to-r from-blue-600 to-emerald-500') }}"
                            style="width: {{ $progress }}%"></div>
                    </div>
                    <p class="text-[11px] text-slate-400">
                        {{ $passedDays }} / {{ $totalDays }} jours écoulés
                        @if($isActive) &bull; Se termine dans {{ $today->diffInDays($end) }} jours @endif
                        @if($isPast) &bull; Terminé il y a {{ $end->diffForHumans() }} @endif
                        @if($isFuture) &bull; Commence dans {{ $today->diffInDays($start) }} jours @endif
                    </p>
                </div>
            </div>
        </div>
    </div>
    <div class="h-1 w-full bg-gradient-to-r from-blue-600 to-emerald-500"></div>
</div>

{{-- ── Stats ────────────────────────────────────────────────────── --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
    @foreach([
    ['label' => 'Notes saisies', 'value' => $stats['total_grades'], 'sub' => 'Total évaluations', 'icon' =>
    'bi-star-fill', 'color' => 'blue'],
    ['label' => 'Bulletins générés', 'value' => $stats['total_report_cards'], 'sub' => 'Documents PDF', 'icon' =>
    'bi-file-earmark-text-fill', 'color' => 'emerald'],
    ['label' => 'Moyenne générale', 'value' => $stats['avg_grade'].'/20', 'sub' => 'Toutes matières confondues', 'icon'
    => 'bi-graph-up-arrow', 'color' => $stats['avg_grade'] >= 12 ? 'emerald' : ($stats['avg_grade'] >= 10 ? 'amber' :
    'red')],
    ] as $s)
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5 shadow-sm">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">{{ $s['label'] }}</p>
                <p class="text-2xl font-bold text-slate-800 dark:text-slate-100 mt-1.5">{{ $s['value'] }}</p>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">{{ $s['sub'] }}</p>
            </div>
            <div class="w-10 h-10 rounded-xl flex items-center justify-center
                {{ $s['color'] === 'blue' ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400' : '' }}
                {{ $s['color'] === 'emerald' ? 'bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400' : '' }}
                {{ $s['color'] === 'amber' ? 'bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400' : '' }}
                {{ $s['color'] === 'red' ? 'bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400' : '' }}">
                <i class="bi {{ $s['icon'] }} text-lg"></i>
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- ── Contenu principal ───────────────────────────────────────── --}}
<div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

    {{-- Dernières notes --}}
    <div class="lg:col-span-7 bg-white dark:bg-slate-800 rounded-2xl border
                border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
        <div class="px-5 sm:px-6 py-4 border-b border-slate-200 dark:border-slate-700
                    bg-slate-50/50 dark:bg-slate-800/50 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-100 flex items-center gap-2">
                <i class="bi bi-award text-amber-500"></i> Dernières notes saisies
            </h3>
            <a href="{{ route('grades.index') }}?term_id={{ $term->id }}"
                class="text-xs font-medium text-blue-600 dark:text-blue-400 hover:underline">
                Tout voir
            </a>
        </div>

        @forelse($term->grades as $grade)
        <div class="px-5 py-3.5 flex flex-col sm:flex-row sm:items-center gap-3
                    border-b border-slate-100 dark:border-slate-700/50 last:border-0
                    hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
            <div class="flex items-center gap-3 flex-1 min-w-0">
                <div class="w-8 h-8 rounded-full
                            bg-gradient-to-br from-blue-500 to-emerald-500
                            flex items-center justify-center text-white text-[10px] font-bold shrink-0">
                    {{ strtoupper(substr($grade->student->user->name ?? 'E', 0, 1)) }}
                </div>
                <div class="min-w-0">
                    <p class="text-sm font-medium text-slate-800 dark:text-slate-200 truncate">
                        {{ $grade->student->user->name ?? 'Élève' }}
                    </p>
                    <p class="text-xs text-slate-500 dark:text-slate-400 truncate">
                        {{ $grade->subject->name ?? 'Matière' }}
                        &bull; {{ $grade->type ?? 'Évaluation' }}
                    </p>
                </div>
            </div>
            <div class="text-right shrink-0">
                <span
                    class="inline-block px-3 py-1 rounded-full text-xs font-bold
                    {{ $grade->score >= 12 ? 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-300' : ($grade->score >= 10 ? 'bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-300' : 'bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-300') }}">
                    {{ $grade->score }}/20
                </span>
                <p class="text-[11px] text-slate-400 mt-1">{{ $grade->created_at->diffForHumans() }}</p>
            </div>
        </div>
        @empty
        <div class="py-16 text-center">
            <div class="w-12 h-12 mx-auto rounded-2xl bg-slate-100 dark:bg-slate-700
                        flex items-center justify-center mb-3">
                <i class="bi bi-journal-x text-xl text-slate-400"></i>
            </div>
            <p class="text-sm font-medium text-slate-600 dark:text-slate-300">Aucune note</p>
            <p class="text-xs text-slate-400 mt-1">Les évaluations apparaîtront ici.</p>
        </div>
        @endforelse
    </div>

    {{-- Bulletins récents --}}
    <div class="lg:col-span-5 bg-white dark:bg-slate-800 rounded-2xl border
                border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden flex flex-col">
        <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-700
                    bg-slate-50/50 dark:bg-slate-800/50 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-100 flex items-center gap-2">
                <i class="bi bi-file-earmark-text text-emerald-500"></i> Bulletins générés
            </h3>
            @can('create', \App\Models\ReportCard::class)
            <a href="{{ route('report-cards.generate-all') }}"
                class="text-xs font-medium text-blue-600 dark:text-blue-400 hover:underline">
                Générer tous
            </a>
            @endcan
        </div>

        <div class="flex-1 divide-y divide-slate-100 dark:divide-slate-700/50">
            @forelse($term->reportCards as $rc)
            <div class="p-4 flex items-center gap-3
                        hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                <div class="w-9 h-9 rounded-xl bg-emerald-50 dark:bg-emerald-900/20
                            flex items-center justify-center shrink-0">
                    <i class="bi bi-file-earmark-pdf text-emerald-600 dark:text-emerald-400"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-slate-800 dark:text-slate-200 truncate">
                        {{ $rc->student->user->name ?? 'Élève' }}
                    </p>
                    <p class="text-xs text-slate-500">
                        {{ $rc->created_at->format('d/m/Y') }}
                        &bull; Moy. {{ number_format($rc->average ?? 0, 2) }}/20
                    </p>
                </div>
                <a href="{{ route('report-cards.show', $rc) }}" class="w-7 h-7 rounded-lg bg-white dark:bg-slate-700
                          border border-slate-200 dark:border-slate-600
                          flex items-center justify-center
                          text-slate-500 hover:text-emerald-600
                          hover:border-emerald-300 dark:hover:border-emerald-500
                          transition-all">
                    <i class="bi bi-eye text-xs"></i>
                </a>
            </div>
            @empty
            <div class="py-16 text-center">
                <div class="w-12 h-12 mx-auto rounded-2xl bg-slate-100 dark:bg-slate-700
                            flex items-center justify-center mb-3">
                    <i class="bi bi-file-earmark-x text-xl text-slate-400"></i>
                </div>
                <p class="text-sm font-medium text-slate-600 dark:text-slate-300">
                    Aucun bulletin
                </p>
                <p class="text-xs text-slate-400 mt-1">
                    Générez les bulletins pour ce trimestre.
                </p>
                @can('create', \App\Models\ReportCard::class)
                <form action="{{ route('report-cards.generate-all') }}" method="POST" class="mt-4">
                    @csrf
                    <input type="hidden" name="term_id" value="{{ $term->id }}">
                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold
                                   bg-emerald-600 hover:bg-emerald-700 text-white transition-all">
                        <i class="bi bi-file-earmark-plus"></i> Générer les bulletins
                    </button>
                </form>
                @endcan
            </div>
            @endforelse
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const btn = document.getElementById('delete-term-btn');
    const form = document.getElementById('delete-term-form');
    btn?.addEventListener('click', () => {
        if (confirm('Supprimer ce trimestre ? Toutes les notes et bulletins liés seront affectés.')) {
            form.submit();
        }
    });
});
</script>
@endsection