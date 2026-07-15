@extends('layouts.base')

@section('page_title', $term->exists ? 'Modifier le trimestre' : 'Nouveau trimestre')

@section('breadcrumb')
<span class="text-slate-300 dark:text-slate-600 font-light select-none">/</span>
<a href="{{ route('terms.index') }}"
    class="text-slate-400 dark:text-slate-500 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
    Trimestres
</a>
@endsection

@section('page_header')
<div class="flex items-center justify-between gap-4">
    <div class="flex items-center gap-3 min-w-0">
        <div class="w-10 h-10 rounded-xl bg-linear-to-br from-blue-600 to-emerald-500
                        flex items-center justify-center shadow-sm shrink-0">
            <i class="bi bi-calendar3 text-white text-lg"></i>
        </div>
        <div class="min-w-0">
            <h1 class="text-lg font-bold text-slate-800 dark:text-slate-100 truncate">
                {{ $term->exists ? 'Modifier : ' . $term->name : 'Nouveau trimestre' }}
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400">
                {{ $term->exists ? 'Modifiez les informations du trimestre' : 'Définissez un nouveau trimestre académique' }}
            </p>
        </div>
    </div>
    <a href="{{ route('terms.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-medium
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
<x-forms.form :model="$term" resource="terms" class="space-y-6 max-w-3xl mx-auto">

    {{-- ── Informations du trimestre ── --}}
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200
                dark:border-slate-700 shadow-sm overflow-hidden">

        <div class="flex items-center gap-3 px-6 py-4
                    border-b border-slate-100 dark:border-slate-700
                    bg-slate-50/60 dark:bg-slate-700/30">
            <div class="w-7 h-7 rounded-lg bg-blue-100 dark:bg-blue-900/40
                        flex items-center justify-center">
                <i class="bi bi-calendar3-fill text-blue-600 dark:text-blue-400 text-sm"></i>
            </div>
            <h2 class="text-sm font-semibold text-slate-700 dark:text-slate-200">
                Détails du trimestre
            </h2>
        </div>

        <div class="p-6 space-y-5">

            {{-- Année académique --}}
            <x-forms.select name="academic_year_id" label="Année académique" icon="bi-mortarboard"
                :options="$academicYears" option-value="id" option-label="name"
                :value="old('academic_year_id', $term->academic_year_id)" placeholder="Sélectionner une année…"
                required />

            {{-- Nom du trimestre --}}
            <x-forms.input-field name="name" label="Nom du trimestre" type="text" :value="old('name', $term->name)"
                placeholder="Ex : Trimestre 1, Semestre 2…" icon="bi-tag" required />

            {{-- Dates --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <x-forms.input-field name="start_date" label="Date de début" type="date"
                    :value="old('start_date', $term->start_date?->format('Y-m-d'))" placeholder="JJ/MM/AAAA"
                    icon="bi-calendar-event" class="flatpickr-date" required />

                <x-forms.input-field name="end_date" label="Date de fin" type="date"
                    :value="old('end_date', $term->end_date?->format('Y-m-d'))" placeholder="JJ/MM/AAAA"
                    icon="bi-calendar-check" class="flatpickr-date" required />
            </div>

            {{-- Info visuelle durée --}}
            <div id="duration-info" class="hidden flex items-center gap-2 px-4 py-2.5 rounded-xl
                        bg-blue-50 dark:bg-blue-950/30
                        border border-blue-100 dark:border-blue-900/50 text-sm">
                <i class="bi bi-clock text-blue-500"></i>
                <span class="text-blue-700 dark:text-blue-300 font-medium" id="duration-text"></span>
            </div>

            {{-- Trimestre actif --}}
            <div class="space-y-1.5">
                <label class="flex items-center gap-1.5 text-sm font-medium
                              text-slate-700 dark:text-slate-200">
                    <i class="bi bi-star text-slate-400"></i>
                    Trimestre actif
                </label>
                <label class="flex items-center gap-3 px-4 py-3 rounded-xl border cursor-pointer
                              border-slate-200 dark:border-slate-600
                              bg-white dark:bg-slate-700/50
                              hover:border-emerald-400 dark:hover:border-emerald-500
                              transition-all duration-200">
                    <input type="hidden" name="is_current" value="0">
                    <input type="checkbox" name="is_current" id="is_current" value="1"
                        {{ old('is_current', $term->is_current ?? false) ? 'checked' : '' }}
                        class="w-4 h-4 rounded accent-emerald-500 cursor-pointer">
                    <div>
                        <p class="text-sm font-medium text-slate-700 dark:text-slate-200">
                            Définir comme trimestre en cours
                        </p>
                        <p class="text-xs text-amber-600 dark:text-amber-400 flex items-center gap-1 mt-0.5">
                            <i class="bi bi-exclamation-triangle-fill text-[10px]"></i>
                            Cela désactivera automatiquement le trimestre actuel de cette année
                        </p>
                    </div>
                </label>
            </div>

        </div>
    </div>

    {{-- ── Actions ── --}}
    <div class="flex items-center justify-between gap-3
                bg-white dark:bg-slate-800 rounded-2xl border border-slate-200
                dark:border-slate-700 shadow-sm px-6 py-4">
        <a href="{{ route('terms.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-medium
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
            <i class="bi {{ $term->exists ? 'bi-check-lg' : 'bi-plus-lg' }}"></i>
            {{ $term->exists ? 'Enregistrer les modifications' : 'Créer le trimestre' }}
        </button>
    </div>

</x-forms.form>
@endsection

@push('scripts')
<script>
(() => {
    // ── Flatpickr ──────────────────────────────────────────────
    const fpConfig = {
        dateFormat: 'Y-m-d',
        altInput: true,
        altFormat: 'd/m/Y',
        locale: 'fr',
        allowInput: true,
    };

    const startPicker = flatpickr('[name="start_date"]', {
        ...fpConfig,
        onChange: ([date]) => {
            if (date) endPicker.set('minDate', date);
            updateDuration();
        },
    });

    const endPicker = flatpickr('[name="end_date"]', {
        ...fpConfig,
        onChange: updateDuration,
    });

    // ── Calcul durée ───────────────────────────────────────────
    function updateDuration() {
        const start = startPicker.selectedDates[0];
        const end = endPicker.selectedDates[0];
        const info = document.getElementById('duration-info');
        const text = document.getElementById('duration-text');

        if (!start || !end || !info || !text) return;

        const diffMs = end - start;
        const diffDays = Math.round(diffMs / 86400000);

        if (diffDays > 0) {
            const weeks = Math.floor(diffDays / 7);
            text.textContent = `Durée : ${diffDays} jours (${weeks} semaines)`;
            info.classList.remove('hidden');
            info.classList.add('flex');
        } else {
            info.classList.add('hidden');
            info.classList.remove('flex');
        }
    }
})();
</script>
@endpush