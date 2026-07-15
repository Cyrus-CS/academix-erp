@extends('layouts.base')

@section('page_title', $subject->exists ? 'Modifier la matière' : 'Nouvelle matière')

@section('breadcrumb')
<span class="text-slate-300 dark:text-slate-600 font-light select-none">/</span>
<a href="{{ route('subjects.index') }}"
    class="text-slate-400 dark:text-slate-500 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
    Matières
</a>
@endsection

@section('page_header')
<div class="flex items-center justify-between gap-4">
    <div class="flex items-center gap-3 min-w-0">
        <div class="w-10 h-10 rounded-xl bg-linear-to-br from-blue-600 to-emerald-500
                        flex items-center justify-center shadow-sm shrink-0">
            <i class="bi bi-journal-bookmark-fill text-white text-lg"></i>
        </div>
        <div class="min-w-0">
            <h1 class="text-lg font-bold text-slate-800 dark:text-slate-100 truncate">
                {{ $subject->exists ? 'Modifier : ' . $subject->name : 'Nouvelle matière' }}
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400">
                {{ $subject->exists ? 'Mettez à jour les informations de la matière' : 'Remplissez les informations pour créer une nouvelle matière' }}
            </p>
        </div>
    </div>

    <a href="{{ route('subjects.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-medium
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
<x-forms.form :model="$subject" resource="subjects" class="space-y-6 max-w-3xl mx-auto">

    {{-- ── Informations principales ── --}}
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200
                dark:border-slate-700 shadow-sm overflow-hidden">

        {{-- Header card --}}
        <div class="flex items-center gap-3 px-6 py-4
                    border-b border-slate-100 dark:border-slate-700
                    bg-slate-50/60 dark:bg-slate-700/30">
            <div class="w-7 h-7 rounded-lg bg-blue-100 dark:bg-blue-900/40
                        flex items-center justify-center">
                <i class="bi bi-info-circle-fill text-blue-600 dark:text-blue-400 text-sm"></i>
            </div>
            <h2 class="text-sm font-semibold text-slate-700 dark:text-slate-200">
                Informations générales
            </h2>
        </div>

        <div class="p-6 space-y-5">

            {{-- Nom + Code --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <x-forms.input-field name="name" label="Nom de la matière" type="text"
                    :value="old('name', $subject->name)" placeholder="Ex : Mathématiques" icon="bi-journal-text"
                    required />

                <x-forms.input-field name="code" label="Code matière" type="text" :value="old('code', $subject->code)"
                    placeholder="Ex : MATH01" icon="bi-hash" required
                    help="Code unique en majuscules, max 20 caractères." />
            </div>

            {{-- Coefficient --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div class="space-y-1.5">
                    <label for="coefficient" class="flex items-center gap-1.5 text-sm font-medium
                                  text-slate-700 dark:text-slate-200">
                        <i class="bi bi-percent text-slate-400"></i>
                        Coefficient
                        <span class="text-red-500">*</span>
                    </label>
                    <div class="flex items-center gap-3">
                        {{-- Slider --}}
                        <input type="range" id="coefficient_range" min="0.5" max="10" step="0.5"
                            value="{{ old('coefficient', $subject->coefficient ?? 1) }}" class="flex-1 h-2 bg-slate-200 dark:bg-slate-700 rounded-full
                                      accent-blue-600 cursor-pointer">
                        {{-- Valeur numérique --}}
                        <input type="number" id="coefficient" name="coefficient" min="0.5" max="10" step="0.5"
                            value="{{ old('coefficient', $subject->coefficient ?? 1) }}" class="w-20 px-3 py-2.5 rounded-xl border text-sm text-center font-semibold
                                      text-slate-800 dark:text-slate-100
                                      bg-white dark:bg-slate-700/50
                                      border-slate-200 dark:border-slate-600
                                      focus:outline-none focus:ring-2 focus:ring-blue-500/30
                                      focus:border-blue-500 dark:focus:border-blue-400
                                      transition-all duration-200" required>
                    </div>
                    @error('coefficient')
                    <p class="mt-1.5 text-xs text-red-600 dark:text-red-400 flex items-center gap-1">
                        <i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
                    </p>
                    @enderror
                </div>

                {{-- Statut actif --}}
                <div class="space-y-1.5">
                    <label class="flex items-center gap-1.5 text-sm font-medium
                                  text-slate-700 dark:text-slate-200">
                        <i class="bi bi-toggle-on text-slate-400"></i>
                        Statut
                    </label>
                    <label class="flex items-center gap-3 px-4 py-2.5 rounded-xl border cursor-pointer
                                  border-slate-200 dark:border-slate-600
                                  bg-white dark:bg-slate-700/50
                                  hover:border-blue-400 dark:hover:border-blue-500
                                  transition-all duration-200 group">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" id="is_active" value="1"
                            {{ old('is_active', $subject->is_active ?? true) ? 'checked' : '' }}
                            class="w-4 h-4 rounded accent-blue-600 cursor-pointer">
                        <div>
                            <p class="text-sm font-medium text-slate-700 dark:text-slate-200">
                                Matière active
                            </p>
                            <p class="text-xs text-slate-400 dark:text-slate-500">
                                La matière sera disponible dans les emplois du temps
                            </p>
                        </div>
                    </label>
                </div>
            </div>

            {{-- Description --}}
            <x-forms.textarea name="description" label="Description" :value="old('description', $subject->description)"
                placeholder="Décrivez brièvement cette matière…" rows="3" help="Optionnel. Maximum 500 caractères." />

        </div>
    </div>

    {{-- ── Actions ── --}}
    <div class="flex items-center justify-between gap-3
                bg-white dark:bg-slate-800 rounded-2xl border border-slate-200
                dark:border-slate-700 shadow-sm px-6 py-4">

        <a href="{{ route('subjects.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-medium
                  border border-slate-200 dark:border-slate-700
                  text-slate-700 dark:text-slate-300
                  hover:bg-slate-50 dark:hover:bg-slate-700/50
                  transition-all duration-200">
            <i class="bi bi-x-lg"></i>
            Annuler
        </a>

        <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl text-sm font-semibold
                       bg-blue-600 hover:bg-blue-700 text-white
                       shadow-sm hover:shadow-md
                       transition-all duration-200
                       focus:outline-none focus:ring-2 focus:ring-blue-500/40">
            <i class="bi {{ $subject->exists ? 'bi-check-lg' : 'bi-plus-lg' }}"></i>
            {{ $subject->exists ? 'Enregistrer les modifications' : 'Créer la matière' }}
        </button>
    </div>

</x-forms.form>
@endsection

@push('scripts')
<script>
(() => {
    const range = document.getElementById('coefficient_range');
    const number = document.getElementById('coefficient');

    if (!range || !number) return;

    // Sync range → number
    range.addEventListener('input', () => {
        number.value = range.value;
    });

    // Sync number → range
    number.addEventListener('input', () => {
        const val = Math.min(10, Math.max(0.5, parseFloat(number.value) || 0.5));
        range.value = val;
        number.value = val;
    });
})();
</script>
@endpush