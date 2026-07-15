@extends('layouts.base')

@section('page_title', $schedule->exists ? 'Modifier le créneau' : 'Nouveau créneau')

@section('breadcrumb')
<span class="text-slate-300 dark:text-slate-600 font-light select-none">/</span>
<a href="{{ route('timetables.index') }}"
    class="text-slate-400 dark:text-slate-500 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
    Emploi du temps
</a>
@endsection

@section('page_header')
<div class="flex items-center justify-between gap-4">
    <div class="flex items-center gap-3 min-w-0">
        <div class="w-10 h-10 rounded-xl bg-linear-to-br from-blue-600 to-emerald-500
                        flex items-center justify-center shadow-sm shrink-0">
            <i class="bi bi-calendar-week-fill text-white text-lg"></i>
        </div>
        <div class="min-w-0">
            <h1 class="text-lg font-bold text-slate-800 dark:text-slate-100 truncate">
                {{ $schedule->exists ? 'Modifier le créneau' : 'Nouveau créneau' }}
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400">
                {{ $schedule->exists
                        ? $schedule->day_of_week . ' · ' . $schedule->start_time . ' – ' . $schedule->end_time
                        : 'Ajoutez un créneau à l\'emploi du temps' }}
            </p>
        </div>
    </div>
    <a href="{{ route('timetables.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-medium
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
<x-forms.form :model="$schedule" resource="timetables" class="space-y-6 max-w-4xl mx-auto">

    {{-- Année académique hidden --}}
    <input type="hidden" name="academic_year_id"
        value="{{ old('academic_year_id', $schedule->academic_year_id ?? $activeYear?->id) }}">

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ── Colonne principale ── --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Contexte --}}
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200
                        dark:border-slate-700 shadow-sm overflow-hidden">

                <div class="flex items-center gap-3 px-6 py-4
                            border-b border-slate-100 dark:border-slate-700
                            bg-slate-50/60 dark:bg-slate-700/30">
                    <div class="w-7 h-7 rounded-lg bg-blue-100 dark:bg-blue-900/40
                                flex items-center justify-center">
                        <i class="bi bi-mortarboard-fill text-blue-600 dark:text-blue-400 text-sm"></i>
                    </div>
                    <h2 class="text-sm font-semibold text-slate-700 dark:text-slate-200">
                        Classe, matière & enseignant
                    </h2>
                </div>

                <div class="p-6 space-y-5">

                    {{-- Classe --}}
                    <div class="space-y-1.5">
                        <label for="class_id" class="flex items-center gap-1.5 text-sm font-medium
                                      text-slate-700 dark:text-slate-200">
                            <i class="bi bi-building text-slate-400"></i>
                            Classe
                            <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3.5
                                         pointer-events-none z-10">
                                <i class="bi bi-building text-slate-400"></i>
                            </span>
                            <select name="class_id" id="class_id" required
                                class="w-full pl-10 pr-9 py-2.5 rounded-xl border text-sm
                                           text-slate-800 dark:text-slate-100
                                           bg-white dark:bg-slate-700/50 appearance-none
                                           focus:outline-none focus:ring-2 transition-all duration-200
                                           {{ $errors->has('class_id')
                                               ? 'border-red-500 focus:ring-red-500/40'
                                               : 'border-slate-200 dark:border-slate-600 focus:ring-blue-500/30 focus:border-blue-500' }}">
                                <option value="">Sélectionner une classe…</option>
                                @foreach($classes as $class)
                                <option value="{{ $class->id }}"
                                    {{ old('class_id', $schedule->class_id) == $class->id ? 'selected' : '' }}>
                                    {{ $class->name }}
                                </option>
                                @endforeach
                            </select>
                            <span class="absolute inset-y-0 right-0 flex items-center pr-3.5
                                         pointer-events-none">
                                <i class="bi bi-chevron-down text-slate-400 text-xs"></i>
                            </span>
                        </div>
                        @error('class_id')
                        <p class="text-xs text-red-600 dark:text-red-400 flex items-center gap-1">
                            <i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
                        </p>
                        @enderror
                    </div>

                    {{-- Matière + Enseignant --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

                        {{-- Matière --}}
                        <div class="space-y-1.5">
                            <label for="subject_id" class="flex items-center gap-1.5 text-sm font-medium
                                          text-slate-700 dark:text-slate-200">
                                <i class="bi bi-journal-bookmark text-slate-400"></i>
                                Matière
                                <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5
                                             pointer-events-none z-10">
                                    <i class="bi bi-journal-bookmark text-slate-400"></i>
                                </span>
                                <select name="subject_id" id="subject_id" required
                                    class="w-full pl-10 pr-9 py-2.5 rounded-xl border text-sm
                                               text-slate-800 dark:text-slate-100
                                               bg-white dark:bg-slate-700/50 appearance-none
                                               focus:outline-none focus:ring-2 transition-all duration-200
                                               {{ $errors->has('subject_id')
                                                   ? 'border-red-500 focus:ring-red-500/40'
                                                   : 'border-slate-200 dark:border-slate-600 focus:ring-blue-500/30 focus:border-blue-500' }}">
                                    <option value="">Sélectionner une matière…</option>
                                    @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}" data-color="{{ $subject->color ?? '#3B82F6' }}"
                                        {{ old('subject_id', $schedule->subject_id) == $subject->id ? 'selected' : '' }}>
                                        {{ $subject->name }}
                                        @if($subject->coefficient)
                                        (coef. {{ $subject->coefficient }})
                                        @endif
                                    </option>
                                    @endforeach
                                </select>
                                <span class="absolute inset-y-0 right-0 flex items-center pr-3.5
                                             pointer-events-none">
                                    <i class="bi bi-chevron-down text-slate-400 text-xs"></i>
                                </span>
                            </div>
                            @error('subject_id')
                            <p class="text-xs text-red-600 dark:text-red-400 flex items-center gap-1">
                                <i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
                            </p>
                            @enderror
                        </div>

                        {{-- Enseignant --}}
                        <div class="space-y-1.5">
                            <label for="teacher_id" class="flex items-center gap-1.5 text-sm font-medium
                                          text-slate-700 dark:text-slate-200">
                                <i class="bi bi-person-badge text-slate-400"></i>
                                Enseignant
                                <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5
                                             pointer-events-none z-10">
                                    <i class="bi bi-person-badge text-slate-400"></i>
                                </span>
                                <select name="teacher_id" id="teacher_id" required
                                    class="w-full pl-10 pr-9 py-2.5 rounded-xl border text-sm
                                               text-slate-800 dark:text-slate-100
                                               bg-white dark:bg-slate-700/50 appearance-none
                                               focus:outline-none focus:ring-2 transition-all duration-200
                                               {{ $errors->has('teacher_id')
                                                   ? 'border-red-500 focus:ring-red-500/40'
                                                   : 'border-slate-200 dark:border-slate-600 focus:ring-blue-500/30 focus:border-blue-500' }}">
                                    <option value="">Sélectionner un enseignant…</option>
                                    @foreach($teachers as $teacher)
                                    <option value="{{ $teacher->id }}"
                                        {{ old('teacher_id', $schedule->teacher_id) == $teacher->id ? 'selected' : '' }}>
                                        {{ $teacher->user->name }}
                                        — {{ $teacher->employee_number }}
                                    </option>
                                    @endforeach
                                </select>
                                <span class="absolute inset-y-0 right-0 flex items-center pr-3.5
                                             pointer-events-none">
                                    <i class="bi bi-chevron-down text-slate-400 text-xs"></i>
                                </span>
                            </div>
                            @error('teacher_id')
                            <p class="text-xs text-red-600 dark:text-red-400 flex items-center gap-1">
                                <i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
                            </p>
                            @enderror
                        </div>
                    </div>

                    {{-- Salle --}}
                    <x-forms.input-field name="room" label="Salle" type="text" :value="old('room', $schedule->room)"
                        placeholder="Ex : Salle A101, Amphithéâtre 2…" icon="bi-geo-alt" help="Optionnel." />

                </div>
            </div>

            {{-- Créneau horaire --}}
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200
                        dark:border-slate-700 shadow-sm overflow-hidden">

                <div class="flex items-center gap-3 px-6 py-4
                            border-b border-slate-100 dark:border-slate-700
                            bg-slate-50/60 dark:bg-slate-700/30">
                    <div class="w-7 h-7 rounded-lg bg-emerald-100 dark:bg-emerald-900/40
                                flex items-center justify-center">
                        <i class="bi bi-clock-fill text-emerald-600 dark:text-emerald-400 text-sm"></i>
                    </div>
                    <h2 class="text-sm font-semibold text-slate-700 dark:text-slate-200">
                        Créneau horaire
                    </h2>
                </div>

                <div class="p-6 space-y-5">

                    {{-- Jour de la semaine --}}
                    <div class="space-y-2">
                        <label class="flex items-center gap-1.5 text-sm font-medium
                                      text-slate-700 dark:text-slate-200">
                            <i class="bi bi-calendar-week text-slate-400"></i>
                            Jour de la semaine
                            <span class="text-red-500">*</span>
                        </label>
                        <div class="grid grid-cols-3 sm:grid-cols-6 gap-2">
                            @foreach(['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'] as $day)
                            @php
                            $isSelected = old('day_of_week', $schedule->day_of_week) === $day;
                            $short = ['Lundi' => 'Lun', 'Mardi' => 'Mar', 'Mercredi' => 'Mer',
                            'Jeudi' => 'Jeu', 'Vendredi' => 'Ven', 'Samedi' => 'Sam'][$day];
                            @endphp
                            <label class="day-option flex flex-col items-center gap-1 px-2 py-3
                                          rounded-xl border-2 cursor-pointer transition-all duration-200
                                          {{ $isSelected
                                              ? 'border-blue-500 bg-blue-50 dark:bg-blue-950/30'
                                              : 'border-slate-200 dark:border-slate-600
                                                 hover:border-slate-300 dark:hover:border-slate-500' }}">
                                <input type="radio" name="day_of_week" value="{{ $day }}"
                                    {{ $isSelected ? 'checked' : '' }} class="sr-only">
                                <span class="text-xs font-bold
                                             {{ $isSelected
                                                 ? 'text-blue-600 dark:text-blue-400'
                                                 : 'text-slate-500 dark:text-slate-400' }}">
                                    {{ $short }}
                                </span>
                                @if($day === 'Samedi')
                                <span
                                    class="text-[8px] font-medium px-1.5 py-0.5 rounded-full
                                             {{ $isSelected
                                                 ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400'
                                                 : 'bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400' }}">
                                    ½ j.
                                </span>
                                @endif
                            </label>
                            @endforeach
                        </div>
                        @error('day_of_week')
                        <p class="text-xs text-red-600 dark:text-red-400 flex items-center gap-1">
                            <i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
                        </p>
                        @enderror
                    </div>

                    {{-- Heures --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

                        {{-- Heure début --}}
                        <div class="space-y-1.5">
                            <label for="start_time" class="flex items-center gap-1.5 text-sm font-medium
                                          text-slate-700 dark:text-slate-200">
                                <i class="bi bi-clock text-slate-400"></i>
                                Heure de début
                                <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5
                                             pointer-events-none z-10">
                                    <i class="bi bi-clock text-slate-400"></i>
                                </span>
                                <input type="text" name="start_time" id="start_time" value="{{ old('start_time', $schedule->start_time
                                           ? \Carbon\Carbon::parse($schedule->start_time)->format('H:i')
                                           : '') }}" placeholder="08:00"
                                    class="w-full pl-10 pr-3.5 py-2.5 rounded-xl border text-sm
                                              text-slate-800 dark:text-slate-100
                                              bg-white dark:bg-slate-700/50 placeholder-slate-400
                                              focus:outline-none focus:ring-2 transition-all duration-200
                                              {{ $errors->has('start_time')
                                                  ? 'border-red-500 focus:ring-red-500/40'
                                                  : 'border-slate-200 dark:border-slate-600 focus:ring-blue-500/30 focus:border-blue-500' }}" required>
                            </div>
                            @error('start_time')
                            <p class="text-xs text-red-600 dark:text-red-400 flex items-center gap-1">
                                <i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
                            </p>
                            @enderror
                        </div>

                        {{-- Heure fin --}}
                        <div class="space-y-1.5">
                            <label for="end_time" class="flex items-center gap-1.5 text-sm font-medium
                                          text-slate-700 dark:text-slate-200">
                                <i class="bi bi-clock-history text-slate-400"></i>
                                Heure de fin
                                <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5
                                             pointer-events-none z-10">
                                    <i class="bi bi-clock-history text-slate-400"></i>
                                </span>
                                <input type="text" name="end_time" id="end_time" value="{{ old('end_time', $schedule->end_time
                                           ? \Carbon\Carbon::parse($schedule->end_time)->format('H:i')
                                           : '') }}" placeholder="10:00"
                                    class="w-full pl-10 pr-3.5 py-2.5 rounded-xl border text-sm
                                              text-slate-800 dark:text-slate-100
                                              bg-white dark:bg-slate-700/50 placeholder-slate-400
                                              focus:outline-none focus:ring-2 transition-all duration-200
                                              {{ $errors->has('end_time')
                                                  ? 'border-red-500 focus:ring-red-500/40'
                                                  : 'border-slate-200 dark:border-slate-600 focus:ring-blue-500/30 focus:border-blue-500' }}" required>
                            </div>
                            @error('end_time')
                            <p class="text-xs text-red-600 dark:text-red-400 flex items-center gap-1">
                                <i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
                            </p>
                            @enderror
                        </div>
                    </div>

                    {{-- Info durée --}}
                    <div id="duration-info" class="hidden items-center gap-2.5 px-4 py-2.5 rounded-xl
                                bg-emerald-50 dark:bg-emerald-950/30
                                border border-emerald-100 dark:border-emerald-900/50">
                        <i class="bi bi-hourglass-split text-emerald-500 shrink-0"></i>
                        <span id="duration-text" class="text-sm font-semibold text-emerald-700 dark:text-emerald-400">
                        </span>
                    </div>

                    {{-- Créneaux suggérés --}}
                    <div class="space-y-2">
                        <p class="text-xs font-medium text-slate-500 dark:text-slate-400 flex items-center gap-1.5">
                            <i class="bi bi-lightning-fill text-amber-500"></i>
                            Créneaux suggérés
                        </p>
                        <div class="flex flex-wrap gap-2">
                            @foreach([
                            ['start' => '07:00', 'end' => '08:00', 'label' => '07h – 08h'],
                            ['start' => '08:00', 'end' => '10:00', 'label' => '08h – 10h'],
                            ['start' => '10:00', 'end' => '12:00', 'label' => '10h – 12h'],
                            ['start' => '12:00', 'end' => '13:00', 'label' => '12h – 13h'],
                            ['start' => '13:00', 'end' => '15:00', 'label' => '13h – 15h'],
                            ['start' => '15:00', 'end' => '17:00', 'label' => '15h – 17h'],
                            ['start' => '17:00', 'end' => '19:00', 'label' => '17h – 19h'],
                            ] as $slot)
                            <button type="button" class="time-slot-btn inline-flex items-center gap-1.5
                                           px-3 py-1.5 rounded-lg text-xs font-medium
                                           border border-slate-200 dark:border-slate-600
                                           text-slate-600 dark:text-slate-400
                                           hover:border-blue-400 hover:text-blue-600
                                           dark:hover:border-blue-500 dark:hover:text-blue-400
                                           hover:bg-blue-50 dark:hover:bg-blue-950/30
                                           transition-all duration-200" data-start="{{ $slot['start'] }}"
                                data-end="{{ $slot['end'] }}">
                                <i class="bi bi-clock text-[10px]"></i>
                                {{ $slot['label'] }}
                            </button>
                            @endforeach
                        </div>
                    </div>

                </div>
            </div>

        </div>

        {{-- ── Colonne latérale ── --}}
        <div class="space-y-6">

            {{-- Aperçu créneau --}}
            <div class="bg-linear-to-br from-blue-600 to-emerald-500
                        rounded-2xl p-5 shadow-sm overflow-hidden relative">

                {{-- Décoration --}}
                <div class="absolute -right-6 -top-6 w-24 h-24 rounded-full
                            bg-white/10"></div>
                <div class="absolute -right-3 -bottom-3 w-16 h-16 rounded-full
                            bg-white/5"></div>

                <p class="text-[10px] font-semibold text-blue-200 uppercase tracking-widest mb-4 relative">
                    Aperçu du créneau
                </p>

                {{-- Jour --}}
                <div class="flex items-center gap-2 mb-4 relative">
                    <div class="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center shrink-0">
                        <i class="bi bi-calendar-week text-white text-sm"></i>
                    </div>
                    <p id="preview-day" class="font-bold text-white text-base">
                        {{ $schedule->day_of_week ?: '—' }}
                    </p>
                </div>

                {{-- Horaire --}}
                <div class="bg-white/15 backdrop-blur-sm rounded-xl px-4 py-3 mb-4 relative">
                    <p class="text-[10px] text-blue-200 uppercase tracking-wide mb-1">
                        Horaire
                    </p>
                    <p class="text-xl font-extrabold text-white leading-none">
                        <span id="preview-start">
                            {{ $schedule->start_time
                                ? \Carbon\Carbon::parse($schedule->start_time)->format('H:i')
                                : '--:--' }}
                        </span>
                        <span class="text-blue-300 mx-1 font-light">→</span>
                        <span id="preview-end">
                            {{ $schedule->end_time
                                ? \Carbon\Carbon::parse($schedule->end_time)->format('H:i')
                                : '--:--' }}
                        </span>
                    </p>
                    <p id="preview-duration" class="text-xs text-blue-200 mt-1"></p>
                </div>

                {{-- Infos --}}
                <div class="space-y-2 relative">
                    <div class="flex items-center gap-2 text-xs text-blue-100">
                        <i class="bi bi-building w-4 text-center text-blue-300"></i>
                        <span id="preview-class" class="truncate">
                            {{ $schedule->classe?->name ?? '—' }}
                        </span>
                    </div>
                    <div class="flex items-center gap-2 text-xs text-blue-100">
                        <i class="bi bi-journal-bookmark w-4 text-center text-blue-300"></i>
                        <span id="preview-subject" class="truncate">
                            {{ $schedule->subject?->name ?? '—' }}
                        </span>
                    </div>
                    <div class="flex items-center gap-2 text-xs text-blue-100">
                        <i class="bi bi-person-badge w-4 text-center text-blue-300"></i>
                        <span id="preview-teacher" class="truncate">
                            {{ $schedule->teacher?->user?->name ?? '—' }}
                        </span>
                    </div>
                    <div class="flex items-center gap-2 text-xs text-blue-100">
                        <i class="bi bi-geo-alt w-4 text-center text-blue-300"></i>
                        <span id="preview-room" class="truncate">
                            {{ $schedule->room ?? 'Salle non définie' }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Année académique active --}}
            @if($activeYear)
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200
                        dark:border-slate-700 shadow-sm p-4">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-blue-100 dark:bg-blue-900/30
                                flex items-center justify-center shrink-0">
                        <i class="bi bi-calendar-check-fill
                                  text-blue-600 dark:text-blue-400"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-[10px] font-semibold text-slate-400 dark:text-slate-500
                                   uppercase tracking-wide">
                            Année active
                        </p>
                        <p class="text-sm font-bold text-slate-800 dark:text-slate-100 truncate">
                            {{ $activeYear->name }}
                        </p>
                        <p class="text-[10px] text-slate-400 dark:text-slate-500">
                            {{ $activeYear->start_date?->format('d/m/Y') }}
                            –
                            {{ $activeYear->end_date?->format('d/m/Y') }}
                        </p>
                    </div>
                </div>
            </div>
            @endif

            {{-- Alerte conflit --}}
            <div id="conflict-alert" class="hidden items-start gap-3 px-4 py-3.5 rounded-2xl
                        bg-red-50 dark:bg-red-900/20
                        border border-red-200 dark:border-red-800">
                <i class="bi bi-exclamation-triangle-fill text-red-500 shrink-0 mt-0.5"></i>
                <div>
                    <p class="text-xs font-semibold text-red-700 dark:text-red-400">
                        Conflit détecté
                    </p>
                    <p id="conflict-text" class="text-xs text-red-600 dark:text-red-400 mt-0.5 leading-relaxed">
                    </p>
                </div>
            </div>

            {{-- Actions --}}
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200
                        dark:border-slate-700 shadow-sm px-5 py-4
                        flex items-center justify-between gap-3">
                <a href="{{ route('timetables.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-medium
                          border border-slate-200 dark:border-slate-700
                          text-slate-700 dark:text-slate-300
                          hover:bg-slate-50 dark:hover:bg-slate-700/50
                          transition-all duration-200">
                    <i class="bi bi-x-lg"></i>
                    Annuler
                </a>
                <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold
                               bg-blue-600 hover:bg-blue-700 text-white
                               shadow-sm hover:shadow-md transition-all duration-200
                               focus:outline-none focus:ring-2 focus:ring-blue-500/40">
                    <i class="bi {{ $schedule->exists ? 'bi-check-lg' : 'bi-plus-lg' }}"></i>
                    {{ $schedule->exists ? 'Enregistrer' : 'Ajouter le créneau' }}
                </button>
            </div>

        </div>
    </div>

</x-forms.form>
@endsection

@push('scripts')
<script>
(() => {
    // ── Refs DOM ──────────────────────────────────────────────────
    const classSel = document.getElementById('class_id');
    const subjectSel = document.getElementById('subject_id');
    const teacherSel = document.getElementById('teacher_id');
    const startInput = document.getElementById('start_time');
    const endInput = document.getElementById('end_time');
    const roomInput = document.querySelector('[name="room"]');

    const previewDay = document.getElementById('preview-day');
    const previewStart = document.getElementById('preview-start');
    const previewEnd = document.getElementById('preview-end');
    const previewDuration = document.getElementById('preview-duration');
    const previewClass = document.getElementById('preview-class');
    const previewSubject = document.getElementById('preview-subject');
    const previewTeacher = document.getElementById('preview-teacher');
    const previewRoom = document.getElementById('preview-room');
    const durationInfo = document.getElementById('duration-info');
    const durationText = document.getElementById('duration-text');

    // ── Flatpickr pour heures ─────────────────────────────────────
    const fpConfig = {
        enableTime: true,
        noCalendar: true,
        dateFormat: 'H:i',
        time_24hr: true,
        minuteIncrement: 15,
        locale: 'fr',
    };

    const startPicker = flatpickr('#start_time', {
        ...fpConfig,
        defaultDate: startInput?.value || null,
        onChange([date]) {
            if (date) {
                endPicker.set('minTime', date);
                updatePreview();
                updateDuration();
            }
        },
    });

    const endPicker = flatpickr('#end_time', {
        ...fpConfig,
        defaultDate: endInput?.value || null,
        onChange() {
            updatePreview();
            updateDuration();
        },
    });

    // ── Jours --
    document.querySelectorAll('.day-option').forEach(label => {
        label.addEventListener('click', () => {
            const radio = label.querySelector('input[type="radio"]');

            // Reset tous
            document.querySelectorAll('.day-option').forEach(l => {
                const span = l.querySelector('span:first-of-type');
                l.className = l.className
                    .replace(/border-blue-500/g, 'border-slate-200 dark:border-slate-600')
                    .replace(/bg-blue-50|dark:bg-blue-950\/30/g, '');
                if (span) {
                    span.className = span.className
                        .replace(/text-blue-600|dark:text-blue-400/g,
                            'text-slate-500 dark:text-slate-400');
                }
            });

            // Activer
            label.classList.remove('border-slate-200', 'dark:border-slate-600');
            label.classList.add('border-blue-500', 'bg-blue-50', 'dark:bg-blue-950/30');
            const span = label.querySelector('span:first-of-type');
            if (span) {
                span.classList.remove('text-slate-500', 'dark:text-slate-400');
                span.classList.add('text-blue-600', 'dark:text-blue-400');
            }

            if (previewDay) previewDay.textContent = radio?.value || '—';
        });
    });

    // ── Créneaux suggérés ─────────────────────────────────────────
    document.querySelectorAll('.time-slot-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const start = btn.dataset.start;
            const end = btn.dataset.end;

            startPicker.setDate(start, true);
            endPicker.setDate(end, true);

            // Highlight le bouton sélectionné
            document.querySelectorAll('.time-slot-btn').forEach(b => {
                b.classList.remove('border-blue-400', 'text-blue-600',
                    'bg-blue-50', 'dark:bg-blue-950/30');
            });
            btn.classList.add('border-blue-400', 'text-blue-600',
                'bg-blue-50', 'dark:bg-blue-950/30');

            updatePreview();
            updateDuration();
        });
    });

    // ── Mise à jour aperçu ────────────────────────────────────────
    function getSelectedText(sel) {
        return sel?.options[sel.selectedIndex]?.text?.split('—')[0]?.trim() ?? '—';
    }

    function updatePreview() {
        const start = startInput?.value || '--:--';
        const end = endInput?.value || '--:--';

        if (previewStart) previewStart.textContent = start;
        if (previewEnd) previewEnd.textContent = end;

        if (previewClass) previewClass.textContent = getSelectedText(classSel);
        if (previewSubject) previewSubject.textContent = getSelectedText(subjectSel);
        if (previewTeacher) previewTeacher.textContent = getSelectedText(teacherSel);
        if (previewRoom) previewRoom.textContent = roomInput?.value || 'Salle non définie';
    }

    // ── Calcul durée ──────────────────────────────────────────────
    function updateDuration() {
        const startVal = startInput?.value;
        const endVal = endInput?.value;

        if (!startVal || !endVal) return;

        const [sh, sm] = startVal.split(':').map(Number);
        const [eh, em] = endVal.split(':').map(Number);
        const totalMin = (eh * 60 + em) - (sh * 60 + sm);

        if (totalMin <= 0) {
            durationInfo?.classList.add('hidden');
            durationInfo?.classList.remove('flex');
            if (previewDuration) previewDuration.textContent = '';
            return;
        }

        const h = Math.floor(totalMin / 60);
        const min = totalMin % 60;
        const label = h > 0 ?
            `${h}h${min > 0 ? min.toString().padStart(2, '0') : ''}` :
            `${min} min`;

        if (durationText) durationText.textContent = `Durée : ${label}`;
        if (previewDuration) previewDuration.textContent = `Durée : ${label}`;

        durationInfo?.classList.remove('hidden');
        durationInfo?.classList.add('flex');
    }

    // ── Listeners ─────────────────────────────────────────────────
    [classSel, subjectSel, teacherSel].forEach(el => {
        el?.addEventListener('change', updatePreview);
    });

    roomInput?.addEventListener('input', updatePreview);

    // ── Init ──────────────────────────────────────────────────────
    updatePreview();
    updateDuration();
})();
</script>
@endpush