@extends('layouts.base')

@section('page_title', 'Affectations enseignants')

@section('breadcrumb')
<span class="text-slate-300 dark:text-slate-600 font-light select-none">/</span>
<span class="font-medium text-slate-600 dark:text-slate-300">Affectations</span>
@endsection

@section('page_header')
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <h1 class="text-xl sm:text-2xl font-bold text-slate-800 dark:text-slate-100">
            Affectations enseignants
        </h1>
        <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">
            Gestion des affectations matières / classes / enseignants
        </p>
    </div>
    <a href="{{ route('teacher-assignments.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold
              bg-blue-600 hover:bg-blue-700 text-white
              shadow-sm hover:shadow-blue-500/20 transition-all duration-200">
        <i class="bi bi-plus-circle-fill"></i>
        Nouvelle affectation
    </a>
</div>
@endsection

@section('content')

{{-- ── Stats ─────────────────────────────────────────────────────── --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    @foreach([
    [
    'label' => 'Total affectations',
    'value' => $stats['total'] ?? 0,
    'sub' => 'Toutes années confondues',
    'icon' => 'bi-diagram-3-fill',
    'color' => 'blue',
    ],
    [
    'label' => 'Année en cours',
    'value' => $stats['current_year'] ?? 0,
    'sub' => 'Affectations actives',
    'icon' => 'bi-calendar-check-fill',
    'color' => 'emerald',
    ],
    [
    'label' => 'Enseignants',
    'value' => $stats['teachers'] ?? 0,
    'sub' => 'Affectés',
    'icon' => 'bi-person-workspace',
    'color' => 'amber',
    ],
    [
    'label' => 'Matières',
    'value' => $stats['subjects'] ?? 0,
    'sub' => 'Enseignées',
    'icon' => 'bi-journal-bookmark-fill',
    'color' => 'cyan',
    ],
    ] as $stat)
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200
                dark:border-slate-700 p-5 shadow-sm">
        <div class="flex items-start justify-between gap-2">
            <div class="min-w-0">
                <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">
                    {{ $stat['label'] }}
                </p>
                <p class="text-2xl font-bold text-slate-800 dark:text-slate-100 mt-1.5">
                    {{ $stat['value'] }}
                </p>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                    {{ $stat['sub'] }}
                </p>
            </div>
            <div
                class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0
                {{ $stat['color'] === 'blue'    ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400' : '' }}
                {{ $stat['color'] === 'emerald' ? 'bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400' : '' }}
                {{ $stat['color'] === 'amber'   ? 'bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400' : '' }}
                {{ $stat['color'] === 'cyan'    ? 'bg-cyan-50 dark:bg-cyan-900/30 text-cyan-600 dark:text-cyan-400' : '' }}">
                <i class="bi {{ $stat['icon'] }} text-lg"></i>
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- ── Filtres ───────────────────────────────────────────────────── --}}
<div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200
            dark:border-slate-700 shadow-sm p-4 sm:p-5 mb-6">
    <form method="GET" action="{{ route('teacher-assignments.index') }}" id="filter-form"
        class="flex flex-col sm:flex-row flex-wrap gap-3">

        {{-- Recherche --}}
        <div class="relative flex-1 min-w-44">
            <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2
                      text-slate-400 text-sm pointer-events-none"></i>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Enseignant, matière, classe…"
                class="w-full pl-9 pr-4 py-2.5 text-sm rounded-xl
                          bg-slate-50 dark:bg-slate-700/50
                          border border-slate-200 dark:border-slate-600
                          text-slate-800 dark:text-slate-100
                          placeholder:text-slate-400
                          focus:outline-none focus:ring-2 focus:ring-blue-500/20
                          focus:border-blue-500 transition-all" />
        </div>

        {{-- Filtre enseignant --}}
        <select name="teacher_id" class="py-2.5 pl-3.5 pr-9 text-sm rounded-xl
                       bg-slate-50 dark:bg-slate-700/50
                       border border-slate-200 dark:border-slate-600
                       text-slate-800 dark:text-slate-100
                       focus:outline-none focus:ring-2 focus:ring-blue-500/20
                       focus:border-blue-500 transition-all appearance-none
                       min-w-44">
            <option value="">Tous les enseignants</option>
            @foreach($teachers ?? [] as $teacher)
            <option value="{{ $teacher->id }}" {{ request('teacher_id') == $teacher->id ? 'selected' : '' }}>
                {{ $teacher->user->name ?? '—' }}
            </option>
            @endforeach
        </select>

        {{-- Filtre classe --}}
        <select name="class_id" class="py-2.5 pl-3.5 pr-9 text-sm rounded-xl
                       bg-slate-50 dark:bg-slate-700/50
                       border border-slate-200 dark:border-slate-600
                       text-slate-800 dark:text-slate-100
                       focus:outline-none focus:ring-2 focus:ring-blue-500/20
                       focus:border-blue-500 transition-all appearance-none
                       min-w-40">
            <option value="">Toutes les classes</option>
            @foreach($classes ?? [] as $class)
            <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                {{ $class->name }}
            </option>
            @endforeach
        </select>

        {{-- Filtre année --}}
        <select name="academic_year_id" class="py-2.5 pl-3.5 pr-9 text-sm rounded-xl
                       bg-slate-50 dark:bg-slate-700/50
                       border border-slate-200 dark:border-slate-600
                       text-slate-800 dark:text-slate-100
                       focus:outline-none focus:ring-2 focus:ring-blue-500/20
                       focus:border-blue-500 transition-all appearance-none
                       min-w-40">
            <option value="">Toutes les années</option>
            @foreach($academicYears ?? [] as $year)
            <option value="{{ $year->id }}" {{ request('academic_year_id') == $year->id ? 'selected' : '' }}>
                {{ $year->name }}
                @if($year->is_current) (Active) @endif
            </option>
            @endforeach
        </select>

        {{-- Actions filtres --}}
        <div class="flex items-center gap-2">
            <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl
                           text-sm font-semibold bg-blue-600 hover:bg-blue-700
                           text-white transition-all duration-200">
                <i class="bi bi-funnel-fill"></i>
                Filtrer
            </button>

            @if(request()->hasAny(['search', 'teacher_id', 'class_id', 'academic_year_id']))
            <a href="{{ route('teacher-assignments.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-medium
                      bg-slate-100 dark:bg-slate-700
                      text-slate-600 dark:text-slate-300
                      hover:bg-slate-200 dark:hover:bg-slate-600
                      transition-all duration-200">
                <i class="bi bi-x-circle"></i>
                Réinitialiser
            </a>
            @endif
        </div>
    </form>
</div>

{{-- ── Tableau ───────────────────────────────────────────────────── --}}
<div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200
            dark:border-slate-700 shadow-sm overflow-hidden">

    {{-- Header --}}
    <div class="px-5 sm:px-6 py-4 border-b border-slate-200 dark:border-slate-700
                bg-slate-50/50 dark:bg-slate-800/50
                flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-100
                   flex items-center gap-2">
            <i class="bi bi-diagram-3-fill text-blue-500"></i>
            Liste des affectations
            <span class="text-xs font-normal text-slate-500 dark:text-slate-400">
                ({{ $assignments->total() }})
            </span>
        </h3>

        <div class="flex items-center gap-2">
            {{-- Bouton export --}}
            <a href="{{ route('exports.attendance') }}" class="inline-flex items-center gap-2 px-3 py-2 rounded-xl text-xs font-medium
                      bg-white dark:bg-slate-700
                      border border-slate-200 dark:border-slate-600
                      text-slate-600 dark:text-slate-300
                      hover:bg-slate-50 dark:hover:bg-slate-600 transition-all">
                <i class="bi bi-download text-emerald-500"></i>
                Exporter
            </a>
        </div>
    </div>

    @if($assignments->isEmpty())
    {{-- État vide --}}
    <div class="flex flex-col items-center justify-center py-20 px-4">
        <div class="w-16 h-16 rounded-3xl bg-slate-100 dark:bg-slate-700
                        flex items-center justify-center mb-4">
            <i class="bi bi-diagram-3 text-3xl text-slate-300 dark:text-slate-500"></i>
        </div>
        <h3 class="text-base font-semibold text-slate-700 dark:text-slate-200 mb-1">
            Aucune affectation trouvée
        </h3>
        <p class="text-sm text-slate-400 dark:text-slate-500 text-center max-w-xs mb-6">
            @if(request()->hasAny(['search', 'teacher_id', 'class_id', 'academic_year_id']))
            Aucun résultat ne correspond à vos filtres.
            @else
            Commencez par créer une affectation enseignant.
            @endif
        </p>
        @if(request()->hasAny(['search', 'teacher_id', 'class_id', 'academic_year_id']))
        <a href="{{ route('teacher-assignments.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-medium
                          border border-slate-200 dark:border-slate-700
                          text-slate-600 dark:text-slate-400
                          hover:bg-slate-100 dark:hover:bg-slate-700 transition-all">
            <i class="bi bi-arrow-counterclockwise"></i>
            Réinitialiser les filtres
        </a>
        @else
        <a href="{{ route('teacher-assignments.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold
                          bg-blue-600 hover:bg-blue-700 text-white transition-all">
            <i class="bi bi-plus-circle-fill"></i>
            Nouvelle affectation
        </a>
        @endif
    </div>

    @else

    {{-- ── Desktop : Tableau ──────────────────────────────── --}}
    <div class="hidden sm:block overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="text-[11px] uppercase font-semibold tracking-wider
                              text-slate-400 dark:text-slate-500
                              bg-slate-50 dark:bg-slate-900/50">
                <tr>
                    <th class="px-5 py-3.5 text-left">Enseignant</th>
                    <th class="px-5 py-3.5 text-left">Matière</th>
                    <th class="px-5 py-3.5 text-left">Classe</th>
                    <th class="px-5 py-3.5 text-left">Année académique</th>
                    <th class="px-5 py-3.5 text-left">Heures / sem.</th>
                    <th class="px-5 py-3.5 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50">
                @foreach($assignments as $assignment)
                <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors group">

                    {{-- Enseignant --}}
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full shrink-0
                                            bg-linear-to-br from-blue-600 to-emerald-500
                                            flex items-center justify-center
                                            text-white text-[10px] font-bold">
                                {{ strtoupper(substr($assignment->teacher->user->name ?? 'T', 0, 2)) }}
                            </div>
                            <div class="min-w-0">
                                <p class="font-medium text-slate-800 dark:text-slate-200 truncate">
                                    {{ $assignment->teacher->user->name ?? '-' }}
                                </p>
                                <p class="text-xs text-slate-500 dark:text-slate-400 font-mono">
                                    {{ $assignment->teacher->employee_number ?? '-' }}
                                </p>
                            </div>
                        </div>
                    </td>

                    {{-- Matière --}}
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-lg shrink-0
                                            bg-amber-50 dark:bg-amber-900/20
                                            flex items-center justify-center">
                                <i class="bi bi-journal-bookmark
                                              text-amber-600 dark:text-amber-400 text-xs"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="font-medium text-slate-800 dark:text-slate-200 truncate">
                                    {{ $assignment->subject->name ?? '-' }}
                                </p>
                                <p class="text-xs text-slate-500 dark:text-slate-400">
                                    Coeff {{ $assignment->subject->coefficient ?? '-' }}
                                </p>
                            </div>
                        </div>
                    </td>

                    {{-- Classe --}}
                    <td class="px-5 py-4">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1
                                         rounded-full text-xs font-medium
                                         bg-blue-50 dark:bg-blue-900/20
                                         text-blue-700 dark:text-blue-300
                                         border border-blue-200 dark:border-blue-800">
                            <i class="bi bi-collection-fill text-xs"></i>
                            {{ $assignment->classe->name ?? '-' }}
                        </span>
                    </td>

                    {{-- Année --}}
                    <td class="px-5 py-4">
                        <span class="px-2.5 py-1 rounded-full text-xs
                                         bg-slate-100 dark:bg-slate-700
                                         text-slate-600 dark:text-slate-300">
                            {{ $assignment->academicYear->name ?? '-' }}
                            @if($assignment->academicYear?->is_current)
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500
                                                 inline-block ml-1"></span>
                            @endif
                        </span>
                    </td>

                    {{-- Heures --}}
                    <td class="px-5 py-4 text-slate-600 dark:text-slate-300">
                        @if($assignment->hours_per_week ?? null)
                        <span class="font-semibold">{{ $assignment->hours_per_week }}h</span>
                        <span class="text-xs text-slate-400">/sem.</span>
                        @else
                        <span class="text-slate-400">-</span>
                        @endif
                    </td>

                    {{-- Actions --}}
                    <td class="px-5 py-4">
                        <div class="flex items-center justify-end gap-1
                                        opacity-0 group-hover:opacity-100 transition-opacity">
                            <a href="{{ route('teacher-assignments.show', $assignment) }}" class="w-8 h-8 rounded-lg flex items-center justify-center
                                          text-slate-500 dark:text-slate-400
                                          hover:bg-blue-50 dark:hover:bg-blue-900/20
                                          hover:text-blue-600 dark:hover:text-blue-400
                                          transition-all" title="Voir">
                                <i class="bi bi-eye text-sm"></i>
                            </a>
                            <a href="{{ route('teacher-assignments.edit', $assignment) }}" class="w-8 h-8 rounded-lg flex items-center justify-center
                                          text-slate-500 dark:text-slate-400
                                          hover:bg-amber-50 dark:hover:bg-amber-900/20
                                          hover:text-amber-600 dark:hover:text-amber-400
                                          transition-all" title="Modifier">
                                <i class="bi bi-pencil text-sm"></i>
                            </a>
                            <button type="button" data-delete-assignment="{{ $assignment->id }}"
                                data-assignment-label="{{ $assignment->teacher->user->name ?? '' }} — {{ $assignment->subject->name ?? '' }}"
                                class="w-8 h-8 rounded-lg flex items-center justify-center
                                               text-slate-500 dark:text-slate-400
                                               hover:bg-red-50 dark:hover:bg-red-900/20
                                               hover:text-red-600 dark:hover:text-red-400
                                               transition-all" title="Supprimer">
                                <i class="bi bi-trash3 text-sm"></i>
                            </button>
                            <form id="delete-assignment-{{ $assignment->id }}"
                                action="{{ route('teacher-assignments.destroy', $assignment) }}" method="POST"
                                class="hidden">
                                @csrf
                                @method('DELETE')
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- ── Mobile : Cards ─────────────────────────────────── --}}
    <div class="sm:hidden divide-y divide-slate-100 dark:divide-slate-700">
        @foreach($assignments as $assignment)
        <div class="p-4">
            {{-- Header card --}}
            <div class="flex items-start justify-between gap-3 mb-3">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-10 h-10 rounded-full shrink-0
                                    bg-linear-to-br from-blue-600 to-emerald-500
                                    flex items-center justify-center
                                    text-white text-xs font-bold">
                        {{ strtoupper(substr($assignment->teacher->user->name ?? 'T', 0, 2)) }}
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-slate-800 dark:text-slate-200 truncate">
                            {{ $assignment->teacher->user->name ?? '—' }}
                        </p>
                        <p class="text-xs text-slate-500 font-mono">
                            {{ $assignment->teacher->employee_number ?? '—' }}
                        </p>
                    </div>
                </div>
                <span class="px-2.5 py-1 rounded-full text-xs font-medium shrink-0
                                 bg-slate-100 dark:bg-slate-700
                                 text-slate-600 dark:text-slate-300">
                    {{ $assignment->academicYear->name ?? '—' }}
                </span>
            </div>

            {{-- Matière + Classe --}}
            <div class="flex flex-wrap gap-2 mb-3">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-xl
                                 text-xs font-medium
                                 bg-amber-50 dark:bg-amber-900/20
                                 text-amber-700 dark:text-amber-300
                                 border border-amber-200 dark:border-amber-800">
                    <i class="bi bi-journal-bookmark"></i>
                    {{ $assignment->subject->name ?? '—' }}
                </span>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-xl
                                 text-xs font-medium
                                 bg-blue-50 dark:bg-blue-900/20
                                 text-blue-700 dark:text-blue-300
                                 border border-blue-200 dark:border-blue-800">
                    <i class="bi bi-collection-fill"></i>
                    {{ $assignment->classe->name ?? '—' }}
                </span>
                @if($assignment->hours_per_week ?? null)
                <span class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl
                                 text-xs font-medium
                                 bg-slate-100 dark:bg-slate-700
                                 text-slate-600 dark:text-slate-300">
                    <i class="bi bi-clock"></i>
                    {{ $assignment->hours_per_week }}h/sem.
                </span>
                @endif
            </div>

            {{-- Actions --}}
            <div class="flex items-center gap-2 pt-2
                            border-t border-slate-100 dark:border-slate-700">
                <a href="{{ route('teacher-assignments.show', $assignment) }}" class="flex-1 inline-flex items-center justify-center gap-1.5
                              py-2 rounded-xl text-xs font-medium
                              bg-slate-50 dark:bg-slate-700
                              text-slate-600 dark:text-slate-300
                              hover:bg-blue-50 dark:hover:bg-blue-900/20
                              hover:text-blue-600 transition-all">
                    <i class="bi bi-eye"></i> Voir
                </a>
                <a href="{{ route('teacher-assignments.edit', $assignment) }}" class="flex-1 inline-flex items-center justify-center gap-1.5
                              py-2 rounded-xl text-xs font-medium
                              bg-slate-50 dark:bg-slate-700
                              text-slate-600 dark:text-slate-300
                              hover:bg-amber-50 dark:hover:bg-amber-900/20
                              hover:text-amber-600 transition-all">
                    <i class="bi bi-pencil"></i> Modifier
                </a>
                <button type="button" data-delete-assignment="{{ $assignment->id }}"
                    data-assignment-label="{{ $assignment->teacher->user->name ?? '' }} — {{ $assignment->subject->name ?? '' }}"
                    class="flex-1 inline-flex items-center justify-center gap-1.5
                                   py-2 rounded-xl text-xs font-medium
                                   bg-slate-50 dark:bg-slate-700
                                   text-slate-600 dark:text-slate-300
                                   hover:bg-red-50 dark:hover:bg-red-900/20
                                   hover:text-red-600 transition-all">
                    <i class="bi bi-trash3"></i> Supprimer
                </button>
                <form id="delete-assignment-{{ $assignment->id }}"
                    action="{{ route('teacher-assignments.destroy', $assignment) }}" method="POST" class="hidden">
                    @csrf @method('DELETE')
                </form>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Pagination --}}
    @if($assignments->hasPages())
    <div class="px-5 py-4 border-t border-slate-100 dark:border-slate-700
                    bg-slate-50/50 dark:bg-slate-800/50">
        {{ $assignments->links() }}
    </div>
    @endif

    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {

    // ── Suppression ──────────────────────────────────────────
    document.querySelectorAll('[data-delete-assignment]').forEach(btn => {
        btn.addEventListener('click', () => {
            const id = btn.dataset.deleteAssignment;
            const label = btn.dataset.assignmentLabel;
            if (confirm(
                    `Supprimer l'affectation "${label}" ?\n\nCette action est irréversible.`)) {
                document.getElementById(`delete-assignment-${id}`)?.submit();
            }
        });
    });

    // ── Auto-submit sur changement de filtre ─────────────────
    document.querySelectorAll('#filter-form select').forEach(select => {
        select.addEventListener('change', () => {
            document.getElementById('filter-form')?.submit();
        });
    });
});
</script>
@endsection