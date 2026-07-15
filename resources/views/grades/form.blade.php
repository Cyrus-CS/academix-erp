@extends('layouts.base')

@section('page_title', $grade->exists ? 'Modifier la note' : 'Saisir une note')

@section('breadcrumb')
<span class="text-slate-300 dark:text-slate-600 font-light select-none">/</span>
<a href="{{ route('grades.index') }}"
    class="text-slate-400 dark:text-slate-500 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
    Notes
</a>
@endsection

@section('page_header')
<div class="flex items-center justify-between gap-4">
    <div class="flex items-center gap-3 min-w-0">
        <div class="w-10 h-10 rounded-xl bg-linear-to-br from-blue-600 to-emerald-500
                        flex items-center justify-center shadow-sm shrink-0">
            <i class="bi bi-pencil-square text-white text-lg"></i>
        </div>
        <div class="min-w-0">
            <h1 class="text-lg font-bold text-slate-800 dark:text-slate-100 truncate">
                {{ $grade->exists ? 'Modifier la note' : 'Saisie de note' }}
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400">
                {{ $grade->exists
                        ? 'Modifiez les informations de cette évaluation'
                        : 'Enregistrez une note pour un étudiant' }}
            </p>
        </div>
    </div>
    <a href="{{ route('grades.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-medium
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
<x-forms.form :model="$grade" resource="grades" class="space-y-6 max-w-3xl mx-auto">

    {{-- ── Contexte ── --}}
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
                Contexte académique
            </h2>
        </div>

        <div class="p-6 space-y-5">

            {{-- Trimestre --}}
            <x-forms.select name="term_id" label="Trimestre" icon="bi-calendar3"
                :options="$terms->map(fn($t) => ['id' => $t->id, 'label' => $t->name . ' — ' . $t->academicYear?->name])"
                option-value="id" option-label="label" :value="old('term_id', $grade->term_id ?? $currentTerm?->id)"
                placeholder="Sélectionner un trimestre…" required />

            {{-- Classe + Matière --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <x-forms.select name="school_class_id" label="Classe" icon="bi-building" :options="$classes"
                    option-value="id" option-label="name" :value="old('school_class_id', $grade->school_class_id)"
                    placeholder="Sélectionner une classe…" required />

                <x-forms.select name="subject_id" label="Matière" icon="bi-journal-bookmark" :options="$subjects"
                    option-value="id" option-label="name" :value="old('subject_id', $grade->subject_id)"
                    placeholder="Sélectionner une matière…" required />
            </div>

            {{-- Étudiant --}}
            <x-forms.select name="student_id" label="Étudiant" icon="bi-person-fill"
                :options="$students->map(fn($s) => ['id' => $s->id, 'label' => $s->user->name . ' — ' . $s->student_number])"
                option-value="id" option-label="label" :value="old('student_id', $grade->student_id)"
                placeholder="Sélectionner un étudiant…" required />

            {{-- Teacher hidden si rôle Teacher --}}
            @if($teacher)
            <input type="hidden" name="teacher_id" value="{{ $teacher->id }}">
            @else
            <x-forms.select name="teacher_id" label="Enseignant" icon="bi-person-badge"
                :options="$students->map(fn($s) => ['id' => $s->id, 'label' => $s->user->name])" option-value="id"
                option-label="label" :value="old('teacher_id', $grade->teacher_id)"
                placeholder="Sélectionner un enseignant…" />
            @endif

        </div>
    </div>

    {{-- ── Évaluation ── --}}
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200
                dark:border-slate-700 shadow-sm overflow-hidden">

        <div class="flex items-center gap-3 px-6 py-4
                    border-b border-slate-100 dark:border-slate-700
                    bg-slate-50/60 dark:bg-slate-700/30">
            <div class="w-7 h-7 rounded-lg bg-emerald-100 dark:bg-emerald-900/40
                        flex items-center justify-center">
                <i class="bi bi-award-fill text-emerald-600 dark:text-emerald-400 text-sm"></i>
            </div>
            <h2 class="text-sm font-semibold text-slate-700 dark:text-slate-200">
                Détails de l'évaluation
            </h2>
        </div>

        <div class="p-6 space-y-5">

            {{-- Type d'évaluation --}}
            <div class="space-y-2">
                <label class="flex items-center gap-1.5 text-sm font-medium
                              text-slate-700 dark:text-slate-200">
                    <i class="bi bi-list-check text-slate-400"></i>
                    Type d'évaluation
                    <span class="text-red-500">*</span>
                </label>
                <div class="grid grid-cols-3 gap-3">
                    @foreach([
                    ['value' => 'homework', 'label' => 'Devoir', 'icon' => 'bi-pencil', 'color' => 'blue'],
                    ['value' => 'test', 'label' => 'Test', 'icon' => 'bi-clipboard', 'color' => 'amber'],
                    ['value' => 'exam', 'label' => 'Examen', 'icon' => 'bi-file-text', 'color' => 'red'],
                    ] as $type)
                    @php $isSelected = old('type', $grade->type) === $type['value']; @endphp
                    <label
                        class="type-option flex flex-col items-center gap-2 p-3 rounded-xl border-2 cursor-pointer
                                  transition-all duration-200
                                  {{ $isSelected
                                      ? 'border-' . $type['color'] . '-500 bg-' . $type['color'] . '-50 dark:bg-' . $type['color'] . '-900/20'
                                      : 'border-slate-200 dark:border-slate-600 hover:border-slate-300 dark:hover:border-slate-500' }}">
                        <input type="radio" name="type" value="{{ $type['value'] }}" {{ $isSelected ? 'checked' : '' }}
                            class="sr-only">
                        <i class="bi {{ $type['icon'] }} text-xl
                                  {{ $isSelected
                                      ? 'text-' . $type['color'] . '-600 dark:text-' . $type['color'] . '-400'
                                      : 'text-slate-400 dark:text-slate-500' }}"></i>
                        <span class="text-xs font-semibold
                                     {{ $isSelected
                                         ? 'text-' . $type['color'] . '-700 dark:text-' . $type['color'] . '-300'
                                         : 'text-slate-600 dark:text-slate-400' }}">
                            {{ $type['label'] }}
                        </span>
                    </label>
                    @endforeach
                </div>
                @error('type')
                <p class="text-xs text-red-600 dark:text-red-400 flex items-center gap-1">
                    <i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
                </p>
                @enderror
            </div>

            {{-- Note + Note max --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div class="space-y-1.5">
                    <label for="score" class="flex items-center gap-1.5 text-sm font-medium
                                  text-slate-700 dark:text-slate-200">
                        <i class="bi bi-123 text-slate-400"></i>
                        Note obtenue
                        <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="number" name="score" id="score" min="0" max="20" step="0.25"
                            value="{{ old('score', $grade->score) }}" placeholder="0 – 20"
                            class="w-full px-3.5 py-2.5 rounded-xl border text-sm pr-16
                                      text-slate-800 dark:text-slate-100
                                      bg-white dark:bg-slate-700/50
                                      placeholder-slate-400
                                      focus:outline-none focus:ring-2 transition-all duration-200
                                      {{ $errors->has('score')
                                          ? 'border-red-500 focus:ring-red-500/40'
                                          : 'border-slate-200 dark:border-slate-600 focus:ring-blue-500/30 focus:border-blue-500' }}" required>
                        <span class="absolute inset-y-0 right-0 flex items-center pr-3.5
                                     text-xs font-medium text-slate-400 pointer-events-none">
                            / <span id="max-score-preview">20</span>
                        </span>
                    </div>
                    @error('score')
                    <p class="mt-1.5 text-xs text-red-600 dark:text-red-400 flex items-center gap-1">
                        <i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
                    </p>
                    @enderror
                </div>

                <x-forms.input-field name="max_score" label="Note maximale" type="number"
                    :value="old('max_score', $grade->max_score ?? 20)" placeholder="20" icon="bi-arrow-up-circle"
                    required />
            </div>

            {{-- Barre de progression visuelle --}}
            <div id="score-bar-container" class="space-y-1.5">
                <div class="flex items-center justify-between text-xs text-slate-500 dark:text-slate-400">
                    <span>Progression</span>
                    <span id="score-percent" class="font-semibold">0%</span>
                </div>
                <div class="h-2.5 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
                    <div id="score-bar" class="h-full rounded-full transition-all duration-500 bg-blue-500"
                        style="width: 0%"></div>
                </div>
                <div id="score-mention" class="text-center text-xs font-semibold py-1 px-3 rounded-full w-fit mx-auto">
                </div>
            </div>

            {{-- Date évaluation --}}
            <x-forms.input-field name="graded_at" label="Date de l'évaluation" type="text"
                :value="old('graded_at', $grade->graded_at?->format('Y-m-d') ?? today()->format('Y-m-d'))"
                icon="bi-calendar-event" class="flatpickr-date" required />

            {{-- Commentaire --}}
            <x-forms.textarea name="comment" label="Commentaire" :value="old('comment', $grade->comment)"
                placeholder="Observations sur la performance de l'étudiant…" rows="3"
                help="Optionnel. Maximum 500 caractères." />

        </div>
    </div>

    {{-- ── Actions ── --}}
    <div class="flex items-center justify-between gap-3
                bg-white dark:bg-slate-800 rounded-2xl border border-slate-200
                dark:border-slate-700 shadow-sm px-6 py-4">
        <a href="{{ route('grades.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-medium
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
            <i class="bi {{ $grade->exists ? 'bi-check-lg' : 'bi-plus-lg' }}"></i>
            {{ $grade->exists ? 'Enregistrer les modifications' : 'Enregistrer la note' }}
        </button>
    </div>

</x-forms.form>
@endsection

@push('scripts')
<script>
(() => {
    // ── Flatpickr ──────────────────────────────────────────────────
    flatpickr('[name="graded_at"]', {
        dateFormat: 'Y-m-d',
        altInput: true,
        altFormat: 'd/m/Y',
        locale: 'fr',
        maxDate: 'today',
        defaultDate: '{{ old("graded_at", today()->format("Y-m-d")) }}',
    });

    // ── Type radio styling ─────────────────────────────────────────
    const typeColors = {
        homework: 'blue',
        test: 'amber',
        exam: 'red',
    };

    document.querySelectorAll('.type-option').forEach(label => {
        label.addEventListener('click', () => {
            const radio = label.querySelector('input[type="radio"]');
            const val = radio?.value;
            const color = typeColors[val] ?? 'blue';

            // Reset tous
            document.querySelectorAll('.type-option').forEach(l => {
                l.className = l.className
                    .replace(/border-\w+-500/g, 'border-slate-200 dark:border-slate-600')
                    .replace(/bg-\w+-50|dark:bg-\w+-900\/20/g, '');
                const i = l.querySelector('i');
                const span = l.querySelector('span');
                if (i) i.className = i.className.replace(/text-\w+-\d+/g,
                    'text-slate-400 dark:text-slate-500');
                if (span) span.className = span.className.replace(/text-\w+-\d+/g,
                    'text-slate-600 dark:text-slate-400');
            });

            // Activer le sélectionné
            label.classList.add(`border-${color}-500`, `bg-${color}-50`, `dark:bg-${color}-900/20`);
            const i = label.querySelector('i');
            const span = label.querySelector('span');
            if (i) i.classList.add(`text-${color}-600`, `dark:text-${color}-400`);
            if (span) span.classList.add(`text-${color}-700`, `dark:text-${color}-300`);
        });
    });

    // ── Barre de score ─────────────────────────────────────────────
    const scoreInput = document.getElementById('score');
    const maxInput = document.querySelector('[name="max_score"]');
    const bar = document.getElementById('score-bar');
    const percent = document.getElementById('score-percent');
    const mention = document.getElementById('score-mention');
    const maxPreview = document.getElementById('max-score-preview');

    function updateBar() {
        const s = parseFloat(scoreInput?.value) || 0;
        const max = parseFloat(maxInput?.value) || 20;
        const pct = Math.min(100, Math.round((s / max) * 100));

        if (maxPreview) maxPreview.textContent = max;
        if (percent) percent.textContent = pct + '%';

        if (bar) {
            bar.style.width = pct + '%';
            bar.className = bar.className.replace(/bg-\w+-\d+/g, '');

            if (pct >= 90) bar.classList.add('bg-emerald-500');
            else if (pct >= 70) bar.classList.add('bg-blue-500');
            else if (pct >= 50) bar.classList.add('bg-amber-500');
            else bar.classList.add('bg-red-500');
        }

        if (mention) {
            const score20 = (s / max) * 20;
            const mentions = [{
                    min: 18,
                    label: 'Excellent',
                    cls: 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400'
                },
                {
                    min: 16,
                    label: 'Très bien',
                    cls: 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400'
                },
                {
                    min: 14,
                    label: 'Bien',
                    cls: 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400'
                },
                {
                    min: 12,
                    label: 'Assez bien',
                    cls: 'bg-cyan-100 dark:bg-cyan-900/30 text-cyan-700 dark:text-cyan-400'
                },
                {
                    min: 10,
                    label: 'Passable',
                    cls: 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400'
                },
                {
                    min: 0,
                    label: 'Insuffisant',
                    cls: 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400'
                },
            ];

            const m = mentions.find(x => score20 >= x.min) ?? mentions.at(-1);
            mention.textContent = s > 0 ? m.label : '';
            mention.className =
                `text-center text-xs font-semibold py-1 px-3 rounded-full w-fit mx-auto ${s > 0 ? m.cls : ''}`;
        }
    }

    scoreInput?.addEventListener('input', updateBar);
    maxInput?.addEventListener('input', updateBar);
    updateBar(); // init
})();
</script>
@endpush