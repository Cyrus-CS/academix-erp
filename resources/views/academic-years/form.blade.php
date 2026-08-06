@extends('layouts.base')

@section('page_title', $academic->exists ? 'Modifier l\'année académique' : 'Nouvelle année académique')

@section('breadcrumb')
<span class="text-slate-300 dark:text-slate-600 font-light select-none">/</span>
<a href="{{ route('academic-years.index') }}"
    class="text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
    Années académiques
</a>
<span class="text-slate-300 dark:text-slate-600 font-light select-none">/</span>
<span class="text-slate-400 dark:text-slate-500">
    {{ $academic->exists ? 'Modifier' : 'Nouvelle' }}
</span>
@endsection

@section('content')

<div class="max-w-3xl mx-auto space-y-6">

    {{-- ══════════════════════════════════════════════════════
         HEADER
    ══════════════════════════════════════════════════════ --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 dark:text-slate-100">
                {{ $academic->exists ? 'Modifier l\'année académique' : 'Nouvelle année académique' }}
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                {{ $academic->exists
                    ? 'Mettez à jour les informations de l\'année académique'
                    : 'Définissez une nouvelle période académique pour l\'établissement' }}
            </p>
        </div>
        <a href="{{ route('academic-years.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-medium
                  border border-slate-200 dark:border-slate-700
                  text-slate-600 dark:text-slate-400
                  hover:border-slate-300 dark:hover:border-slate-600
                  hover:bg-slate-50 dark:hover:bg-slate-800
                  transition-all duration-200 shrink-0">
            <i class="bi bi-arrow-left"></i>
            Retour
        </a>
    </div>

    {{-- ══════════════════════════════════════════════════════
         FORMULAIRE
    ══════════════════════════════════════════════════════ --}}
    <x-forms.form :model="$academic" resource="academic-years">

        {{-- ── Carte principale ────────────────────────────── --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm
                    border border-slate-200 dark:border-slate-700 overflow-hidden">

            {{-- Header carte --}}
            <div class="flex items-center gap-3 px-6 py-4
                        border-b border-slate-200 dark:border-slate-700">
                <div class="w-9 h-9 rounded-xl bg-blue-100 dark:bg-blue-900/40
                            flex items-center justify-center shrink-0">
                    <i class="bi bi-calendar2-range-fill text-blue-600 dark:text-blue-400"></i>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-slate-800 dark:text-slate-100">
                        Informations de l'année académique
                    </h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400">
                        Les champs marqués d'un <span class="text-red-500">*</span> sont obligatoires
                    </p>
                </div>
            </div>

            <div class="p-6 space-y-6">

                {{-- ── Nom de l'année ─────────────────────── --}}
                <x-forms.input-field name="name" label="Nom de l'année académique" :required="true" icon="bi-fonts"
                    :value="$academic->name" placeholder="Ex. 2024 – 2025" help="Format recommandé : AAAA – AAAA" />

                {{-- ── Dates ────────────────────────────────── --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

                    {{-- Date de début --}}
                    <div>
                        <label for="start_date" class="flex items-center gap-1.5 text-sm font-medium
                                      text-slate-700 dark:text-slate-200 mb-1.5">
                            <i class="bi bi-calendar-event text-slate-400"></i>
                            Date de début
                            <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="start_date" id="start_date"
                            value="{{ old('start_date', $academic->start_date?->format('Y-m-d')) }}"
                            placeholder="Sélectionner une date" readonly
                            class="w-full rounded-lg border text-sm
                                      text-slate-800 dark:text-slate-100
                                      bg-white dark:bg-slate-800
                                      placeholder:text-slate-400
                                      py-2.5 px-3.5 cursor-pointer
                                      focus:outline-none focus:ring-2 transition
                                      {{ $errors->has('start_date')
                                          ? 'border-red-500 focus:ring-red-500/40 focus:border-red-500'
                                          : 'border-slate-300 dark:border-slate-600 focus:ring-blue-600/40 focus:border-blue-600' }}" />
                        @error('start_date')
                        <p class="mt-1.5 text-xs text-red-600 dark:text-red-400 flex items-center gap-1">
                            <i class="bi bi-exclamation-circle-fill"></i>
                            {{ $message }}
                        </p>
                        @enderror
                    </div>

                    {{-- Date de fin --}}
                    <div>
                        <label for="end_date" class="flex items-center gap-1.5 text-sm font-medium
                                      text-slate-700 dark:text-slate-200 mb-1.5">
                            <i class="bi bi-calendar-check text-slate-400"></i>
                            Date de fin
                            <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="end_date" id="end_date"
                            value="{{ old('end_date', $academic->end_date?->format('Y-m-d')) }}"
                            placeholder="Sélectionner une date" readonly
                            class="w-full rounded-lg border text-sm
                                      text-slate-800 dark:text-slate-100
                                      bg-white dark:bg-slate-800
                                      placeholder:text-slate-400
                                      py-2.5 px-3.5 cursor-pointer
                                      focus:outline-none focus:ring-2 transition
                                      {{ $errors->has('end_date')
                                          ? 'border-red-500 focus:ring-red-500/40 focus:border-red-500'
                                          : 'border-slate-300 dark:border-slate-600 focus:ring-blue-600/40 focus:border-blue-600' }}" />
                        @error('end_date')
                        <p class="mt-1.5 text-xs text-red-600 dark:text-red-400 flex items-center gap-1">
                            <i class="bi bi-exclamation-circle-fill"></i>
                            {{ $message }}
                        </p>
                        @enderror
                    </div>
                </div>

                {{-- ── Aperçu durée ────────────────────────── --}}
                <div id="duration-preview" class="hidden items-center gap-3 px-4 py-3 rounded-xl
                            bg-blue-50 dark:bg-blue-900/20
                            border border-blue-100 dark:border-blue-900/40">
                    <div class="w-8 h-8 rounded-lg bg-blue-100 dark:bg-blue-900/40
                                flex items-center justify-center shrink-0">
                        <i class="bi bi-clock-history text-blue-600 dark:text-blue-400 text-sm"></i>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-blue-700 dark:text-blue-300">
                            Durée de l'année académique
                        </p>
                        <p id="duration-text" class="text-xs text-blue-600 dark:text-blue-400 mt-0.5">
                        </p>
                    </div>
                </div>

                {{-- ── Année active ────────────────────────── --}}
                <div class="flex items-start gap-4 p-4 rounded-xl
                            bg-slate-50 dark:bg-slate-700/30
                            border border-slate-200 dark:border-slate-700">

                    <div class="flex items-center h-5 mt-0.5">
                        <input type="hidden" name="is_current" value="0">
                        <input type="checkbox" name="is_current" id="is_current" value="1"
                            {{ old('is_current', $academic->is_current) ? 'checked' : '' }} class="w-4 h-4 rounded border-slate-300 dark:border-slate-600
                                      text-blue-600 bg-white dark:bg-slate-800
                                      focus:ring-blue-500 focus:ring-2
                                      cursor-pointer transition" />
                    </div>

                    <div class="flex-1 min-w-0">
                        <label for="is_current"
                            class="text-sm font-semibold text-slate-700 dark:text-slate-200 cursor-pointer">
                            Définir comme année active
                        </label>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">
                            L'année active est utilisée par défaut dans toutes les fonctionnalités
                            de la plateforme (emplois du temps, présences, notes...).
                            <br>
                            <span class="text-amber-600 dark:text-amber-400 font-medium">
                                ⚠ Cocher cette option désactivera automatiquement l'année actuellement active.
                            </span>
                        </p>
                    </div>

                    {{-- Badge statut actuel --}}
                    @if($academic->exists && $academic->is_current)
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full
                                     text-xs font-semibold shrink-0
                                     bg-emerald-100 dark:bg-emerald-900/40
                                     text-emerald-700 dark:text-emerald-300">
                        <i class="bi bi-check-circle-fill"></i>
                        Active
                    </span>
                    @endif
                </div>

                {{-- ── Alerte si modification année active ─── --}}
                @if($academic->exists && $academic->is_current)
                <div class="flex items-start gap-3 px-4 py-3.5 rounded-xl
                                bg-amber-50 dark:bg-amber-900/20
                                border border-amber-200 dark:border-amber-800">
                    <i class="bi bi-exclamation-triangle-fill text-amber-500 text-lg shrink-0 mt-0.5"></i>
                    <div>
                        <p class="text-sm font-semibold text-amber-700 dark:text-amber-400">
                            Vous modifiez l'année académique active
                        </p>
                        <p class="text-xs text-amber-600 dark:text-amber-400/80 mt-0.5">
                            Les modifications affecteront immédiatement toute la plateforme.
                            Procédez avec précaution.
                        </p>
                    </div>
                </div>
                @endif

            </div>

            {{-- ── Actions ──────────────────────────────────── --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3
                        px-6 py-4 bg-slate-50 dark:bg-slate-800/50
                        border-t border-slate-200 dark:border-slate-700">

                {{-- Info create/edit --}}
                <p class="text-xs text-slate-400 dark:text-slate-500 flex items-center gap-1.5">
                    <i class="bi bi-info-circle"></i>
                    {{ $academic->exists
                        ? 'Dernière modification : ' . $academic->updated_at->diffForHumans()
                        : 'L\'année sera créée immédiatement après validation.' }}
                </p>

                <div class="flex items-center gap-3">
                    <a href="{{ route('academic-years.index') }}" class="px-4 py-2 rounded-xl text-sm font-medium
                              text-slate-600 dark:text-slate-400
                              hover:bg-slate-100 dark:hover:bg-slate-700
                              transition-all duration-200">
                        Annuler
                    </a>
                    <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl
                                   text-sm font-semibold text-white
                                   bg-blue-600 hover:bg-blue-700
                                   shadow-sm hover:shadow-md
                                   transition-all duration-200">
                        <i class="bi {{ $academic->exists ? 'bi-check-lg' : 'bi-plus-lg' }}"></i>
                        {{ $academic->exists ? 'Enregistrer les modifications' : 'Créer l\'année académique' }}
                    </button>
                </div>
            </div>
        </div>

    </x-forms.form>

</div>

{{-- ══════════════════════════════════════════════════════
     SCRIPTS — Flatpickr + Aperçu durée
══════════════════════════════════════════════════════ --}}
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {

    // ── Flatpickr config commune ─────────────────────────────
    const fpConfig = {
        dateFormat: 'Y-m-d',
        locale: 'fr',
        disableMobile: true,
    };

    // ── Date début ───────────────────────────────────────────
    const fpStart = flatpickr('#start_date', {
        ...fpConfig,
        maxDate: document.getElementById('end_date')?.value || null,
        onChange: ([date]) => {
            if (date) {
                fpEnd.set('minDate', date);
            }
            updateDurationPreview();
        },
    });

    // ── Date fin ─────────────────────────────────────────────
    const fpEnd = flatpickr('#end_date', {
        ...fpConfig,
        minDate: document.getElementById('start_date')?.value || null,
        onChange: ([date]) => {
            if (date) {
                fpStart.set('maxDate', date);
            }
            updateDurationPreview();
        },
    });

    // ── Aperçu durée ─────────────────────────────────────────
    function updateDurationPreview() {
        const startVal = document.getElementById('start_date')?.value;
        const endVal = document.getElementById('end_date')?.value;
        const preview = document.getElementById('duration-preview');
        const text = document.getElementById('duration-text');

        if (!startVal || !endVal || !preview || !text) return;

        const start = new Date(startVal);
        const end = new Date(endVal);

        if (end <= start) {
            preview.classList.add('hidden');
            preview.classList.remove('flex');
            return;
        }

        // Calcul durée
        const diffMs = end - start;
        const diffDays = Math.floor(diffMs / (1000 * 60 * 60 * 24));
        const diffMonths = Math.floor(diffDays / 30);
        const remDays = diffDays % 30;

        // Formatage
        let durationStr = '';
        if (diffMonths > 0) {
            durationStr += `${diffMonths} mois`;
            if (remDays > 0) durationStr += ` et ${remDays} jour${remDays > 1 ? 's' : ''}`;
        } else {
            durationStr = `${diffDays} jour${diffDays > 1 ? 's' : ''}`;
        }

        // Dates formatées
        const fmt = (d) => d.toLocaleDateString('fr-FR', {
            day: 'numeric',
            month: 'long',
            year: 'numeric'
        });

        text.textContent = `Du ${fmt(start)} au ${fmt(end)} — soit ${durationStr}`;

        preview.classList.remove('hidden');
        preview.classList.add('flex');
    }

    // Init aperçu si dates déjà remplies (mode édition)
    updateDurationPreview();

});
</script>
@endpush

@endsection