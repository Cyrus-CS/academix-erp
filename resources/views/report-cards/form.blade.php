@extends('layouts.base')

@section('page_title', $reportCard->exists ? 'Recalculer le bulletin' : 'Générer un bulletin')

@section('breadcrumb')
<span class="text-slate-300 dark:text-slate-600 font-light select-none">/</span>
<a href="{{ route('report-cards.index') }}"
    class="text-slate-400 dark:text-slate-500 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
    Bulletins
</a>
@endsection

@section('page_header')
<div class="flex items-center justify-between gap-4">
    <div class="flex items-center gap-3 min-w-0">
        <div class="w-10 h-10 rounded-xl bg-linear-to-br from-blue-600 to-emerald-500
                        flex items-center justify-center shadow-sm shrink-0">
            <i class="bi bi-file-earmark-text-fill text-white text-lg"></i>
        </div>
        <div class="min-w-0">
            <h1 class="text-lg font-bold text-slate-800 dark:text-slate-100 truncate">
                {{ $reportCard->exists ? 'Recalculer le bulletin' : 'Générer un bulletin' }}
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400">
                {{ $reportCard->exists
                        ? 'Recalculez la moyenne et le rang de l\'étudiant'
                        : 'Générez un bulletin à partir des notes existantes' }}
            </p>
        </div>
    </div>
    <div class="flex items-center gap-2 shrink-0">
        {{-- Générer tous --}}
        @if(!$reportCard->exists)
        <button type="button" id="btn-generate-all" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-medium
                           border border-emerald-200 dark:border-emerald-800
                           text-emerald-700 dark:text-emerald-400
                           hover:bg-emerald-50 dark:hover:bg-emerald-900/20
                           transition-all duration-200">
            <i class="bi bi-lightning-fill"></i>
            <span class="hidden sm:inline">Générer tous</span>
        </button>
        @endif
        <a href="{{ route('report-cards.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-medium
                      border border-slate-200 dark:border-slate-700
                      text-slate-700 dark:text-slate-300
                      hover:bg-slate-50 dark:hover:bg-slate-700/50
                      transition-all duration-200">
            <i class="bi bi-arrow-left"></i>
            <span class="hidden sm:inline">Retour</span>
        </a>
    </div>
</div>
@endsection

@section('content')
<x-forms.form :model="$reportCard" resource="report-cards" class="space-y-6 max-w-3xl mx-auto">

    {{-- ── Sélection ── --}}
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200
                dark:border-slate-700 shadow-sm overflow-hidden">

        <div class="flex items-center gap-3 px-6 py-4
                    border-b border-slate-100 dark:border-slate-700
                    bg-slate-50/60 dark:bg-slate-700/30">
            <div class="w-7 h-7 rounded-lg bg-blue-100 dark:bg-blue-900/40
                        flex items-center justify-center">
                <i class="bi bi-person-fill text-blue-600 dark:text-blue-400 text-sm"></i>
            </div>
            <h2 class="text-sm font-semibold text-slate-700 dark:text-slate-200">
                Sélection étudiant & trimestre
            </h2>
        </div>

        <div class="p-6 space-y-5">

            {{-- Année académique (hidden) --}}
            <input type="hidden" name="academic_year_id"
                value="{{ old('academic_year_id', $reportCard->academic_year_id ?? $activeYear?->id) }}">

            {{-- Étudiant --}}
            <div class="space-y-1.5">
                <label for="student_id" class="flex items-center gap-1.5 text-sm font-medium
                              text-slate-700 dark:text-slate-200">
                    <i class="bi bi-person-fill text-slate-400"></i>
                    Étudiant
                    <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none z-10">
                        <i class="bi bi-person-fill text-slate-400"></i>
                    </span>
                    <select name="student_id" id="student_id" required {{ $reportCard->exists ? 'disabled' : '' }}
                        class="w-full pl-10 pr-9 py-2.5 rounded-xl border text-sm
                                   text-slate-800 dark:text-slate-100
                                   bg-white dark:bg-slate-700/50 appearance-none
                                   focus:outline-none focus:ring-2 transition-all duration-200
                                   disabled:bg-slate-100 disabled:text-slate-400 disabled:cursor-not-allowed
                                   dark:disabled:bg-slate-800
                                   {{ $errors->has('student_id')
                                       ? 'border-red-500 focus:ring-red-500/40'
                                       : 'border-slate-200 dark:border-slate-600 focus:ring-blue-500/30 focus:border-blue-500' }}">
                        <option value="">Sélectionner un étudiant…</option>
                        @foreach($students as $student)
                        <option value="{{ $student->id }}" data-class="{{ $student->class_id }}"
                            {{ old('student_id', $reportCard->student_id) == $student->id ? 'selected' : '' }}>
                            {{ $student->user->name }} — {{ $student->student_number }}
                        </option>
                        @endforeach
                    </select>
                    <span class="absolute inset-y-0 right-0 flex items-center pr-3.5 pointer-events-none">
                        <i class="bi bi-chevron-down text-slate-400 text-xs"></i>
                    </span>
                </div>
                @error('student_id')
                <p class="mt-1.5 text-xs text-red-600 dark:text-red-400 flex items-center gap-1">
                    <i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
                </p>
                @enderror
            </div>

            {{-- Classe + Trimestre --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <x-forms.select name="class_id" label="Classe" icon="bi-building" :options="$classes" option-value="id"
                    option-label="name" :value="old('class_id', $reportCard->class_id)"
                    placeholder="Sélectionner une classe…" required />

                <x-forms.select name="term_id" label="Trimestre" icon="bi-calendar3"
                    :options="$terms->map(fn($t) => ['id' => $t->id, 'label' => $t->name])" option-value="id"
                    option-label="label" :value="old('term_id', $reportCard->term_id)"
                    placeholder="Sélectionner un trimestre…" required />
            </div>

        </div>
    </div>

    {{-- ── Info génération ── --}}
    <div class="flex items-start gap-3 px-5 py-4 rounded-2xl
                bg-blue-50 dark:bg-blue-950/30
                border border-blue-100 dark:border-blue-900/50">
        <i class="bi bi-info-circle-fill text-blue-500 text-lg shrink-0 mt-0.5"></i>
        <div class="text-sm text-blue-700 dark:text-blue-300 space-y-1">
            <p class="font-semibold">Comment fonctionne la génération ?</p>
            <ul class="text-xs text-blue-600 dark:text-blue-400 space-y-0.5 list-disc list-inside">
                <li>Les notes existantes du trimestre sélectionné sont récupérées automatiquement</li>
                <li>La moyenne est calculée en tenant compte des coefficients de chaque matière</li>
                <li>Le rang est calculé par rapport aux autres étudiants de la classe</li>
                <li>Une appréciation est attribuée selon la moyenne obtenue</li>
            </ul>
        </div>
    </div>

    {{-- ── Actions ── --}}
    <div class="flex items-center justify-between gap-3
                bg-white dark:bg-slate-800 rounded-2xl border border-slate-200
                dark:border-slate-700 shadow-sm px-6 py-4">
        <a href="{{ route('report-cards.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-medium
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
            <i class="bi {{ $reportCard->exists ? 'bi-arrow-clockwise' : 'bi-file-earmark-plus' }}"></i>
            {{ $reportCard->exists ? 'Recalculer le bulletin' : 'Générer le bulletin' }}
        </button>
    </div>

</x-forms.form>

{{-- Modal Générer tous --}}
@if(!$reportCard->exists)
<div id="modal-generate-all" class="hidden fixed inset-0 z-50 flex items-center justify-center
            bg-slate-900/60 backdrop-blur-sm px-4">
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-xl
                border border-slate-200 dark:border-slate-700
                w-full max-w-md p-6 space-y-5">

        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-100 dark:bg-emerald-900/30
                        flex items-center justify-center">
                <i class="bi bi-lightning-fill text-emerald-600 dark:text-emerald-400 text-lg"></i>
            </div>
            <div>
                <h3 class="font-bold text-slate-800 dark:text-slate-100">
                    Générer tous les bulletins
                </h3>
                <p class="text-xs text-slate-500 dark:text-slate-400">
                    Génération en masse pour un trimestre
                </p>
            </div>
        </div>

        <form method="POST" action="{{ route('report-cards.generate-all') }}" class="space-y-4">
            @csrf
            <x-forms.select name="term_id" label="Trimestre" icon="bi-calendar3"
                :options="$terms->map(fn($t) => ['id' => $t->id, 'label' => $t->name])" option-value="id"
                option-label="label" placeholder="Sélectionner un trimestre…" required />

            <x-forms.select name="class_id" label="Classe (optionnel)" icon="bi-building" :options="$classes"
                option-value="id" option-label="name" placeholder="Toutes les classes" />

            <div class="flex items-center gap-3 pt-2">
                <button type="button" id="btn-close-modal" class="flex-1 px-4 py-2.5 rounded-xl text-sm font-medium
                               border border-slate-200 dark:border-slate-700
                               text-slate-700 dark:text-slate-300
                               hover:bg-slate-50 dark:hover:bg-slate-700/50
                               transition-all duration-200">
                    Annuler
                </button>
                <button type="submit" class="flex-1 px-4 py-2.5 rounded-xl text-sm font-semibold
                               bg-emerald-600 hover:bg-emerald-700 text-white
                               transition-all duration-200">
                    <i class="bi bi-lightning-fill me-1"></i>
                    Générer
                </button>
            </div>
        </form>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
(() => {
    const modal = document.getElementById('modal-generate-all');
    const openBtn = document.getElementById('btn-generate-all');
    const closeBtn = document.getElementById('btn-close-modal');

    openBtn?.addEventListener('click', () => modal?.classList.remove('hidden'));
    closeBtn?.addEventListener('click', () => modal?.classList.add('hidden'));
    modal?.addEventListener('click', (e) => {
        if (e.target === modal) modal.classList.add('hidden');
    });
})();
</script>
@endpush