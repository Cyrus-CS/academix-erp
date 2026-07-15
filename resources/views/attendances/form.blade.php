@extends('layouts.base')

@section('page_title', 'Saisie des présences')

@section('breadcrumb')
<span class="text-slate-300 dark:text-slate-600 font-light select-none">/</span>
<a href="{{ route('attendance.index') }}"
    class="text-slate-400 dark:text-slate-500 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
    Présences
</a>
@endsection

@section('page_header')
<div class="flex items-center justify-between gap-4">
    <div class="flex items-center gap-3 min-w-0">
        <div class="w-10 h-10 rounded-xl bg-linear-to-br from-blue-600 to-emerald-500
                        flex items-center justify-center shadow-sm shrink-0">
            <i class="bi bi-person-check-fill text-white text-lg"></i>
        </div>
        <div class="min-w-0">
            <h1 class="text-lg font-bold text-slate-800 dark:text-slate-100 truncate">
                Saisie des présences
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400">
                @if($selectedClass && $date)
                {{ $selectedClass->name }} —
                {{ \Carbon\Carbon::parse($date)->translatedFormat('l d F Y') }}
                @else
                Sélectionnez une classe et une date pour commencer
                @endif
            </p>
        </div>
    </div>
    <a href="{{ route('attendance.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-medium
                  border border-slate-200 dark:border-slate-700
                  text-slate-700 dark:text-slate-300
                  hover:bg-slate-50 dark:hover:bg-slate-700/50
                  transition-all duration-200 shrink-0">
        <i class="bi bi-arrow-left"></i>
        <span class="hidden sm:inline">Retour</span>
    </a>
</div>
@endsection

@section('content')
<div class="space-y-6 max-w-4xl mx-auto">

    {{-- ── Sélection classe & date ── --}}
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200
                dark:border-slate-700 shadow-sm overflow-hidden">

        <div class="flex items-center gap-3 px-6 py-4
                    border-b border-slate-100 dark:border-slate-700
                    bg-slate-50/60 dark:bg-slate-700/30">
            <div class="w-7 h-7 rounded-lg bg-blue-100 dark:bg-blue-900/40
                        flex items-center justify-center">
                <i class="bi bi-filter-circle-fill text-blue-600 dark:text-blue-400 text-sm"></i>
            </div>
            <h2 class="text-sm font-semibold text-slate-700 dark:text-slate-200">
                Sélection classe & date
            </h2>
        </div>

        <div class="p-6">
            <form method="GET" action="{{ route('attendance.create') }}" class="flex flex-col sm:flex-row gap-4">
                <div class="flex-1 relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
                        <i class="bi bi-building text-slate-400"></i>
                    </span>
                    <select name="class_id" class="w-full pl-10 pr-9 py-2.5 rounded-xl border text-sm
                                   text-slate-800 dark:text-slate-100
                                   bg-white dark:bg-slate-700/50 appearance-none
                                   focus:outline-none focus:ring-2 transition-all duration-200
                                   border-slate-200 dark:border-slate-600
                                   focus:ring-blue-500/30 focus:border-blue-500">
                        <option value="">Sélectionner une classe…</option>
                        @foreach($classes as $class)
                        <option value="{{ $class->id }}" {{ $selectedClass?->id == $class->id ? 'selected' : '' }}>
                            {{ $class->name }}
                        </option>
                        @endforeach
                    </select>
                    <span class="absolute inset-y-0 right-0 flex items-center pr-3.5 pointer-events-none">
                        <i class="bi bi-chevron-down text-slate-400 text-xs"></i>
                    </span>
                </div>

                <div class="flex-1 relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none z-10">
                        <i class="bi bi-calendar-event text-slate-400"></i>
                    </span>
                    <input type="text" name="date" id="date-picker" value="{{ $date }}" placeholder="Date" class="w-full pl-10 pr-3.5 py-2.5 rounded-xl border text-sm
                                  text-slate-800 dark:text-slate-100
                                  bg-white dark:bg-slate-700/50 placeholder-slate-400
                                  focus:outline-none focus:ring-2 transition-all duration-200
                                  border-slate-200 dark:border-slate-600
                                  focus:ring-blue-500/30 focus:border-blue-500">
                </div>

                <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-medium
                               bg-blue-600 hover:bg-blue-700 text-white
                               transition-all duration-200 shrink-0">
                    <i class="bi bi-search"></i>
                    Charger
                </button>
            </form>
        </div>
    </div>

    {{-- ── Liste étudiants ── --}}
    @if($selectedClass && $selectedClass->students->isNotEmpty())
    <form method="POST" action="{{ route('attendance.store') }}">
        @csrf
        <input type="hidden" name="class_id" value="{{ $selectedClass->id }}">
        <input type="hidden" name="date" value="{{ $date }}">

        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200
                    dark:border-slate-700 shadow-sm overflow-hidden">

            {{-- Header --}}
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3
                        px-6 py-4 border-b border-slate-100 dark:border-slate-700
                        bg-slate-50/60 dark:bg-slate-700/30">
                <div class="flex items-center gap-3">
                    <div class="w-7 h-7 rounded-lg bg-emerald-100 dark:bg-emerald-900/40
                                flex items-center justify-center">
                        <i class="bi bi-people-fill text-emerald-600 dark:text-emerald-400 text-sm"></i>
                    </div>
                    <h2 class="text-sm font-semibold text-slate-700 dark:text-slate-200">
                        {{ $selectedClass->name }}
                        <span class="text-slate-400 font-normal">
                            ({{ $selectedClass->students->count() }} étudiants)
                        </span>
                    </h2>
                </div>

                {{-- Actions rapides --}}
                <div class="flex items-center gap-2">
                    <button type="button" id="btn-all-present" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium
                                   bg-emerald-100 dark:bg-emerald-900/30
                                   text-emerald-700 dark:text-emerald-400
                                   hover:bg-emerald-200 dark:hover:bg-emerald-900/50
                                   transition-all duration-200">
                        <i class="bi bi-check-all"></i>
                        Tous présents
                    </button>
                    <button type="button" id="btn-all-absent" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium
                                   bg-red-100 dark:bg-red-900/30
                                   text-red-700 dark:text-red-400
                                   hover:bg-red-200 dark:hover:bg-red-900/50
                                   transition-all duration-200">
                        <i class="bi bi-x-lg"></i>
                        Tous absents
                    </button>
                </div>
            </div>

            {{-- Compteur stats live --}}
            <div class="grid grid-cols-3 gap-0 border-b border-slate-100 dark:border-slate-700">
                @foreach([
                ['id' => 'count-present', 'label' => 'Présents', 'color' => 'emerald', 'icon' =>
                'bi-check-circle-fill'],
                ['id' => 'count-absent', 'label' => 'Absents', 'color' => 'red', 'icon' => 'bi-x-circle-fill'],
                ['id' => 'count-late', 'label' => 'En retard', 'color' => 'amber', 'icon' => 'bi-clock-fill'],
                ] as $stat)
                <div class="flex flex-col items-center justify-center py-3
                            border-r border-slate-100 dark:border-slate-700 last:border-0">
                    <div class="flex items-center gap-1.5">
                        <i class="bi {{ $stat['icon'] }} text-{{ $stat['color'] }}-500 text-sm"></i>
                        <span id="{{ $stat['id'] }}"
                            class="text-xl font-bold text-slate-800 dark:text-slate-100">0</span>
                    </div>
                    <p class="text-xs text-slate-400 dark:text-slate-500">{{ $stat['label'] }}</p>
                </div>
                @endforeach
            </div>

            {{-- Liste --}}
            <div class="divide-y divide-slate-100 dark:divide-slate-700">
                @foreach($selectedClass->students->sortBy('user.name') as $index => $student)
                @php
                $ex = $existing[$student->id] ?? null;
                $currentStatus = $ex['status'] ?? 'present';
                @endphp
                <div class="student-row flex flex-col sm:flex-row sm:items-center gap-3
                            px-5 py-4 hover:bg-slate-50 dark:hover:bg-slate-700/30
                            transition-colors duration-200" data-status="{{ $currentStatus }}">

                    <input type="hidden" name="attendances[{{ $index }}][student_id]" value="{{ $student->id }}">

                    {{-- Avatar + Nom --}}
                    <div class="flex items-center gap-3 flex-1 min-w-0">
                        <div class="w-9 h-9 rounded-full shrink-0
                                    bg-linear-to-br from-blue-500 to-emerald-500
                                    flex items-center justify-center text-white text-xs font-bold">
                            {{ strtoupper(substr($student->user->name, 0, 1)) }}
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-slate-800 dark:text-slate-100 truncate">
                                {{ $student->user->name }}
                            </p>
                            <p class="text-xs text-slate-400 dark:text-slate-500">
                                {{ $student->student_number }}
                            </p>
                        </div>
                    </div>

                    {{-- Status radios --}}
                    <div class="flex items-center gap-2 shrink-0">
                        @foreach([
                        ['value' => 'present', 'label' => 'Présent', 'color' => 'emerald', 'icon' => 'bi-check-lg'],
                        ['value' => 'absent', 'label' => 'Absent', 'color' => 'red', 'icon' => 'bi-x-lg'],
                        ['value' => 'late', 'label' => 'En retard', 'color' => 'amber', 'icon' => 'bi-clock'],
                        ] as $status)
                        @php $isChecked = $currentStatus === $status['value']; @endphp
                        <label
                            class="status-btn inline-flex items-center gap-1.5 px-3 py-1.5
                                      rounded-lg border-2 cursor-pointer text-xs font-semibold
                                      transition-all duration-200
                                      {{ $isChecked
                                          ? 'border-' . $status['color'] . '-500 bg-' . $status['color'] . '-100 dark:bg-' . $status['color'] . '-900/30 text-' . $status['color'] . '-700 dark:text-' . $status['color'] . '-400'
                                          : 'border-slate-200 dark:border-slate-600 text-slate-500 dark:text-slate-400 hover:border-slate-300' }}">
                            <input type="radio" name="attendances[{{ $index }}][status]" value="{{ $status['value'] }}"
                                {{ $isChecked ? 'checked' : '' }} class="sr-only status-radio">
                            <i class="bi {{ $status['icon'] }}"></i>
                            <span class="hidden sm:inline">{{ $status['label'] }}</span>
                        </label>
                        @endforeach
                    </div>

                    {{-- Note --}}
                    <input type="text" name="attendances[{{ $index }}][note]" value="{{ $ex['note'] ?? '' }}"
                        placeholder="Note…" class="w-full sm:w-36 px-3 py-1.5 rounded-lg border text-xs
                                  text-slate-800 dark:text-slate-100
                                  bg-white dark:bg-slate-700/50 placeholder-slate-400
                                  focus:outline-none focus:ring-2 transition-all duration-200
                                  border-slate-200 dark:border-slate-600
                                  focus:ring-blue-500/30 focus:border-blue-500">
                </div>
                @endforeach
            </div>

            {{-- Footer actions --}}
            <div class="flex items-center justify-between gap-3 px-5 py-4
                        border-t border-slate-100 dark:border-slate-700
                        bg-slate-50/60 dark:bg-slate-700/30">
                <a href="{{ route('attendance.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-medium
                          border border-slate-200 dark:border-slate-700
                          text-slate-700 dark:text-slate-300
                          hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-all duration-200">
                    <i class="bi bi-x-lg"></i>
                    Annuler
                </a>
                <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl text-sm font-semibold
                               bg-blue-600 hover:bg-blue-700 text-white
                               shadow-sm hover:shadow-md transition-all duration-200
                               focus:outline-none focus:ring-2 focus:ring-blue-500/40">
                    <i class="bi bi-save-fill"></i>
                    Enregistrer les présences
                </button>
            </div>
        </div>
    </form>

    @elseif($selectedClass && $selectedClass->students->isEmpty())
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200
                dark:border-slate-700 shadow-sm p-12 text-center">
        <div class="w-16 h-16 rounded-2xl bg-slate-100 dark:bg-slate-700/50
                    flex items-center justify-center mx-auto mb-4">
            <i class="bi bi-people text-3xl text-slate-300 dark:text-slate-600"></i>
        </div>
        <p class="text-sm font-semibold text-slate-600 dark:text-slate-400">
            Aucun étudiant dans cette classe
        </p>
    </div>

    @elseif(!$selectedClass)
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200
                dark:border-slate-700 shadow-sm p-12 text-center">
        <div class="w-16 h-16 rounded-2xl bg-blue-50 dark:bg-blue-950/30
                    flex items-center justify-center mx-auto mb-4">
            <i class="bi bi-arrow-up-circle text-3xl text-blue-400"></i>
        </div>
        <p class="text-sm font-semibold text-slate-600 dark:text-slate-400">
            Sélectionnez une classe et une date pour commencer la saisie
        </p>
    </div>
    @endif

</div>
@endsection

@push('scripts')
<script>
(() => {
    // ── Flatpickr ─────────────────────────────────────────────────
    flatpickr('#date-picker', {
        dateFormat: 'Y-m-d',
        altInput: true,
        altFormat: 'd/m/Y',
        locale: 'fr',
        maxDate: 'today',
        defaultDate: '{{ $date }}',
    });

    // ── Compteurs live ────────────────────────────────────────────
    function updateCounts() {
        let present = 0,
            absent = 0,
            late = 0;

        document.querySelectorAll('.student-row').forEach(row => {
            const checked = row.querySelector('.status-radio:checked');
            if (!checked) return;
            if (checked.value === 'present') present++;
            else if (checked.value === 'absent') absent++;
            else if (checked.value === 'late') late++;
        });

        document.getElementById('count-present').textContent = present;
        document.getElementById('count-absent').textContent = absent;
        document.getElementById('count-late').textContent = late;
    }

    // ── Styling status buttons ────────────────────────────────────
    const statusClasses = {
        present: 'border-emerald-500 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400',
        absent: 'border-red-500 bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400',
        late: 'border-amber-500 bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400',
    };

    const defaultClass = 'border-slate-200 dark:border-slate-600 text-slate-500 dark:text-slate-400';

    document.querySelectorAll('.student-row').forEach(row => {
        const labels = row.querySelectorAll('.status-btn');

        labels.forEach(label => {
            label.addEventListener('click', () => {
                const radio = label.querySelector('.status-radio');
                const val = radio?.value;

                // Reset tous dans cette ligne
                labels.forEach(l => {
                    l.className = l.className
                        .replace(/border-\w+-500/g, '')
                        .replace(/bg-\w+-100|dark:bg-\w+-900\/30/g, '')
                        .replace(/text-\w+-\d+/g, '');
                    l.classList.add(...defaultClass.split(' '));
                });

                // Activer le sélectionné
                label.classList.remove(...defaultClass.split(' '));
                label.classList.add(...(statusClasses[val] ?? '').split(' '));

                updateCounts();
            });
        });
    });

    // ── Tout présents / tout absents ─────────────────────────────
    function setAll(status) {
        document.querySelectorAll('.student-row').forEach(row => {
            const radio = row.querySelector(`.status-radio[value="${status}"]`);
            if (radio) {
                radio.checked = true;
                const labels = row.querySelectorAll('.status-btn');
                labels.forEach(l => {
                    l.className = l.className
                        .replace(/border-\w+-500/g, '')
                        .replace(/bg-\w+-100|dark:bg-\w+-900\/30/g, '')
                        .replace(/text-\w+-\d+/g, '');
                    l.classList.add(...defaultClass.split(' '));
                });
                const activeLabel = row.querySelector(`.status-btn:has(.status-radio[value="${status}"])`);
                if (activeLabel) {
                    activeLabel.classList.remove(...defaultClass.split(' '));
                    activeLabel.classList.add(...(statusClasses[status] ?? '').split(' '));
                }
            }
        });
        updateCounts();
    }

    document.getElementById('btn-all-present')?.addEventListener('click', () => setAll('present'));
    document.getElementById('btn-all-absent')?.addEventListener('click', () => setAll('absent'));

    // Init compteurs
    updateCounts();
})();
</script>
@endpush