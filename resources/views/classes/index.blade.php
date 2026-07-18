@extends('layouts.base')

@section('title', 'Classes')
@section('page_title', 'Gestion des classes')

@section('breadcrumb')
<span class="text-slate-300 dark:text-slate-600">/</span>
<span class="font-semibold text-slate-700 dark:text-slate-200">Classes</span>
@endsection

@section('content')
<div class="space-y-5">

    {{-- ══════════════════════════════════════════════════════════
         EN-TÊTE
    ══════════════════════════════════════════════════════════ --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-800 dark:text-slate-100">
                Classes
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                <span class="font-semibold text-blue-600 dark:text-blue-400">
                    {{ $totalClasses }}
                </span>
                classe{{ $totalClasses > 1 ? 's' : '' }} ·
                Année
                <span class="font-semibold text-emerald-600 dark:text-emerald-400">
                    {{ $activeYear?->name ?? '—' }}
                </span>
            </p>
        </div>

        <div class="flex items-center gap-2 flex-wrap self-start sm:self-auto">

            {{-- Toggle vue --}}
            <div class="flex items-center rounded-xl border border-slate-200 dark:border-slate-700
                        bg-white dark:bg-slate-800 overflow-hidden">
                <button id="btn-grid" onclick="setView('grid')"
                    class="px-3 py-2 text-sm transition-all bg-blue-600 text-white" title="Vue grille">
                    <i class="bi bi-grid-3x3-gap-fill"></i>
                </button>
                <button id="btn-list" onclick="setView('list')" class="px-3 py-2 text-sm transition-all
                               text-slate-500 dark:text-slate-400
                               hover:bg-slate-100 dark:hover:bg-slate-700" title="Vue liste">
                    <i class="bi bi-list-ul"></i>
                </button>
            </div>

            <a href="{{ route('classes.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold
                      bg-blue-600 hover:bg-blue-700 text-white
                      transition-all shadow-sm shadow-blue-500/30">
                <i class="bi bi-plus-lg"></i>
                <span class="hidden sm:inline">Nouvelle classe</span>
                <span class="sm:hidden">Nouveau</span>
            </a>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════
         STATS RAPIDES
    ══════════════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
        @foreach([
        [
        'label' => 'Total classes',
        'value' => $totalClasses,
        'icon' => 'bi-building',
        'color' => 'blue',
        'desc' => $activeYear?->name ?? '—',
        ],
        [
        'label' => 'Élèves inscrits',
        'value' => $totalStudents,
        'icon' => 'bi-people-fill',
        'color' => 'emerald',
        'desc' => 'Tous niveaux',
        ],
        [
        'label' => 'Taux d\'occupation',
        'value' => round($avgOccupancy) . '%',
        'icon' => 'bi-bar-chart-fill',
        'color' => $avgOccupancy >= 80 ? 'red' : ($avgOccupancy >= 60 ? 'amber' : 'emerald'),
        'desc' => 'Moyenne des classes',
        ],
        ] as $card)
        <div class="bg-white dark:bg-slate-800 rounded-2xl px-5 py-4
                    border border-slate-200 dark:border-slate-700 shadow-sm
                    flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl shrink-0
                        bg-{{ $card['color'] }}-100 dark:bg-{{ $card['color'] }}-900/30
                        flex items-center justify-center">
                <i class="bi {{ $card['icon'] }}
                          text-{{ $card['color'] }}-600 dark:text-{{ $card['color'] }}-400
                          text-xl"></i>
            </div>
            <div class="min-w-0">
                <p class="text-2xl font-bold text-slate-800 dark:text-slate-100 leading-tight">
                    {{ $card['value'] }}
                </p>
                <p class="text-sm font-medium text-slate-600 dark:text-slate-300">
                    {{ $card['label'] }}
                </p>
                <p class="text-xs text-slate-400 dark:text-slate-500">
                    {{ $card['desc'] }}
                </p>
            </div>
        </div>
        @endforeach
    </div>

    {{-- ══════════════════════════════════════════════════════════
         VUE GRILLE (SortableJS)
    ══════════════════════════════════════════════════════════ --}}
    <div id="view-grid">
        @if($classes->isEmpty())
        <div class="bg-white dark:bg-slate-800 rounded-2xl
                    border border-slate-200 dark:border-slate-700
                    shadow-sm flex flex-col items-center justify-center py-20 px-4">
            <div class="w-16 h-16 rounded-2xl bg-slate-100 dark:bg-slate-700
                        flex items-center justify-center mb-4">
                <i class="bi bi-building text-3xl text-slate-300 dark:text-slate-500"></i>
            </div>
            <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1">
                Aucune classe configurée
            </h3>
            <p class="text-xs text-slate-400 dark:text-slate-500 text-center mb-5">
                Commencez par créer votre première classe.
            </p>
            <a href="{{ route('classes.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold
                      bg-blue-600 hover:bg-blue-700 text-white
                      transition-all shadow-sm shadow-blue-500/30">
                <i class="bi bi-plus-lg"></i>
                Créer une classe
            </a>
        </div>
        @else
        <x-sortable-grid resource="classes" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">

            @foreach($classes as $class)
            @php
            $occupancy = $class->occupancyRate();
            $studentsCount = $class->studentsCount();
            $remaining = $class->remaining_capacity;
            $occupancyColor = $occupancy >= 90
            ? 'red'
            : ($occupancy >= 70 ? 'amber' : 'emerald');
            $gradients = [
            'from-blue-500 to-indigo-600',
            'from-emerald-500 to-teal-600',
            'from-violet-500 to-purple-600',
            'from-amber-500 to-orange-600',
            'from-cyan-500 to-blue-600',
            'from-pink-500 to-rose-600',
            ];
            $gradient = $gradients[$loop->index % count($gradients)];
            @endphp
            <x-sortable-item :id="$class->id" class="class-card group bg-white dark:bg-slate-800 rounded-2xl border border-slate-200
                dark:border-slate-700 shadow-sm hover:shadow-md hover:border-blue-200 dark:hover:border-blue-800
                transition-all duration-200 overflow-hidden cursor-grab active:cursor-grabbing">

                {{-- Header coloré --}}
                <div class="relative h-24 bg-linear-to-br {{ $gradient }}
                            flex items-center justify-center">

                    {{-- Déco --}}
                    <div class="absolute inset-0 opacity-10">
                        <i class="bi bi-building text-[100px] text-white absolute
                                  -bottom-4 -right-4 leading-none"></i>
                    </div>

                    {{-- Nom classe --}}
                    <div class="relative text-center">
                        <h3 class="text-2xl font-black text-white tracking-tight">
                            {{ $class->name }}
                        </h3>
                        @if($class->level)
                        <p class="text-xs text-white/80 mt-0.5 font-medium">
                            {{ $class->level }}
                        </p>
                        @endif
                    </div>

                    {{-- Menu actions --}}
                    <div class="absolute top-2 right-2
                                opacity-0 group-hover:opacity-100 transition-opacity">
                        <button data-dropdown="class-actions-{{ $class->id }}" class="w-7 h-7 rounded-lg bg-white/20 backdrop-blur-sm
                                       flex items-center justify-center text-white
                                       hover:bg-white/30 transition-colors focus:outline-none">
                            <i class="bi bi-three-dots-vertical text-sm"></i>
                        </button>
                        <div id="class-actions-{{ $class->id }}" data-dropdown-menu class="hidden absolute right-0 top-8 w-48
                                    bg-white dark:bg-slate-800
                                    border border-slate-200 dark:border-slate-700
                                    rounded-xl shadow-lg overflow-hidden z-20
                                    opacity-0 scale-95 translate-y-1
                                    transition-all duration-150">
                            <a href="{{ route('students.index', ['class_id' => $class->id]) }}" class="flex items-center gap-2.5 px-3 py-2.5 text-xs
                                      text-slate-700 dark:text-slate-300
                                      hover:bg-slate-50 dark:hover:bg-slate-700/50
                                      hover:text-blue-600 transition-colors">
                                <i class="bi bi-people-fill w-4 text-center text-blue-400"></i>
                                Voir les élèves
                            </a>
                            <a href="{{ route('timetables.index', ['class_id' => $class->id]) }}" class="flex items-center gap-2.5 px-3 py-2.5 text-xs
                                      text-slate-700 dark:text-slate-300
                                      hover:bg-slate-50 dark:hover:bg-slate-700/50
                                      hover:text-violet-600 transition-colors">
                                <i class="bi bi-clock-history w-4 text-center text-violet-400"></i>
                                Emploi du temps
                            </a>
                            <a href="{{ route('grades.index', ['class_id' => $class->id]) }}" class="flex items-center gap-2.5 px-3 py-2.5 text-xs
                                      text-slate-700 dark:text-slate-300
                                      hover:bg-slate-50 dark:hover:bg-slate-700/50
                                      hover:text-emerald-600 transition-colors">
                                <i class="bi bi-pencil-square w-4 text-center text-emerald-400"></i>
                                Notes
                            </a>
                            <a href="{{ route('attendance.index', ['class_id' => $class->id]) }}" class="flex items-center gap-2.5 px-3 py-2.5 text-xs
                                      text-slate-700 dark:text-slate-300
                                      hover:bg-slate-50 dark:hover:bg-slate-700/50
                                      hover:text-amber-600 transition-colors">
                                <i class="bi bi-clipboard2-check-fill w-4 text-center text-amber-400"></i>
                                Présences
                            </a>
                            <div class="border-t border-slate-100 dark:border-slate-700 mt-1 pt-1">
                                <a href="{{ route('classes.edit', $class) }}" class="flex items-center gap-2.5 px-3 py-2.5 text-xs
                                          text-slate-700 dark:text-slate-300
                                          hover:bg-slate-50 dark:hover:bg-slate-700/50
                                          hover:text-blue-600 transition-colors">
                                    <i class="bi bi-pencil-fill w-4 text-center"></i>
                                    Modifier
                                </a>
                                <button onclick="deleteClass({{ $class->id }}, '{{ addslashes($class->name) }}')" class="flex items-center gap-2.5 w-full px-3 py-2.5 text-xs
                                               text-red-600 dark:text-red-400
                                               hover:bg-red-50 dark:hover:bg-red-900/20
                                               transition-colors focus:outline-none">
                                    <i class="bi bi-trash3-fill w-4 text-center"></i>
                                    Supprimer
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Badge capacité --}}
                    <div class="absolute bottom-2 left-1/2 -translate-x-1/2">
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full
                                     text-[10px] font-semibold bg-white/20 text-white backdrop-blur-sm">
                            <i class="bi bi-people-fill text-[9px]"></i>
                            {{ $studentsCount }} / {{ $class->capacity }}
                        </span>
                    </div>
                </div>

                {{-- Corps --}}
                <div class="p-4 space-y-4">

                    {{-- Barre d'occupation --}}
                    <div>
                        <div class="flex items-center justify-between text-xs mb-1.5">
                            <span class="text-slate-500 dark:text-slate-400 font-medium">
                                Occupation
                            </span>
                            <span class="font-bold
                                         text-{{ $occupancyColor }}-600
                                         dark:text-{{ $occupancyColor }}-400">
                                {{ round($occupancy) }}%
                            </span>
                        </div>
                        <div class="h-2 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
                            <div class="h-full rounded-full transition-all duration-700
                                        bg-{{ $occupancyColor }}-500" style="width: {{ min(100, $occupancy) }}%">
                            </div>
                        </div>
                        <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-1">
                            {{ $remaining > 0
                                ? $remaining . ' place(s) disponible(s)'
                                : 'Classe complète' }}
                        </p>
                    </div>

                    {{-- Infos matières & enseignants --}}
                    <div class="grid grid-cols-2 gap-3">
                        <div class="bg-slate-50 dark:bg-slate-700/40 rounded-xl p-2.5 text-center">
                            <p class="text-lg font-bold text-slate-800 dark:text-slate-100">
                                {{ $class->subjects->count() }}
                            </p>
                            <p class="text-[10px] text-slate-400 dark:text-slate-500">
                                Matière(s)
                            </p>
                        </div>
                        <div class="bg-slate-50 dark:bg-slate-700/40 rounded-xl p-2.5 text-center">
                            <p class="text-lg font-bold text-slate-800 dark:text-slate-100">
                                {{ $class->teachers->count() }}
                            </p>
                            <p class="text-[10px] text-slate-400 dark:text-slate-500">
                                Enseignant(s)
                            </p>
                        </div>
                    </div>

                    {{-- Enseignants assignés --}}
                    @if($class->teachers->isNotEmpty())
                    <div class="flex items-center gap-1">
                        <div class="flex -space-x-2 flex-1">
                            @foreach($class->teachers->take(4) as $teacher)
                            <div class="w-7 h-7 rounded-full ring-2 ring-white dark:ring-slate-800
                                        bg-linear-to-br from-emerald-400 to-teal-500
                                        flex items-center justify-center shrink-0"
                                title="{{ $teacher->user->name ?? '' }}">
                                @if($teacher->user->avatar)
                                <img src="{{ asset('storage/' . $teacher->user->avatar) }}"
                                    class="w-full h-full rounded-full object-cover" alt="{{ $teacher->user->name }}" />
                                @else
                                <span class="text-[9px] font-bold text-white">
                                    {{ strtoupper(substr($teacher->user->name ?? 'P', 0, 1)) }}
                                </span>
                                @endif
                            </div>
                            @endforeach
                            @if($class->teachers->count() > 4)
                            <div class="w-7 h-7 rounded-full ring-2 ring-white dark:ring-slate-800
                                        bg-slate-200 dark:bg-slate-600
                                        flex items-center justify-center shrink-0">
                                <span class="text-[9px] font-bold text-slate-600 dark:text-slate-300">
                                    +{{ $class->teachers->count() - 4 }}
                                </span>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif

                    {{-- Actions --}}
                    <div class="grid grid-cols-2 gap-2 pt-1">
                        <a href="{{ route('students.index', ['class_id' => $class->id]) }}" class="inline-flex items-center justify-center gap-1.5
                                  py-2 rounded-xl text-xs font-medium
                                  bg-blue-50 dark:bg-blue-900/20
                                  text-blue-600 dark:text-blue-400
                                  hover:bg-blue-100 dark:hover:bg-blue-900/40
                                  transition-all">
                            <i class="bi bi-people-fill"></i>
                            Élèves
                        </a>
                        <a href="{{ route('classes.edit', $class) }}" class="inline-flex items-center justify-center gap-1.5
                                  py-2 rounded-xl text-xs font-medium
                                  bg-slate-100 dark:bg-slate-700
                                  text-slate-600 dark:text-slate-300
                                  hover:bg-slate-200 dark:hover:bg-slate-600
                                  transition-all">
                            <i class="bi bi-pencil-fill"></i>
                            Modifier
                        </a>
                    </div>
                </div>
            </x-sortable-item>
            @endforeach
        </x-sortable-grid>
        @endif

        {{-- Pagination --}}
        @if($classes->hasPages())
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4
                    bg-white dark:bg-slate-800 rounded-2xl
                    border border-slate-200 dark:border-slate-700
                    shadow-sm px-5 py-3.5">
            <p class="text-xs text-slate-500 dark:text-slate-400 order-2 sm:order-1">
                Affichage de
                <span class="font-semibold text-slate-700 dark:text-slate-300">
                    {{ $classes->firstItem() }}
                </span>
                à
                <span class="font-semibold text-slate-700 dark:text-slate-300">
                    {{ $classes->lastItem() }}
                </span>
                sur
                <span class="font-semibold text-slate-700 dark:text-slate-300">
                    {{ $classes->total() }}
                </span>
                classes
            </p>
            <div class="flex items-center gap-1 order-1 sm:order-2">
                @if(!$classes->onFirstPage())
                <a href="{{ $classes->previousPageUrl() }}" class="px-3 py-1.5 rounded-xl text-xs
                          border border-slate-200 dark:border-slate-700
                          text-slate-600 dark:text-slate-400
                          hover:bg-slate-100 dark:hover:bg-slate-700 transition-all">
                    <i class="bi bi-chevron-left"></i>
                </a>
                @endif
                @foreach($classes->getUrlRange(
                max(1, $classes->currentPage() - 2),
                min($classes->lastPage(), $classes->currentPage() + 2)
                ) as $page => $url)
                @if($page == $classes->currentPage())
                <span class="px-3 py-1.5 rounded-xl text-xs font-semibold
                                 bg-blue-600 text-white shadow-sm">
                    {{ $page }}
                </span>
                @else
                <a href="{{ $url }}" class="px-3 py-1.5 rounded-xl text-xs
                              border border-slate-200 dark:border-slate-700
                              text-slate-600 dark:text-slate-400
                              hover:bg-slate-100 dark:hover:bg-slate-700 transition-all">
                    {{ $page }}
                </a>
                @endif
                @endforeach
                @if($classes->hasMorePages())
                <a href="{{ $classes->nextPageUrl() }}" class="px-3 py-1.5 rounded-xl text-xs
                          border border-slate-200 dark:border-slate-700
                          text-slate-600 dark:text-slate-400
                          hover:bg-slate-100 dark:hover:bg-slate-700 transition-all">
                    <i class="bi bi-chevron-right"></i>
                </a>
                @endif
            </div>
        </div>
        @endif
    </div>

    {{-- ══════════════════════════════════════════════════════════
         VUE LISTE
    ══════════════════════════════════════════════════════════ --}}
    <div id="view-list" class="hidden">
        <div class="bg-white dark:bg-slate-800 rounded-2xl
                    border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-700/50
                                   border-b border-slate-200 dark:border-slate-700">
                            <th class="text-left px-5 py-3.5 text-xs font-semibold
                                       text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                Classe
                            </th>
                            <th class="text-center px-4 py-3.5 text-xs font-semibold
                                       text-slate-500 dark:text-slate-400 uppercase tracking-wider
                                       hidden sm:table-cell">
                                Élèves
                            </th>
                            <th class="text-center px-4 py-3.5 text-xs font-semibold
                                       text-slate-500 dark:text-slate-400 uppercase tracking-wider
                                       hidden md:table-cell">
                                Capacité
                            </th>
                            <th class="text-left px-4 py-3.5 text-xs font-semibold
                                       text-slate-500 dark:text-slate-400 uppercase tracking-wider
                                       hidden lg:table-cell">
                                Occupation
                            </th>
                            <th class="text-center px-4 py-3.5 text-xs font-semibold
                                       text-slate-500 dark:text-slate-400 uppercase tracking-wider
                                       hidden lg:table-cell">
                                Matières
                            </th>
                            <th class="text-left px-4 py-3.5 text-xs font-semibold
                                       text-slate-500 dark:text-slate-400 uppercase tracking-wider
                                       hidden xl:table-cell">
                                Enseignants
                            </th>
                            <th class="text-right px-5 py-3.5 text-xs font-semibold
                                       text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody id="classes-list" class="divide-y divide-slate-100 dark:divide-slate-700">
                        @forelse($classes as $class)
                        @php
                        $occupancy = $class->occupancyRate();
                        $studentsCount = $class->studentsCount();
                        $occupancyColor = $occupancy >= 90
                        ? 'red' : ($occupancy >= 70 ? 'amber' : 'emerald');
                        $gradients = [
                        'from-blue-500 to-indigo-600',
                        'from-emerald-500 to-teal-600',
                        'from-violet-500 to-purple-600',
                        'from-amber-500 to-orange-600',
                        'from-cyan-500 to-blue-600',
                        'from-pink-500 to-rose-600',
                        ];
                        $gradient = $gradients[$loop->index % count($gradients)];
                        @endphp
                        <tr class="group hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors"
                            data-id="{{ $class->id }}">

                            {{-- Classe --}}
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl flex-shrink-0
                                                bg-gradient-to-br {{ $gradient }}
                                                flex items-center justify-center shadow-sm">
                                        <span class="text-sm font-black text-white">
                                            {{ strtoupper(substr($class->name, 0, 2)) }}
                                        </span>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-bold text-slate-800 dark:text-slate-100">
                                            {{ $class->name }}
                                        </p>
                                        @if($class->level)
                                        <p class="text-xs text-slate-400 dark:text-slate-500">
                                            {{ $class->level }}
                                        </p>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            {{-- Élèves --}}
                            <td class="px-4 py-3.5 text-center hidden sm:table-cell">
                                <span class="text-sm font-bold
                                             text-slate-700 dark:text-slate-200">
                                    {{ $studentsCount }}
                                </span>
                            </td>

                            {{-- Capacité --}}
                            <td class="px-4 py-3.5 text-center hidden md:table-cell">
                                <span class="text-sm font-medium text-slate-500 dark:text-slate-400">
                                    {{ $class->capacity }}
                                </span>
                            </td>

                            {{-- Occupation --}}
                            <td class="px-4 py-3.5 hidden lg:table-cell">
                                <div class="flex items-center gap-3 min-w-[140px]">
                                    <div class="flex-1 h-2 bg-slate-100 dark:bg-slate-700
                                                rounded-full overflow-hidden">
                                        <div class="h-full rounded-full
                                                    bg-{{ $occupancyColor }}-500
                                                    transition-all duration-700"
                                            style="width: {{ min(100, $occupancy) }}%">
                                        </div>
                                    </div>
                                    <span class="text-xs font-semibold flex-shrink-0
                                                 text-{{ $occupancyColor }}-600
                                                 dark:text-{{ $occupancyColor }}-400">
                                        {{ round($occupancy) }}%
                                    </span>
                                </div>
                            </td>

                            {{-- Matières --}}
                            <td class="px-4 py-3.5 text-center hidden lg:table-cell">
                                <span class="inline-flex items-center justify-center
                                             w-7 h-7 rounded-lg text-xs font-bold
                                             bg-violet-50 dark:bg-violet-900/20
                                             text-violet-600 dark:text-violet-400">
                                    {{ $class->subjects->count() }}
                                </span>
                            </td>

                            {{-- Enseignants --}}
                            <td class="px-4 py-3.5 hidden xl:table-cell">
                                <div class="flex -space-x-2">
                                    @foreach($class->teachers->take(4) as $teacher)
                                    <div class="w-7 h-7 rounded-full
                                                ring-2 ring-white dark:ring-slate-800
                                                bg-gradient-to-br from-emerald-400 to-teal-500
                                                flex items-center justify-center"
                                        title="{{ $teacher->user->name ?? '' }}">
                                        @if($teacher->user->avatar)
                                        <img src="{{ asset('storage/' . $teacher->user->avatar) }}"
                                            class="w-full h-full rounded-full object-cover"
                                            alt="{{ $teacher->user->name }}" />
                                        @else
                                        <span class="text-[9px] font-bold text-white">
                                            {{ strtoupper(substr($teacher->user->name ?? 'P', 0, 1)) }}
                                        </span>
                                        @endif
                                    </div>
                                    @endforeach
                                    @if($class->teachers->isEmpty())
                                    <span class="text-xs text-slate-400 dark:text-slate-500 italic">
                                        Aucun
                                    </span>
                                    @endif
                                </div>
                            </td>

                            {{-- Actions --}}
                            <td class="px-5 py-3.5 text-right">
                                <div class="flex items-center justify-end gap-1.5
                                            opacity-0 group-hover:opacity-100 transition-opacity">
                                    <a href="{{ route('students.index', ['class_id' => $class->id]) }}"
                                        title="Voir les élèves" class="w-8 h-8 rounded-lg flex items-center justify-center
                                              bg-blue-50 dark:bg-blue-900/20
                                              text-blue-600 dark:text-blue-400
                                              hover:bg-blue-100 dark:hover:bg-blue-900/40
                                              transition-colors">
                                        <i class="bi bi-people-fill text-sm"></i>
                                    </a>
                                    <a href="{{ route('timetables.index', ['class_id' => $class->id]) }}"
                                        title="Emploi du temps" class="w-8 h-8 rounded-lg flex items-center justify-center
                                              bg-violet-50 dark:bg-violet-900/20
                                              text-violet-600 dark:text-violet-400
                                              hover:bg-violet-100 dark:hover:bg-violet-900/40
                                              transition-colors">
                                        <i class="bi bi-clock-history text-sm"></i>
                                    </a>
                                    <a href="{{ route('classes.edit', $class) }}" title="Modifier" class="w-8 h-8 rounded-lg flex items-center justify-center
                                              bg-slate-100 dark:bg-slate-700
                                              text-slate-600 dark:text-slate-300
                                              hover:bg-slate-200 dark:hover:bg-slate-600
                                              transition-colors">
                                        <i class="bi bi-pencil-fill text-sm"></i>
                                    </a>
                                    <button onclick="deleteClass({{ $class->id }}, '{{ addslashes($class->name) }}')"
                                        title="Supprimer" class="w-8 h-8 rounded-lg flex items-center justify-center
                                                   bg-red-50 dark:bg-red-900/20
                                                   text-red-500 dark:text-red-400
                                                   hover:bg-red-100 dark:hover:bg-red-900/40
                                                   transition-colors focus:outline-none">
                                        <i class="bi bi-trash3-fill text-sm"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-5 py-16 text-center">
                                <i class="bi bi-building text-4xl text-slate-300 dark:text-slate-600 block mb-3"></i>
                                <p class="text-sm text-slate-500 dark:text-slate-400">
                                    Aucune classe trouvée
                                </p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

{{-- Formulaire suppression --}}
<form id="delete-form" method="POST" class="hidden">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {

    const savedView = localStorage.getItem('classes-view') ?? 'grid';
    setView(savedView, false);

});

function setView(view, save = true) {
    const gridView = document.getElementById('view-grid');
    const listView = document.getElementById('view-list');
    const btnGrid = document.getElementById('btn-grid');
    const btnList = document.getElementById('btn-list');

    const active = 'bg-blue-600 text-white';
    const inactive = 'text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700';

    if (view === 'grid') {
        gridView?.classList.remove('hidden');
        listView?.classList.add('hidden');
        btnGrid?.classList.add(...active.split(' '));
        btnGrid?.classList.remove(...inactive.split(' '));
        btnList?.classList.remove(...active.split(' '));
        btnList?.classList.add(...inactive.split(' '));
    } else {
        listView?.classList.remove('hidden');
        gridView?.classList.add('hidden');
        btnList?.classList.add(...active.split(' '));
        btnList?.classList.remove(...inactive.split(' '));
        btnGrid?.classList.remove(...active.split(' '));
        btnGrid?.classList.add(...inactive.split(' '));
    }

    if (save) localStorage.setItem('classes-view', view);
}

function deleteClass(id, name) {
    if (!confirm(`Supprimer la classe "${name}" ? Les élèves associés seront désaffectés.`)) return;
    const form = document.getElementById('delete-form');
    form.action = `/classes/${id}`;
    form.submit();
}
</script>
@endpush