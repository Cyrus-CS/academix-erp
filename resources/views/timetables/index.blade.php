@extends('layouts.base')

@section('title', 'Emplois du temps')
@section('page_title', 'Emplois du temps')

@section('breadcrumb')
<span class="text-slate-300 dark:text-slate-600">/</span>
<span class="font-semibold text-slate-700 dark:text-slate-200">Emplois du temps</span>
@endsection

@section('content')
<div class="space-y-5">

    {{-- ══════════════════════════════════════════════════════════
         EN-TÊTE
    ══════════════════════════════════════════════════════════ --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-800 dark:text-slate-100">
                Emplois du temps
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                Année
                <span class="font-semibold text-blue-600 dark:text-blue-400">
                    {{ $activeYear?->name ?? '—' }}
                </span>
                @if($selectedClass)
                · Classe :
                <span class="font-semibold text-emerald-600 dark:text-emerald-400">
                    {{ $selectedClass->name }}
                </span>
                @endif
            </p>
        </div>

        <div class="flex items-center gap-2 flex-wrap self-start sm:self-auto">

            {{-- Toggle vue --}}
            <div class="flex items-center rounded-xl border border-slate-200 dark:border-slate-700
                        bg-white dark:bg-slate-800 overflow-hidden">
                <button id="btn-grid" onclick="setView('grid')"
                    class="px-3 py-2 text-sm transition-all bg-blue-600 text-white" title="Vue grille">
                    <i class="bi bi-table"></i>
                </button>
                <button id="btn-list" onclick="setView('list')" class="px-3 py-2 text-sm transition-all
                               text-slate-500 dark:text-slate-400
                               hover:bg-slate-100 dark:hover:bg-slate-700" title="Vue liste">
                    <i class="bi bi-list-ul"></i>
                </button>
            </div>

            {{-- Imprimer --}}
            <button onclick="window.print()" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-medium
                           border border-slate-200 dark:border-slate-700
                           text-slate-600 dark:text-slate-400
                           hover:bg-slate-100 dark:hover:bg-slate-800 transition-all">
                <i class="bi bi-printer text-slate-400"></i>
                <span class="hidden sm:inline">Imprimer</span>
            </button>

            {{-- Nouveau créneau --}}
            @can('view timetables')
            <a href="{{ route('timetables.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold
                      bg-blue-600 hover:bg-blue-700 text-white
                      transition-all shadow-sm shadow-blue-500/30">
                <i class="bi bi-plus-lg"></i>
                <span class="hidden sm:inline">Nouveau créneau</span>
                <span class="sm:hidden">Nouveau</span>
            </a>
            @endcan
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════
         FILTRE CLASSE
    ══════════════════════════════════════════════════════════ --}}
    <div class="bg-white dark:bg-slate-800 rounded-2xl
                border border-slate-200 dark:border-slate-700 shadow-sm p-4">
        <form method="GET" action="{{ route('timetables.index') }}" class="flex flex-col sm:flex-row gap-3 items-end">

            <div class="flex-1 min-w-0">
                <label class="text-xs font-medium text-slate-500 dark:text-slate-400 mb-1.5 block">
                    Sélectionner une classe
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <i class="bi bi-building text-slate-400 text-sm"></i>
                    </span>
                    <select name="class_id" onchange="this.form.submit()" class="w-full pl-9 pr-9 py-2.5 text-sm rounded-xl appearance-none
                                   border border-slate-200 dark:border-slate-700
                                   bg-slate-50 dark:bg-slate-900/50
                                   text-slate-800 dark:text-slate-100
                                   focus:outline-none focus:ring-2
                                   focus:ring-blue-600/40 focus:border-blue-600 transition">
                        <option value="">— Toutes les classes —</option>
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

            {{-- Raccourcis classes --}}
            <div class="flex flex-wrap gap-1.5">
                <a href="{{ route('timetables.index') }}"
                    class="px-3 py-2 rounded-xl text-xs font-medium transition-all
                          {{ !request('class_id')
                            ? 'bg-blue-600 text-white shadow-sm shadow-blue-500/20'
                            : 'border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700' }}">
                    Toutes
                </a>
                @foreach($classes->take(6) as $class)
                <a href="{{ route('timetables.index', ['class_id' => $class->id]) }}"
                    class="px-3 py-2 rounded-xl text-xs font-medium transition-all
                          {{ request('class_id') == $class->id
                            ? 'bg-blue-600 text-white shadow-sm shadow-blue-500/20'
                            : 'border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700' }}">
                    {{ $class->name }}
                </a>
                @endforeach
            </div>
        </form>
    </div>

    {{-- ══════════════════════════════════════════════════════════
         VUE GRILLE — Tableau par jour
    ══════════════════════════════════════════════════════════ --}}
    <div id="view-grid">

        @if($schedules->isEmpty())
        <div class="bg-white dark:bg-slate-800 rounded-2xl
                    border border-slate-200 dark:border-slate-700
                    shadow-sm flex flex-col items-center justify-center py-20 px-4">
            <div class="w-16 h-16 rounded-2xl bg-slate-100 dark:bg-slate-700
                        flex items-center justify-center mb-4">
                <i class="bi bi-clock text-3xl text-slate-300 dark:text-slate-500"></i>
            </div>
            <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1">
                Aucun créneau configuré
            </h3>
            <p class="text-xs text-slate-400 dark:text-slate-500 text-center mb-5">
                {{ $selectedClass
                    ? 'Aucun créneau pour cette classe.'
                    : 'Commencez par créer un emploi du temps.' }}
            </p>
            @can('view timetables')
            <a href="{{ route('timetables.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold
                      bg-blue-600 hover:bg-blue-700 text-white
                      transition-all shadow-sm shadow-blue-500/30">
                <i class="bi bi-plus-lg"></i>
                Créer un créneau
            </a>
            @endcan
        </div>

        @else

        {{-- Si une classe est sélectionnée : vue tableau semaine --}}
        @if($selectedClass)

        {{-- Légende matières --}}
        @php
        $subjectColors = [
        'blue', 'emerald', 'violet', 'amber', 'cyan',
        'pink', 'indigo', 'orange', 'teal', 'rose',
        ];
        $classSchedules = $schedules->get($selectedClass->id, collect());
        $uniqueSubjects = $classSchedules->pluck('subject')->unique('id')->values();
        $subjectColorMap = [];
        foreach ($uniqueSubjects as $i => $subject) {
        $subjectColorMap[$subject->id] = $subjectColors[$i % count($subjectColors)];
        }
        @endphp

        @if($uniqueSubjects->isNotEmpty())
        <div class="flex flex-wrap gap-2">
            @foreach($uniqueSubjects as $subject)
            @php $color = $subjectColorMap[$subject->id]; @endphp
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-xs font-medium
                         bg-{{ $color }}-100 dark:bg-{{ $color }}-900/30
                         text-{{ $color }}-700 dark:text-{{ $color }}-400
                         border border-{{ $color }}-200 dark:border-{{ $color }}-800">
                <span class="w-2 h-2 rounded-full bg-{{ $color }}-500"></span>
                {{ $subject->name }}
            </span>
            @endforeach
        </div>
        @endif

        {{-- Tableau semaine --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl
                    border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">

            <div class="flex items-center justify-between px-5 py-4
                        border-b border-slate-100 dark:border-slate-700">
                <div>
                    <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-100">
                        {{ $selectedClass->name }}
                    </h3>
                    <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">
                        {{ $classSchedules->count() }} créneau(x) par semaine
                    </p>
                </div>
                @can('view timetables')
                <a href="{{ route('timetables.create', ['class_id' => $selectedClass->id]) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-medium
                          bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400
                          hover:bg-blue-100 dark:hover:bg-blue-900/40 transition-all">
                    <i class="bi bi-plus-lg"></i>
                    Ajouter un créneau
                </a>
                @endcan
            </div>

            {{-- Grille jours --}}
            <div class="overflow-x-auto">
                <div class="min-w-160 p-4">
                    <div class="grid grid-cols-6 gap-3">
                        @foreach($days as $day)
                        @php
                        $daySlots = $classSchedules
                        ->filter(fn($s) => strtolower($s->day_of_week) === strtolower($day))
                        ->sortBy('start_time');
                        $isToday = strtolower(now()->locale('fr')->dayName) === strtolower($day);
                        @endphp
                        <div class="min-h-50">
                            {{-- Header jour --}}
                            <div class="text-center mb-2 py-2 rounded-xl text-xs font-semibold uppercase
                                        tracking-wide
                                        {{ $isToday
                                            ? 'bg-blue-600 text-white shadow-sm shadow-blue-500/30'
                                            : 'bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400' }}">
                                {{ $day }}
                                @if($isToday)
                                <span class="block text-[10px] font-normal opacity-80 normal-case tracking-normal">
                                    Aujourd'hui
                                </span>
                                @endif
                            </div>

                            {{-- Créneaux du jour (SortableJS) --}}
                            <div class="day-column space-y-2 min-h-37.5 p-1 rounded-xl
                                        border-2 border-dashed border-transparent
                                        transition-colors" data-day="{{ strtolower($day) }}"
                                data-class="{{ $selectedClass->id }}">

                                @forelse($daySlots as $slot)
                                @php $color = $subjectColorMap[$slot->subject_id] ?? 'slate'; @endphp
                                <div class="schedule-slot group relative rounded-xl p-2.5
                                            bg-{{ $color }}-50 dark:bg-{{ $color }}-900/20
                                            border border-{{ $color }}-200 dark:border-{{ $color }}-800
                                            cursor-grab active:cursor-grabbing
                                            hover:shadow-md transition-all duration-200" data-id="{{ $slot->id }}">

                                    {{-- Heure --}}
                                    <div class="flex items-center justify-between mb-1.5">
                                        <span class="text-[10px] font-semibold
                                                     text-{{ $color }}-600 dark:text-{{ $color }}-400
                                                     bg-{{ $color }}-100 dark:bg-{{ $color }}-900/40
                                                     px-1.5 py-0.5 rounded-lg">
                                            {{ \Carbon\Carbon::parse($slot->start_time)->format('H:i') }}
                                            –
                                            {{ \Carbon\Carbon::parse($slot->end_time)->format('H:i') }}
                                        </span>

                                        {{-- Actions slot --}}
                                        <div class="flex items-center gap-1
                                                    opacity-0 group-hover:opacity-100
                                                    transition-opacity">
                                            <a href="{{ route('timetables.edit', $slot) }}" class="w-5 h-5 rounded flex items-center justify-center
                                                      text-slate-400 hover:text-blue-600
                                                      transition-colors" title="Modifier">
                                                <i class="bi bi-pencil-fill text-[10px]"></i>
                                            </a>
                                            <button onclick="deleteSlot({{ $slot->id }})" class="w-5 h-5 rounded flex items-center justify-center
                                                           text-slate-400 hover:text-red-500
                                                           transition-colors focus:outline-none" title="Supprimer">
                                                <i class="bi bi-trash3-fill text-[10px]"></i>
                                            </button>
                                        </div>
                                    </div>

                                    {{-- Matière --}}
                                    <p class="text-xs font-bold text-slate-800 dark:text-slate-100
                                              truncate leading-tight">
                                        {{ $slot->subject->name }}
                                    </p>

                                    {{-- Enseignant --}}
                                    <div class="flex items-center gap-1.5 mt-1">
                                        <div class="w-4 h-4 rounded-full bg-{{ $color }}-200
                                                    dark:bg-{{ $color }}-800
                                                    flex items-center justify-center shrink-0">
                                            <span class="text-[8px] font-bold
                                                         text-{{ $color }}-700 dark:text-{{ $color }}-300">
                                                {{ strtoupper(substr($slot->teacher->user->name ?? 'P', 0, 1)) }}
                                            </span>
                                        </div>
                                        <span class="text-[10px] text-slate-500 dark:text-slate-400 truncate">
                                            {{ $slot->teacher->user->name ?? '—' }}
                                        </span>
                                    </div>
                                </div>
                                @empty
                                <div class="flex items-center justify-center h-20
                                            text-slate-300 dark:text-slate-600 text-xs">
                                    Libre
                                </div>
                                @endforelse
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        @else
        {{-- Vue toutes les classes --}}
        <div id="timetable-classes-grid" class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            @foreach($schedules as $classId => $classSlots)
            @php
            $class = $classes->firstWhere('id', $classId);
            if (!$class) continue;
            $slotsByDay = $classSlots->groupBy(fn($s) => strtolower($s->day_of_week));
            @endphp
            <div class="timetable-class-card bg-white dark:bg-slate-800 rounded-2xl
                        border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden
                        cursor-grab active:cursor-grabbing" data-id="{{ $classId }}">

                {{-- Header --}}
                <div class="flex items-center justify-between px-5 py-3.5
                            border-b border-slate-100 dark:border-slate-700
                            bg-linear-to-r from-slate-50 to-white
                            dark:from-slate-700/30 dark:to-slate-800">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-blue-100 dark:bg-blue-900/30
                                    flex items-center justify-center shrink-0">
                            <i class="bi bi-building text-blue-600 dark:text-blue-400 text-base"></i>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-slate-800 dark:text-slate-100">
                                {{ $class->name }}
                            </h4>
                            <p class="text-[10px] text-slate-400 dark:text-slate-500">
                                {{ $classSlots->count() }} créneau(x)
                            </p>
                        </div>
                    </div>
                    <a href="{{ route('timetables.index', ['class_id' => $classId]) }}" class="text-xs text-blue-600 dark:text-blue-400
                              hover:underline font-medium">
                        Voir détail
                    </a>
                </div>

                {{-- Résumé par jour --}}
                <div class="p-4">
                    <div class="grid grid-cols-3 sm:grid-cols-6 gap-2">
                        @foreach($days as $day)
                        @php
                        $dayKey = strtolower($day);
                        $slots = $slotsByDay->get($dayKey, collect());
                        $isToday = strtolower(now()->locale('fr')->dayName) === $dayKey;
                        @endphp
                        <div class="text-center">
                            <div class="text-[10px] font-semibold uppercase tracking-wide mb-1.5
                                        {{ $isToday
                                            ? 'text-blue-600 dark:text-blue-400'
                                            : 'text-slate-400 dark:text-slate-500' }}">
                                {{ substr($day, 0, 3) }}
                            </div>
                            <div class="space-y-1">
                                @forelse($slots->take(3) as $slot)
                                <div class="w-full py-1 px-1.5 rounded-lg text-[9px] font-medium
                                            bg-blue-50 dark:bg-blue-900/20
                                            text-blue-700 dark:text-blue-400
                                            truncate leading-tight">
                                    {{ $slot->subject->name }}
                                </div>
                                @empty
                                <div class="w-full py-1.5 rounded-lg
                                            bg-slate-50 dark:bg-slate-700/40
                                            text-[10px] text-slate-300 dark:text-slate-600
                                            text-center">
                                    —
                                </div>
                                @endforelse
                                @if($slots->count() > 3)
                                <div class="text-[9px] text-slate-400 dark:text-slate-500 text-center">
                                    +{{ $slots->count() - 3 }}
                                </div>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif

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
                            <th class="text-left px-4 py-3.5 text-xs font-semibold
                                       text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                Jour
                            </th>
                            <th class="text-left px-4 py-3.5 text-xs font-semibold
                                       text-slate-500 dark:text-slate-400 uppercase tracking-wider
                                       hidden sm:table-cell">
                                Horaire
                            </th>
                            <th class="text-left px-4 py-3.5 text-xs font-semibold
                                       text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                Matière
                            </th>
                            <th class="text-left px-4 py-3.5 text-xs font-semibold
                                       text-slate-500 dark:text-slate-400 uppercase tracking-wider
                                       hidden md:table-cell">
                                Enseignant
                            </th>
                            <th class="text-right px-5 py-3.5 text-xs font-semibold
                                       text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody id="timetable-list" class="divide-y divide-slate-100 dark:divide-slate-700">
                        @forelse($schedules->flatten() as $slot)
                        @php
                        $isToday = strtolower(now()->locale('fr')->dayName)
                        === strtolower($slot->day_of_week);
                        @endphp
                        <tr class="group hover:bg-slate-50 dark:hover:bg-slate-700/50
                                   transition-colors {{ $isToday ? 'bg-blue-50/50 dark:bg-blue-950/20' : '' }}"
                            data-id="{{ $slot->id }}">

                            {{-- Classe --}}
                            <td class="px-5 py-3">
                                <span class="inline-flex items-center gap-1.5 text-xs font-semibold
                                             text-slate-700 dark:text-slate-200">
                                    <i class="bi bi-building text-blue-400 text-xs"></i>
                                    {{ $slot->schoolClass->name ?? '—' }}
                                </span>
                            </td>

                            {{-- Jour --}}
                            <td class="px-4 py-3">
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full
                                             text-[10px] font-semibold
                                             {{ $isToday
                                                ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400'
                                                : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300' }}">
                                    {{ ucfirst($slot->day_of_week) }}
                                    @if($isToday)
                                    <span class="ml-1 w-1.5 h-1.5 rounded-full
                                                 bg-blue-500 animate-pulse"></span>
                                    @endif
                                </span>
                            </td>

                            {{-- Horaire --}}
                            <td class="px-4 py-3 hidden sm:table-cell">
                                <span class="text-xs font-mono font-medium
                                             text-slate-600 dark:text-slate-300">
                                    {{ \Carbon\Carbon::parse($slot->start_time)->format('H:i') }}
                                    –
                                    {{ \Carbon\Carbon::parse($slot->end_time)->format('H:i') }}
                                </span>
                                <p class="text-[10px] text-slate-400 mt-0.5">
                                    {{ \Carbon\Carbon::parse($slot->start_time)
                                        ->diffInMinutes(\Carbon\Carbon::parse($slot->end_time)) }} min
                                </p>
                            </td>

                            {{-- Matière --}}
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center gap-1.5 text-xs font-semibold
                                             text-slate-700 dark:text-slate-200">
                                    <span class="w-2 h-2 rounded-full bg-blue-500 shrink-0"></span>
                                    {{ $slot->subject->name ?? '—' }}
                                </span>
                            </td>

                            {{-- Enseignant --}}
                            <td class="px-4 py-3 hidden md:table-cell">
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-lg bg-emerald-100 dark:bg-emerald-900/30
                                                flex items-center justify-center shrink-0">
                                        <span class="text-[9px] font-bold
                                                     text-emerald-700 dark:text-emerald-400">
                                            {{ strtoupper(substr($slot->teacher->user->name ?? 'P', 0, 1)) }}
                                        </span>
                                    </div>
                                    <span class="text-xs text-slate-600 dark:text-slate-300 truncate max-w-30">
                                        {{ $slot->teacher->user->name ?? '—' }}
                                    </span>
                                </div>
                            </td>

                            {{-- Actions --}}
                            <td class="px-5 py-3 text-right">
                                <div class="flex items-center justify-end gap-1.5
                                            opacity-0 group-hover:opacity-100 transition-opacity">
                                    <a href="{{ route('timetables.edit', $slot) }}" class="w-8 h-8 rounded-lg flex items-center justify-center
                                              bg-slate-100 dark:bg-slate-700
                                              text-slate-600 dark:text-slate-300
                                              hover:bg-slate-200 dark:hover:bg-slate-600
                                              transition-colors">
                                        <i class="bi bi-pencil-fill text-sm"></i>
                                    </a>
                                    <button onclick="deleteSlot({{ $slot->id }})" class="w-8 h-8 rounded-lg flex items-center justify-center
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
                                <i class="bi bi-clock text-4xl text-slate-300 dark:text-slate-600 block mb-3"></i>
                                <p class="text-sm text-slate-500 dark:text-slate-400">
                                    Aucun créneau configuré
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

    const savedView = localStorage.getItem('timetable-view') ?? 'grid';
    setView(savedView, false);

    // ── SortableJS — Colonnes jours (vue grille classe) ────────
    document.querySelectorAll('.day-column').forEach(col => {
        if (typeof Sortable === 'undefined') return;

        Sortable.create(col, {
            group: 'timetable-slots',
            animation: 200,
            ghostClass: 'opacity-40',
            chosenClass: 'ring-2 ring-blue-400 shadow-lg scale-[1.02]',
            dragClass: 'shadow-xl rotate-1',
            delay: 80,
            delayOnTouchOnly: true,

            // Highlight drop zone
            onDragenter(evt) {
                evt.to.classList.add(
                    'border-blue-400', 'dark:border-blue-600',
                    'bg-blue-50', 'dark:bg-blue-950/20'
                );
            },
            onDragleave(evt) {
                evt.to.classList.remove(
                    'border-blue-400', 'dark:border-blue-600',
                    'bg-blue-50', 'dark:bg-blue-950/20'
                );
            },

            onEnd(evt) {
                evt.to.classList.remove(
                    'border-blue-400', 'dark:border-blue-600',
                    'bg-blue-50', 'dark:bg-blue-950/20'
                );

                const slotId = evt.item.dataset.id;
                const newDay = evt.to.dataset.day;
                const classId = evt.to.dataset.class;

                // Persister le changement de jour via AJAX
                fetch(`/timetables/${slotId}/move`, {
                        method: 'PATCH',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector(
                                'meta[name="csrf-token"]').content,
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            day_of_week: newDay,
                            class_id: classId
                        }),
                    })
                    .then(r => r.json())
                    .then(() => {
                        window.showToast({
                            type: 'success',
                            title: 'Créneau déplacé',
                            message: `Déplacé vers ${newDay.charAt(0).toUpperCase() + newDay.slice(1)}.`,
                            delay: 3000,
                        });
                    })
                    .catch(() => {
                        window.showToast({
                            type: 'error',
                            title: 'Erreur',
                            message: 'Impossible de déplacer le créneau.',
                        });
                    });
            }
        });
    });

    // ── SortableJS — Cards classes (vue toutes classes) ────────
    const classesGrid = document.getElementById('timetable-classes-grid');
    if (classesGrid && typeof Sortable !== 'undefined') {
        Sortable.create(classesGrid, {
            animation: 200,
            ghostClass: 'opacity-40',
            chosenClass: 'ring-2 ring-blue-400 shadow-lg',
            delay: 80,
            delayOnTouchOnly: true,
        });
    }

    // ── SortableJS — Vue liste ─────────────────────────────────
    const list = document.getElementById('timetable-list');
    if (list && typeof Sortable !== 'undefined') {
        Sortable.create(list, {
            animation: 150,
            ghostClass: 'opacity-40 bg-blue-50 dark:bg-blue-950/30',
            delay: 80,
            delayOnTouchOnly: true,
        });
    }
});

// ── Toggle vue ─────────────────────────────────────────────────
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

    if (save) localStorage.setItem('timetable-view', view);
}

// ── Suppression créneau ────────────────────────────────────────
function deleteSlot(id) {
    if (!confirm('Supprimer ce créneau ? Cette action est irréversible.')) return;
    const form = document.getElementById('delete-form');
    form.action = `/timetables/${id}`;
    form.submit();
}
</script>
@endpush