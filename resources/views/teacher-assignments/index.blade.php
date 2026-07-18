@extends('layouts.base')

@section('title', 'Affectations')
@section('page_title', 'Affectations des enseignants')

@section('breadcrumb')
<span class="text-slate-300 dark:text-slate-600">/</span>
<span class="font-semibold text-slate-700 dark:text-slate-200">Affectations</span>
@endsection

@section('content')
<div class="space-y-5">

    {{-- ══════════════════════════════════════════════════════════
         EN-TÊTE
    ══════════════════════════════════════════════════════════ --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-800 dark:text-slate-100">
                Affectations enseignants
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                <span class="font-semibold text-blue-600 dark:text-blue-400">
                    {{ $total }}
                </span>
                affectation{{ $total > 1 ? 's' : '' }} ·
                Année
                <span class="font-semibold text-emerald-600 dark:text-emerald-400">
                    {{ $activeYear?->name ?? '—' }}
                </span>
                @if(request()->hasAny(['teacher_id', 'subject_id', 'class_id', 'academic_year_id']))
                · <span class="text-amber-500">Filtres actifs</span>
                @endif
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

            <a href="{{ route('teacher-assignments.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold
                      bg-blue-600 hover:bg-blue-700 text-white
                      transition-all shadow-sm shadow-blue-500/30">
                <i class="bi bi-plus-lg"></i>
                <span class="hidden sm:inline">Nouvelle affectation</span>
                <span class="sm:hidden">Nouveau</span>
            </a>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════
         STATS RAPIDES
    ══════════════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        @foreach([
        [
        'label' => 'Affectations',
        'value' => $total,
        'icon' => 'bi-diagram-3-fill',
        'color' => 'blue',
        ],
        [
        'label' => 'Enseignants',
        'value' => $teachers->count(),
        'icon' => 'bi-person-badge-fill',
        'color' => 'emerald',
        ],
        [
        'label' => 'Matières',
        'value' => $subjects->count(),
        'icon' => 'bi-book-fill',
        'color' => 'violet',
        ],
        [
        'label' => 'Classes',
        'value' => $classes->count(),
        'icon' => 'bi-building',
        'color' => 'amber',
        ],
        ] as $card)
        <div class="bg-white dark:bg-slate-800 rounded-2xl px-4 py-3.5
                    border border-slate-200 dark:border-slate-700 shadow-sm
                    flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl shrink-0
                        bg-{{ $card['color'] }}-100 dark:bg-{{ $card['color'] }}-900/30
                        flex items-center justify-center">
                <i class="bi {{ $card['icon'] }}
                          text-{{ $card['color'] }}-600 dark:text-{{ $card['color'] }}-400
                          text-lg"></i>
            </div>
            <div class="min-w-0">
                <p class="text-xl font-bold text-slate-800 dark:text-slate-100 leading-tight">
                    {{ $card['value'] }}
                </p>
                <p class="text-xs text-slate-500 dark:text-slate-400 truncate">
                    {{ $card['label'] }}
                </p>
            </div>
        </div>
        @endforeach
    </div>

    {{-- ══════════════════════════════════════════════════════════
         FILTRES
    ══════════════════════════════════════════════════════════ --}}
    <div class="bg-white dark:bg-slate-800 rounded-2xl
                border border-slate-200 dark:border-slate-700 shadow-sm">
        <form method="GET" action="{{ route('teacher-assignments.index') }}" id="filter-form"
            class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 p-4">

            {{-- Enseignant --}}
            <div>
                <label class="text-xs font-medium text-slate-500 dark:text-slate-400 mb-1.5 block">
                    Enseignant
                </label>
                <div class="relative">
                    <select name="teacher_id" onchange="document.getElementById('filter-form').submit()" class="w-full pl-3.5 pr-9 py-2.5 text-sm rounded-xl appearance-none
                                   border border-slate-200 dark:border-slate-700
                                   bg-slate-50 dark:bg-slate-900/50
                                   text-slate-800 dark:text-slate-100
                                   focus:outline-none focus:ring-2
                                   focus:ring-blue-600/40 focus:border-blue-600 transition">
                        <option value="">Tous</option>
                        @foreach($teachers as $teacher)
                        <option value="{{ $teacher->id }}"
                            {{ request('teacher_id') == $teacher->id ? 'selected' : '' }}>
                            {{ $teacher->user->name }}
                        </option>
                        @endforeach
                    </select>
                    <span class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                        <i class="bi bi-chevron-down text-slate-400 text-xs"></i>
                    </span>
                </div>
            </div>

            {{-- Matière --}}
            <div>
                <label class="text-xs font-medium text-slate-500 dark:text-slate-400 mb-1.5 block">
                    Matière
                </label>
                <div class="relative">
                    <select name="subject_id" onchange="document.getElementById('filter-form').submit()" class="w-full pl-3.5 pr-9 py-2.5 text-sm rounded-xl appearance-none
                                   border border-slate-200 dark:border-slate-700
                                   bg-slate-50 dark:bg-slate-900/50
                                   text-slate-800 dark:text-slate-100
                                   focus:outline-none focus:ring-2
                                   focus:ring-blue-600/40 focus:border-blue-600 transition">
                        <option value="">Toutes</option>
                        @foreach($subjects as $subject)
                        <option value="{{ $subject->id }}"
                            {{ request('subject_id') == $subject->id ? 'selected' : '' }}>
                            {{ $subject->name }}
                        </option>
                        @endforeach
                    </select>
                    <span class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                        <i class="bi bi-chevron-down text-slate-400 text-xs"></i>
                    </span>
                </div>
            </div>

            {{-- Classe --}}
            <div>
                <label class="text-xs font-medium text-slate-500 dark:text-slate-400 mb-1.5 block">
                    Classe
                </label>
                <div class="relative">
                    <select name="class_id" onchange="document.getElementById('filter-form').submit()" class="w-full pl-3.5 pr-9 py-2.5 text-sm rounded-xl appearance-none
                                   border border-slate-200 dark:border-slate-700
                                   bg-slate-50 dark:bg-slate-900/50
                                   text-slate-800 dark:text-slate-100
                                   focus:outline-none focus:ring-2
                                   focus:ring-blue-600/40 focus:border-blue-600 transition">
                        <option value="">Toutes</option>
                        @foreach($classes as $class)
                        <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                            {{ $class->name }}
                        </option>
                        @endforeach
                    </select>
                    <span class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                        <i class="bi bi-chevron-down text-slate-400 text-xs"></i>
                    </span>
                </div>
            </div>

            {{-- Année --}}
            <div>
                <label class="text-xs font-medium text-slate-500 dark:text-slate-400 mb-1.5 block">
                    Année académique
                </label>
                <div class="relative">
                    <select name="academic_year_id" onchange="document.getElementById('filter-form').submit()" class="w-full pl-3.5 pr-9 py-2.5 text-sm rounded-xl appearance-none
                                   border border-slate-200 dark:border-slate-700
                                   bg-slate-50 dark:bg-slate-900/50
                                   text-slate-800 dark:text-slate-100
                                   focus:outline-none focus:ring-2
                                   focus:ring-blue-600/40 focus:border-blue-600 transition">
                        <option value="">Toutes</option>
                        @foreach($academicYears as $year)
                        <option value="{{ $year->id }}"
                            {{ request('academic_year_id') == $year->id ? 'selected' : '' }}>
                            {{ $year->name }}
                        </option>
                        @endforeach
                    </select>
                    <span class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                        <i class="bi bi-chevron-down text-slate-400 text-xs"></i>
                    </span>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 inline-flex items-center justify-center gap-2
                               px-4 py-2.5 rounded-xl text-sm font-medium
                               bg-blue-600 hover:bg-blue-700 text-white
                               transition-all shadow-sm shadow-blue-500/20">
                    <i class="bi bi-funnel-fill"></i>
                    Filtrer
                </button>
                @if(request()->hasAny(['teacher_id', 'subject_id', 'class_id', 'academic_year_id']))
                <a href="{{ route('teacher-assignments.index') }}" class="shrink-0 inline-flex items-center justify-center
                          w-10 h-10 rounded-xl
                          border border-slate-200 dark:border-slate-700
                          text-slate-500 dark:text-slate-400
                          hover:bg-slate-100 dark:hover:bg-slate-700 transition-all" title="Réinitialiser">
                    <i class="bi bi-x-circle"></i>
                </a>
                @endif
            </div>
        </form>
    </div>

    {{-- ══════════════════════════════════════════════════════════
         VUE GRILLE
    ══════════════════════════════════════════════════════════ --}}
    <div id="view-grid">
        @if($assignments->isEmpty())
        <div class="bg-white dark:bg-slate-800 rounded-2xl
                    border border-slate-200 dark:border-slate-700
                    shadow-sm flex flex-col items-center justify-center py-20 px-4">
            <div class="w-16 h-16 rounded-2xl bg-slate-100 dark:bg-slate-700
                        flex items-center justify-center mb-4">
                <i class="bi bi-diagram-3 text-3xl text-slate-300 dark:text-slate-500"></i>
            </div>
            <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1">
                Aucune affectation
            </h3>
            <p class="text-xs text-slate-400 dark:text-slate-500 text-center mb-5">
                @if(request()->hasAny(['teacher_id', 'subject_id', 'class_id']))
                Aucun résultat pour ces filtres.
                @else
                Commencez par affecter un enseignant à une matière.
                @endif
            </p>
            <a href="{{ route('teacher-assignments.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold
                      bg-blue-600 hover:bg-blue-700 text-white
                      transition-all shadow-sm shadow-blue-500/30">
                <i class="bi bi-plus-lg"></i>
                Créer une affectation
            </a>
        </div>
        @else
        <x-sortable-grid resource="teacher-assignments"
            class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">

            @foreach($assignments as $assignment)
            @php
            $colors = [
            'blue', 'emerald', 'violet', 'amber',
            'cyan', 'pink', 'indigo', 'orange',
            ];
            $color = $colors[$loop->index % count($colors)];
            @endphp
            <x-sortable-item :id="$assignment->id" class="assignment-card group bg-white dark:bg-slate-800 rounded-2xl
                        border border-slate-200 dark:border-slate-700 shadow-sm
                        hover:shadow-md hover:border-blue-200 dark:hover:border-blue-800
                        transition-all duration-200 overflow-hidden
                        cursor-grab active:cursor-grabbing">

                {{-- Bande colorée --}}
                <div class="h-1.5 bg-{{ $color }}-500"></div>

                <div class="p-4 space-y-3">

                    {{-- En-tête : matière + menu --}}
                    <div class="flex items-start justify-between gap-2">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <div class="w-9 h-9 rounded-xl shrink-0
                                        bg-{{ $color }}-100 dark:bg-{{ $color }}-900/30
                                        flex items-center justify-center">
                                <i class="bi bi-book-fill
                                          text-{{ $color }}-600 dark:text-{{ $color }}-400
                                          text-sm"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-bold text-slate-800 dark:text-slate-100 truncate">
                                    {{ $assignment->subject->name ?? '—' }}
                                </p>
                                @if($assignment->subject?->coefficient)
                                <p class="text-[10px] text-slate-400 dark:text-slate-500">
                                    Coeff. {{ $assignment->subject->coefficient }}
                                </p>
                                @endif
                            </div>
                        </div>

                        {{-- Menu --}}
                        <div class="shrink-0 opacity-0 group-hover:opacity-100 transition-opacity">
                            <button data-dropdown="assign-actions-{{ $assignment->id }}" class="w-7 h-7 rounded-lg flex items-center justify-center
                                           bg-slate-100 dark:bg-slate-700
                                           text-slate-500 dark:text-slate-400
                                           hover:bg-slate-200 dark:hover:bg-slate-600
                                           transition-colors focus:outline-none">
                                <i class="bi bi-three-dots-vertical text-sm"></i>
                            </button>
                            <div id="assign-actions-{{ $assignment->id }}" data-dropdown-menu class="hidden absolute right-0 w-44 mt-1
                                        bg-white dark:bg-slate-800
                                        border border-slate-200 dark:border-slate-700
                                        rounded-xl shadow-lg overflow-hidden z-20
                                        opacity-0 scale-95 translate-y-1
                                        transition-all duration-150">
                                <a href="{{ route('teacher-assignments.edit', $assignment) }}" class="flex items-center gap-2.5 px-3 py-2.5 text-xs
                                          text-slate-700 dark:text-slate-300
                                          hover:bg-slate-50 dark:hover:bg-slate-700/50
                                          hover:text-blue-600 transition-colors">
                                    <i class="bi bi-pencil-fill w-4 text-center"></i>
                                    Modifier
                                </a>
                                <button onclick="deleteAssignment({{ $assignment->id }})" class="flex items-center gap-2.5 w-full px-3 py-2.5 text-xs
                                               text-red-600 dark:text-red-400
                                               hover:bg-red-50 dark:hover:bg-red-900/20
                                               transition-colors focus:outline-none">
                                    <i class="bi bi-trash3-fill w-4 text-center"></i>
                                    Supprimer
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Enseignant --}}
                    <div class="flex items-center gap-2.5 p-2.5 rounded-xl
                                bg-slate-50 dark:bg-slate-700/40">
                        <div class="w-8 h-8 rounded-xl overflow-hidden shrink-0
                                    bg-linear-to-br from-emerald-400 to-teal-500
                                    flex items-center justify-center">
                            @if($assignment->teacher->user->avatar)
                            <img src="{{ asset('storage/' . $assignment->teacher->user->avatar) }}"
                                class="w-full h-full object-cover" alt="{{ $assignment->teacher->user->name }}" />
                            @else
                            <span class="text-xs font-bold text-white">
                                {{ strtoupper(substr($assignment->teacher->user->name ?? 'P', 0, 1)) }}
                            </span>
                            @endif
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-semibold text-slate-700 dark:text-slate-200 truncate">
                                {{ $assignment->teacher->user->name ?? '—' }}
                            </p>
                            <p class="text-[10px] text-slate-400 dark:text-slate-500">
                                {{ $assignment->teacher->employee_number ?? 'Enseignant' }}
                            </p>
                        </div>
                        <i class="bi bi-person-badge-fill text-emerald-400 text-sm ml-auto shrink-0"></i>
                    </div>

                    {{-- Classe + Année --}}
                    <div class="grid grid-cols-2 gap-2">
                        <div class="flex flex-col items-center justify-center p-2 rounded-xl
                                    bg-blue-50 dark:bg-blue-900/20 text-center">
                            <i class="bi bi-building text-blue-500 text-sm mb-0.5"></i>
                            <p class="text-xs font-bold text-slate-700 dark:text-slate-200 truncate w-full text-center">
                                {{ $assignment->schoolClass->name ?? '—' }}
                            </p>
                            <p class="text-[10px] text-slate-400">Classe</p>
                        </div>
                        <div class="flex flex-col items-center justify-center p-2 rounded-xl
                                    bg-violet-50 dark:bg-violet-900/20 text-center">
                            <i class="bi bi-calendar3 text-violet-500 text-sm mb-0.5"></i>
                            <p class="text-xs font-bold text-slate-700 dark:text-slate-200 truncate w-full text-center">
                                {{ $assignment->academicYear->name ?? '—' }}
                            </p>
                            <p class="text-[10px] text-slate-400">Année</p>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex gap-2 pt-1">
                        <a href="{{ route('teacher-assignments.edit', $assignment) }}" class="flex-1 inline-flex items-center justify-center gap-1.5
                                  py-2 rounded-xl text-xs font-medium
                                  bg-blue-50 dark:bg-blue-900/20
                                  text-blue-600 dark:text-blue-400
                                  hover:bg-blue-100 dark:hover:bg-blue-900/40
                                  transition-all">
                            <i class="bi bi-pencil-fill"></i>
                            Modifier
                        </a>
                        <button onclick="deleteAssignment({{ $assignment->id }})" class="flex-1 inline-flex items-center justify-center gap-1.5
                                       py-2 rounded-xl text-xs font-medium
                                       bg-red-50 dark:bg-red-900/20
                                       text-red-600 dark:text-red-400
                                       hover:bg-red-100 dark:hover:bg-red-900/40
                                       transition-all focus:outline-none">
                            <i class="bi bi-trash3-fill"></i>
                            Supprimer
                        </button>
                    </div>
                </div>
            </x-sortable-item>
            @endforeach
        </x-sortable-grid>
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
                                Enseignant
                            </th>
                            <th class="text-left px-4 py-3.5 text-xs font-semibold
                                       text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                Matière
                            </th>
                            <th class="text-left px-4 py-3.5 text-xs font-semibold
                                       text-slate-500 dark:text-slate-400 uppercase tracking-wider
                                       hidden md:table-cell">
                                Classe
                            </th>
                            <th class="text-left px-4 py-3.5 text-xs font-semibold
                                       text-slate-500 dark:text-slate-400 uppercase tracking-wider
                                       hidden lg:table-cell">
                                Année
                            </th>
                            <th class="text-center px-4 py-3.5 text-xs font-semibold
                                       text-slate-500 dark:text-slate-400 uppercase tracking-wider
                                       hidden xl:table-cell">
                                Coefficient
                            </th>
                            <th class="text-right px-5 py-3.5 text-xs font-semibold
                                       text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody id="assignments-list" class="divide-y divide-slate-100 dark:divide-slate-700">
                        @forelse($assignments as $assignment)
                        @php
                        $colors = ['blue','emerald','violet','amber','cyan','pink','indigo','orange'];
                        $color = $colors[$loop->index % count($colors)];
                        @endphp
                        <tr class="group hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors"
                            data-id="{{ $assignment->id }}">

                            {{-- Enseignant --}}
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-xl overflow-hidden shrink-0
                                                bg-linear-to-br from-emerald-400 to-teal-500
                                                flex items-center justify-center">
                                        @if($assignment->teacher->user->avatar)
                                        <img src="{{ asset('storage/' . $assignment->teacher->user->avatar) }}"
                                            class="w-full h-full object-cover"
                                            alt="{{ $assignment->teacher->user->name }}" />
                                        @else
                                        <span class="text-xs font-bold text-white">
                                            {{ strtoupper(substr($assignment->teacher->user->name ?? 'P', 0, 1)) }}
                                        </span>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-semibold text-slate-700 dark:text-slate-200 truncate">
                                            {{ $assignment->teacher->user->name ?? '—' }}
                                        </p>
                                        <p class="text-[10px] text-slate-400 truncate">
                                            {{ $assignment->teacher->employee_number ?? '—' }}
                                        </p>
                                    </div>
                                </div>
                            </td>

                            {{-- Matière --}}
                            <td class="px-4 py-3.5">
                                <div class="flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full shrink-0
                                                 bg-{{ $color }}-500">
                                    </span>
                                    <span class="text-sm font-medium text-slate-700 dark:text-slate-200">
                                        {{ $assignment->subject->name ?? '—' }}
                                    </span>
                                </div>
                            </td>

                            {{-- Classe --}}
                            <td class="px-4 py-3.5 hidden md:table-cell">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg
                                             text-xs font-medium
                                             bg-blue-50 dark:bg-blue-900/20
                                             text-blue-700 dark:text-blue-400">
                                    <i class="bi bi-building text-[10px]"></i>
                                    {{ $assignment->schoolClass->name ?? '—' }}
                                </span>
                            </td>

                            {{-- Année --}}
                            <td class="px-4 py-3.5 hidden lg:table-cell">
                                <span class="text-xs font-medium text-slate-500 dark:text-slate-400">
                                    {{ $assignment->academicYear->name ?? '—' }}
                                </span>
                            </td>

                            {{-- Coefficient --}}
                            <td class="px-4 py-3.5 text-center hidden xl:table-cell">
                                <span class="inline-flex items-center justify-center
                                             w-8 h-8 rounded-xl text-sm font-bold
                                             bg-{{ $color }}-100 dark:bg-{{ $color }}-900/30
                                             text-{{ $color }}-700 dark:text-{{ $color }}-400">
                                    {{ $assignment->subject->coefficient ?? '—' }}
                                </span>
                            </td>

                            {{-- Actions --}}
                            <td class="px-5 py-3.5 text-right">
                                <div class="flex items-center justify-end gap-1.5
                                            opacity-0 group-hover:opacity-100 transition-opacity">
                                    <a href="{{ route('teacher-assignments.edit', $assignment) }}" class="w-8 h-8 rounded-lg flex items-center justify-center
                                              bg-slate-100 dark:bg-slate-700
                                              text-slate-600 dark:text-slate-300
                                              hover:bg-slate-200 dark:hover:bg-slate-600
                                              transition-colors">
                                        <i class="bi bi-pencil-fill text-sm"></i>
                                    </a>
                                    <button onclick="deleteAssignment({{ $assignment->id }})" class="w-8 h-8 rounded-lg flex items-center justify-center
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
                            <td colspan="6" class="px-5 py-16 text-center">
                                <i class="bi bi-diagram-3 text-4xl text-slate-300
                                          dark:text-slate-600 block mb-3"></i>
                                <p class="text-sm text-slate-500 dark:text-slate-400">
                                    Aucune affectation trouvée
                                </p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════
         PAGINATION
    ══════════════════════════════════════════════════════════ --}}
    @if($assignments->hasPages())
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4
                bg-white dark:bg-slate-800 rounded-2xl
                border border-slate-200 dark:border-slate-700
                shadow-sm px-5 py-3.5">
        <p class="text-xs text-slate-500 dark:text-slate-400 order-2 sm:order-1">
            Affichage de
            <span class="font-semibold text-slate-700 dark:text-slate-300">
                {{ $assignments->firstItem() }}
            </span>
            à
            <span class="font-semibold text-slate-700 dark:text-slate-300">
                {{ $assignments->lastItem() }}
            </span>
            sur
            <span class="font-semibold text-slate-700 dark:text-slate-300">
                {{ $assignments->total() }}
            </span>
            affectations
        </p>
        <div class="flex items-center gap-1 order-1 sm:order-2">
            @if(!$assignments->onFirstPage())
            <a href="{{ $assignments->previousPageUrl() }}" class="px-3 py-1.5 rounded-xl text-xs border border-slate-200
                      dark:border-slate-700 text-slate-600 dark:text-slate-400
                      hover:bg-slate-100 dark:hover:bg-slate-700 transition-all">
                <i class="bi bi-chevron-left"></i>
            </a>
            @endif
            @foreach($assignments->getUrlRange(
            max(1, $assignments->currentPage() - 2),
            min($assignments->lastPage(), $assignments->currentPage() + 2)
            ) as $page => $url)
            @if($page == $assignments->currentPage())
            <span class="px-3 py-1.5 rounded-xl text-xs font-semibold
                             bg-blue-600 text-white shadow-sm">{{ $page }}</span>
            @else
            <a href="{{ $url }}" class="px-3 py-1.5 rounded-xl text-xs border border-slate-200
                          dark:border-slate-700 text-slate-600 dark:text-slate-400
                          hover:bg-slate-100 dark:hover:bg-slate-700 transition-all">
                {{ $page }}
            </a>
            @endif
            @endforeach
            @if($assignments->hasMorePages())
            <a href="{{ $assignments->nextPageUrl() }}" class="px-3 py-1.5 rounded-xl text-xs border border-slate-200
                      dark:border-slate-700 text-slate-600 dark:text-slate-400
                      hover:bg-slate-100 dark:hover:bg-slate-700 transition-all">
                <i class="bi bi-chevron-right"></i>
            </a>
            @endif
        </div>
    </div>
    @endif

</div>

<form id="delete-form" method="POST" class="hidden">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {

    const savedView = localStorage.getItem('assignments-view') ?? 'grid';
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
    if (save) localStorage.setItem('assignments-view', view);
}

function deleteAssignment(id) {
    if (!confirm('Supprimer cette affectation ? Cette action est irréversible.')) return;
    const form = document.getElementById('delete-form');
    form.action = `/teacher-assignments/${id}`;
    form.submit();
}
</script>
@endpush