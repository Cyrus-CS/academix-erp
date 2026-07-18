@extends('layouts.base')

@section('page_title', 'Emploi du temps')

@section('breadcrumb')
<span class="text-slate-300 dark:text-slate-600">/</span>
<a href="{{ route('timetables.index') }}"
    class="text-slate-500 dark:text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Emplois du
    temps</a>
@endsection

@section('page_header')
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div class="flex items-center gap-3">
        <a href="{{ route('timetables.index') }}"
            class="w-9 h-9 rounded-xl flex items-center justify-center bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-500 hover:text-blue-600 transition-all">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-800 dark:text-slate-100">Créneau horaire</h1>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                {{ $timetable->subject->name ?? 'Matière' }} • {{ $timetable->classe->name ?? 'Classe' }}</p>
        </div>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ route('timetables.edit', $timetable) }}"
            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-medium bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-50 transition-all">
            <i class="bi bi-pencil-square"></i> Modifier
        </a>
        <form id="delete-timetable-form" action="{{ route('timetables.destroy', $timetable) }}" method="POST">
            @csrf @method('DELETE')
            <button id="delete-timetable-btn" type="button"
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold bg-red-600 hover:bg-red-700 text-white shadow-sm transition-all">
                <i class="bi bi-trash3"></i> Supprimer
            </button>
        </form>
    </div>
</div>
@endsection

@section('content')
@php
$days =
['monday'=>'Lundi','tuesday'=>'Mardi','wednesday'=>'Mercredi','thursday'=>'Jeudi','friday'=>'Vendredi','saturday'=>'Samedi','sunday'=>'Dimanche'];
$dayLabel = $days[strtolower($timetable->day_of_week ?? '')] ?? ucfirst($timetable->day_of_week ?? '—');
$start = \Carbon\Carbon::parse($timetable->start_time);
$end = \Carbon\Carbon::parse($timetable->end_time);
$duration = $start->diffInMinutes($end);
$hours = intdiv($duration, 60);
$mins = $duration % 60;
@endphp

{{-- Hero Timeline --}}
<div
    class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden mb-6">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-0">
        <div class="lg:col-span-8 p-6 sm:p-8">
            <div class="flex flex-wrap items-center gap-3 mb-5">
                <span
                    class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-bold bg-blue-600 text-white shadow-sm shadow-blue-500/20">
                    <i class="bi bi-calendar-week"></i> {{ $dayLabel }}
                </span>
                <span
                    class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-semibold bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-600">
                    <i class="bi bi-clock"></i> {{ $start->format('H:i') }} - {{ $end->format('H:i') }}
                </span>
                @if($timetable->room)
                <span
                    class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-medium bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800">
                    <i class="bi bi-geo-alt"></i> Salle {{ $timetable->room }}
                </span>
                @endif
            </div>

            <h2 class="text-2xl sm:text-3xl font-bold text-slate-800 dark:text-slate-100 flex items-center gap-3">
                <div
                    class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-600 to-emerald-500 flex items-center justify-center shadow-md">
                    <i class="bi bi-journal-bookmark-fill text-white"></i>
                </div>
                {{ $timetable->subject->name ?? 'Matière non définie' }}
                <span class="text-sm font-medium text-slate-400">{{ $timetable->subject->code ?? '' }}</span>
            </h2>

            <div class="mt-6 grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div
                    class="rounded-xl bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 p-4">
                    <p class="text-[11px] uppercase tracking-wider font-semibold text-slate-400">Classe</p>
                    <p class="text-sm font-semibold text-slate-800 dark:text-slate-100 mt-1 flex items-center gap-2"><i
                            class="bi bi-collection text-blue-500"></i> {{ $timetable->classe->name ?? '—' }}</p>
                    <p class="text-xs text-slate-500 mt-1">{{ $timetable->classe->level ?? '' }}</p>
                </div>
                <div
                    class="rounded-xl bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 p-4">
                    <p class="text-[11px] uppercase tracking-wider font-semibold text-slate-400">Enseignant</p>
                    <p class="text-sm font-semibold text-slate-800 dark:text-slate-100 mt-1 flex items-center gap-2"><i
                            class="bi bi-person-workspace text-emerald-500"></i>
                        {{ $timetable->teacher->user->name ?? '—' }}</p>
                    <p class="text-xs text-slate-500 mt-1">{{ $timetable->teacher->employee_number ?? '' }}</p>
                </div>
                <div
                    class="rounded-xl bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 p-4">
                    <p class="text-[11px] uppercase tracking-wider font-semibold text-slate-400">Durée</p>
                    <p class="text-sm font-semibold text-slate-800 dark:text-slate-100 mt-1">
                        {{ $hours > 0 ? $hours.'h ' : '' }}{{ $mins > 0 ? $mins.'min' : '' }} @if($duration==0) 0 min
                        @endif</p>
                    <div class="mt-2 w-full h-1.5 bg-slate-200 dark:bg-slate-700 rounded-full overflow-hidden">
                        <div class="h-full bg-gradient-to-r from-blue-600 to-emerald-500"
                            style="width: {{ min(100, ($duration/120)*100) }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        <div
            class="lg:col-span-4 bg-gradient-to-br from-slate-50 to-blue-50/50 dark:from-slate-900 dark:to-blue-950/20 p-6 sm:p-8 border-t lg:border-t-0 lg:border-l border-slate-200 dark:border-slate-700 flex flex-col justify-between">
            <div>
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-4">Informations académiques</h3>
                <div class="space-y-4">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-9 h-9 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center">
                            <i class="bi bi-calendar-range text-blue-500"></i>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500">Année académique</p>
                            <p class="text-sm font-semibold text-slate-800 dark:text-slate-100">
                                {{ $timetable->academicYear->name ?? $timetable->academic_year_id ?? 'Non assignée' }}
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <div
                            class="w-9 h-9 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center">
                            <i class="bi bi-clock-history text-amber-500"></i>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500">Horaires</p>
                            <p class="text-sm font-semibold text-slate-800 dark:text-slate-100">
                                {{ $start->format('H:i') }} → {{ $end->format('H:i') }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <div
                            class="w-9 h-9 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center">
                            <i class="bi bi-door-open text-emerald-500"></i>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500">Salle / Lieu</p>
                            <p class="text-sm font-semibold text-slate-800 dark:text-slate-100">
                                {{ $timetable->room ?? 'Non définie' }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div
                class="mt-6 p-3 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center gap-2.5">
                <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>
                <p class="text-xs font-medium text-slate-600 dark:text-slate-300">Créneau actif pour cette année</p>
            </div>
        </div>
    </div>
    <div class="h-1 w-full bg-gradient-to-r from-blue-600 to-emerald-500"></div>
</div>

{{-- Details Grid --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
        {{-- Teacher Card --}}
        <div
            class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm p-5 sm:p-6">
            <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-100 flex items-center gap-2 mb-4"><i
                    class="bi bi-person-badge-fill text-blue-500"></i> Détails enseignant</h3>
            @if($timetable->teacher)
            <div class="flex items-start gap-4">
                <div
                    class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-600 to-emerald-500 flex items-center justify-center text-white font-bold text-sm">
                    {{ strtoupper(substr($timetable->teacher->user->name ?? 'T',0,2)) }}</div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-slate-800 dark:text-slate-100">
                        {{ $timetable->teacher->user->name }}</p>
                    <p class="text-xs text-slate-500 mt-0.5">{{ $timetable->teacher->qualification ?? 'Enseignant' }} •
                        {{ $timetable->teacher->nationalité ?? $timetable->teacher->nationality ?? '' }}</p>
                    <div class="flex flex-wrap gap-2 mt-3">
                        <span
                            class="px-2.5 py-1 rounded-full bg-slate-100 dark:bg-slate-700 text-xs text-slate-600 dark:text-slate-300"><i
                                class="bi bi-hash"></i> {{ $timetable->teacher->employee_number ?? '|' }}</span>
                        <a href="{{ route('teachers.show', $timetable->teacher) }}"
                            class="px-2.5 py-1 rounded-full bg-blue-50 dark:bg-blue-900/30 text-xs font-medium text-blue-700 dark:text-blue-300 border border-blue-100 dark:border-blue-800 hover:bg-blue-100 transition-colors">Voir
                            profil <i class="bi bi-arrow-right-short"></i></a>
                    </div>
                </div>
            </div>
            @else
            <p class="text-sm text-slate-400">Aucun enseignant assigné.</p>
            @endif
        </div>

        {{-- Subject Card --}}
        <div
            class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm p-5 sm:p-6">
            <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-100 flex items-center gap-2 mb-4"><i
                    class="bi bi-book-half text-emerald-500"></i> Détails matière</h3>
            @if($timetable->subject)
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-base font-bold text-slate-800 dark:text-slate-100">{{ $timetable->subject->name }}
                    </p>
                    <p class="text-xs text-slate-500 mt-1">Code: {{ $timetable->subject->code ?? '—' }} • Coeff:
                        {{ $timetable->subject->coefficient ?? $timetable->subject->coef ?? '—' }}</p>
                </div>
                <a href="{{ route('subjects.show', $timetable->subject) }}"
                    class="w-8 h-8 rounded-xl bg-slate-50 dark:bg-slate-700 flex items-center justify-center text-slate-500 hover:text-blue-600 transition-colors"><i
                        class="bi bi-box-arrow-up-right"></i></a>
            </div>
            @endif
        </div>
    </div>

    <div class="space-y-6">
        <div
            class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-700">
                <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-100">Classe concernée</h3>
            </div>
            <div class="p-5">
                @if($timetable->classe)
                <div class="flex items-center gap-3">
                    <div
                        class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-blue-400">
                        <i class="bi bi-collection-fill"></i>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-slate-800 dark:text-slate-100">
                            {{ $timetable->classe->name }}</p>
                        <p class="text-xs text-slate-500">{{ $timetable->classe->students->count() ?? 0 }} élèves •
                            {{ $timetable->classe->level ?? '' }}</p>
                    </div>
                </div>
                <a href="{{ route('classes.show', $timetable->classe) }}"
                    class="mt-4 w-full inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl text-sm font-medium bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600 text-slate-700 dark:text-slate-300 hover:bg-white dark:hover:bg-slate-700 transition-all"><i
                        class="bi bi-eye"></i> Voir la classe</a>
                @endif
            </div>
        </div>

        <div
            class="bg-gradient-to-br from-blue-600 to-emerald-600 rounded-2xl p-5 text-white shadow-lg shadow-blue-500/20">
            <div class="flex items-center gap-2 mb-3"><i class="bi bi-lightbulb-fill text-white/80"></i>
                <p class="text-sm font-semibold">Astuce</p>
            </div>
            <p class="text-xs leading-relaxed text-white/90">Tu peux déplacer ce créneau directement depuis la vue
                calendrier avec le drag & drop (SortableJS). Les modifications seront enregistrées automatiquement.</p>
            <a href="{{ route('timetables.index') }}"
                class="mt-4 inline-flex items-center gap-1.5 text-xs font-semibold bg-white/15 hover:bg-white/25 backdrop-blur px-3 py-1.5 rounded-full transition-all">Aller
                au planning <i class="bi bi-arrow-right"></i></a>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const btn = document.getElementById('delete-timetable-btn');
    const form = document.getElementById('delete-timetable-form');
    if (btn && form) {
        btn.addEventListener('click', () => {
            if (confirm('Supprimer ce créneau ?')) form.submit();
        });
    }
});
</script>
@endsection