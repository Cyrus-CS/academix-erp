@extends('layouts.base')

@section('title', 'Bulletins')
@section('page_title', 'Bulletins scolaires')

@section('breadcrumb')
<span class="text-slate-300 dark:text-slate-600">/</span>
<span class="font-semibold text-slate-700 dark:text-slate-200">Bulletins</span>
@endsection

@section('content')
<div class="space-y-5">

    {{-- ══════════════════════════════════════════════════════════
         EN-TÊTE
    ══════════════════════════════════════════════════════════ --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-800 dark:text-slate-100">
                Bulletins scolaires
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                <span class="font-semibold text-blue-600 dark:text-blue-400">
                    {{ $generated }}
                </span>
                bulletin{{ $generated > 1 ? 's' : '' }} générés ·
                @if($currentTerm)
                <span class="font-semibold text-emerald-600 dark:text-emerald-400">
                    {{ $currentTerm->name }}
                </span>
                en cours
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

            {{-- Générer tous --}}
            @can('view report_cards')
            <button onclick="generateAll()" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-medium
                           border border-slate-200 dark:border-slate-700
                           text-slate-600 dark:text-slate-400
                           hover:bg-slate-100 dark:hover:bg-slate-800 transition-all">
                <i class="bi bi-lightning-fill text-amber-500"></i>
                <span class="hidden sm:inline">Générer tous</span>
            </button>
            @endcan

            <a href="{{ route('report-cards.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold
                      bg-blue-600 hover:bg-blue-700 text-white
                      transition-all shadow-sm shadow-blue-500/30">
                <i class="bi bi-file-earmark-plus-fill"></i>
                <span class="hidden sm:inline">Nouveau bulletin</span>
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
        'label' => 'Bulletins générés',
        'value' => $generated,
        'icon' => 'bi-file-earmark-bar-graph-fill',
        'color' => 'blue',
        ],
        [
        'label' => 'Classes concernées',
        'value' => $classes->count(),
        'icon' => 'bi-building',
        'color' => 'emerald',
        ],
        [
        'label' => 'Trimestre actuel',
        'value' => $currentTerm?->name ?? '—',
        'icon' => 'bi-calendar-range',
        'color' => 'violet',
        'small' => true,
        ],
        [
        'label' => 'Année académique',
        'value' => $activeYear?->name ?? '—',
        'icon' => 'bi-calendar3',
        'color' => 'amber',
        'small' => true,
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
                <p class="{{ isset($card['small']) ? 'text-sm' : 'text-xl' }}
                           font-bold text-slate-800 dark:text-slate-100 leading-tight truncate">
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
        <form method="GET" action="{{ route('report-cards.index') }}" id="filter-form"
            class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 p-4">

            {{-- Recherche --}}
            <div>
                <label class="text-xs font-medium text-slate-500 dark:text-slate-400 mb-1.5 block">
                    Rechercher
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <i class="bi bi-search text-slate-400 text-sm"></i>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Nom de l'élève…"
                        class="w-full pl-9 pr-4 py-2.5 text-sm rounded-xl
                                  border border-slate-200 dark:border-slate-700
                                  bg-slate-50 dark:bg-slate-900/50
                                  text-slate-800 dark:text-slate-100
                                  placeholder:text-slate-400
                                  focus:outline-none focus:ring-2
                                  focus:ring-blue-600/40 focus:border-blue-600 transition" />
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
                        <option value="">Toutes les classes</option>
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

            {{-- Trimestre --}}
            <div>
                <label class="text-xs font-medium text-slate-500 dark:text-slate-400 mb-1.5 block">
                    Trimestre
                </label>
                <div class="relative">
                    <select name="term_id" onchange="document.getElementById('filter-form').submit()" class="w-full pl-3.5 pr-9 py-2.5 text-sm rounded-xl appearance-none
                                   border border-slate-200 dark:border-slate-700
                                   bg-slate-50 dark:bg-slate-900/50
                                   text-slate-800 dark:text-slate-100
                                   focus:outline-none focus:ring-2
                                   focus:ring-blue-600/40 focus:border-blue-600 transition">
                        <option value="">Tous les trimestres</option>
                        @foreach($terms as $term)
                        <option value="{{ $term->id }}" {{ request('term_id') == $term->id ? 'selected' : '' }}>
                            {{ $term->name }}
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
                @if(request()->hasAny(['search', 'class_id', 'term_id']))
                <a href="{{ route('report-cards.index') }}" class="shrink-0 inline-flex items-center justify-center
                          w-10 h-10 rounded-xl border border-slate-200 dark:border-slate-700
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
        @if($reportCards->isEmpty())
        <div class="bg-white dark:bg-slate-800 rounded-2xl
                    border border-slate-200 dark:border-slate-700
                    shadow-sm flex flex-col items-center justify-center py-20 px-4">
            <div class="w-16 h-16 rounded-2xl bg-slate-100 dark:bg-slate-700
                        flex items-center justify-center mb-4">
                <i class="bi bi-file-earmark-bar-graph text-3xl
                          text-slate-300 dark:text-slate-500"></i>
            </div>
            <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1">
                Aucun bulletin trouvé
            </h3>
            <p class="text-xs text-slate-400 dark:text-slate-500 text-center mb-5">
                @if(request()->hasAny(['search', 'class_id', 'term_id']))
                Aucun résultat pour ces filtres.
                @else
                Générez votre premier bulletin scolaire.
                @endif
            </p>
            <a href="{{ route('report-cards.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold
                      bg-blue-600 hover:bg-blue-700 text-white
                      transition-all shadow-sm shadow-blue-500/30">
                <i class="bi bi-file-earmark-plus-fill"></i>
                Créer un bulletin
            </a>
        </div>
        @else
        <x-sortable-grid resource="report-cards"
            class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            @foreach($reportCards as $reportCard)
            @php
            $avg = $reportCard->average ?? 0;
            $avgColor = $avg >= 14
            ? 'emerald' : ($avg >= 10 ? 'blue' : ($avg >= 8 ? 'amber' : 'red'));
            $mention = match(true) {
            $avg >= 16 => ['label' => 'Très bien', 'color' => 'emerald'],
            $avg >= 14 => ['label' => 'Bien', 'color' => 'blue'],
            $avg >= 12 => ['label' => 'Assez bien', 'color' => 'cyan'],
            $avg >= 10 => ['label' => 'Passable', 'color' => 'amber'],
            default => ['label' => 'Insuffisant','color' => 'red'],
            };
            @endphp
            <x-sortable-item :id="$reportCard->id" class="report-card-item group bg-white dark:bg-slate-800 rounded-2xl
                        border border-slate-200 dark:border-slate-700 shadow-sm
                        hover:shadow-md hover:border-blue-200 dark:hover:border-blue-800
                        transition-all duration-200 overflow-hidden
                        cursor-grab active:cursor-grabbing">

                {{-- Header --}}
                <div class="relative h-20 bg-linear-to-br
                            from-slate-700 to-slate-900
                            dark:from-slate-800 dark:to-slate-950
                            flex items-center px-4 gap-3">

                    {{-- Avatar élève --}}
                    <div class="w-12 h-12 rounded-2xl overflow-hidden shrink-0
                                ring-2 ring-white/20 shadow-md">
                        @if($reportCard->student->photo_path)
                        <img src="{{ asset('storage/' . $reportCard->student->photo_path) }}"
                            class="w-full h-full object-cover" alt="{{ $reportCard->student->user->name }}" />
                        @else
                        <div class="w-full h-full
                                        bg-linear-to-br from-blue-500 to-indigo-600
                                        flex items-center justify-center">
                            <span class="text-lg font-black text-white">
                                {{ strtoupper(substr($reportCard->student->user->name ?? 'E', 0, 1)) }}
                            </span>
                        </div>
                        @endif
                    </div>

                    <div class="min-w-0 flex-1">
                        <h3 class="text-sm font-bold text-white truncate">
                            {{ $reportCard->student->user->name ?? '—' }}
                        </h3>
                        <p class="text-[11px] text-slate-400 truncate">
                            {{ $reportCard->student->matricule ?? '—' }}
                        </p>
                    </div>

                    {{-- Menu --}}
                    <div class="shrink-0 opacity-0 group-hover:opacity-100 transition-opacity">
                        <button data-dropdown="rc-actions-{{ $reportCard->id }}" class="w-7 h-7 rounded-lg bg-white/10 backdrop-blur-sm
                                       flex items-center justify-center text-white
                                       hover:bg-white/20 transition-colors focus:outline-none">
                            <i class="bi bi-three-dots-vertical text-sm"></i>
                        </button>
                        <div id="rc-actions-{{ $reportCard->id }}" data-dropdown-menu class="hidden absolute right-0 top-8 w-44
                                    bg-white dark:bg-slate-800
                                    border border-slate-200 dark:border-slate-700
                                    rounded-xl shadow-lg overflow-hidden z-20
                                    opacity-0 scale-95 translate-y-1
                                    transition-all duration-150">
                            <a href="{{ route('report-cards.show', $reportCard) }}" class="flex items-center gap-2.5 px-3 py-2.5 text-xs
                                      text-slate-700 dark:text-slate-300
                                      hover:bg-slate-50 dark:hover:bg-slate-700/50
                                      hover:text-blue-600 transition-colors">
                                <i class="bi bi-eye-fill w-4 text-center"></i>
                                Voir le bulletin
                            </a>
                            <a href="{{ route('report-cards.download', $reportCard) }}" target="_blank" class="flex items-center gap-2.5 px-3 py-2.5 text-xs
                                      text-slate-700 dark:text-slate-300
                                      hover:bg-slate-50 dark:hover:bg-slate-700/50
                                      hover:text-emerald-600 transition-colors">
                                <i class="bi bi-download w-4 text-center"></i>
                                Télécharger PDF
                            </a>
                            <button onclick="deleteReportCard({{ $reportCard->id }})" class="flex items-center gap-2.5 w-full px-3 py-2.5 text-xs
                                           text-red-600 dark:text-red-400
                                           hover:bg-red-50 dark:hover:bg-red-900/20
                                           transition-colors focus:outline-none">
                                <i class="bi bi-trash3-fill w-4 text-center"></i>
                                Supprimer
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Corps --}}
                <div class="p-4 space-y-3">

                    {{-- Infos : classe + trimestre --}}
                    <div class="flex flex-wrap gap-1.5">
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg
                                     text-[10px] font-medium
                                     bg-blue-50 dark:bg-blue-900/20
                                     text-blue-700 dark:text-blue-400">
                            <i class="bi bi-building text-[9px]"></i>
                            {{ $reportCard->student->classe->name ?? '—' }}
                        </span>
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg
                                     text-[10px] font-medium
                                     bg-violet-50 dark:bg-violet-900/20
                                     text-violet-700 dark:text-violet-400">
                            <i class="bi bi-calendar-range text-[9px]"></i>
                            {{ $reportCard->term->name ?? '—' }}
                        </span>
                    </div>

                    {{-- Moyenne + Mention --}}
                    <div class="flex items-center justify-between
                                p-3 rounded-xl
                                bg-{{ $avgColor }}-50 dark:bg-{{ $avgColor }}-900/20
                                border border-{{ $avgColor }}-200 dark:border-{{ $avgColor }}-800">
                        <div>
                            <p class="text-2xl font-black
                                      text-{{ $avgColor }}-700 dark:text-{{ $avgColor }}-400">
                                {{ number_format($avg, 2) }}
                                <span class="text-sm font-normal text-{{ $avgColor }}-500">/20</span>
                            </p>
                            <p class="text-[10px] text-{{ $avgColor }}-600 dark:text-{{ $avgColor }}-500
                                      font-medium mt-0.5">
                                Moyenne générale
                            </p>
                        </div>
                        <div class="text-right">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-xl
                                         text-xs font-bold
                                         bg-{{ $mention['color'] }}-100
                                         dark:bg-{{ $mention['color'] }}-900/40
                                         text-{{ $mention['color'] }}-700
                                         dark:text-{{ $mention['color'] }}-400">
                                {{ $mention['label'] }}
                            </span>
                        </div>
                    </div>

                    {{-- Rang --}}
                    @if($reportCard->rank)
                    <div class="flex items-center justify-between text-xs
                                px-3 py-2 rounded-xl
                                bg-slate-50 dark:bg-slate-700/40">
                        <span class="text-slate-500 dark:text-slate-400 flex items-center gap-1.5">
                            <i class="bi bi-trophy-fill text-amber-400"></i>
                            Classement
                        </span>
                        <span class="font-bold text-slate-700 dark:text-slate-200">
                            {{ $reportCard->rank }}
                            <span class="text-slate-400 font-normal">
                                / {{ $reportCard->total_students ?? '—' }}
                            </span>
                        </span>
                    </div>
                    @endif

                    {{-- Actions --}}
                    <div class="grid grid-cols-2 gap-2 pt-1">
                        <a href="{{ route('report-cards.show', $reportCard) }}" class="inline-flex items-center justify-center gap-1.5
                                  py-2 rounded-xl text-xs font-medium
                                  bg-blue-50 dark:bg-blue-900/20
                                  text-blue-600 dark:text-blue-400
                                  hover:bg-blue-100 dark:hover:bg-blue-900/40
                                  transition-all">
                            <i class="bi bi-eye-fill"></i>
                            Voir
                        </a>
                        <a href="{{ route('report-cards.download', $reportCard) }}" target="_blank" class="inline-flex items-center justify-center gap-1.5
                                  py-2 rounded-xl text-xs font-medium
                                  bg-emerald-50 dark:bg-emerald-900/20
                                  text-emerald-600 dark:text-emerald-400
                                  hover:bg-emerald-100 dark:hover:bg-emerald-900/40
                                  transition-all">
                            <i class="bi bi-file-pdf-fill"></i>
                            PDF
                        </a>
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
                                Élève
                            </th>
                            <th class="text-left px-4 py-3.5 text-xs font-semibold
                                       text-slate-500 dark:text-slate-400 uppercase tracking-wider
                                       hidden sm:table-cell">
                                Classe
                            </th>
                            <th class="text-left px-4 py-3.5 text-xs font-semibold
                                       text-slate-500 dark:text-slate-400 uppercase tracking-wider
                                       hidden md:table-cell">
                                Trimestre
                            </th>
                            <th class="text-center px-4 py-3.5 text-xs font-semibold
                                       text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                Moyenne
                            </th>
                            <th class="text-center px-4 py-3.5 text-xs font-semibold
                                       text-slate-500 dark:text-slate-400 uppercase tracking-wider
                                       hidden lg:table-cell">
                                Mention
                            </th>
                            <th class="text-center px-4 py-3.5 text-xs font-semibold
                                       text-slate-500 dark:text-slate-400 uppercase tracking-wider
                                       hidden xl:table-cell">
                                Rang
                            </th>
                            <th class="text-right px-5 py-3.5 text-xs font-semibold
                                       text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody id="report-cards-list" class="divide-y divide-slate-100 dark:divide-slate-700">
                        @forelse($reportCards as $reportCard)
                        @php
                        $avg = $reportCard->average ?? 0;
                        $avgColor = $avg >= 14
                        ? 'emerald' : ($avg >= 10 ? 'blue' : ($avg >= 8 ? 'amber' : 'red'));
                        $mention = match(true) {
                        $avg >= 16 => ['label' => 'Très bien', 'color' => 'emerald'],
                        $avg >= 14 => ['label' => 'Bien', 'color' => 'blue'],
                        $avg >= 12 => ['label' => 'Assez bien', 'color' => 'cyan'],
                        $avg >= 10 => ['label' => 'Passable', 'color' => 'amber'],
                        default => ['label' => 'Insuffisant','color' => 'red'],
                        };
                        @endphp
                        <tr class="group hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors"
                            data-id="{{ $reportCard->id }}">

                            {{-- Élève --}}
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl overflow-hidden shrink-0
                                                ring-2 ring-slate-200 dark:ring-slate-700">
                                        @if($reportCard->student->photo_path)
                                        <img src="{{ asset('storage/' . $reportCard->student->photo_path) }}"
                                            class="w-full h-full object-cover"
                                            alt="{{ $reportCard->student->user->name }}" />
                                        @else
                                        <div class="w-full h-full
                                                        bg-linear-to-br from-blue-400 to-indigo-500
                                                        flex items-center justify-center">
                                            <span class="text-xs font-bold text-white">
                                                {{ strtoupper(substr($reportCard->student->user->name ?? 'E', 0, 1)) }}
                                            </span>
                                        </div>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <a href="{{ route('report-cards.show', $reportCard) }}" class="text-sm font-semibold text-slate-700 dark:text-slate-200
                                                  hover:text-blue-600 dark:hover:text-blue-400
                                                  transition-colors truncate block max-w-35">
                                            {{ $reportCard->student->user->name ?? '—' }}
                                        </a>
                                        <p class="text-[10px] font-mono text-slate-400">
                                            {{ $reportCard->student->matricule ?? '—' }}
                                        </p>
                                    </div>
                                </div>
                            </td>

                            {{-- Classe --}}
                            <td class="px-4 py-3.5 hidden sm:table-cell">
                                <span class="inline-flex items-center gap-1.5 text-xs font-medium
                                             text-slate-600 dark:text-slate-300">
                                    <i class="bi bi-building text-blue-400 text-xs"></i>
                                    {{ $reportCard->student->classe->name ?? '—' }}
                                </span>
                            </td>

                            {{-- Trimestre --}}
                            <td class="px-4 py-3.5 hidden md:table-cell">
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full
                                             text-[10px] font-semibold
                                             bg-violet-100 dark:bg-violet-900/30
                                             text-violet-700 dark:text-violet-400">
                                    {{ $reportCard->term->name ?? '—' }}
                                </span>
                            </td>

                            {{-- Moyenne --}}
                            <td class="px-4 py-3.5 text-center">
                                <span class="text-base font-black
                                             text-{{ $avgColor }}-600 dark:text-{{ $avgColor }}-400">
                                    {{ number_format($avg, 2) }}
                                </span>
                                <span class="text-xs text-slate-400">/20</span>
                            </td>

                            {{-- Mention --}}
                            <td class="px-4 py-3.5 text-center hidden lg:table-cell">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full
                                             text-[10px] font-semibold
                                             bg-{{ $mention['color'] }}-100
                                             dark:bg-{{ $mention['color'] }}-900/30
                                             text-{{ $mention['color'] }}-700
                                             dark:text-{{ $mention['color'] }}-400">
                                    {{ $mention['label'] }}
                                </span>
                            </td>

                            {{-- Rang --}}
                            <td class="px-4 py-3.5 text-center hidden xl:table-cell">
                                @if($reportCard->rank)
                                <span class="inline-flex items-center gap-1 text-sm font-bold
                                             text-slate-700 dark:text-slate-200">
                                    <i class="bi bi-trophy-fill text-amber-400 text-xs"></i>
                                    {{ $reportCard->rank }}
                                    <span class="text-xs font-normal text-slate-400">
                                        /{{ $reportCard->total_students ?? '—' }}
                                    </span>
                                </span>
                                @else
                                <span class="text-slate-400 text-xs">—</span>
                                @endif
                            </td>

                            {{-- Actions --}}
                            <td class="px-5 py-3.5 text-right">
                                <div class="flex items-center justify-end gap-1.5
                                            opacity-0 group-hover:opacity-100 transition-opacity">
                                    <a href="{{ route('report-cards.show', $reportCard) }}" title="Voir" class="w-8 h-8 rounded-lg flex items-center justify-center
                                              bg-blue-50 dark:bg-blue-900/20
                                              text-blue-600 dark:text-blue-400
                                              hover:bg-blue-100 dark:hover:bg-blue-900/40
                                              transition-colors">
                                        <i class="bi bi-eye-fill text-sm"></i>
                                    </a>
                                    <a href="{{ route('report-cards.download', $reportCard) }}" target="_blank"
                                        title="Télécharger PDF" class="w-8 h-8 rounded-lg flex items-center justify-center
                                              bg-emerald-50 dark:bg-emerald-900/20
                                              text-emerald-600 dark:text-emerald-400
                                              hover:bg-emerald-100 dark:hover:bg-emerald-900/40
                                              transition-colors">
                                        <i class="bi bi-file-pdf-fill text-sm"></i>
                                    </a>
                                    <button onclick="deleteReportCard({{ $reportCard->id }})" title="Supprimer" class="w-8 h-8 rounded-lg flex items-center justify-center
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
                                <i class="bi bi-file-earmark-bar-graph text-4xl
                                          text-slate-300 dark:text-slate-600 block mb-3"></i>
                                <p class="text-sm text-slate-500 dark:text-slate-400">
                                    Aucun bulletin trouvé
                                </p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Pagination --}}
    @if($reportCards->hasPages())
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4
                bg-white dark:bg-slate-800 rounded-2xl
                border border-slate-200 dark:border-slate-700
                shadow-sm px-5 py-3.5">
        <p class="text-xs text-slate-500 dark:text-slate-400 order-2 sm:order-1">
            Affichage de
            <span class="font-semibold text-slate-700 dark:text-slate-300">
                {{ $reportCards->firstItem() }}
            </span>
            à
            <span class="font-semibold text-slate-700 dark:text-slate-300">
                {{ $reportCards->lastItem() }}
            </span>
            sur
            <span class="font-semibold text-slate-700 dark:text-slate-300">
                {{ $reportCards->total() }}
            </span>
            bulletins
        </p>
        <div class="flex items-center gap-1 order-1 sm:order-2">
            @if(!$reportCards->onFirstPage())
            <a href="{{ $reportCards->previousPageUrl() }}" class="px-3 py-1.5 rounded-xl text-xs border border-slate-200
                      dark:border-slate-700 text-slate-600 dark:text-slate-400
                      hover:bg-slate-100 dark:hover:bg-slate-700 transition-all">
                <i class="bi bi-chevron-left"></i>
            </a>
            @endif
            @foreach($reportCards->getUrlRange(
            max(1, $reportCards->currentPage() - 2),
            min($reportCards->lastPage(), $reportCards->currentPage() + 2)
            ) as $page => $url)
            @if($page == $reportCards->currentPage())
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
            @if($reportCards->hasMorePages())
            <a href="{{ $reportCards->nextPageUrl() }}" class="px-3 py-1.5 rounded-xl text-xs border border-slate-200
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

    const savedView = localStorage.getItem('report-cards-view') ?? 'grid';
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
    if (save) localStorage.setItem('report-cards-view', view);
}

// ── Générer tous les bulletins ─────────────────────────────────
function generateAll() {
    if (!confirm('Générer tous les bulletins du trimestre en cours ?')) return;

    fetch('{{ route("report-cards.generate-all") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
            },
        })
        .then(r => r.json())
        .then(data => {
            window.showToast({
                type: 'success',
                title: 'Bulletins générés',
                message: `${data.count ?? 0} bulletin(s) généré(s) avec succès.`,
                delay: 5000,
            });
            setTimeout(() => window.location.reload(), 2000);
        })
        .catch(() => {
            window.showToast({
                type: 'error',
                title: 'Erreur',
                message: 'Impossible de générer les bulletins.',
            });
        });
}

function deleteReportCard(id) {
    if (!confirm('Supprimer ce bulletin ? Cette action est irréversible.')) return;
    const form = document.getElementById('delete-form');
    form.action = `/report-cards/${id}`;
    form.submit();
}
</script>
@endpush