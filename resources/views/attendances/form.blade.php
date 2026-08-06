@extends('layouts.base')

@section('page_title', 'Saisie des présences')

@section('breadcrumb')
<span class="text-slate-300 dark:text-slate-600 font-light select-none">/</span>
<a href="{{ route('attendance.index') }}"
    class="text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
    Présences
</a>
<span class="text-slate-300 dark:text-slate-600 font-light select-none">/</span>
<span class="text-slate-400 dark:text-slate-500">Saisie</span>
@endsection

@section('content')

<div class="max-w-6xl mx-auto space-y-6">

    {{-- ══════════════════════════════════════════════════════
         FILTRES — Classe / Date / Créneau
    ══════════════════════════════════════════════════════ --}}
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm
                border border-slate-200 dark:border-slate-700 overflow-hidden">

        <div class="flex items-center gap-3 px-6 py-4
                    border-b border-slate-200 dark:border-slate-700">
            <div class="w-9 h-9 rounded-xl bg-blue-100 dark:bg-blue-900/40
                        flex items-center justify-center">
                <i class="bi bi-funnel-fill text-blue-600 dark:text-blue-400"></i>
            </div>
            <div>
                <h2 class="text-sm font-bold text-slate-800 dark:text-slate-100">
                    Sélection de la session
                </h2>
                <p class="text-xs text-slate-500 dark:text-slate-400">
                    Choisissez la classe, la date et le créneau
                </p>
            </div>
        </div>

        <form method="GET" action="{{ route('attendance.create') }}" id="filter-form" class="p-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">

                {{-- Classe --}}
                <div>
                    <label class="flex items-center gap-1.5 text-sm font-medium
                                  text-slate-700 dark:text-slate-200 mb-1.5">
                        <i class="bi bi-building text-slate-400"></i>
                        Classe
                        <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <select name="class_id" id="class-select" class="w-full rounded-lg border border-slate-300 dark:border-slate-600
                                       bg-white dark:bg-slate-800 text-sm
                                       text-slate-800 dark:text-slate-100
                                       py-2.5 pl-3.5 pr-9 appearance-none
                                       focus:outline-none focus:ring-2
                                       focus:ring-blue-600/40 focus:border-blue-600
                                       transition">
                            <option value="">— Sélectionner une classe —</option>
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
                </div>

                {{-- Date --}}
                <div>
                    <label class="flex items-center gap-1.5 text-sm font-medium
                                  text-slate-700 dark:text-slate-200 mb-1.5">
                        <i class="bi bi-calendar3 text-slate-400"></i>
                        Date
                        <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="date" id="date-picker" value="{{ $date }}"
                        placeholder="Sélectionner une date" readonly class="w-full rounded-lg border border-slate-300 dark:border-slate-600
                                  bg-white dark:bg-slate-800 text-sm
                                  text-slate-800 dark:text-slate-100
                                  py-2.5 px-3.5 cursor-pointer
                                  focus:outline-none focus:ring-2
                                  focus:ring-blue-600/40 focus:border-blue-600
                                  transition" />
                </div>

                {{-- Créneau --}}
                <div>
                    <label class="flex items-center gap-1.5 text-sm font-medium
                                  text-slate-700 dark:text-slate-200 mb-1.5">
                        <i class="bi bi-clock text-slate-400"></i>
                        Créneau
                        <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <select name="schedule_id" id="schedule-select" class="w-full rounded-lg border border-slate-300 dark:border-slate-600
                                       bg-white dark:bg-slate-800 text-sm
                                       text-slate-800 dark:text-slate-100
                                       py-2.5 pl-3.5 pr-9 appearance-none
                                       focus:outline-none focus:ring-2
                                       focus:ring-blue-600/40 focus:border-blue-600
                                       transition
                                       disabled:bg-slate-100 disabled:cursor-not-allowed
                                       dark:disabled:bg-slate-900" {{ !$selectedClass ? 'disabled' : '' }}>
                            <option value="">— Sélectionner un créneau —</option>
                            @foreach($schedules as $schedule)
                            <option value="{{ $schedule->id }}" {{ $scheduleId == $schedule->id ? 'selected' : '' }}>
                                {{ $schedule->start_time->format('H:i') }}
                                – {{ $schedule->end_time->format('H:i') }}
                                | {{ $schedule->subject->name }}
                                ({{ $schedule->teacher->user->name }})
                            </option>
                            @endforeach
                        </select>
                        <span class="absolute inset-y-0 right-0 flex items-center pr-3.5 pointer-events-none">
                            <i class="bi bi-chevron-down text-slate-400 text-xs"></i>
                        </span>
                    </div>
                </div>

            </div>

            <div class="flex items-center justify-end mt-5 pt-5
                        border-t border-slate-200 dark:border-slate-700">
                <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl
                               text-sm font-semibold text-white
                               bg-blue-600 hover:bg-blue-700
                               shadow-sm transition-all duration-200">
                    <i class="bi bi-search"></i>
                    Charger les élèves
                </button>
            </div>
        </form>
    </div>

    {{-- ══════════════════════════════════════════════════════
         SESSION INFO — Résumé du créneau sélectionné
    ══════════════════════════════════════════════════════ --}}
    @if($selectedSchedule && $selectedClass)
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        @foreach([
        ['icon' => 'bi-building', 'color' => 'blue', 'label' => 'Classe', 'value' => $selectedClass->name],
        ['icon' => 'bi-book-fill', 'color' => 'emerald', 'label' => 'Matière', 'value' =>
        $selectedSchedule->subject->name],
        ['icon' => 'bi-person-fill', 'color' => 'cyan', 'label' => 'Enseignant','value' =>
        $selectedSchedule->teacher->user->name],
        ['icon' => 'bi-calendar3', 'color' => 'amber', 'label' => 'Date', 'value' =>
        \Carbon\Carbon::parse($date)->translatedFormat('d M Y')],
        ] as $info)
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm
                    border border-slate-200 dark:border-slate-700 p-4">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl shrink-0
                            bg-{{ $info['color'] }}-100 dark:bg-{{ $info['color'] }}-900/40
                            flex items-center justify-center">
                    <i class="bi {{ $info['icon'] }}
                              text-{{ $info['color'] }}-600 dark:text-{{ $info['color'] }}-400
                              text-sm"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-[10px] uppercase tracking-wider font-semibold
                              text-slate-400 dark:text-slate-500">
                        {{ $info['label'] }}
                    </p>
                    <p class="text-sm font-semibold text-slate-800 dark:text-slate-100 truncate">
                        {{ $info['value'] }}
                    </p>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    {{-- ══════════════════════════════════════════════════════
         FORMULAIRE DE PRÉSENCES
    ══════════════════════════════════════════════════════ --}}
    @if($selectedClass && $selectedSchedule && $selectedClass->students->count() > 0)

    <form action="{{ route('attendance.store') }}" method="POST" id="attendance-form">
        @csrf

        <input type="hidden" name="class_id" value="{{ $selectedClass->id }}">
        <input type="hidden" name="date" value="{{ $date }}">
        <input type="hidden" name="schedule_id" value="{{ $selectedSchedule->id }}">

        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm
                    border border-slate-200 dark:border-slate-700 overflow-hidden">

            {{-- Header avec actions rapides --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4
                        px-6 py-4 border-b border-slate-200 dark:border-slate-700">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-emerald-100 dark:bg-emerald-900/40
                                flex items-center justify-center">
                        <i class="bi bi-person-check-fill text-emerald-600 dark:text-emerald-400"></i>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-slate-800 dark:text-slate-100">
                            Liste des élèves
                        </h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400">
                            {{ $selectedClass->students->count() }} élève(s) enregistré(s)
                        </p>
                    </div>
                </div>

                {{-- Actions rapides --}}
                <div class="flex flex-wrap items-center gap-2">
                    <span class="text-xs font-medium text-slate-500 dark:text-slate-400 mr-1">
                        Tout marquer :
                    </span>
                    <button type="button" data-mark-all="present" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg
                                   text-xs font-semibold
                                   bg-emerald-100 dark:bg-emerald-900/40
                                   text-emerald-700 dark:text-emerald-400
                                   hover:bg-emerald-200 dark:hover:bg-emerald-900/60
                                   transition-colors">
                        <i class="bi bi-check-circle-fill"></i>
                        Présent
                    </button>
                    <button type="button" data-mark-all="absent" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg
                                   text-xs font-semibold
                                   bg-red-100 dark:bg-red-900/40
                                   text-red-700 dark:text-red-400
                                   hover:bg-red-200 dark:hover:bg-red-900/60
                                   transition-colors">
                        <i class="bi bi-x-circle-fill"></i>
                        Absent
                    </button>
                    <button type="button" data-mark-all="late" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg
                                   text-xs font-semibold
                                   bg-amber-100 dark:bg-amber-900/40
                                   text-amber-700 dark:text-amber-400
                                   hover:bg-amber-200 dark:hover:bg-amber-900/60
                                   transition-colors">
                        <i class="bi bi-clock-fill"></i>
                        Retard
                    </button>
                </div>
            </div>

            {{-- Compteur en temps réel --}}
            <div class="grid grid-cols-3 gap-0 border-b border-slate-200 dark:border-slate-700">
                @foreach([
                ['status' => 'present', 'label' => 'Présents', 'color' => 'emerald', 'icon' => 'bi-check-circle-fill'],
                ['status' => 'absent', 'label' => 'Absents', 'color' => 'red', 'icon' => 'bi-x-circle-fill'],
                ['status' => 'late', 'label' => 'Retards', 'color' => 'amber', 'icon' => 'bi-clock-fill'],
                ] as $stat)
                <div class="flex flex-col items-center py-3 
                            {{ !$loop->last ? 'border-r border-slate-200 dark:border-slate-700' : '' }}">
                    <i class="bi {{ $stat['icon'] }} text-{{ $stat['color'] }}-500 mb-1"></i>
                    <span id="count-{{ $stat['status'] }}"
                        class="text-xl font-bold text-{{ $stat['color'] }}-600 dark:text-{{ $stat['color'] }}-400">
                        0
                    </span>
                    <span class="text-xs text-slate-500 dark:text-slate-400">
                        {{ $stat['label'] }}
                    </span>
                </div>
                @endforeach
            </div>

            {{-- Liste des élèves --}}
            <div class="divide-y divide-slate-100 dark:divide-slate-700">
                @foreach($selectedClass->students->sortBy('matricule') as $index => $student)
                @php
                $existingRecord = $existing[$student->id] ?? null;
                $defaultStatus = $existingRecord['status'] ?? 'present';
                @endphp
                <div class="attendance-row flex flex-col sm:flex-row sm:items-center gap-4
                            px-6 py-4 hover:bg-slate-50 dark:hover:bg-slate-700/30
                            transition-colors" data-student-id="{{ $student->id }}">

                    <input type="hidden" name="attendances[{{ $index }}][student_id]" value="{{ $student->id }}">

                    {{-- Avatar + Nom --}}
                    <div class="flex items-center gap-3 flex-1 min-w-0">
                        <div class="w-10 h-10 rounded-full shrink-0
                                    bg-gradient-to-br from-blue-500 to-emerald-500
                                    flex items-center justify-center
                                    text-white text-sm font-bold">
                            {{ strtoupper(substr($student->user->name, 0, 1)) }}
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-slate-800 dark:text-slate-100 truncate">
                                {{ $student->user->name }}
                            </p>
                            <p class="text-xs text-slate-500 dark:text-slate-400">
                                {{ $student->matricule }}
                            </p>
                        </div>
                    </div>

                    {{-- Statut --}}
                    <div class="flex items-center gap-2">
                        @foreach([
                        ['value' => 'present', 'label' => 'Présent', 'color' => 'emerald'],
                        ['value' => 'absent', 'label' => 'Absent', 'color' => 'red'],
                        ['value' => 'late', 'label' => 'Retard', 'color' => 'amber'],
                        ] as $option)
                        <label class="status-label cursor-pointer">
                            <input type="radio" name="attendances[{{ $index }}][status]" value="{{ $option['value'] }}"
                                class="sr-only status-radio" data-color="{{ $option['color'] }}"
                                {{ $defaultStatus === $option['value'] ? 'checked' : '' }}>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg
                                         text-xs font-semibold border-2 transition-all duration-200
                                         border-slate-200 dark:border-slate-600
                                         text-slate-500 dark:text-slate-400
                                         hover:border-{{ $option['color'] }}-300
                                         status-option" data-color="{{ $option['color'] }}">
                                {{ $option['label'] }}
                            </span>
                        </label>
                        @endforeach
                    </div>

                    {{-- Note / Raison --}}
                    <div class="sm:w-48">
                        <input type="text" name="attendances[{{ $index }}][note]"
                            value="{{ $existingRecord['reason'] ?? '' }}" placeholder="Motif (optionnel)" class="w-full rounded-lg border border-slate-200 dark:border-slate-600
                                      bg-slate-50 dark:bg-slate-700/50
                                      text-xs text-slate-700 dark:text-slate-300
                                      placeholder:text-slate-400
                                      px-3 py-1.5
                                      focus:outline-none focus:ring-2
                                      focus:ring-blue-600/40 focus:border-blue-600
                                      transition" />
                    </div>

                </div>
                @endforeach
            </div>

            {{-- Footer --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4
                        px-6 py-4 bg-slate-50 dark:bg-slate-800/50
                        border-t border-slate-200 dark:border-slate-700">
                <p class="text-xs text-slate-500 dark:text-slate-400 flex items-center gap-1.5">
                    <i class="bi bi-info-circle text-blue-500"></i>
                    Les parents des élèves absents seront notifiés automatiquement.
                </p>
                <div class="flex items-center gap-3">
                    <a href="{{ route('attendance.index') }}" class="px-4 py-2 rounded-xl text-sm font-medium
                              text-slate-600 dark:text-slate-400
                              hover:bg-slate-100 dark:hover:bg-slate-700
                              transition-all duration-200">
                        Annuler
                    </a>
                    <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl
                                   text-sm font-semibold text-white
                                   bg-blue-600 hover:bg-blue-700
                                   shadow-sm transition-all duration-200">
                        <i class="bi bi-check-lg"></i>
                        Enregistrer les présences
                    </button>
                </div>
            </div>
        </div>
    </form>

    @elseif($selectedClass && !$selectedSchedule)
    {{-- Aucun créneau --}}
    <div class="flex flex-col items-center justify-center py-16
                bg-white dark:bg-slate-800 rounded-2xl shadow-sm
                border border-slate-200 dark:border-slate-700">
        <div class="w-16 h-16 rounded-2xl bg-amber-100 dark:bg-amber-900/40
                    flex items-center justify-center mb-4">
            <i class="bi bi-clock text-amber-500 text-2xl"></i>
        </div>
        <h3 class="text-base font-bold text-slate-800 dark:text-slate-100 mb-2">
            Aucun créneau pour ce jour
        </h3>
        <p class="text-sm text-slate-500 dark:text-slate-400 text-center max-w-sm">
            Il n'y a pas de cours programmé pour cette classe à cette date.
        </p>
    </div>

    @elseif($selectedClass && $selectedClass->students->count() === 0)
    {{-- Classe vide --}}
    <div class="flex flex-col items-center justify-center py-16
                bg-white dark:bg-slate-800 rounded-2xl shadow-sm
                border border-slate-200 dark:border-slate-700">
        <div class="w-16 h-16 rounded-2xl bg-slate-100 dark:bg-slate-700
                    flex items-center justify-center mb-4">
            <i class="bi bi-people text-slate-400 text-2xl"></i>
        </div>
        <h3 class="text-base font-bold text-slate-800 dark:text-slate-100 mb-2">
            Classe vide
        </h3>
        <p class="text-sm text-slate-500 dark:text-slate-400 text-center max-w-sm">
            Aucun élève n'est inscrit dans cette classe.
        </p>
    </div>
    @endif

</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {

    // ── Flatpickr sur date ───────────────────────────────────
    if (typeof flatpickr !== 'undefined') {
        flatpickr('#date-picker', {
            dateFormat: 'Y-m-d',
            maxDate: 'today',
            locale: 'fr',
            onChange: () => {
                document.getElementById('filter-form').submit();
            },
        });
    }

    // ── Auto-submit quand classe change ─────────────────────
    document.getElementById('class-select')?.addEventListener('change', () => {
        document.getElementById('filter-form').submit();
    });

    // ── Auto-submit quand créneau change ────────────────────
    document.getElementById('schedule-select')?.addEventListener('change', () => {
        document.getElementById('filter-form').submit();
    });

    // ── Styling des radio buttons statut ────────────────────
    const colorMap = {
        emerald: {
            active: 'border-emerald-500 bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400',
            inactive: 'border-slate-200 dark:border-slate-600 text-slate-500 dark:text-slate-400',
        },
        red: {
            active: 'border-red-500 bg-red-50 dark:bg-red-900/30 text-red-700 dark:text-red-400',
            inactive: 'border-slate-200 dark:border-slate-600 text-slate-500 dark:text-slate-400',
        },
        amber: {
            active: 'border-amber-500 bg-amber-50 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400',
            inactive: 'border-slate-200 dark:border-slate-600 text-slate-500 dark:text-slate-400',
        },
    };

    const updateRadioStyles = (radio) => {
        const row = radio.closest('.attendance-row');
        const radios = row.querySelectorAll('.status-radio');

        radios.forEach(r => {
            const span = r.nextElementSibling;
            const color = r.dataset.color;
            const map = colorMap[color];

            // Reset
            map.active.split(' ').forEach(c => span.classList.remove(c));
            map.inactive.split(' ').forEach(c => span.classList.remove(c));

            if (r.checked) {
                map.active.split(' ').forEach(c => span.classList.add(c));
            } else {
                map.inactive.split(' ').forEach(c => span.classList.add(c));
            }
        });

        updateCounts();
    };

    // Initialiser les styles au chargement
    document.querySelectorAll('.status-radio').forEach(radio => {
        radio.addEventListener('change', () => updateRadioStyles(radio));
        if (radio.checked) updateRadioStyles(radio);
    });

    // ── Compteurs en temps réel ──────────────────────────────
    const updateCounts = () => {
        const counts = {
            present: 0,
            absent: 0,
            late: 0
        };

        document.querySelectorAll('.status-radio:checked').forEach(radio => {
            if (counts[radio.value] !== undefined) counts[radio.value]++;
        });

        Object.entries(counts).forEach(([status, count]) => {
            const el = document.getElementById(`count-${status}`);
            if (el) el.textContent = count;
        });
    };

    updateCounts();

    // ── Boutons "Tout marquer" ───────────────────────────────
    document.querySelectorAll('[data-mark-all]').forEach(btn => {
        btn.addEventListener('click', () => {
            const status = btn.dataset.markAll;

            document.querySelectorAll('.attendance-row').forEach(row => {
                const radio = row.querySelector(`.status-radio[value="${status}"]`);
                if (radio) {
                    radio.checked = true;
                    updateRadioStyles(radio);
                }
            });
        });
    });

});
</script>
@endpush

@endsection