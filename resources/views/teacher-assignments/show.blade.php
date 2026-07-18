@extends('layouts.base')

@section('page_title', 'Affectation enseignant')

@section('breadcrumb')
<span class="text-slate-300 dark:text-slate-600">/</span>
<a href="{{ route('teacher-assignments.index') }}"
    class="text-slate-500 dark:text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
    Affectations
</a>
@endsection

@section('page_header')
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div class="flex items-center gap-3">
        <a href="{{ route('teacher-assignments.index') }}" class="w-9 h-9 rounded-xl flex items-center justify-center
                  bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700
                  text-slate-500 hover:text-blue-600 hover:border-blue-300 dark:hover:border-blue-600
                  transition-all duration-200">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-800 dark:text-slate-100">
                Détail de l'affectation
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                {{ $teacherAssignment->teacher->user->name ?? '—' }}
                &rarr;
                {{ $teacherAssignment->subject->name ?? '—' }}
                &bull;
                {{ $teacherAssignment->schoolClass->name ?? '—' }}
            </p>
        </div>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ route('teacher-assignments.edit', $teacherAssignment) }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-medium
                  bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700
                  text-slate-700 dark:text-slate-300
                  hover:bg-slate-50 dark:hover:bg-slate-700 transition-all duration-200">
            <i class="bi bi-pencil-square"></i>
            <span class="hidden sm:inline">Modifier</span>
        </a>
        <form id="delete-assign-form" action="{{ route('teacher-assignments.destroy', $teacherAssignment) }}"
            method="POST">
            @csrf @method('DELETE')
            <button id="delete-assign-btn" type="button" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold
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
$studentsCount = $teacherAssignment->schoolClass?->students->count() ?? 0;
@endphp

{{-- ── Hero ─────────────────────────────────────────────────────── --}}
<div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700
            shadow-sm overflow-hidden mb-6">
    <div class="grid grid-cols-1 lg:grid-cols-12">

        {{-- Gauche --}}
        <div class="lg:col-span-8 p-6 sm:p-8">

            {{-- Badges --}}
            <div class="flex flex-wrap gap-2 mb-5">
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full
                             text-xs font-bold bg-blue-600 text-white shadow-sm shadow-blue-500/20">
                    <i class="bi bi-diagram-3-fill"></i>
                    {{ $teacherAssignment->academicYear->name ?? 'Année non définie' }}
                </span>
                @if($teacherAssignment->is_active ?? true)
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full
                             text-xs font-semibold bg-emerald-50 dark:bg-emerald-900/20
                             text-emerald-700 dark:text-emerald-300
                             border border-emerald-200 dark:border-emerald-800">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    Affectation active
                </span>
                @else
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full
                             text-xs font-semibold bg-slate-100 dark:bg-slate-700
                             text-slate-500 dark:text-slate-400
                             border border-slate-200 dark:border-slate-600">
                    <i class="bi bi-pause-circle"></i> Inactive
                </span>
                @endif
            </div>

            {{-- Titre principal --}}
            <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-linear-to-br from-blue-600 to-emerald-500
                            flex items-center justify-center shadow-lg shadow-blue-500/20 shrink-0">
                    <i class="bi bi-person-check-fill text-white text-2xl"></i>
                </div>
                <div>
                    <h2 class="text-xl sm:text-2xl font-bold text-slate-800 dark:text-slate-100">
                        {{ $teacherAssignment->teacher->user->name ?? 'Enseignant inconnu' }}
                    </h2>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                        {{ $teacherAssignment->teacher->qualification ?? 'Enseignant' }}
                        &bull; Matricule :
                        <span class="font-mono font-semibold text-slate-700 dark:text-slate-300">
                            {{ $teacherAssignment->teacher->employee_number ?? '—' }}
                        </span>
                    </p>
                </div>
            </div>

            {{-- Ligne de liaison --}}
            <div class="mt-6 flex flex-col sm:flex-row items-start sm:items-center gap-3">

                {{-- Matière --}}
                <div class="flex-1 flex items-center gap-3 p-4 rounded-xl
                            bg-amber-50 dark:bg-amber-900/10
                            border border-amber-200 dark:border-amber-800">
                    <div class="w-9 h-9 rounded-lg bg-amber-100 dark:bg-amber-900/40
                                flex items-center justify-center shrink-0">
                        <i class="bi bi-journal-bookmark-fill text-amber-600 dark:text-amber-400"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-[10px] font-semibold uppercase tracking-wider
                                  text-amber-500 dark:text-amber-400">Matière</p>
                        <p class="text-sm font-bold text-slate-800 dark:text-slate-100 truncate">
                            {{ $teacherAssignment->subject->name ?? '—' }}
                        </p>
                        <p class="text-[11px] text-slate-500">
                            Code : {{ $teacherAssignment->subject->code ?? '—' }}
                            &bull; Coeff {{ $teacherAssignment->subject->coefficient ?? '—' }}
                        </p>
                    </div>
                </div>

                <div class="hidden sm:flex items-center text-slate-300 dark:text-slate-600 text-xl font-light px-2">
                    <i class="bi bi-arrow-right-circle-fill text-blue-300 dark:text-blue-700"></i>
                </div>

                {{-- Classe --}}
                <div class="flex-1 flex items-center gap-3 p-4 rounded-xl
                            bg-blue-50 dark:bg-blue-900/10
                            border border-blue-200 dark:border-blue-800">
                    <div class="w-9 h-9 rounded-lg bg-blue-100 dark:bg-blue-900/40
                                flex items-center justify-center shrink-0">
                        <i class="bi bi-collection-fill text-blue-600 dark:text-blue-400"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-[10px] font-semibold uppercase tracking-wider
                                  text-blue-500 dark:text-blue-400">Classe</p>
                        <p class="text-sm font-bold text-slate-800 dark:text-slate-100 truncate">
                            {{ $teacherAssignment->schoolClass->name ?? '—' }}
                        </p>
                        <p class="text-[11px] text-slate-500">
                            {{ $studentsCount }} élève(s) inscrit(s)
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Droite --}}
        <div class="lg:col-span-4 bg-linear-to-br from-slate-50 to-blue-50/30
                    dark:from-slate-900 dark:to-blue-950/20 p-6 sm:p-8
                    border-t lg:border-t-0 lg:border-l border-slate-200 dark:border-slate-700">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-5">
                Informations complémentaires
            </h3>
            <div class="space-y-4">
                @foreach([
                ['icon' => 'bi-calendar-range', 'color' => 'blue', 'label' => 'Année académique', 'value' =>
                $teacherAssignment->academicYear->name ?? '—'],
                ['icon' => 'bi-calendar-plus', 'color' => 'emerald', 'label' => 'Créé le', 'value' =>
                $teacherAssignment->created_at->format('d/m/Y')],
                ['icon' => 'bi-calendar-check', 'color' => 'amber', 'label' => 'Mis à jour', 'value' =>
                $teacherAssignment->updated_at->diffForHumans()],
                ] as $info)
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl
                        {{ $info['color'] === 'blue' ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400' : '' }}
                        {{ $info['color'] === 'emerald' ? 'bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400' : '' }}
                        {{ $info['color'] === 'amber' ? 'bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400' : '' }}
                        flex items-center justify-center shrink-0 border
                        {{ $info['color'] === 'blue' ? 'border-blue-100 dark:border-blue-800' : '' }}
                        {{ $info['color'] === 'emerald' ? 'border-emerald-100 dark:border-emerald-800' : '' }}
                        {{ $info['color'] === 'amber' ? 'border-amber-100 dark:border-amber-800' : '' }}">
                        <i class="bi {{ $info['icon'] }} text-sm"></i>
                    </div>
                    <div>
                        <p class="text-[10px] text-slate-500 dark:text-slate-400">{{ $info['label'] }}</p>
                        <p class="text-sm font-semibold text-slate-800 dark:text-slate-100">{{ $info['value'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Liens rapides --}}
            <div class="mt-6 space-y-2">
                <a href="{{ route('teachers.show', $teacherAssignment->teacher) }}" class="flex items-center justify-between p-3 rounded-xl
                          bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700
                          hover:border-blue-300 dark:hover:border-blue-600
                          hover:bg-blue-50 dark:hover:bg-blue-950/20
                          transition-all duration-200 group">
                    <span
                        class="text-xs font-medium text-slate-700 dark:text-slate-300 group-hover:text-blue-600 dark:group-hover:text-blue-400">
                        <i class="bi bi-person-workspace mr-1.5"></i> Profil enseignant
                    </span>
                    <i class="bi bi-arrow-right-short text-slate-400 group-hover:text-blue-500 transition-colors"></i>
                </a>
                <a href="{{ route('subjects.show', $teacherAssignment->subject) }}" class="flex items-center justify-between p-3 rounded-xl
                          bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700
                          hover:border-amber-300 dark:hover:border-amber-600
                          hover:bg-amber-50 dark:hover:bg-amber-950/20
                          transition-all duration-200 group">
                    <span
                        class="text-xs font-medium text-slate-700 dark:text-slate-300 group-hover:text-amber-600 dark:group-hover:text-amber-400">
                        <i class="bi bi-journal-bookmark mr-1.5"></i> Détail matière
                    </span>
                    <i class="bi bi-arrow-right-short text-slate-400 group-hover:text-amber-500 transition-colors"></i>
                </a>
                <a href="{{ route('classes.show', $teacherAssignment->schoolClass) }}" class="flex items-center justify-between p-3 rounded-xl
                          bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700
                          hover:border-emerald-300 dark:hover:border-emerald-600
                          hover:bg-emerald-50 dark:hover:bg-emerald-950/20
                          transition-all duration-200 group">
                    <span
                        class="text-xs font-medium text-slate-700 dark:text-slate-300 group-hover:text-emerald-600 dark:group-hover:text-emerald-400">
                        <i class="bi bi-collection mr-1.5"></i> Voir la classe
                    </span>
                    <i
                        class="bi bi-arrow-right-short text-slate-400 group-hover:text-emerald-500 transition-colors"></i>
                </a>
            </div>
        </div>
    </div>
    <div class="h-1 w-full bg-linear-to-r from-blue-600 via-blue-500 to-emerald-500"></div>
</div>

{{-- ── Liste des élèves de la classe ───────────────────────────── --}}
<div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700
            shadow-sm overflow-hidden">
    <div class="px-5 sm:px-6 py-4 border-b border-slate-200 dark:border-slate-700
                bg-slate-50/50 dark:bg-slate-800/50
                flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-100 flex items-center gap-2">
            <i class="bi bi-people-fill text-blue-500"></i>
            Élèves de la classe
            <span class="text-xs font-normal text-slate-500 dark:text-slate-400">
                ({{ $studentsCount }})
            </span>
        </h3>
        @if($studentsCount > 0)
        <div class="relative">
            <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
            <input type="text" id="student-search" placeholder="Filtrer les élèves…" class="pl-8 pr-4 py-2 text-xs rounded-lg w-full sm:w-48
                          bg-white dark:bg-slate-700
                          border border-slate-200 dark:border-slate-600
                          text-slate-700 dark:text-slate-300
                          placeholder:text-slate-400
                          focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500
                          transition-all duration-200">
        </div>
        @endif
    </div>

    @if($teacherAssignment->schoolClass?->students->count())

    {{-- Desktop --}}
    <div class="hidden sm:block overflow-x-auto">
        <table class="w-full text-sm" id="students-table">
            <thead class="text-[11px] uppercase font-semibold tracking-wider
                              text-slate-400 dark:text-slate-500
                              bg-slate-50 dark:bg-slate-900/50">
                <tr>
                    <th class="px-5 py-3 text-left">#</th>
                    <th class="px-5 py-3 text-left">Élève</th>
                    <th class="px-5 py-3 text-left">Email</th>
                    <th class="px-5 py-3 text-left">Matricule</th>
                    <th class="px-5 py-3 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50" id="students-body">
                @foreach($teacherAssignment->schoolClass->students as $i => $student)
                <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors student-row"
                    data-name="{{ strtolower($student->user->name ?? '') }}">
                    <td class="px-5 py-3 text-slate-400 dark:text-slate-500 font-mono text-xs">
                        {{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}
                    </td>
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full
                                            bg-linear-to-br from-blue-500 to-emerald-500
                                            flex items-center justify-center
                                            text-white text-[10px] font-bold shrink-0">
                                {{ strtoupper(substr($student->user->name ?? 'E', 0, 2)) }}
                            </div>
                            <p class="font-medium text-slate-800 dark:text-slate-200 truncate">
                                {{ $student->user->name ?? 'N/A' }}
                            </p>
                        </div>
                    </td>
                    <td class="px-5 py-3 text-slate-500 dark:text-slate-400 text-xs">
                        {{ $student->user->email ?? '—' }}
                    </td>
                    <td class="px-5 py-3">
                        <span class="px-2.5 py-1 rounded-full text-xs font-mono
                                         bg-slate-100 dark:bg-slate-700
                                         text-slate-600 dark:text-slate-300">
                            {{ $student->admission_number ?? $student->matricule ?? '—' }}
                        </span>
                    </td>
                    <td class="px-5 py-3 text-right">
                        <a href="{{ route('students.show', $student) }}" class="inline-flex w-7 h-7 items-center justify-center rounded-lg
                                      border border-slate-200 dark:border-slate-600
                                      text-slate-500 hover:text-blue-600
                                      hover:border-blue-300 dark:hover:border-blue-500
                                      transition-all">
                            <i class="bi bi-eye text-xs"></i>
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Mobile --}}
    <div class="sm:hidden divide-y divide-slate-100 dark:divide-slate-700" id="students-mobile">
        @foreach($teacherAssignment->schoolClass->students as $student)
        <div class="p-4 flex items-center gap-3 student-row" data-name="{{ strtolower($student->user->name ?? '') }}">
            <div class="w-10 h-10 rounded-full
                            bg-linear-to-br from-blue-500 to-emerald-500
                            flex items-center justify-center text-white text-xs font-bold shrink-0">
                {{ strtoupper(substr($student->user->name ?? 'E', 0, 1)) }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-slate-800 dark:text-slate-200 truncate">
                    {{ $student->user->name ?? 'N/A' }}
                </p>
                <p class="text-xs text-slate-500">
                    {{ $student->admission_number ?? '—' }}
                </p>
            </div>
            <a href="{{ route('students.show', $student) }}" class="w-8 h-8 rounded-xl bg-slate-50 dark:bg-slate-700
                          flex items-center justify-center text-slate-400
                          hover:text-blue-600 transition-colors">
                <i class="bi bi-chevron-right text-xs"></i>
            </a>
        </div>
        @endforeach
    </div>

    {{-- Empty search --}}
    <div id="no-results" class="hidden py-12 text-center">
        <i class="bi bi-search text-2xl text-slate-300 dark:text-slate-600"></i>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-2">Aucun élève trouvé</p>
    </div>

    @else
    <div class="py-16 text-center">
        <div class="w-14 h-14 mx-auto rounded-2xl bg-slate-100 dark:bg-slate-700
                        flex items-center justify-center mb-3">
            <i class="bi bi-people text-2xl text-slate-400"></i>
        </div>
        <p class="text-sm font-medium text-slate-600 dark:text-slate-300">Aucun élève inscrit</p>
        <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">
            Cette classe ne contient pas encore d'élèves.
        </p>
    </div>
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {

    // ── Suppression ──
    const deleteBtn = document.getElementById('delete-assign-btn');
    const deleteForm = document.getElementById('delete-assign-form');
    deleteBtn?.addEventListener('click', () => {
        if (confirm('Supprimer définitivement cette affectation ?')) deleteForm.submit();
    });

    // ── Recherche élèves ──
    const searchInput = document.getElementById('student-search');
    const rows = document.querySelectorAll('.student-row');
    const noResults = document.getElementById('no-results');

    searchInput?.addEventListener('input', () => {
        const q = searchInput.value.toLowerCase().trim();
        let visible = 0;
        rows.forEach(row => {
            const name = row.dataset.name ?? '';
            const show = name.includes(q);
            row.style.display = show ? '' : 'none';
            if (show) visible++;
        });
        noResults?.classList.toggle('hidden', visible > 0);
    });
});
</script>
@endsection