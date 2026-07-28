@extends('layouts.base')

@section('page_title', $parent->exists ? 'Modifier le parent' : 'Nouveau parent')

@section('breadcrumb')
<a href="{{ route('parents.index') }}" class="text-slate-400 dark:text-slate-500
          hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
    Parents
</a>
<span class="text-slate-300 dark:text-slate-600 font-light select-none">/</span>
@endsection

@section('content')

@php
$isEdit = $parent->exists;
$selectedIds = $isEdit
? $parent->students->pluck('id')->toArray()
: (is_array(old('student_ids')) ? old('student_ids') : []);
@endphp

<div class="max-w-5xl mx-auto space-y-6">

    {{-- ════════════════════ HEADER ════════════════════ --}}
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border
                border-slate-200 dark:border-slate-700 overflow-hidden">
        <div class="px-6 py-5 bg-gradient-to-r from-blue-50 to-emerald-50
                    dark:from-blue-950/30 dark:to-emerald-950/30
                    border-b border-slate-200 dark:border-slate-700
                    flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-blue-600 to-emerald-500
                        flex items-center justify-center shrink-0 shadow-md">
                <i class="bi {{ $isEdit ? 'bi-pencil-fill' : 'bi-person-plus-fill' }}
                          text-white text-xl"></i>
            </div>
            <div>
                <h1 class="text-lg font-bold text-slate-800 dark:text-slate-100">
                    {{ $isEdit ? 'Modifier le parent' : 'Ajouter un parent' }}
                </h1>
                <p class="text-sm text-slate-500 dark:text-slate-400">
                    {{ $isEdit
                        ? 'Mettez à jour les informations et les élèves associés'
                        : 'Créez un compte parent et associez-lui des élèves' }}
                </p>
            </div>
        </div>
    </div>

    {{-- ════════════════════ FORMULAIRE ════════════════════ --}}
    <x-forms.form :model="$parent" resource="parents" enctype="multipart/form-data">

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 p-6">

            {{-- ── Colonne principale ── --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- Informations personnelles --}}
                <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border
                            border-slate-200 dark:border-slate-700 overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700
                                flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-lg bg-blue-50 dark:bg-blue-900/30
                                    flex items-center justify-center">
                            <i class="bi bi-person-fill text-blue-600 dark:text-blue-400 text-sm"></i>
                        </div>
                        <h2 class="text-sm font-semibold text-slate-800 dark:text-slate-100">
                            Informations personnelles
                        </h2>
                    </div>

                    <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-5">

                        <div class="sm:col-span-2">
                            <x-forms.input-field name="name" label="Nom complet" icon="bi-person"
                                :value="$isEdit ? $parent->name : ''" placeholder="Ex: Kofi Mensah" required />
                        </div>

                        <x-forms.input-field type="email" name="email" label="Adresse e-mail" icon="bi-envelope"
                            :value="$isEdit ? $parent->email : ''" placeholder="parent@email.com" required
                            help="Servira d'identifiant de connexion." />

                        <x-forms.input-field type="text" name="phone" label="Numéro de téléphone" icon="bi-telephone"
                            :value="$isEdit ? ($parent->phone ?? '') : ''" placeholder="+228 90 00 00 00" />

                        <div class="sm:col-span-2">
                            <x-forms.input-field type="password" name="password"
                                label="{{ $isEdit ? 'Nouveau mot de passe' : 'Mot de passe' }}" icon="bi-lock" value=""
                                :help="$isEdit
                                    ? 'Laissez vide pour conserver le mot de passe actuel.'
                                    : 'Si vide, un mot de passe aléatoire sera généré et devra être réinitialisé.'"
                                :required="!$isEdit" />
                        </div>
                    </div>
                </div>

                {{-- Association des élèves --}}
                <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border
                            border-slate-200 dark:border-slate-700 overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700
                                flex items-center justify-between gap-2">
                        <div class="flex items-center gap-2.5">
                            <div class="w-7 h-7 rounded-lg bg-emerald-50 dark:bg-emerald-900/30
                                        flex items-center justify-center">
                                <i class="bi bi-mortarboard-fill text-emerald-600 dark:text-emerald-400 text-sm"></i>
                            </div>
                            <h2 class="text-sm font-semibold text-slate-800 dark:text-slate-100">
                                Élèves associés
                            </h2>
                        </div>
                        <span id="selected-count" class="text-xs px-2 py-1 rounded-full font-medium
                                     bg-blue-100 dark:bg-blue-900/40
                                     text-blue-600 dark:text-blue-400">
                            {{ count($selectedIds) }} sélectionné{{ count($selectedIds) > 1 ? 's' : '' }}
                        </span>
                    </div>

                    {{-- Barre de recherche dans la liste --}}
                    <div class="px-6 pt-4 pb-2">
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
                                <i class="bi bi-search text-slate-400 text-sm"></i>
                            </span>
                            <input type="text" id="student-search"
                                placeholder="Filtrer les élèves par nom ou matricule..." class="w-full pl-9 pr-4 py-2.5 rounded-lg border text-sm
                                          text-slate-800 dark:text-slate-100
                                          bg-white dark:bg-slate-900
                                          border-slate-300 dark:border-slate-600
                                          placeholder:text-slate-400
                                          focus:outline-none focus:ring-2 focus:ring-blue-600/40
                                          focus:border-blue-600 transition" />
                        </div>
                    </div>

                    {{-- Liste des élèves (checkboxes) --}}
                    <div class="px-6 pb-6">
                        @if($errors->has('student_ids') || $errors->has('student_ids.*'))
                        <p class="text-xs text-red-600 dark:text-red-400 flex items-center gap-1 mb-3">
                            <i class="bi bi-exclamation-circle-fill"></i>
                            {{ $errors->first('student_ids') }}
                        </p>
                        @endif

                        <div id="students-list" class="max-h-80 overflow-y-auto rounded-xl border
                                    border-slate-200 dark:border-slate-700 divide-y
                                    divide-slate-100 dark:divide-slate-700">

                            @forelse($students as $student)
                            @php $checked = in_array($student->id, $selectedIds); @endphp
                            <label data-student-item data-name="{{ strtolower($student->user?->name ?? '') }}"
                                data-matricule="{{ strtolower($student->matricule ?? '') }}" class="student-item flex items-center gap-3 px-4 py-3 cursor-pointer
                                          hover:bg-slate-50 dark:hover:bg-slate-700/40
                                          transition-colors {{ $checked ? 'bg-blue-50/50 dark:bg-blue-900/10' : '' }}">

                                <input type="checkbox" name="student_ids[]" value="{{ $student->id }}"
                                    {{ $checked ? 'checked' : '' }} class="student-checkbox w-4 h-4 rounded border-slate-300
                                              dark:border-slate-600 text-blue-600
                                              focus:ring-blue-500 focus:ring-2 cursor-pointer" />

                                <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-blue-500 to-emerald-500
                                            flex items-center justify-center text-white text-xs font-bold shrink-0">
                                    {{ strtoupper(substr($student->user?->name ?? 'E', 0, 1)) }}
                                </div>

                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-slate-700 dark:text-slate-200 truncate">
                                        {{ $student->user?->name ?? 'Nom inconnu' }}
                                    </p>
                                    <p class="text-xs text-slate-400 dark:text-slate-500">
                                        {{ $student->matricule }}
                                        @if($student->classe)
                                        · {{ $student->classe->name }}
                                        @endif
                                    </p>
                                </div>

                                @if($checked)
                                <i class="bi bi-check-circle-fill text-blue-500 text-sm shrink-0 check-icon"></i>
                                @else
                                <i
                                    class="bi bi-circle text-slate-300 dark:text-slate-600 text-sm shrink-0 check-icon"></i>
                                @endif
                            </label>
                            @empty
                            <div class="flex flex-col items-center justify-center py-10
                                        text-slate-400 dark:text-slate-500">
                                <i class="bi bi-mortarboard text-3xl mb-2"></i>
                                <p class="text-sm">Aucun élève disponible</p>
                            </div>
                            @endforelse
                        </div>

                        {{-- État vide de la recherche --}}
                        <div id="students-empty" class="hidden text-center py-6
                                                         text-slate-400 dark:text-slate-500">
                            <i class="bi bi-search text-2xl"></i>
                            <p class="text-sm mt-1">Aucun élève ne correspond à votre recherche</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── Colonne latérale ── --}}
            <div class="space-y-5">

                {{-- Résumé --}}
                <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border
                            border-slate-200 dark:border-slate-700 overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-700
                                flex items-center gap-2">
                        <i class="bi bi-info-circle text-blue-600 dark:text-blue-400 text-sm"></i>
                        <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-100">
                            Résumé
                        </h3>
                    </div>
                    <div class="p-5 space-y-4">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-slate-500 dark:text-slate-400">Mode</span>
                            <span
                                class="font-semibold {{ $isEdit ? 'text-amber-600 dark:text-amber-400' : 'text-emerald-600 dark:text-emerald-400' }}">
                                {{ $isEdit ? 'Modification' : 'Création' }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-slate-500 dark:text-slate-400">Élèves associés</span>
                            <span id="sidebar-count" class="font-semibold text-blue-600 dark:text-blue-400">
                                {{ count($selectedIds) }}
                            </span>
                        </div>
                        @if($isEdit)
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-slate-500 dark:text-slate-400">Inscrit le</span>
                            <span class="font-medium text-slate-700 dark:text-slate-200">
                                {{ $parent->created_at?->format('d/m/Y') }}
                            </span>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Sélection rapide --}}
                <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border
                            border-slate-200 dark:border-slate-700 overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-700
                                flex items-center gap-2">
                        <i class="bi bi-lightning-fill text-amber-500 text-sm"></i>
                        <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-100">
                            Sélection rapide
                        </h3>
                    </div>
                    <div class="p-5 space-y-2">
                        <button type="button" id="select-all" class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-lg text-sm
                                       font-medium text-slate-600 dark:text-slate-300
                                       bg-slate-50 dark:bg-slate-900/50
                                       hover:bg-blue-50 dark:hover:bg-blue-900/20
                                       hover:text-blue-600 dark:hover:text-blue-400
                                       border border-slate-200 dark:border-slate-700 transition-colors">
                            <span class="flex items-center gap-2">
                                <i class="bi bi-check-all"></i>
                                Tout sélectionner
                            </span>
                        </button>
                        <button type="button" id="deselect-all" class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-lg text-sm
                                       font-medium text-slate-600 dark:text-slate-300
                                       bg-slate-50 dark:bg-slate-900/50
                                       hover:bg-red-50 dark:hover:bg-red-900/20
                                       hover:text-red-600 dark:hover:text-red-400
                                       border border-slate-200 dark:border-slate-700 transition-colors">
                            <span class="flex items-center gap-2">
                                <i class="bi bi-x-square"></i>
                                Tout désélectionner
                            </span>
                        </button>
                    </div>
                </div>

                {{-- Info sécurité --}}
                <div class="flex items-start gap-2.5 p-4 rounded-xl
                            bg-cyan-50 dark:bg-cyan-900/20
                            border border-cyan-200 dark:border-cyan-800">
                    <i class="bi bi-shield-check text-cyan-500 text-sm shrink-0 mt-0.5"></i>
                    <p class="text-xs text-cyan-700 dark:text-cyan-400 leading-relaxed">
                        Le parent recevra les notifications de présence, de notes et de paiements
                        pour les élèves qui lui sont associés.
                    </p>
                </div>
            </div>
        </div>

        {{-- ── Barre d'actions ── --}}
        <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-700
                    bg-slate-50 dark:bg-slate-900/50
                    flex flex-col sm:flex-row items-center justify-between gap-3">
            <a href="{{ route('parents.index') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2
                      px-5 py-2.5 rounded-xl text-sm font-medium
                      text-slate-600 dark:text-slate-300
                      bg-white dark:bg-slate-700
                      border border-slate-200 dark:border-slate-600
                      hover:bg-slate-50 dark:hover:bg-slate-600 transition-colors">
                <i class="bi bi-arrow-left"></i>
                Annuler
            </a>
            <button type="submit" id="submit-btn" class="w-full sm:w-auto inline-flex items-center justify-center gap-2
                           px-6 py-2.5 rounded-xl text-sm font-medium text-white
                           bg-blue-600 hover:bg-blue-700
                           shadow-sm shadow-blue-600/25 transition-colors
                           disabled:opacity-60 disabled:cursor-not-allowed">
                <i class="bi bi-check-circle-fill" id="submit-icon"></i>
                <span id="submit-label">
                    {{ $isEdit ? 'Enregistrer les modifications' : 'Créer le parent' }}
                </span>
            </button>
        </div>

    </x-forms.form>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {

    // ── Références DOM ─────────────────────────────────────────────
    const searchInput = document.getElementById('student-search');
    const studentItems = document.querySelectorAll('[data-student-item]');
    const emptyState = document.getElementById('students-empty');
    const selectedCount = document.getElementById('selected-count');
    const sidebarCount = document.getElementById('sidebar-count');
    const selectAll = document.getElementById('select-all');
    const deselectAll = document.getElementById('deselect-all');
    const submitBtn = document.getElementById('submit-btn');
    const submitIcon = document.getElementById('submit-icon');
    const submitLabel = document.getElementById('submit-label');
    const form = document.querySelector('#settings-form') ?? document.querySelector('form');

    // ── Mise à jour du compteur ────────────────────────────────────
    function updateCount() {
        const checked = document.querySelectorAll('.student-checkbox:checked').length;
        const label = checked + ' sélectionné' + (checked > 1 ? 's' : '');

        if (selectedCount) selectedCount.textContent = label;
        if (sidebarCount) sidebarCount.textContent = checked;
    }

    // ── Mise à jour visuelle d'un item ────────────────────────────
    function updateItemStyle(label, checkbox) {
        const icon = label.querySelector('.check-icon');

        if (checkbox.checked) {
            label.classList.add('bg-blue-50/50', 'dark:bg-blue-900/10');
            if (icon) {
                icon.className = 'bi bi-check-circle-fill text-blue-500 text-sm shrink-0 check-icon';
            }
        } else {
            label.classList.remove('bg-blue-50/50', 'dark:bg-blue-900/10');
            if (icon) {
                icon.className = 'bi bi-circle text-slate-300 dark:text-slate-600 text-sm shrink-0 check-icon';
            }
        }
    }

    // ── Écouter les checkboxes ─────────────────────────────────────
    document.querySelectorAll('.student-checkbox').forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            const label = checkbox.closest('[data-student-item]');
            if (label) updateItemStyle(label, checkbox);
            updateCount();
        });
    });

    // ── Recherche en temps réel ────────────────────────────────────
    searchInput?.addEventListener('input', function() {
        const query = this.value.toLowerCase().trim();
        let visible = 0;

        studentItems.forEach(function(item) {
            const name = item.dataset.name ?? '';
            const matricule = item.dataset.matricule ?? '';
            const matches = !query || name.includes(query) || matricule.includes(query);

            item.classList.toggle('hidden', !matches);
            if (matches) visible++;
        });

        emptyState?.classList.toggle('hidden', visible > 0);
    });

    // ── Sélectionner tous les éléments visibles ────────────────────
    selectAll?.addEventListener('click', function() {
        studentItems.forEach(function(item) {
            if (item.classList.contains('hidden')) return;
            const checkbox = item.querySelector('.student-checkbox');
            if (checkbox && !checkbox.checked) {
                checkbox.checked = true;
                updateItemStyle(item, checkbox);
            }
        });
        updateCount();
    });

    // ── Désélectionner tous les éléments visibles ─────────────────
    deselectAll?.addEventListener('click', function() {
        studentItems.forEach(function(item) {
            if (item.classList.contains('hidden')) return;
            const checkbox = item.querySelector('.student-checkbox');
            if (checkbox && checkbox.checked) {
                checkbox.checked = false;
                updateItemStyle(item, checkbox);
            }
        });
        updateCount();
    });

    // ── État loading du bouton submit ──────────────────────────────
    document.querySelector('form')?.addEventListener('submit', function() {
        if (submitBtn) {
            submitBtn.disabled = true;
            submitIcon.className = 'bi bi-arrow-repeat animate-spin';
            submitLabel.textContent = 'Enregistrement…';
        }
    });

    // ── Init du compteur ──────────────────────────────────────────
    updateCount();
});
</script>
@endpush