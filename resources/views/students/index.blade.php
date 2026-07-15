@extends('layouts.base')

@section('title', 'Élèves')
@section('page_title', 'Gestion des élèves')

@section('breadcrumb')
<span class="text-slate-300 dark:text-slate-600">/</span>
<span class="font-semibold text-slate-700 dark:text-slate-200">Élèves</span>
@endsection

@section('content')
<div class="space-y-5">

    {{-- ══════════════════════════════════════════════════════════
         EN-TÊTE
    ══════════════════════════════════════════════════════════ --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-800 dark:text-slate-100">
                Élèves inscrits
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                <span class="font-semibold text-blue-600 dark:text-blue-400">
                    {{ $students->total() }}
                </span>
                élève{{ $students->total() > 1 ? 's' : '' }} au total
                @if(request()->hasAny(['search', 'class_id', 'gender', 'academic_year_id']))
                · <span class="text-amber-500">Filtres actifs</span>
                @endif
            </p>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            {{-- Export --}}
            <a href="{{ route('exports.students', request()->only(['search', 'class_id', 'gender', 'academic_year_id'])) }}"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-medium
                      border border-slate-200 dark:border-slate-700
                      text-slate-600 dark:text-slate-400
                      hover:bg-slate-100 dark:hover:bg-slate-800
                      transition-all duration-200">
                <i class="bi bi-file-earmark-spreadsheet text-emerald-500"></i>
                <span class="hidden sm:inline">Exporter</span>
            </a>

            {{-- Toggle vue --}}
            <div class="flex items-center rounded-xl border border-slate-200 dark:border-slate-700
                        bg-white dark:bg-slate-800 overflow-hidden">
                <button id="btn-grid" onclick="setView('grid')" class="px-3 py-2 text-sm transition-all duration-200
                               bg-blue-600 text-white" title="Vue grille">
                    <i class="bi bi-grid-3x3-gap-fill"></i>
                </button>
                <button id="btn-list" onclick="setView('list')" class="px-3 py-2 text-sm transition-all duration-200
                               text-slate-500 dark:text-slate-400
                               hover:bg-slate-100 dark:hover:bg-slate-700" title="Vue liste">
                    <i class="bi bi-list-ul"></i>
                </button>
            </div>

            {{-- Nouveau --}}
            <a href="{{ route('students.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold
                      bg-blue-600 hover:bg-blue-700 text-white
                      transition-all duration-200 shadow-sm shadow-blue-500/30">
                <i class="bi bi-person-plus-fill"></i>
                <span class="hidden sm:inline">Nouvel élève</span>
                <span class="sm:hidden">Nouveau</span>
            </a>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════
         STATS RAPIDES
    ══════════════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        @php
        $statCards = [
        [
        'label' => 'Total inscrits',
        'value' => $students->total(),
        'icon' => 'bi-people-fill',
        'color' => 'blue',
        ],
        [
        'label' => 'Garçons',
        'value' => $students->getCollection()->where('gender', 'male')->count(),
        'icon' => 'bi-gender-male',
        'color' => 'cyan',
        ],
        [
        'label' => 'Filles',
        'value' => $students->getCollection()->where('gender', 'female')->count(),
        'icon' => 'bi-gender-female',
        'color' => 'pink',
        ],
        [
        'label' => 'Classes',
        'value' => $classes->count(),
        'icon' => 'bi-building',
        'color' => 'violet',
        ],
        ];
        @endphp

        @foreach($statCards as $card)
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

        <form method="GET" action="{{ route('students.index') }}" id="filter-form"
            class="flex flex-col lg:flex-row lg:items-end gap-4 p-4">

            {{-- Recherche --}}
            <div class="flex-1 min-w-0">
                <label class="text-xs font-medium text-slate-500 dark:text-slate-400 mb-1.5 block">
                    Rechercher
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <i class="bi bi-search text-slate-400 text-sm"></i>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Nom, email, matricule…" class="w-full pl-9 pr-4 py-2.5 text-sm rounded-xl
                                  border border-slate-200 dark:border-slate-700
                                  bg-slate-50 dark:bg-slate-900/50
                                  text-slate-800 dark:text-slate-100
                                  placeholder:text-slate-400
                                  focus:outline-none focus:ring-2
                                  focus:ring-blue-600/40 focus:border-blue-600
                                  transition" />
                </div>
            </div>

            {{-- Classe --}}
            <div class="w-full lg:w-44">
                <label class="text-xs font-medium text-slate-500 dark:text-slate-400 mb-1.5 block">
                    Classe
                </label>
                <div class="relative">
                    <select name="class_id" onchange="document.getElementById('filter-form').submit()" class="w-full pl-3.5 pr-9 py-2.5 text-sm rounded-xl appearance-none
                                   border border-slate-200 dark:border-slate-700
                                   bg-slate-50 dark:bg-slate-900/50
                                   text-slate-800 dark:text-slate-100
                                   focus:outline-none focus:ring-2
                                   focus:ring-blue-600/40 focus:border-blue-600
                                   transition">
                        <option value="">Toutes les classes</option>
                        @foreach($classes as $class)
                        <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                            {{ $class?->name }}
                        </option>
                        @endforeach
                    </select>
                    <span class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                        <i class="bi bi-chevron-down text-slate-400 text-xs"></i>
                    </span>
                </div>
            </div>

            {{-- Genre --}}
            <div class="w-full lg:w-36">
                <label class="text-xs font-medium text-slate-500 dark:text-slate-400 mb-1.5 block">
                    Genre
                </label>
                <div class="relative">
                    <select name="gender" onchange="document.getElementById('filter-form').submit()" class="w-full pl-3.5 pr-9 py-2.5 text-sm rounded-xl appearance-none
                                   border border-slate-200 dark:border-slate-700
                                   bg-slate-50 dark:bg-slate-900/50
                                   text-slate-800 dark:text-slate-100
                                   focus:outline-none focus:ring-2
                                   focus:ring-blue-600/40 focus:border-blue-600
                                   transition">
                        <option value="">Tous</option>
                        <option value="male" {{ request('gender') === 'male'   ? 'selected' : '' }}>
                            Garçons
                        </option>
                        <option value="female" {{ request('gender') === 'female' ? 'selected' : '' }}>
                            Filles
                        </option>
                    </select>
                    <span class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                        <i class="bi bi-chevron-down text-slate-400 text-xs"></i>
                    </span>
                </div>
            </div>

            {{-- Année académique --}}
            <div class="w-full lg:w-44">
                <label class="text-xs font-medium text-slate-500 dark:text-slate-400 mb-1.5 block">
                    Année académique
                </label>
                <div class="relative">
                    <select name="academic_year_id" onchange="document.getElementById('filter-form').submit()" class="w-full pl-3.5 pr-9 py-2.5 text-sm rounded-xl appearance-none
                                   border border-slate-200 dark:border-slate-700
                                   bg-slate-50 dark:bg-slate-900/50
                                   text-slate-800 dark:text-slate-100
                                   focus:outline-none focus:ring-2
                                   focus:ring-blue-600/40 focus:border-blue-600
                                   transition">
                        <option value="">Toutes les années</option>
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

            {{-- Actions filtres --}}
            <div class="flex items-center gap-2 shrink-0">
                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-medium
                               bg-blue-600 hover:bg-blue-700 text-white
                               transition-all shadow-sm shadow-blue-500/20">
                    <i class="bi bi-funnel-fill"></i>
                    Filtrer
                </button>

                @if(request()->hasAny(['search', 'class_id', 'gender', 'academic_year_id']))
                <a href="{{ route('students.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-medium
                          border border-slate-200 dark:border-slate-700
                          text-slate-600 dark:text-slate-400
                          hover:bg-slate-100 dark:hover:bg-slate-700
                          transition-all">
                    <i class="bi bi-x-circle"></i>
                    Réinitialiser
                </a>
                @endif
            </div>
        </form>
    </div>

    {{-- ══════════════════════════════════════════════════════════
         VUE GRILLE (SortableJS)
    ══════════════════════════════════════════════════════════ --}}
    <div id="view-grid">
        @if($students->isEmpty())
        {{-- État vide --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl
                        border border-slate-200 dark:border-slate-700
                        shadow-sm flex flex-col items-center justify-center py-20 px-4">
            <div class="w-20 h-20 rounded-3xl bg-slate-100 dark:bg-slate-700
                            flex items-center justify-center mb-5">
                <i class="bi bi-people text-4xl text-slate-300 dark:text-slate-500"></i>
            </div>
            <h3 class="text-base font-semibold text-slate-700 dark:text-slate-200 mb-1">
                Aucun élève trouvé
            </h3>
            <p class="text-sm text-slate-400 dark:text-slate-500 text-center max-w-xs mb-6">
                @if(request()->hasAny(['search', 'class_id', 'gender']))
                Aucun résultat ne correspond à vos filtres.
                @else
                Commencez par inscrire votre premier élève.
                @endif
            </p>
            @if(request()->hasAny(['search', 'class_id', 'gender']))
            <a href="{{ route('students.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-medium
                              border border-slate-200 dark:border-slate-700
                              text-slate-600 dark:text-slate-400
                              hover:bg-slate-100 dark:hover:bg-slate-700 transition-all">
                <i class="bi bi-arrow-counterclockwise"></i>
                Réinitialiser les filtres
            </a>
            @else
            <a href="{{ route('students.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold
                              bg-blue-600 hover:bg-blue-700 text-white
                              transition-all shadow-sm shadow-blue-500/30">
                <i class="bi bi-person-plus-fill"></i>
                Inscrire un élève
            </a>
            @endif
        </div>
        @else
        {{-- Grille --}}
        <div id="students-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            @foreach($students as $student)
            <div class="student-card group bg-white dark:bg-slate-800 rounded-2xl
                            border border-slate-200 dark:border-slate-700 shadow-sm
                            hover:shadow-md hover:border-blue-200 dark:hover:border-blue-800
                            transition-all duration-200 overflow-hidden cursor-grab active:cursor-grabbing"
                data-id="{{ $student->id }}">

                {{-- Header carte --}}
                <div class="relative h-20 bg-linear-to-br
                                @if($student->gender === 'female')
                                    from-pink-500 to-rose-600
                                @else
                                    from-blue-500 to-indigo-600
                                @endif">

                    {{-- Badge genre --}}
                    <span class="absolute top-3 left-3 inline-flex items-center gap-1
                                     px-2 py-0.5 rounded-full text-[10px] font-medium
                                     bg-white/20 text-white backdrop-blur-sm">
                        <i class="bi {{ $student->gender === 'female' ? 'bi-gender-female' : 'bi-gender-male' }}"></i>
                        {{ $student->gender === 'female' ? 'Fille' : 'Garçon' }}
                    </span>

                    {{-- Menu actions --}}
                    <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100
                                    transition-opacity duration-200">
                        <div class="relative" data-dropdown="card-menu-{{ $student->id }}">
                            <button data-dropdown="card-actions-{{ $student->id }}" class="w-7 h-7 rounded-lg bg-white/20 backdrop-blur-sm
                                               flex items-center justify-center
                                               text-white hover:bg-white/30
                                               transition-colors focus:outline-none">
                                <i class="bi bi-three-dots-vertical text-sm"></i>
                            </button>
                            <div id="card-actions-{{ $student->id }}" data-dropdown-menu class="hidden absolute right-0 top-8 w-40
                                            bg-white dark:bg-slate-800
                                            border border-slate-200 dark:border-slate-700
                                            rounded-xl shadow-lg overflow-hidden z-20
                                            opacity-0 scale-95 translate-y-1
                                            transition-all duration-150">
                                <a href="{{ route('students.show', $student) }}" class="flex items-center gap-2.5 px-3 py-2.5 text-xs
                                              text-slate-700 dark:text-slate-300
                                              hover:bg-slate-50 dark:hover:bg-slate-700/50
                                              hover:text-blue-600 dark:hover:text-blue-400
                                              transition-colors">
                                    <i class="bi bi-eye-fill w-4 text-center"></i>
                                    Voir le profil
                                </a>
                                <a href="{{ route('students.edit', $student) }}" class="flex items-center gap-2.5 px-3 py-2.5 text-xs
                                              text-slate-700 dark:text-slate-300
                                              hover:bg-slate-50 dark:hover:bg-slate-700/50
                                              hover:text-blue-600 dark:hover:text-blue-400
                                              transition-colors">
                                    <i class="bi bi-pencil-fill w-4 text-center"></i>
                                    Modifier
                                </a>
                                <button
                                    onclick="deleteStudent({{ $student->id }}, '{{ addslashes($student->user->name) }}')"
                                    class="flex items-center gap-2.5 w-full px-3 py-2.5 text-xs
                                                   text-red-600 dark:text-red-400
                                                   hover:bg-red-50 dark:hover:bg-red-900/20
                                                   transition-colors focus:outline-none">
                                    <i class="bi bi-trash3-fill w-4 text-center"></i>
                                    Supprimer
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Avatar --}}
                    <div class="absolute -bottom-7 left-1/2 -translate-x-1/2">
                        <div class="w-14 h-14 rounded-2xl ring-4 ring-white dark:ring-slate-800
                                        overflow-hidden shrink-0 shadow-md">
                            @if($student->photo)
                            <img src="{{ asset('storage/' . $student->photo) }}" alt="{{ $student->user->name }}"
                                class="w-full h-full object-cover" />
                            @else
                            <div class="w-full h-full
                                                bg-linear-to-br
                                                @if($student->gender === 'female')
                                                    from-pink-400 to-rose-500
                                                @else
                                                    from-blue-400 to-indigo-500
                                                @endif
                                                flex items-center justify-center
                                                text-white text-xl font-bold">
                                {{ strtoupper(substr($student->user->name, 0, 1)) }}
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Corps carte --}}
                <div class="pt-10 px-4 pb-4">

                    {{-- Nom + matricule --}}
                    <div class="text-center mb-3">
                        <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100
                                       truncate group-hover:text-blue-600
                                       dark:group-hover:text-blue-400 transition-colors">
                            {{ $student->user->name }}
                        </h3>
                        <p class="text-[11px] font-mono text-slate-400 dark:text-slate-500 mt-0.5">
                            {{ $student->matricule }}
                        </p>
                    </div>

                    {{-- Infos --}}
                    <div class="space-y-1.5 mb-4">
                        <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400">
                            <i class="bi bi-building w-3.5 text-center text-blue-400"></i>
                            <span class="truncate">{{ $student->classe->name ?? '—' }}</span>
                        </div>
                        <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400">
                            <i class="bi bi-calendar3 w-3.5 text-center text-emerald-400"></i>
                            <span>{{ $student->age ?? '—' }} ans</span>
                        </div>
                        <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400">
                            <i class="bi bi-envelope w-3.5 text-center text-violet-400"></i>
                            <span class="truncate">{{ $student->user->email }}</span>
                        </div>
                    </div>

                    {{-- Stats mini --}}
                    <div class="grid grid-cols-3 gap-2 mb-4
                                    bg-slate-50 dark:bg-slate-700/40
                                    rounded-xl p-2">
                        @php
                        $rate = $student->attendanceRate();
                        $rateColor = $rate >= 80
                        ? 'text-emerald-600 dark:text-emerald-400'
                        : ($rate >= 60
                        ? 'text-amber-600 dark:text-amber-400'
                        : 'text-red-600 dark:text-red-400');
                        @endphp
                        <div class="text-center">
                            <p class="text-xs font-bold {{ $rateColor }}">
                                {{ $rate }}%
                            </p>
                            <p class="text-[10px] text-slate-400 dark:text-slate-500">
                                Présence
                            </p>
                        </div>
                        <div class="text-center border-x border-slate-200 dark:border-slate-600">
                            <p class="text-xs font-bold text-slate-700 dark:text-slate-200">
                                {{ $student->absencesCount() }}
                            </p>
                            <p class="text-[10px] text-slate-400 dark:text-slate-500">
                                Absences
                            </p>
                        </div>
                        <div class="text-center">
                            @php $avg = $student->average(); @endphp
                            <p class="text-xs font-bold
                                          {{ $avg >= 10
                                            ? 'text-emerald-600 dark:text-emerald-400'
                                            : 'text-red-600 dark:text-red-400' }}">
                                {{ $avg > 0 ? $avg : '—' }}
                            </p>
                            <p class="text-[10px] text-slate-400 dark:text-slate-500">
                                Moyenne
                            </p>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex gap-2">
                        <a href="{{ route('students.show', $student) }}" class="flex-1 inline-flex items-center justify-center gap-1.5
                                      py-2 rounded-xl text-xs font-medium
                                      bg-blue-50 dark:bg-blue-900/20
                                      text-blue-600 dark:text-blue-400
                                      hover:bg-blue-100 dark:hover:bg-blue-900/40
                                      transition-all">
                            <i class="bi bi-eye-fill"></i>
                            Profil
                        </a>
                        <a href="{{ route('students.edit', $student) }}" class="flex-1 inline-flex items-center justify-center gap-1.5
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
            </div>
            @endforeach
        </div>
        @endif
    </div>

    {{-- ══════════════════════════════════════════════════════════
         VUE LISTE (masquée par défaut)
    ══════════════════════════════════════════════════════════ --}}
    <div id="view-list" class="hidden">
        <div class="bg-white dark:bg-slate-800 rounded-2xl
                    border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">

            {{-- Header tableau --}}
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-700/50
                                   border-b border-slate-200 dark:border-slate-700">
                            <th class="text-left px-5 py-3.5 text-xs font-semibold
                                       text-slate-500 dark:text-slate-400
                                       uppercase tracking-wider whitespace-nowrap">
                                Élève
                            </th>
                            <th class="text-left px-4 py-3.5 text-xs font-semibold
                                       text-slate-500 dark:text-slate-400
                                       uppercase tracking-wider whitespace-nowrap
                                       hidden sm:table-cell">
                                Matricule
                            </th>
                            <th class="text-left px-4 py-3.5 text-xs font-semibold
                                       text-slate-500 dark:text-slate-400
                                       uppercase tracking-wider whitespace-nowrap
                                       hidden md:table-cell">
                                Classe
                            </th>
                            <th class="text-center px-4 py-3.5 text-xs font-semibold
                                       text-slate-500 dark:text-slate-400
                                       uppercase tracking-wider whitespace-nowrap
                                       hidden lg:table-cell">
                                Présence
                            </th>
                            <th class="text-center px-4 py-3.5 text-xs font-semibold
                                       text-slate-500 dark:text-slate-400
                                       uppercase tracking-wider whitespace-nowrap
                                       hidden lg:table-cell">
                                Moyenne
                            </th>
                            <th class="text-left px-4 py-3.5 text-xs font-semibold
                                       text-slate-500 dark:text-slate-400
                                       uppercase tracking-wider whitespace-nowrap
                                       hidden xl:table-cell">
                                Genre
                            </th>
                            <th class="text-right px-5 py-3.5 text-xs font-semibold
                                       text-slate-500 dark:text-slate-400
                                       uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody id="students-list" class="divide-y divide-slate-100 dark:divide-slate-700">
                        @forelse($students as $student)
                        <tr class="group hover:bg-slate-50 dark:hover:bg-slate-700/50
                                   transition-colors" data-id="{{ $student->id }}">

                            {{-- Élève --}}
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-3">
                                    {{-- Avatar --}}
                                    <div class="w-9 h-9 rounded-xl overflow-hidden shrink-0
                                                ring-2 ring-slate-200 dark:ring-slate-700">
                                        @if($student->photo_path)
                                        <img src="{{ asset('storage/' . $student->photo_path) }}"
                                            alt="{{ $student->user->name }}" class="w-full h-full object-cover" />
                                        @else
                                        <div class="w-full h-full
                                                        bg-linear-to-br
                                                        {{ $student->gender === 'female'
                                                            ? 'from-pink-400 to-rose-500'
                                                            : 'from-blue-400 to-indigo-500' }}
                                                        flex items-center justify-center
                                                        text-white text-xs font-bold">
                                            {{ strtoupper(substr($student->user->name, 0, 1)) }}
                                        </div>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <a href="{{ route('students.show', $student) }}" class="text-sm font-semibold text-slate-700 dark:text-slate-200
                                                  hover:text-blue-600 dark:hover:text-blue-400
                                                  transition-colors truncate block max-w-35">
                                            {{ $student->user->name }}
                                        </a>
                                        <p class="text-xs text-slate-400 dark:text-slate-500 truncate">
                                            {{ $student->user->email }}
                                        </p>
                                    </div>
                                </div>
                            </td>

                            {{-- Matricule --}}
                            <td class="px-4 py-3.5 hidden sm:table-cell">
                                <span class="font-mono text-xs font-medium
                                             text-blue-600 dark:text-blue-400
                                             bg-blue-50 dark:bg-blue-900/20
                                             px-2 py-0.5 rounded-lg">
                                    {{ $student->matricule }}
                                </span>
                            </td>

                            {{-- Classe --}}
                            <td class="px-4 py-3.5 hidden md:table-cell">
                                <span class="inline-flex items-center gap-1.5 text-xs
                                             text-slate-600 dark:text-slate-300">
                                    <i class="bi bi-building text-slate-400 text-xs"></i>
                                    {{ $student->classe->name ?? '—' }}
                                </span>
                            </td>

                            {{-- Présence --}}
                            <td class="px-4 py-3.5 text-center hidden lg:table-cell">
                                @php
                                $rate = $student->attendanceRate();
                                $rateColor = $rate >= 80
                                ? 'text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-900/20'
                                : ($rate >= 60
                                ? 'text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-900/20'
                                : 'text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/20');
                                @endphp
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full
                                             text-xs font-semibold {{ $rateColor }}">
                                    {{ $rate }}%
                                </span>
                            </td>

                            {{-- Moyenne --}}
                            <td class="px-4 py-3.5 text-center hidden lg:table-cell">
                                @php $avg = $student->average(); @endphp
                                <span class="text-sm font-bold
                                             {{ $avg >= 10
                                                ? 'text-emerald-600 dark:text-emerald-400'
                                                : ($avg > 0 ? 'text-red-600 dark:text-red-400' : 'text-slate-400') }}">
                                    {{ $avg > 0 ? $avg . '/20' : '—' }}
                                </span>
                            </td>

                            {{-- Genre --}}
                            <td class="px-4 py-3.5 hidden xl:table-cell">
                                <span
                                    class="inline-flex items-center gap-1 text-xs font-medium
                                             px-2 py-0.5 rounded-full
                                             {{ $student->gender === 'female'
                                                ? 'bg-pink-50 dark:bg-pink-900/20 text-pink-600 dark:text-pink-400'
                                                : 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400' }}">
                                    <i class="bi {{ $student->gender === 'female'
                                                    ? 'bi-gender-female'
                                                    : 'bi-gender-male' }}"></i>
                                    {{ $student->gender === 'female' ? 'Fille' : 'Garçon' }}
                                </span>
                            </td>

                            {{-- Actions --}}
                            <td class="px-5 py-3.5 text-right">
                                <div class="flex items-center justify-end gap-1.5
                                            opacity-0 group-hover:opacity-100 transition-opacity">
                                    <a href="{{ route('students.show', $student) }}" title="Voir le profil" class="w-8 h-8 rounded-lg flex items-center justify-center
                                              bg-blue-50 dark:bg-blue-900/20
                                              text-blue-600 dark:text-blue-400
                                              hover:bg-blue-100 dark:hover:bg-blue-900/40
                                              transition-colors">
                                        <i class="bi bi-eye-fill text-sm"></i>
                                    </a>
                                    <a href="{{ route('students.edit', $student) }}" title="Modifier" class="w-8 h-8 rounded-lg flex items-center justify-center
                                              bg-slate-100 dark:bg-slate-700
                                              text-slate-600 dark:text-slate-300
                                              hover:bg-slate-200 dark:hover:bg-slate-600
                                              transition-colors">
                                        <i class="bi bi-pencil-fill text-sm"></i>
                                    </a>
                                    <button
                                        onclick="deleteStudent({{ $student->id }}, '{{ addslashes($student->user->name) }}')"
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
                                <i class="bi bi-people text-4xl text-slate-300 dark:text-slate-600 block mb-3"></i>
                                <p class="text-sm text-slate-500 dark:text-slate-400">
                                    Aucun élève trouvé
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
    @if($students->hasPages())
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4
                bg-white dark:bg-slate-800 rounded-2xl
                border border-slate-200 dark:border-slate-700
                shadow-sm px-5 py-3.5">

        <p class="text-xs text-slate-500 dark:text-slate-400 order-2 sm:order-1">
            Affichage de
            <span class="font-semibold text-slate-700 dark:text-slate-300">
                {{ $students->firstItem() }}
            </span>
            à
            <span class="font-semibold text-slate-700 dark:text-slate-300">
                {{ $students->lastItem() }}
            </span>
            sur
            <span class="font-semibold text-slate-700 dark:text-slate-300">
                {{ $students->total() }}
            </span>
            élèves
        </p>

        {{-- Pagination custom --}}
        <div class="flex items-center gap-1 order-1 sm:order-2">
            {{-- Précédent --}}
            @if($students->onFirstPage())
            <span class="px-3 py-1.5 rounded-xl text-xs text-slate-300 dark:text-slate-600
                             border border-slate-200 dark:border-slate-700 cursor-not-allowed">
                <i class="bi bi-chevron-left"></i>
            </span>
            @else
            <a href="{{ $students->previousPageUrl() }}" class="px-3 py-1.5 rounded-xl text-xs
                          border border-slate-200 dark:border-slate-700
                          text-slate-600 dark:text-slate-400
                          hover:bg-slate-100 dark:hover:bg-slate-700
                          transition-all">
                <i class="bi bi-chevron-left"></i>
            </a>
            @endif

            {{-- Pages --}}
            @foreach($students->getUrlRange(
            max(1, $students->currentPage() - 2),
            min($students->lastPage(), $students->currentPage() + 2)
            ) as $page => $url)
            @if($page == $students->currentPage())
            <span class="px-3 py-1.5 rounded-xl text-xs font-semibold
                                 bg-blue-600 text-white shadow-sm shadow-blue-500/30">
                {{ $page }}
            </span>
            @else
            <a href="{{ $url }}" class="px-3 py-1.5 rounded-xl text-xs
                              border border-slate-200 dark:border-slate-700
                              text-slate-600 dark:text-slate-400
                              hover:bg-slate-100 dark:hover:bg-slate-700
                              transition-all">
                {{ $page }}
            </a>
            @endif
            @endforeach

            {{-- Suivant --}}
            @if($students->hasMorePages())
            <a href="{{ $students->nextPageUrl() }}" class="px-3 py-1.5 rounded-xl text-xs
                          border border-slate-200 dark:border-slate-700
                          text-slate-600 dark:text-slate-400
                          hover:bg-slate-100 dark:hover:bg-slate-700
                          transition-all">
                <i class="bi bi-chevron-right"></i>
            </a>
            @else
            <span class="px-3 py-1.5 rounded-xl text-xs text-slate-300 dark:text-slate-600
                             border border-slate-200 dark:border-slate-700 cursor-not-allowed">
                <i class="bi bi-chevron-right"></i>
            </span>
            @endif
        </div>
    </div>
    @endif

</div>

{{-- Formulaire de suppression caché --}}
<form id="delete-form" method="POST" class="hidden">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {

    // ── Vue sauvegardée ────────────────────────────────────────
    const savedView = localStorage.getItem('students-view') ?? 'grid';
    setView(savedView, false);

    // ── SortableJS — Vue grille ────────────────────────────────
    const grid = document.getElementById('students-grid');
    if (grid && typeof Sortable !== 'undefined') {
        Sortable.create(grid, {
            animation: 200,
            ghostClass: 'opacity-40',
            chosenClass: 'ring-2 ring-blue-500 shadow-xl scale-[1.02]',
            dragClass: 'shadow-2xl rotate-1',
            handle: '.student-card',
            delay: 80,
            delayOnTouchOnly: true,

            onEnd(evt) {
                // Récupérer le nouvel ordre des IDs
                const order = [...grid.querySelectorAll('[data-id]')]
                    .map(el => el.dataset.id);

                // Persister l'ordre (optionnel : appel API)
                localStorage.setItem('students-order', JSON.stringify(order));

                window.showToast({
                    type: 'info',
                    title: 'Ordre mis à jour',
                    message: 'Le classement a été réorganisé.',
                    delay: 2500,
                });
            }
        });
    }

    // ── SortableJS — Vue liste ─────────────────────────────────
    const list = document.getElementById('students-list');
    if (list && typeof Sortable !== 'undefined') {
        Sortable.create(list, {
            animation: 150,
            ghostClass: 'opacity-40 bg-blue-50 dark:bg-blue-950/30',
            handle: 'tr',
            delay: 80,
            delayOnTouchOnly: true,
        });
    }
});

// ── Toggle vue grille / liste ──────────────────────────────────
function setView(view, save = true) {
    const gridView = document.getElementById('view-grid');
    const listView = document.getElementById('view-list');
    const btnGrid = document.getElementById('btn-grid');
    const btnList = document.getElementById('btn-list');

    const activeClass = 'bg-blue-600 text-white';
    const inactiveClass = 'text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700';

    if (view === 'grid') {
        gridView?.classList.remove('hidden');
        listView?.classList.add('hidden');
        btnGrid?.classList.add(...activeClass.split(' '));
        btnGrid?.classList.remove(...inactiveClass.split(' '));
        btnList?.classList.remove(...activeClass.split(' '));
        btnList?.classList.add(...inactiveClass.split(' '));
    } else {
        listView?.classList.remove('hidden');
        gridView?.classList.add('hidden');
        btnList?.classList.add(...activeClass.split(' '));
        btnList?.classList.remove(...inactiveClass.split(' '));
        btnGrid?.classList.remove(...activeClass.split(' '));
        btnGrid?.classList.add(...inactiveClass.split(' '));
    }

    if (save) localStorage.setItem('students-view', view);
}

// ── Suppression ────────────────────────────────────────────────
function deleteStudent(id, name) {
    if (!confirm(`Supprimer l'élève "${name}" ? Cette action est irréversible.`)) return;

    const form = document.getElementById('delete-form');
    form.action = `/students/${id}`;
    form.submit();
}
</script>
@endpush