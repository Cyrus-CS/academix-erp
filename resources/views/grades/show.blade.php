@extends('layouts.base')

@section('page_title', 'Détail de note')

@section('breadcrumb')
<span class="text-slate-300 dark:text-slate-600 font-light select-none">/</span>
<a href="{{ route('grades.index') }}"
    class="text-slate-500 dark:text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
    Notes
</a>
@endsection

@section('page_header')
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div class="flex items-center gap-3">
        <a href="{{ route('grades.index') }}" class="w-9 h-9 rounded-xl flex items-center justify-center
                  bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700
                  text-slate-500 hover:text-blue-600 hover:border-blue-300 dark:hover:border-blue-600
                  transition-all duration-200">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-800 dark:text-slate-100">
                Détail de note
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                {{ $grade->student->user->name ?? 'Élève' }} • {{ $grade->subject->name ?? 'Matière' }}
            </p>
        </div>
    </div>

    <div class="flex items-center gap-2">
        <a href="{{ route('grades.edit', $grade) }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-medium
                  bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700
                  text-slate-700 dark:text-slate-300
                  hover:bg-slate-50 dark:hover:bg-slate-700 transition-all duration-200">
            <i class="bi bi-pencil-square"></i>
            <span class="hidden sm:inline">Modifier</span>
        </a>

        <form id="delete-grade-form" action="{{ route('grades.destroy', $grade) }}" method="POST">
            @csrf
            @method('DELETE')
            <button id="delete-grade-btn" type="button"
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold
                           bg-red-600 hover:bg-red-700 text-white shadow-sm hover:shadow-red-500/20 transition-all duration-200">
                <i class="bi bi-trash3"></i>
                <span class="hidden sm:inline">Supprimer</span>
            </button>
        </form>
    </div>
</div>
@endsection

@section('content')
@php
$score = (float) ($grade->score ?? 0);
$percentage = max(0, min(100, round(($score / 20) * 100)));
$coefficient = $grade->coefficient ?? 1;
$evaluationType = $grade->type ?? $grade->evaluation_type ?? 'Évaluation';
$gradeDate = $grade->date ? \Carbon\Carbon::parse($grade->date) : $grade->created_at;

if ($score >= 16) {
$appreciation = 'Excellent';
$color = 'emerald';
} elseif ($score >= 14) {
$appreciation = 'Très bien';
$color = 'blue';
} elseif ($score >= 12) {
$appreciation = 'Bien';
$color = 'cyan';
} elseif ($score >= 10) {
$appreciation = 'Passable';
$color = 'amber';
} else {
$appreciation = 'Insuffisant';
$color = 'red';
}

$colorClasses = [
'emerald' => [
'text' => 'text-emerald-700 dark:text-emerald-300',
'bg' => 'bg-emerald-50 dark:bg-emerald-900/20',
'border' => 'border-emerald-200 dark:border-emerald-800',
'bar' => 'bg-emerald-500',
],
'blue' => [
'text' => 'text-blue-700 dark:text-blue-300',
'bg' => 'bg-blue-50 dark:bg-blue-900/20',
'border' => 'border-blue-200 dark:border-blue-800',
'bar' => 'bg-blue-500',
],
'cyan' => [
'text' => 'text-cyan-700 dark:text-cyan-300',
'bg' => 'bg-cyan-50 dark:bg-cyan-900/20',
'border' => 'border-cyan-200 dark:border-cyan-800',
'bar' => 'bg-cyan-500',
],
'amber' => [
'text' => 'text-amber-700 dark:text-amber-300',
'bg' => 'bg-amber-50 dark:bg-amber-900/20',
'border' => 'border-amber-200 dark:border-amber-800',
'bar' => 'bg-amber-500',
],
'red' => [
'text' => 'text-red-700 dark:text-red-300',
'bg' => 'bg-red-50 dark:bg-red-900/20',
'border' => 'border-red-200 dark:border-red-800',
'bar' => 'bg-red-500',
],
];

$cfg = $colorClasses[$color];
@endphp

{{-- Hero --}}
<div
    class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden mb-6">
    <div class="p-6 sm:p-8">
        <div class="flex flex-col lg:flex-row lg:items-start gap-6">
            <div
                class="w-18 h-18 rounded-3xl shrink-0 flex items-center justify-center border shadow-lg {{ $cfg['bg'] }} {{ $cfg['border'] }}">
                <div class="text-center">
                    <p class="text-2xl font-black {{ $cfg['text'] }}">{{ number_format($score, 2) }}</p>
                    <p class="text-[10px] font-semibold text-slate-500">/20</p>
                </div>
            </div>

            <div class="flex-1 min-w-0">
                <div class="flex flex-wrap items-center gap-2.5 mb-3">
                    <span
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold border {{ $cfg['bg'] }} {{ $cfg['border'] }} {{ $cfg['text'] }}">
                        <i class="bi bi-award-fill"></i>
                        {{ $appreciation }}
                    </span>

                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-medium
                                 bg-slate-100 dark:bg-slate-700
                                 text-slate-600 dark:text-slate-300
                                 border border-slate-200 dark:border-slate-600">
                        <i class="bi bi-journal-text"></i>
                        {{ $evaluationType }}
                    </span>

                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-medium
                                 bg-blue-50 dark:bg-blue-900/20
                                 text-blue-700 dark:text-blue-300
                                 border border-blue-200 dark:border-blue-800">
                        <i class="bi bi-book-fill"></i>
                        {{ $grade->subject->name ?? 'Matière' }}
                    </span>
                </div>

                <h2 class="text-2xl font-bold text-slate-800 dark:text-slate-100">
                    {{ $grade->student->user->name ?? 'Élève inconnu' }}
                </h2>

                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                    {{ $grade->schoolClass->name ?? 'Classe non définie' }} •
                    {{ $grade->term->name ?? 'Trimestre' }} •
                    {{ $grade->term->academicYear->name ?? 'Année académique' }}
                </p>

                <div class="mt-5">
                    <div class="flex items-center justify-between text-xs mb-1.5">
                        <span class="font-medium text-slate-500 dark:text-slate-400">Performance</span>
                        <span class="font-bold {{ $cfg['text'] }}">{{ $percentage }}%</span>
                    </div>
                    <div class="w-full h-2.5 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
                        <div class="h-full rounded-full transition-all duration-700 {{ $cfg['bar'] }}"
                            style="width: {{ $percentage }}%"></div>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-6">
                    <div
                        class="rounded-xl bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 p-4">
                        <p class="text-[11px] uppercase tracking-wider font-semibold text-slate-400">Note</p>
                        <p class="text-lg font-bold mt-1 {{ $cfg['text'] }}">{{ number_format($score, 2) }}/20</p>
                    </div>
                    <div
                        class="rounded-xl bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 p-4">
                        <p class="text-[11px] uppercase tracking-wider font-semibold text-slate-400">Coefficient</p>
                        <p class="text-lg font-bold text-slate-800 dark:text-slate-100 mt-1">{{ $coefficient }}</p>
                    </div>
                    <div
                        class="rounded-xl bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 p-4">
                        <p class="text-[11px] uppercase tracking-wider font-semibold text-slate-400">Date</p>
                        <p class="text-lg font-bold text-slate-800 dark:text-slate-100 mt-1">
                            {{ $gradeDate->format('d/m/Y') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="h-1 w-full {{ $cfg['bar'] }}"></div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
    {{-- Main --}}
    <div class="lg:col-span-8 space-y-6">
        <div
            class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
            <div
                class="px-5 sm:px-6 py-4 border-b border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/50">
                <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-100 flex items-center gap-2">
                    <i class="bi bi-card-checklist text-blue-500"></i>
                    Détails de l'évaluation
                </h3>
            </div>

            <div class="p-5 sm:p-6 grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <p class="text-[11px] uppercase tracking-wider font-semibold text-slate-400 mb-1">Type d'évaluation
                    </p>
                    <p class="text-sm font-semibold text-slate-800 dark:text-slate-100">{{ $evaluationType }}</p>
                </div>

                <div>
                    <p class="text-[11px] uppercase tracking-wider font-semibold text-slate-400 mb-1">Matière</p>
                    <p class="text-sm font-semibold text-slate-800 dark:text-slate-100">
                        {{ $grade->subject->name ?? '—' }}</p>
                </div>

                <div>
                    <p class="text-[11px] uppercase tracking-wider font-semibold text-slate-400 mb-1">Trimestre /
                        Période</p>
                    <p class="text-sm font-semibold text-slate-800 dark:text-slate-100">{{ $grade->term->name ?? '—' }}
                    </p>
                </div>

                <div>
                    <p class="text-[11px] uppercase tracking-wider font-semibold text-slate-400 mb-1">Année académique
                    </p>
                    <p class="text-sm font-semibold text-slate-800 dark:text-slate-100">
                        {{ $grade->term->academicYear->name ?? '—' }}</p>
                </div>

                <div>
                    <p class="text-[11px] uppercase tracking-wider font-semibold text-slate-400 mb-1">Classe</p>
                    <p class="text-sm font-semibold text-slate-800 dark:text-slate-100">
                        {{ $grade->schoolClass->name ?? '—' }}</p>
                </div>

                <div>
                    <p class="text-[11px] uppercase tracking-wider font-semibold text-slate-400 mb-1">Date de saisie</p>
                    <p class="text-sm font-semibold text-slate-800 dark:text-slate-100">
                        {{ $grade->created_at->format('d/m/Y à H:i') }}</p>
                </div>

                <div class="md:col-span-2">
                    <p class="text-[11px] uppercase tracking-wider font-semibold text-slate-400 mb-1.5">Observation /
                        Remarque</p>
                    <div
                        class="rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/40 p-4 min-h-24">
                        <p class="text-sm text-slate-700 dark:text-slate-300 leading-relaxed">
                            {{ $grade->remark ?? $grade->remarks ?? $grade->comment ?? 'Aucune observation ajoutée pour cette note.' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div
            class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm p-5 sm:p-6">
            <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-100 flex items-center gap-2 mb-4">
                <i class="bi bi-bar-chart-line-fill text-emerald-500"></i>
                Lecture de la performance
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="rounded-xl border {{ $cfg['border'] }} {{ $cfg['bg'] }} p-4">
                    <p class="text-[11px] uppercase tracking-wider font-semibold text-slate-400">Appréciation</p>
                    <p class="text-base font-bold mt-1 {{ $cfg['text'] }}">{{ $appreciation }}</p>
                    <p class="text-xs mt-1 text-slate-500 dark:text-slate-400">
                        Résultat basé sur une note de {{ number_format($score, 2) }}/20.
                    </p>
                </div>

                <div
                    class="rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/40 p-4">
                    <p class="text-[11px] uppercase tracking-wider font-semibold text-slate-400">Impact pondéré</p>
                    <p class="text-base font-bold mt-1 text-slate-800 dark:text-slate-100">
                        {{ number_format($score * $coefficient, 2) }}
                    </p>
                    <p class="text-xs mt-1 text-slate-500 dark:text-slate-400">
                        Score pondéré avec coefficient {{ $coefficient }}.
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Sidebar --}}
    <div class="lg:col-span-4 space-y-6">
        <div
            class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/50">
                <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-100 flex items-center gap-2">
                    <i class="bi bi-person-fill text-blue-500"></i>
                    Élève concerné
                </h3>
            </div>

            <div class="p-5">
                <div class="flex items-center gap-3">
                    <div
                        class="w-12 h-12 rounded-full bg-linear-to-br from-blue-600 to-emerald-500 flex items-center justify-center text-white font-bold text-sm">
                        {{ strtoupper(substr($grade->student->user->name ?? 'E', 0, 2)) }}
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-slate-800 dark:text-slate-100 truncate">
                            {{ $grade->student->user->name ?? 'Élève' }}</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400 truncate">
                            {{ $grade->student->user->email ?? 'Aucun email' }}</p>
                    </div>
                </div>

                <div class="mt-4 space-y-3">
                    <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400">
                        <i class="bi bi-collection text-slate-400"></i>
                        <span>{{ $grade->schoolClass->name ?? 'Classe non définie' }}</span>
                    </div>
                    <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400">
                        <i class="bi bi-book text-slate-400"></i>
                        <span>{{ $grade->subject->name ?? 'Matière non définie' }}</span>
                    </div>
                </div>

                <a href="{{ route('students.show', $grade->student) }}" class="mt-5 w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-sm font-medium
                          bg-blue-600 hover:bg-blue-700 text-white transition-all duration-200">
                    <i class="bi bi-eye"></i>
                    Voir l'élève
                </a>
            </div>
        </div>

        <div
            class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/50">
                <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-100 flex items-center gap-2">
                    <i class="bi bi-person-workspace text-emerald-500"></i>
                    Enseignant
                </h3>
            </div>

            <div class="p-5">
                @if($grade->teacher)
                <div class="flex items-center gap-3">
                    <div
                        class="w-11 h-11 rounded-xl bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center text-emerald-600 dark:text-emerald-400 font-bold">
                        {{ strtoupper(substr($grade->teacher->user->name ?? 'T', 0, 1)) }}
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-slate-800 dark:text-slate-100 truncate">
                            {{ $grade->teacher->user->name ?? '—' }}</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400">
                            {{ $grade->teacher->employee_number ?? 'Matricule non défini' }}</p>
                    </div>
                </div>

                <a href="{{ route('teachers.show', $grade->teacher) }}"
                    class="mt-5 w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-sm font-medium
                              bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600
                              text-slate-700 dark:text-slate-300 hover:bg-white dark:hover:bg-slate-700 transition-all duration-200">
                    <i class="bi bi-box-arrow-up-right"></i>
                    Voir le profil enseignant
                </a>
                @else
                <p class="text-sm text-slate-500 dark:text-slate-400">Aucun enseignant associé à cette note.</p>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const btn = document.getElementById('delete-grade-btn');
    const form = document.getElementById('delete-grade-form');

    if (btn && form) {
        btn.addEventListener('click', () => {
            if (confirm('Supprimer cette note ?')) {
                form.submit();
            }
        });
    }
});
</script>
@endsection