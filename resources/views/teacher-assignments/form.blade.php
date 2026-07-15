@extends('layouts.base')

@section('page_title', isset($teacherAssignment) && $teacherAssignment->exists
? 'Modifier l\'assignation'
: 'Nouvelle assignation')

@section('breadcrumb')
<span class="text-slate-300 dark:text-slate-600 font-light select-none">/</span>
<a href="{{ route('teacher-assignments.index') }}"
    class="text-slate-400 dark:text-slate-500 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
    Assignations
</a>
@endsection

@section('page_header')
@php $isEdit = isset($teacherAssignment) && $teacherAssignment->exists; @endphp
<div class="flex items-center justify-between gap-4">
    <div class="flex items-center gap-3 min-w-0">
        <div class="w-10 h-10 rounded-xl bg-linear-to-br from-blue-600 to-emerald-500
                        flex items-center justify-center shadow-sm shrink-0">
            <i class="bi bi-person-workspace text-white text-lg"></i>
        </div>
        <div class="min-w-0">
            <h1 class="text-lg font-bold text-slate-800 dark:text-slate-100 truncate">
                {{ $isEdit ? 'Modifier l\'assignation' : 'Nouvelle assignation enseignant' }}
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400">
                {{ $isEdit ? 'Modifiez les informations de l\'assignation' : 'Assignez un enseignant à une matière et une classe' }}
            </p>
        </div>
    </div>
    <a href="{{ route('teacher-assignments.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-medium
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
@php
$model = isset($teacherAssignment) && $teacherAssignment->exists
? $teacherAssignment
: new \App\Models\TeacherContract();
@endphp

<x-forms.form :model="$model" resource="teacher-assignments" class="space-y-6 max-w-3xl mx-auto">

    {{-- ── Assignation ── --}}
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200
                dark:border-slate-700 shadow-sm overflow-hidden">

        <div class="flex items-center gap-3 px-6 py-4
                    border-b border-slate-100 dark:border-slate-700
                    bg-slate-50/60 dark:bg-slate-700/30">
            <div class="w-7 h-7 rounded-lg bg-blue-100 dark:bg-blue-900/40
                        flex items-center justify-center">
                <i class="bi bi-person-workspace text-blue-600 dark:text-blue-400 text-sm"></i>
            </div>
            <h2 class="text-sm font-semibold text-slate-700 dark:text-slate-200">
                Détails de l'assignation
            </h2>
        </div>

        <div class="p-6 space-y-5">

            {{-- Année académique --}}
            <x-forms.select name="academic_year_id" label="Année académique" icon="bi-mortarboard"
                :options="$academicYears" option-value="id" option-label="name"
                :value="old('academic_year_id', $model->academic_year_id ?? $currentYear?->id)"
                placeholder="Sélectionner une année…" required />

            {{-- Enseignant --}}
            <div class="space-y-1.5">
                <label for="teacher_id" class="flex items-center gap-1.5 text-sm font-medium
                              text-slate-700 dark:text-slate-200">
                    <i class="bi bi-person-badge text-slate-400"></i>
                    Enseignant
                    <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none z-10">
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
                            {{ old('teacher_id', $model->teacher_id) == $teacher->id ? 'selected' : '' }}
                            data-number="{{ $teacher->employee_number }}">
                            {{ $teacher->user->name }} — {{ $teacher->employee_number }}
                        </option>
                        @endforeach
                    </select>
                    <span class="absolute inset-y-0 right-0 flex items-center pr-3.5 pointer-events-none">
                        <i class="bi bi-chevron-down text-slate-400 text-xs"></i>
                    </span>
                </div>
                @error('teacher_id')
                <p class="mt-1.5 text-xs text-red-600 dark:text-red-400 flex items-center gap-1">
                    <i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
                </p>
                @enderror
            </div>

            {{-- Matière + Classe --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <x-forms.select name="subject_id" label="Matière" icon="bi-journal-bookmark" :options="$subjects"
                    option-value="id" option-label="name" :value="old('subject_id', $model->subject_id)"
                    placeholder="Sélectionner une matière…" required />

                <x-forms.select name="school_class_id" label="Classe" icon="bi-building" :options="$classes"
                    option-value="id" option-label="name" :value="old('school_class_id', $model->school_class_id)"
                    placeholder="Sélectionner une classe…" required />
            </div>

            {{-- Alerte conflits --}}
            <div id="conflict-alert" class="hidden items-center gap-3 px-4 py-3 rounded-xl
                        bg-amber-50 dark:bg-amber-900/20
                        border border-amber-200 dark:border-amber-800">
                <i class="bi bi-exclamation-triangle-fill text-amber-500 shrink-0"></i>
                <p class="text-sm text-amber-700 dark:text-amber-400" id="conflict-text">
                    Vérification en cours…
                </p>
            </div>

        </div>
    </div>

    {{-- ── Récapitulatif visuel ── --}}
    <div id="assignment-preview" class="hidden bg-linear-to-r from-blue-50 to-emerald-50
                dark:from-blue-950/30 dark:to-emerald-950/30
                rounded-2xl border border-blue-100 dark:border-blue-900/50 p-5">
        <p class="text-xs font-semibold text-blue-500 dark:text-blue-400 uppercase tracking-wide mb-3">
            Récapitulatif de l'assignation
        </p>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            @foreach([
            ['id' => 'preview-teacher', 'icon' => 'bi-person-badge', 'label' => 'Enseignant'],
            ['id' => 'preview-subject', 'icon' => 'bi-journal-bookmark', 'label' => 'Matière'],
            ['id' => 'preview-class', 'icon' => 'bi-building', 'label' => 'Classe'],
            ['id' => 'preview-year', 'icon' => 'bi-mortarboard', 'label' => 'Année'],
            ] as $item)
            <div class="bg-white dark:bg-slate-800/80 rounded-xl p-3 border
                        border-slate-200 dark:border-slate-700">
                <div class="flex items-center gap-1.5 text-xs text-slate-500 dark:text-slate-400 mb-1">
                    <i class="bi {{ $item['icon'] }} text-[10px]"></i>
                    {{ $item['label'] }}
                </div>
                <p id="{{ $item['id'] }}" class="text-sm font-semibold text-slate-800 dark:text-slate-100 truncate">
                    —
                </p>
            </div>
            @endforeach
        </div>
    </div>

    {{-- ── Actions ── --}}
    <div class="flex items-center justify-between gap-3
                bg-white dark:bg-slate-800 rounded-2xl border border-slate-200
                dark:border-slate-700 shadow-sm px-6 py-4">
        <a href="{{ route('teacher-assignments.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-medium
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
            <i class="bi {{ $isEdit ? 'bi-check-lg' : 'bi-plus-lg' }}"></i>
            {{ $isEdit ? 'Enregistrer les modifications' : 'Créer l\'assignation' }}
        </button>
    </div>

</x-forms.form>
@endsection

@push('scripts')
<script>
(() => {
    const teacherSel = document.getElementById('teacher_id');
    const subjectSel = document.querySelector('[name="subject_id"]');
    const classSel = document.querySelector('[name="school_class_id"]');
    const yearSel = document.querySelector('[name="academic_year_id"]');
    const preview = document.getElementById('assignment-preview');
    const conflictBox = document.getElementById('conflict-alert');
    const conflictTxt = document.getElementById('conflict-text');

    function getSelectedText(sel) {
        return sel?.options[sel.selectedIndex]?.text ?? '—';
    }

    function updatePreview() {
        const teacher = getSelectedText(teacherSel);
        const subject = getSelectedText(subjectSel);
        const cls = getSelectedText(classSel);
        const year = getSelectedText(yearSel);

        const hasValue = teacherSel?.value || subjectSel?.value || classSel?.value;

        if (hasValue) {
            preview.classList.remove('hidden');
            document.getElementById('preview-teacher').textContent = teacher;
            document.getElementById('preview-subject').textContent = subject;
            document.getElementById('preview-class').textContent = cls;
            document.getElementById('preview-year').textContent = year;
        } else {
            preview.classList.add('hidden');
        }
    }

    [teacherSel, subjectSel, classSel, yearSel].forEach(el => {
        el?.addEventListener('change', updatePreview);
    });

    // Appel initial si édition
    updatePreview();
})();
</script>
@endpush