@extends('layouts.base')

@section('title', 'Enseignants')
@section('page_title', 'Gestion des enseignants')

@section('breadcrumb')
<span class="text-slate-300 dark:text-slate-600">/</span>
<span class="font-semibold text-slate-700 dark:text-slate-200">Enseignants</span>
@endsection

@section('content')
<div class="space-y-5">

    {{-- ══════════════════════════════════════════════════════════
         EN-TÊTE
    ══════════════════════════════════════════════════════════ --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-800 dark:text-slate-100">
                Corps enseignant
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                <span class="font-semibold text-blue-600 dark:text-blue-400">
                    {{ $teachers->total() }}
                </span>
                enseignant{{ $teachers->total() > 1 ? 's' : '' }} au total
                @if(request()->hasAny(['search', 'subject_id', 'contract_status']))
                · <span class="text-amber-500">Filtres actifs</span>
                @endif
            </p>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            {{-- Export --}}
            <a href="{{ route('exports.teachers', request()->only(['search', 'subject_id', 'contract_status'])) }}"
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
            <a href="{{ route('teachers.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold
                      bg-blue-600 hover:bg-blue-700 text-white
                      transition-all duration-200 shadow-sm shadow-blue-500/30">
                <i class="bi bi-person-plus-fill"></i>
                <span class="hidden sm:inline">Nouvel enseignant</span>
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
        'label' => 'Total enseignants',
        'value' => $totalTeachers,
        'icon' => 'bi-person-badge-fill',
        'color' => 'blue',
        ],
        [
        'label' => 'Contrats actifs',
        'value' => $activeContracts,
        'icon' => 'bi-file-earmark-check-fill',
        'color' => 'emerald',
        ],
        [
        'label' => 'Matières enseignées',
        'value' => $subjects->count(),
        'icon' => 'bi-book-fill',
        'color' => 'violet',
        ],
        [
        'label' => 'Nouveaux ce mois',
        'value' => $newThisMonth,
        'icon' => 'bi-person-plus-fill',
        'color' => 'amber',
        ],
        ] as $card)
        <div class="bg-white dark:bg-slate-800 rounded-2xl px-4 py-3.5
                    border border-slate-200 dark:border-slate-700 shadow-sm
                    flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl flex-shrink-0
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

        <form method="GET" action="{{ route('teachers.index') }}" id="filter-form"
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

            {{-- Matière --}}
            <div class="w-full lg:w-48">
                <label class="text-xs font-medium text-slate-500 dark:text-slate-400 mb-1.5 block">
                    Matière
                </label>
                <div class="relative">
                    <select name="subject_id" onchange="document.getElementById('filter-form').submit()" class="w-full pl-3.5 pr-9 py-2.5 text-sm rounded-xl appearance-none
                                   border border-slate-200 dark:border-slate-700
                                   bg-slate-50 dark:bg-slate-900/50
                                   text-slate-800 dark:text-slate-100
                                   focus:outline-none focus:ring-2
                                   focus:ring-blue-600/40 focus:border-blue-600
                                   transition">
                        <option value="">Toutes les matières</option>
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

            {{-- Statut contrat --}}
            <div class="w-full lg:w-44">
                <label class="text-xs font-medium text-slate-500 dark:text-slate-400 mb-1.5 block">
                    Statut contrat
                </label>
                <div class="relative">
                    <select name="contract_status" onchange="document.getElementById('filter-form').submit()" class="w-full pl-3.5 pr-9 py-2.5 text-sm rounded-xl appearance-none
                                   border border-slate-200 dark:border-slate-700
                                   bg-slate-50 dark:bg-slate-900/50
                                   text-slate-800 dark:text-slate-100
                                   focus:outline-none focus:ring-2
                                   focus:ring-blue-600/40 focus:border-blue-600
                                   transition">
                        <option value="">Tous</option>
                        <option value="active" {{ request('contract_status') === 'active'   ? 'selected' : '' }}>
                            Actif
                        </option>
                        <option value="expired" {{ request('contract_status') === 'expired'  ? 'selected' : '' }}>
                            Expiré
                        </option>
                        <option value="pending" {{ request('contract_status') === 'pending'  ? 'selected' : '' }}>
                            En attente
                        </option>
                    </select>
                    <span class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                        <i class="bi bi-chevron-down text-slate-400 text-xs"></i>
                    </span>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex items-center gap-2 shrink-0">
                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-medium
                               bg-blue-600 hover:bg-blue-700 text-white
                               transition-all shadow-sm shadow-blue-500/20">
                    <i class="bi bi-funnel-fill"></i>
                    Filtrer
                </button>

                @if(request()->hasAny(['search', 'subject_id', 'contract_status']))
                <a href="{{ route('teachers.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-medium
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
         VUE GRILLE
    ══════════════════════════════════════════════════════════ --}}
    <div id="view-grid">
        @if($teachers->isEmpty())
        <div class="bg-white dark:bg-slate-800 rounded-2xl
                    border border-slate-200 dark:border-slate-700
                    shadow-sm flex flex-col items-center justify-center py-20 px-4">
            <div class="w-20 h-20 rounded-3xl bg-slate-100 dark:bg-slate-700
                        flex items-center justify-center mb-5">
                <i class="bi bi-person-badge text-4xl text-slate-300 dark:text-slate-500"></i>
            </div>
            <h3 class="text-base font-semibold text-slate-700 dark:text-slate-200 mb-1">
                Aucun enseignant trouvé
            </h3>
            <p class="text-sm text-slate-400 dark:text-slate-500 text-center max-w-xs mb-6">
                @if(request()->hasAny(['search', 'subject_id', 'contract_status']))
                Aucun résultat ne correspond à vos filtres.
                @else
                Commencez par enregistrer votre premier enseignant.
                @endif
            </p>
            @if(request()->hasAny(['search', 'subject_id', 'contract_status']))
            <a href="{{ route('teachers.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-medium
                          border border-slate-200 dark:border-slate-700
                          text-slate-600 dark:text-slate-400
                          hover:bg-slate-100 dark:hover:bg-slate-700 transition-all">
                <i class="bi bi-arrow-counterclockwise"></i>
                Réinitialiser les filtres
            </a>
            @else
            <a href="{{ route('teachers.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold
                          bg-blue-600 hover:bg-blue-700 text-white
                          transition-all shadow-sm shadow-blue-500/30">
                <i class="bi bi-person-plus-fill"></i>
                Ajouter un enseignant
            </a>
            @endif
        </div>
        @else
        <div id="teachers-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            @foreach($teachers as $teacher)
            @php
            $contract = $teacher->contracts->where('status', 'active')->first();
            $contractColor = match($contract?->status) {
            'active' => 'emerald',
            'expired' => 'red',
            'pending' => 'amber',
            default => 'slate',
            };
            $contractLabel = match($contract?->status) {
            'active' => 'Contrat actif',
            'expired' => 'Contrat expiré',
            'pending' => 'En attente',
            default => 'Sans contrat',
            };
            $subjectsList = $teacher->assignments
            ->where('academic_year_id', $activeYear?->id)
            ->pluck('subject.name')
            ->unique()
            ->take(3);
            @endphp
            <div class="teacher-card group bg-white dark:bg-slate-800 rounded-2xl
                        border border-slate-200 dark:border-slate-700 shadow-sm
                        hover:shadow-md hover:border-emerald-200 dark:hover:border-emerald-800
                        transition-all duration-200 overflow-hidden cursor-grab active:cursor-grabbing"
                data-id="{{ $teacher->id }}">

                {{-- Header --}}
                <div class="relative h-20 bg-linear-to-br from-emerald-500 to-teal-600">

                    {{-- Badge contrat --}}
                    <span class="absolute top-3 left-3 inline-flex items-center gap-1
                                 px-2 py-0.5 rounded-full text-[10px] font-medium
                                 bg-white/20 text-white backdrop-blur-sm">
                        <span class="w-1.5 h-1.5 rounded-full
                                     {{ $contract ? 'bg-white animate-pulse' : 'bg-white/50' }}">
                        </span>
                        {{ $contractLabel }}
                    </span>

                    {{-- Menu actions --}}
                    <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100
                                transition-opacity duration-200">
                        <button data-dropdown="teacher-actions-{{ $teacher->id }}" class="w-7 h-7 rounded-lg bg-white/20 backdrop-blur-sm
                                       flex items-center justify-center
                                       text-white hover:bg-white/30
                                       transition-colors focus:outline-none">
                            <i class="bi bi-three-dots-vertical text-sm"></i>
                        </button>
                        <div id="teacher-actions-{{ $teacher->id }}" data-dropdown-menu class="hidden absolute right-0 top-8 w-44
                                    bg-white dark:bg-slate-800
                                    border border-slate-200 dark:border-slate-700
                                    rounded-xl shadow-lg overflow-hidden z-20
                                    opacity-0 scale-95 translate-y-1
                                    transition-all duration-150">
                            <a href="{{ route('teachers.show', $teacher) }}" class="flex items-center gap-2.5 px-3 py-2.5 text-xs
                                      text-slate-700 dark:text-slate-300
                                      hover:bg-slate-50 dark:hover:bg-slate-700/50
                                      hover:text-blue-600 transition-colors">
                                <i class="bi bi-eye-fill w-4 text-center"></i>
                                Voir le profil
                            </a>
                            <a href="{{ route('teachers.edit', $teacher) }}" class="flex items-center gap-2.5 px-3 py-2.5 text-xs
                                      text-slate-700 dark:text-slate-300
                                      hover:bg-slate-50 dark:hover:bg-slate-700/50
                                      hover:text-blue-600 transition-colors">
                                <i class="bi bi-pencil-fill w-4 text-center"></i>
                                Modifier
                            </a>
                            <a href="{{ route('teacher-contracts.create', ['teacher_id' => $teacher->id]) }}" class="flex items-center gap-2.5 px-3 py-2.5 text-xs
                                      text-slate-700 dark:text-slate-300
                                      hover:bg-slate-50 dark:hover:bg-slate-700/50
                                      hover:text-emerald-600 transition-colors">
                                <i class="bi bi-file-earmark-plus w-4 text-center"></i>
                                Nouveau contrat
                            </a>
                            <button
                                onclick="deleteTeacher({{ $teacher->id }}, '{{ addslashes($teacher->user->name) }}')"
                                class="flex items-center gap-2.5 w-full px-3 py-2.5 text-xs
                                           text-red-600 dark:text-red-400
                                           hover:bg-red-50 dark:hover:bg-red-900/20
                                           transition-colors focus:outline-none">
                                <i class="bi bi-trash3-fill w-4 text-center"></i>
                                Supprimer
                            </button>
                        </div>
                    </div>

                    {{-- Avatar --}}
                    <div class="absolute -bottom-7 left-1/2 -translate-x-1/2">
                        <div class="w-14 h-14 rounded-2xl ring-4 ring-white dark:ring-slate-800
                                    overflow-hidden shadow-md">
                            @if($teacher->user->avatar)
                            <img src="{{ asset('storage/' . $teacher->user->avatar) }}" alt="{{ $teacher->user->name }}"
                                class="w-full h-full object-cover" />
                            @else
                            <div class="w-full h-full bg-linear-to-br
                                            from-emerald-400 to-teal-500
                                            flex items-center justify-center
                                            text-white text-xl font-bold">
                                {{ strtoupper(substr($teacher->user->name, 0, 1)) }}
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Corps --}}
                <div class="pt-10 px-4 pb-4">

                    {{-- Nom + matricule --}}
                    <div class="text-center mb-3">
                        <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100
                                   truncate group-hover:text-emerald-600
                                   dark:group-hover:text-emerald-400 transition-colors">
                            {{ $teacher->user->name }}
                        </h3>
                        <p class="text-[11px] font-mono text-slate-400 dark:text-slate-500 mt-0.5">
                            {{ $teacher->employee_number ?? '—' }}
                        </p>
                    </div>

                    {{-- Infos --}}
                    <div class="space-y-1.5 mb-3">
                        <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400">
                            <i class="bi bi-envelope w-3.5 text-center text-emerald-400"></i>
                            <span class="truncate">{{ $teacher->user->email }}</span>
                        </div>
                        <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400">
                            <i class="bi bi-award w-3.5 text-center text-blue-400"></i>
                            <span class="truncate">{{ $teacher->qualification ?? 'Non renseigné' }}</span>
                        </div>
                        @if($teacher->user->phone)
                        <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400">
                            <i class="bi bi-telephone w-3.5 text-center text-violet-400"></i>
                            <span>{{ $teacher->user->phone }}</span>
                        </div>
                        @endif
                    </div>

                    {{-- Matières --}}
                    @if($subjectsList->isNotEmpty())
                    <div class="flex flex-wrap gap-1 mb-4">
                        @foreach($subjectsList as $subject)
                        <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-[10px] font-medium
                                     bg-emerald-50 dark:bg-emerald-900/20
                                     text-emerald-700 dark:text-emerald-400
                                     border border-emerald-200 dark:border-emerald-800">
                            {{ $subject }}
                        </span>
                        @endforeach
                        @if($teacher->assignments->where('academic_year_id',
                        $activeYear?->id)->pluck('subject.name')->unique()->count() > 3)
                        <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-[10px] font-medium
                                     bg-slate-100 dark:bg-slate-700
                                     text-slate-500 dark:text-slate-400">
                            +{{ $teacher->assignments->where('academic_year_id', $activeYear?->id)->pluck('subject.name')->unique()->count() - 3 }}
                        </span>
                        @endif
                    </div>
                    @else
                    <div class="mb-4">
                        <span class="text-[10px] text-slate-400 dark:text-slate-500 italic">
                            Aucune matière assignée
                        </span>
                    </div>
                    @endif

                    {{-- Stats --}}
                    <div class="grid grid-cols-2 gap-2 mb-4
                                bg-slate-50 dark:bg-slate-700/40 rounded-xl p-2">
                        <div class="text-center">
                            <p class="text-xs font-bold text-slate-700 dark:text-slate-200">
                                {{ $teacher->assignments
                                    ->where('academic_year_id', $activeYear?->id)
                                    ->pluck('school_class_id')->unique()->count() }}
                            </p>
                            <p class="text-[10px] text-slate-400 dark:text-slate-500">Classes</p>
                        </div>
                        <div class="text-center border-l border-slate-200 dark:border-slate-600">
                            @php
                            $daysLeft = $contract?->end_date
                            ? now()->diffInDays($contract->end_date, false)
                            : null;
                            @endphp
                            <p class="text-xs font-bold
                                      {{ $daysLeft === null ? 'text-slate-400' :
                                         ($daysLeft <= 30 ? 'text-red-600 dark:text-red-400' :
                                         'text-emerald-600 dark:text-emerald-400') }}">
                                {{ $daysLeft !== null ? 'J-' . max(0, $daysLeft) : '—' }}
                            </p>
                            <p class="text-[10px] text-slate-400 dark:text-slate-500">Contrat</p>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex gap-2">
                        <a href="{{ route('teachers.show', $teacher) }}" class="flex-1 inline-flex items-center justify-center gap-1.5
                                  py-2 rounded-xl text-xs font-medium
                                  bg-emerald-50 dark:bg-emerald-900/20
                                  text-emerald-600 dark:text-emerald-400
                                  hover:bg-emerald-100 dark:hover:bg-emerald-900/40
                                  transition-all">
                            <i class="bi bi-eye-fill"></i>
                            Profil
                        </a>
                        <a href="{{ route('teachers.edit', $teacher) }}" class="flex-1 inline-flex items-center justify-center gap-1.5
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
                                       text-slate-500 dark:text-slate-400 uppercase tracking-wider
                                       hidden sm:table-cell">
                                Matricule
                            </th>
                            <th class="text-left px-4 py-3.5 text-xs font-semibold
                                       text-slate-500 dark:text-slate-400 uppercase tracking-wider
                                       hidden md:table-cell">
                                Qualification
                            </th>
                            <th class="text-left px-4 py-3.5 text-xs font-semibold
                                       text-slate-500 dark:text-slate-400 uppercase tracking-wider
                                       hidden lg:table-cell">
                                Matières
                            </th>
                            <th class="text-center px-4 py-3.5 text-xs font-semibold
                                       text-slate-500 dark:text-slate-400 uppercase tracking-wider
                                       hidden lg:table-cell">
                                Classes
                            </th>
                            <th class="text-center px-4 py-3.5 text-xs font-semibold
                                       text-slate-500 dark:text-slate-400 uppercase tracking-wider
                                       hidden xl:table-cell">
                                Contrat
                            </th>
                            <th class="text-right px-5 py-3.5 text-xs font-semibold
                                       text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody id="teachers-list" class="divide-y divide-slate-100 dark:divide-slate-700">
                        @forelse($teachers as $teacher)
                        @php
                        $contract = $teacher->contracts->where('status', 'active')->first();
                        $subjectsCount = $teacher->assignments
                        ->where('academic_year_id', $activeYear?->id)
                        ->pluck('subject.name')->unique();
                        @endphp
                        <tr class="group hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors"
                            data-id="{{ $teacher->id }}">

                            {{-- Enseignant --}}
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl overflow-hidden shrink-0
                                                ring-2 ring-slate-200 dark:ring-slate-700">
                                        @if($teacher->user->avatar)
                                        <img src="{{ asset('storage/' . $teacher->user?->avatar) }}"
                                            alt="{{ $teacher->user->name }}" class="w-full h-full object-cover" />
                                        @else
                                        <div class="w-full h-full bg-linear-to-br
                                                        from-emerald-400 to-teal-500
                                                        flex items-center justify-center
                                                        text-white text-xs font-bold">
                                            {{ strtoupper(substr($teacher->user->name, 0, 1)) }}
                                        </div>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <a href="{{ route('teachers.show', $teacher) }}" class="text-sm font-semibold text-slate-700 dark:text-slate-200
                                                  hover:text-emerald-600 dark:hover:text-emerald-400
                                                  transition-colors truncate block max-w-35">
                                            {{ $teacher->user->name }}
                                        </a>
                                        <p class="text-xs text-slate-400 truncate">
                                            {{ $teacher->user->email }}
                                        </p>
                                    </div>
                                </div>
                            </td>

                            {{-- Matricule --}}
                            <td class="px-4 py-3.5 hidden sm:table-cell">
                                <span class="font-mono text-xs font-medium
                                             text-emerald-600 dark:text-emerald-400
                                             bg-emerald-50 dark:bg-emerald-900/20
                                             px-2 py-0.5 rounded-lg">
                                    {{ $teacher->employee_number ?? '—' }}
                                </span>
                            </td>

                            {{-- Qualification --}}
                            <td class="px-4 py-3.5 hidden md:table-cell">
                                <span class="text-xs text-slate-600 dark:text-slate-300">
                                    {{ $teacher->qualification ?? '—' }}
                                </span>
                            </td>

                            {{-- Matières --}}
                            <td class="px-4 py-3.5 hidden lg:table-cell">
                                <div class="flex flex-wrap gap-1">
                                    @foreach($subjectsCount->take(2) as $subject)
                                    <span class="text-[10px] px-1.5 py-0.5 rounded-lg font-medium
                                                 bg-emerald-50 dark:bg-emerald-900/20
                                                 text-emerald-700 dark:text-emerald-400">
                                        {{ $subject }}
                                    </span>
                                    @endforeach
                                    @if($subjectsCount->count() > 2)
                                    <span class="text-[10px] px-1.5 py-0.5 rounded-lg
                                                 bg-slate-100 dark:bg-slate-700
                                                 text-slate-500 dark:text-slate-400">
                                        +{{ $subjectsCount->count() - 2 }}
                                    </span>
                                    @endif
                                </div>
                            </td>

                            {{-- Classes --}}
                            <td class="px-4 py-3.5 text-center hidden lg:table-cell">
                                <span class="inline-flex items-center justify-center
                                             w-7 h-7 rounded-lg text-xs font-bold
                                             bg-blue-50 dark:bg-blue-900/20
                                             text-blue-600 dark:text-blue-400">
                                    {{ $teacher->assignments
                                        ->where('academic_year_id', $activeYear?->id)
                                        ->pluck('school_class_id')->unique()->count() }}
                                </span>
                            </td>

                            {{-- Contrat --}}
                            <td class="px-4 py-3.5 text-center hidden xl:table-cell">
                                @if($contract)
                                @php
                                $daysLeft = now()->diffInDays($contract->end_date, false);
                                $color = $daysLeft <= 30 ? 'red' : 'emerald' ; @endphp <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full
                                             text-[10px] font-semibold
                                             bg-{{ $color }}-50 dark:bg-{{ $color }}-900/20
                                             text-{{ $color }}-600 dark:text-{{ $color }}-400">
                                    <span class="w-1.5 h-1.5 rounded-full bg-{{ $color }}-500
                                                 {{ $daysLeft > 30 ? 'animate-pulse' : '' }}"></span>
                                    J-{{ max(0, $daysLeft) }}
                                    </span>
                                    @else
                                    <span class="text-xs text-slate-400 dark:text-slate-500">—</span>
                                    @endif
                            </td>

                            {{-- Actions --}}
                            <td class="px-5 py-3.5 text-right">
                                <div class="flex items-center justify-end gap-1.5
                                            opacity-0 group-hover:opacity-100 transition-opacity">
                                    <a href="{{ route('teachers.show', $teacher) }}" title="Voir le profil" class="w-8 h-8 rounded-lg flex items-center justify-center
                                              bg-emerald-50 dark:bg-emerald-900/20
                                              text-emerald-600 dark:text-emerald-400
                                              hover:bg-emerald-100 dark:hover:bg-emerald-900/40
                                              transition-colors">
                                        <i class="bi bi-eye-fill text-sm"></i>
                                    </a>
                                    <a href="{{ route('teachers.edit', $teacher) }}" title="Modifier" class="w-8 h-8 rounded-lg flex items-center justify-center
                                              bg-slate-100 dark:bg-slate-700
                                              text-slate-600 dark:text-slate-300
                                              hover:bg-slate-200 dark:hover:bg-slate-600
                                              transition-colors">
                                        <i class="bi bi-pencil-fill text-sm"></i>
                                    </a>
                                    <button
                                        onclick="deleteTeacher({{ $teacher->id }}, '{{ addslashes($teacher->user->name) }}')"
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
                                <i
                                    class="bi bi-person-badge text-4xl text-slate-300 dark:text-slate-600 block mb-3"></i>
                                <p class="text-sm text-slate-500 dark:text-slate-400">
                                    Aucun enseignant trouvé
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
    @if($teachers->hasPages())
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4
                bg-white dark:bg-slate-800 rounded-2xl
                border border-slate-200 dark:border-slate-700
                shadow-sm px-5 py-3.5">
        <p class="text-xs text-slate-500 dark:text-slate-400 order-2 sm:order-1">
            Affichage de
            <span class="font-semibold text-slate-700 dark:text-slate-300">
                {{ $teachers->firstItem() }}
            </span>
            à
            <span class="font-semibold text-slate-700 dark:text-slate-300">
                {{ $teachers->lastItem() }}
            </span>
            sur
            <span class="font-semibold text-slate-700 dark:text-slate-300">
                {{ $teachers->total() }}
            </span>
            enseignants
        </p>

        <div class="flex items-center gap-1 order-1 sm:order-2">
            @if($teachers->onFirstPage())
            <span class="px-3 py-1.5 rounded-xl text-xs text-slate-300 dark:text-slate-600
                             border border-slate-200 dark:border-slate-700 cursor-not-allowed">
                <i class="bi bi-chevron-left"></i>
            </span>
            @else
            <a href="{{ $teachers->previousPageUrl() }}" class="px-3 py-1.5 rounded-xl text-xs
                          border border-slate-200 dark:border-slate-700
                          text-slate-600 dark:text-slate-400
                          hover:bg-slate-100 dark:hover:bg-slate-700 transition-all">
                <i class="bi bi-chevron-left"></i>
            </a>
            @endif

            @foreach($teachers->getUrlRange(
            max(1, $teachers->currentPage() - 2),
            min($teachers->lastPage(), $teachers->currentPage() + 2)
            ) as $page => $url)
            @if($page == $teachers->currentPage())
            <span class="px-3 py-1.5 rounded-xl text-xs font-semibold
                                 bg-blue-600 text-white shadow-sm shadow-blue-500/30">
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

            @if($teachers->hasMorePages())
            <a href="{{ $teachers->nextPageUrl() }}" class="px-3 py-1.5 rounded-xl text-xs
                          border border-slate-200 dark:border-slate-700
                          text-slate-600 dark:text-slate-400
                          hover:bg-slate-100 dark:hover:bg-slate-700 transition-all">
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

{{-- Formulaire suppression --}}
<form id="delete-form" method="POST" class="hidden">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {

    const savedView = localStorage.getItem('teachers-view') ?? 'grid';
    setView(savedView, false);

    // SortableJS — Grille
    const grid = document.getElementById('teachers-grid');
    if (grid && typeof Sortable !== 'undefined') {
        Sortable.create(grid, {
            animation: 200,
            ghostClass: 'opacity-40',
            chosenClass: 'ring-2 ring-emerald-500 shadow-xl scale-[1.02]',
            dragClass: 'shadow-2xl rotate-1',
            delay: 80,
            delayOnTouchOnly: true,
            onEnd() {
                window.showToast({
                    type: 'info',
                    title: 'Ordre mis à jour',
                    message: 'Le classement a été réorganisé.',
                    delay: 2500,
                });
            }
        });
    }

    // SortableJS — Liste
    const list = document.getElementById('teachers-list');
    if (list && typeof Sortable !== 'undefined') {
        Sortable.create(list, {
            animation: 150,
            ghostClass: 'opacity-40 bg-emerald-50 dark:bg-emerald-950/30',
            delay: 80,
            delayOnTouchOnly: true,
        });
    }
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

    if (save) localStorage.setItem('teachers-view', view);
}

function deleteTeacher(id, name) {
    if (!confirm(`Supprimer l'enseignant "${name}" ? Cette action est irréversible.`)) return;
    const form = document.getElementById('delete-form');
    form.action = `/teachers/${id}`;
    form.submit();
}
</script>
@endpush