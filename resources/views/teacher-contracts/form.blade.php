@extends('layouts.base')

@php
$isEdit = $teacherContract->exists;
$pageTitle = $isEdit ? 'Modifier le contrat' : 'Nouveau contrat';
$teacherName = $isEdit ? ($teacherContract->teacher->user->name ?? '') : '';
@endphp

@section('page_title', $pageTitle)

@section('breadcrumb')
<span class="text-slate-300 dark:text-slate-600 font-light select-none">/</span>
<a href="{{ route('teacher-contracts.index') }}" class="text-slate-500 dark:text-slate-400 hover:text-blue-600
              dark:hover:text-blue-400 transition-colors">
    Contrats
</a>
@if($isEdit)
<span class="text-slate-300 dark:text-slate-600">/</span>
<span class="text-slate-500 dark:text-slate-400">Modifier</span>
@endif
@endsection

@section('page_header')
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div class="flex items-center gap-3">
        <a href="{{ route('teacher-contracts.index') }}" class="w-9 h-9 rounded-xl flex items-center justify-center
                  bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700
                  text-slate-500 hover:text-blue-600 hover:border-blue-300
                  dark:hover:border-blue-600 transition-all duration-200">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-800 dark:text-slate-100">
                {{ $pageTitle }}
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                @if($isEdit)
                Modification du contrat de
                <span class="font-semibold text-slate-700 dark:text-slate-300">
                    {{ $teacherName }}
                </span>
                @else
                Remplissez les informations du nouveau contrat
                @endif
            </p>
        </div>
    </div>

    @if($isEdit)
    <a href="{{ route('teacher-contracts.show', $teacherContract) }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-medium
              bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700
              text-slate-700 dark:text-slate-300
              hover:bg-slate-50 dark:hover:bg-slate-700 transition-all duration-200">
        <i class="bi bi-eye"></i>
        <span class="hidden sm:inline">Voir le contrat</span>
    </a>
    @endif
</div>
@endsection

@section('content')

<x-forms.form :model="$teacherContract" resource="teacher-contracts" enctype="multipart/form-data" autocomplete="off">

    <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">

        {{-- ══════════════════════════════════════════════════
             COLONNE PRINCIPALE
        ══════════════════════════════════════════════════ --}}
        <div class="xl:col-span-8 space-y-6">

            {{-- ── Section 1 : Identification ─────────────── --}}
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200
                        dark:border-slate-700 shadow-sm overflow-hidden">

                {{-- Header --}}
                <div class="px-5 sm:px-6 py-4 border-b border-slate-200 dark:border-slate-700
                            bg-slate-50/50 dark:bg-slate-800/50">
                    <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-100
                               flex items-center gap-2">
                        <i class="bi bi-person-badge-fill text-blue-500"></i>
                        Identification du contrat
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                        Enseignant concerné et numéro de contrat
                    </p>
                </div>

                <div class="p-5 sm:p-6 grid grid-cols-1 sm:grid-cols-2 gap-5">

                    {{-- Enseignant --}}
                    <div class="sm:col-span-2">
                        <x-forms.select name="teacher_id" label="Enseignant" :required="true" icon="bi-person-workspace"
                            placeholder="Sélectionner un enseignant…" :options="$teachers" option-value="id"
                            option-label="user.name" :value="old('teacher_id', $teacherContract->teacher_id ?? '')"
                            help="Seuls les enseignants actifs sont listés." />
                    </div>

                    {{-- Numéro de contrat --}}
                    <div class="sm:col-span-2">
                        <x-forms.input-field name="contract_number" label="Numéro de contrat" type="text"
                            :required="true" icon="bi-hash" placeholder="Ex : CTR-2025-001"
                            :value="old('contract_number', $teacherContract->contract_number ?? '')"
                            help="Identifiant unique du contrat dans votre système." />
                    </div>
                </div>
            </div>

            {{-- ── Section 2 : Type & Statut ──────────────── --}}
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200
                        dark:border-slate-700 shadow-sm overflow-hidden">

                <div class="px-5 sm:px-6 py-4 border-b border-slate-200 dark:border-slate-700
                            bg-slate-50/50 dark:bg-slate-800/50">
                    <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-100
                               flex items-center gap-2">
                        <i class="bi bi-file-earmark-check-fill text-emerald-500"></i>
                        Type & Statut
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                        Nature du contrat et état actuel
                    </p>
                </div>

                <div class="p-5 sm:p-6 grid grid-cols-1 sm:grid-cols-2 gap-5">

                    {{-- Type de contrat --}}
                    <div>
                        <label class="flex items-center gap-1.5 text-sm font-medium
                                      text-slate-700 dark:text-slate-200 mb-3">
                            <i class="bi bi-file-earmark-text text-slate-400"></i>
                            Type de contrat
                            <span class="text-red-500">*</span>
                        </label>

                        <div class="grid grid-cols-2 gap-2" id="contract-type-group">
                            @foreach([
                            ['value' => 'permanent', 'label' => 'Permanent (CDI)', 'icon' =>
                            'bi-file-earmark-check-fill', 'color' => 'emerald'],
                            ['value' => 'temporary', 'label' => 'Temporaire (CDD)', 'icon' =>
                            'bi-file-earmark-text-fill', 'color' => 'blue'],
                            ['value' => 'part_time', 'label' => 'Temps partiel', 'icon' => 'bi-clock-fill', 'color' =>
                            'amber'],
                            ['value' => 'internship', 'label' => 'Stage', 'icon' => 'bi-mortarboard-fill', 'color' =>
                            'cyan'],
                            ] as $type)
                            @php
                            $isSelected = old('contract_type', $teacherContract->contract_type ?? '') ===
                            $type['value'];
                            @endphp
                            <label class="contract-type-card flex items-center gap-2.5 p-3 rounded-xl
                                          border-2 cursor-pointer transition-all duration-200
                                          {{ $isSelected
                                              ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20'
                                              : 'border-slate-200 dark:border-slate-700
                                                 bg-white dark:bg-slate-800
                                                 hover:border-slate-300 dark:hover:border-slate-600' }}">
                                <input type="radio" name="contract_type" value="{{ $type['value'] }}" class="sr-only"
                                    {{ $isSelected ? 'checked' : '' }}>
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0
                                            bg-{{ $type['color'] }}-50 dark:bg-{{ $type['color'] }}-900/20">
                                    <i class="bi {{ $type['icon'] }}
                                              text-{{ $type['color'] }}-600 dark:text-{{ $type['color'] }}-400
                                              text-sm"></i>
                                </div>
                                <span class="text-xs font-medium text-slate-700 dark:text-slate-300 leading-tight">
                                    {{ $type['label'] }}
                                </span>
                            </label>
                            @endforeach
                        </div>

                        <x-forms.error name="contract_type" />
                    </div>

                    {{-- Statut --}}
                    <div>
                        <label class="flex items-center gap-1.5 text-sm font-medium
                                      text-slate-700 dark:text-slate-200 mb-3">
                            <i class="bi bi-toggle-on text-slate-400"></i>
                            Statut du contrat
                            <span class="text-red-500">*</span>
                        </label>

                        <div class="space-y-2" id="contract-status-group">
                            @foreach([
                            ['value' => 'active', 'label' => 'Actif', 'sub' => 'Contrat en cours de validité', 'icon' =>
                            'bi-check-circle-fill', 'color' => 'emerald'],
                            ['value' => 'expired', 'label' => 'Expiré', 'sub' => 'Contrat arrivé à terme', 'icon' =>
                            'bi-calendar-x-fill', 'color' => 'slate'],
                            ['value' => 'terminated', 'label' => 'Résilié', 'sub' => 'Contrat résilié avant terme',
                            'icon' => 'bi-x-circle-fill', 'color' => 'red'],
                            ] as $statusOpt)
                            @php
                            $isStatusSelected = old('status', $teacherContract->status ?? 'active') ===
                            $statusOpt['value'];
                            @endphp
                            <label class="contract-status-card flex items-center gap-3 p-3 rounded-xl
                                          border-2 cursor-pointer transition-all duration-200
                                          {{ $isStatusSelected
                                              ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20'
                                              : 'border-slate-200 dark:border-slate-700
                                                 bg-white dark:bg-slate-800
                                                 hover:border-slate-300 dark:hover:border-slate-600' }}">
                                <input type="radio" name="status" value="{{ $statusOpt['value'] }}" class="sr-only"
                                    {{ $isStatusSelected ? 'checked' : '' }}>
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0
                                            bg-{{ $statusOpt['color'] }}-50 dark:bg-{{ $statusOpt['color'] }}-900/20">
                                    <i class="bi {{ $statusOpt['icon'] }}
                                              text-{{ $statusOpt['color'] }}-600 dark:text-{{ $statusOpt['color'] }}-400
                                              text-sm"></i>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-xs font-semibold text-slate-800 dark:text-slate-200">
                                        {{ $statusOpt['label'] }}
                                    </p>
                                    <p class="text-[10px] text-slate-500 dark:text-slate-400">
                                        {{ $statusOpt['sub'] }}
                                    </p>
                                </div>
                            </label>
                            @endforeach
                        </div>

                        <x-forms.error name="status" />
                    </div>
                </div>
            </div>

            {{-- ── Section 3 : Période & Rémunération ─────── --}}
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200
                        dark:border-slate-700 shadow-sm overflow-hidden">

                <div class="px-5 sm:px-6 py-4 border-b border-slate-200 dark:border-slate-700
                            bg-slate-50/50 dark:bg-slate-800/50">
                    <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-100
                               flex items-center gap-2">
                        <i class="bi bi-calendar-range-fill text-amber-500"></i>
                        Période & Rémunération
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                        Dates de validité et conditions salariales
                    </p>
                </div>

                <div class="p-5 sm:p-6 grid grid-cols-1 sm:grid-cols-2 gap-5">

                    {{-- Date de début --}}
                    <div>
                        <x-forms.input-field name="start_date" label="Date de début" type="date" :required="true"
                            icon="bi-calendar-plus" placeholder="jj/mm/aaaa" :value="old('start_date', $teacherContract->start_date
                                ? \Carbon\Carbon::parse($teacherContract->start_date)->format('Y-m-d')
                                : '')" class="flatpickr-date" help="Date d'entrée en vigueur du contrat." />
                    </div>

                    {{-- Date de fin --}}
                    <div>
                        <x-forms.input-field name="end_date" label="Date de fin" type="date" icon="bi-calendar-minus"
                            placeholder="jj/mm/aaaa (optionnel pour CDI)" :value="old('end_date', $teacherContract->end_date
                                ? \Carbon\Carbon::parse($teacherContract->end_date)->format('Y-m-d')
                                : '')" class="flatpickr-date"
                            help="Laissez vide pour un contrat CDI sans terme défini." />
                    </div>

                    {{-- Salaire --}}
                    <div>
                        <label class="flex items-center gap-1.5 text-sm font-medium
                                      text-slate-700 dark:text-slate-200 mb-1.5">
                            <i class="bi bi-cash-stack text-slate-400"></i>
                            Salaire mensuel
                            <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="number" name="salary" id="salary"
                                value="{{ old('salary', $teacherContract->salary ?? '') }}" min="0" step="500"
                                placeholder="Ex : 150000"
                                class="w-full pl-3.5 pr-20 py-2.5 text-sm rounded-xl
                                          border text-slate-800 dark:text-slate-100
                                          bg-white dark:bg-slate-800
                                          placeholder:text-slate-400
                                          focus:outline-none focus:ring-2 transition
                                          {{ $errors->has('salary')
                                              ? 'border-red-500 focus:ring-red-500/40 focus:border-red-500'
                                              : 'border-slate-300 dark:border-slate-600 focus:ring-blue-600/40 focus:border-blue-600' }}" />
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3.5
                                        pointer-events-none">
                                <span class="text-xs font-semibold text-slate-500
                                             dark:text-slate-400 bg-slate-100
                                             dark:bg-slate-700 px-2 py-1 rounded-lg">
                                    FCFA
                                </span>
                            </div>
                        </div>

                        {{-- Aperçu formaté --}}
                        <p id="salary-preview" class="mt-1.5 text-xs text-slate-500 dark:text-slate-400 hidden">
                            Soit :
                            <span id="salary-formatted"
                                class="font-semibold text-emerald-600 dark:text-emerald-400"></span>
                        </p>
                        <x-forms.error name="salary" />
                    </div>

                    {{-- Durée calculée --}}
                    <div>
                        <label class="flex items-center gap-1.5 text-sm font-medium
                                      text-slate-700 dark:text-slate-200 mb-1.5">
                            <i class="bi bi-hourglass-split text-slate-400"></i>
                            Durée du contrat
                        </label>
                        <div id="duration-display" class="h-10.5 px-3.5 rounded-xl border border-slate-200
                                    dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50
                                    flex items-center text-sm text-slate-500 dark:text-slate-400">
                            <span id="duration-text">Calculée automatiquement</span>
                        </div>
                        <p class="mt-1.5 text-xs text-slate-500 dark:text-slate-400">
                            Calculée à partir des dates saisies.
                        </p>
                    </div>
                </div>
            </div>

            {{-- ── Section 4 : Description --}}
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200
                        dark:border-slate-700 shadow-sm overflow-hidden">

                <div class="px-5 sm:px-6 py-4 border-b border-slate-200 dark:border-slate-700
                            bg-slate-50/50 dark:bg-slate-800/50">
                    <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-100
                               flex items-center gap-2">
                        <i class="bi bi-chat-text-fill text-cyan-500"></i>
                        Clauses & Observations
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                        Informations complémentaires sur ce contrat
                    </p>
                </div>

                <div class="p-5 sm:p-6">
                    <x-forms.textarea name="description" label="Description / Clauses"
                        placeholder="Décrivez les conditions particulières, clauses spécifiques, observations…"
                        :value="old('description', $teacherContract->description ?? '')" :rows="5"
                        help="Maximum 1000 caractères. Ce champ est optionnel." />
                </div>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════
             SIDEBAR
        ══════════════════════════════════════════════════ --}}
        <div class="xl:col-span-4 space-y-6">

            {{-- ── Document PDF ────────────────────────────── --}}
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200
                        dark:border-slate-700 shadow-sm overflow-hidden">

                <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-700
                            bg-slate-50/50 dark:bg-slate-800/50">
                    <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-100
                               flex items-center gap-2">
                        <i class="bi bi-file-earmark-pdf-fill text-red-500"></i>
                        Document contrat
                    </h3>
                </div>

                <div class="p-5">
                    {{-- Document existant --}}
                    @if($isEdit && $teacherContract->contract_pdf_path)
                    <div class="mb-4 flex items-center gap-3 p-3 rounded-xl
                                bg-emerald-50 dark:bg-emerald-900/20
                                border border-emerald-200 dark:border-emerald-800">
                        <div class="w-9 h-9 rounded-lg bg-emerald-100 dark:bg-emerald-900/30
                                    flex items-center justify-center shrink-0">
                            <i class="bi bi-file-earmark-check-fill
                                      text-emerald-600 dark:text-emerald-400"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-xs font-semibold text-emerald-700 dark:text-emerald-300">
                                Document actuel
                            </p>
                            <p class="text-[11px] text-emerald-600 dark:text-emerald-400 truncate">
                                {{ basename($teacherContract->contract_pdf_path) }}
                            </p>
                        </div>
                        <a href="{{ Storage::url($teacherContract->contract_pdf_path) }}" target="_blank" class="w-7 h-7 rounded-lg bg-white dark:bg-slate-800
                                  border border-emerald-300 dark:border-emerald-700
                                  flex items-center justify-center
                                  text-emerald-600 hover:text-emerald-700 transition-colors" title="Voir le document">
                            <i class="bi bi-eye text-xs"></i>
                        </a>
                    </div>
                    @endif

                    {{-- Zone upload --}}
                    <div id="upload-zone" class="relative border-2 border-dashed rounded-xl p-6 text-center
                                border-slate-300 dark:border-slate-600
                                hover:border-blue-400 dark:hover:border-blue-500
                                transition-colors cursor-pointer
                                bg-slate-50 dark:bg-slate-900/30">

                        <input type="file" name="contract_pdf_path" id="contract_pdf_path" accept=".pdf,.doc,.docx"
                            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" />

                        <div id="upload-placeholder">
                            <div class="w-12 h-12 mx-auto rounded-xl
                                        bg-blue-50 dark:bg-blue-900/20
                                        flex items-center justify-center mb-3">
                                <i class="bi bi-cloud-arrow-up-fill
                                          text-blue-500 dark:text-blue-400 text-xl"></i>
                            </div>
                            <p class="text-sm font-medium text-slate-700 dark:text-slate-300">
                                Glissez votre fichier ici
                            </p>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                                ou <span class="text-blue-600 dark:text-blue-400 font-medium">
                                    cliquez pour parcourir
                                </span>
                            </p>
                            <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-2">
                                PDF, DOC, DOCX — max 5 Mo
                            </p>
                        </div>

                        {{-- Aperçu fichier sélectionné --}}
                        <div id="upload-preview" class="hidden">
                            <div class="w-12 h-12 mx-auto rounded-xl
                                        bg-red-50 dark:bg-red-900/20
                                        flex items-center justify-center mb-3">
                                <i class="bi bi-file-earmark-pdf-fill
                                          text-red-500 text-xl"></i>
                            </div>
                            <p id="upload-filename" class="text-sm font-semibold text-slate-800
                                      dark:text-slate-100 truncate mb-1"></p>
                            <p id="upload-filesize" class="text-xs text-slate-500 dark:text-slate-400"></p>
                            <button type="button" id="upload-clear" class="mt-3 text-xs text-red-500 hover:text-red-700
                                           dark:text-red-400 dark:hover:text-red-300
                                           transition-colors">
                                <i class="bi bi-x-circle"></i> Retirer
                            </button>
                        </div>
                    </div>

                    <x-forms.error name="contract_pdf_path" />
                </div>
            </div>

            {{-- ── Aperçu contrat ───────────────────────────── --}}
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200
                        dark:border-slate-700 shadow-sm overflow-hidden">

                <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-700
                            bg-slate-50/50 dark:bg-slate-800/50">
                    <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-100
                               flex items-center gap-2">
                        <i class="bi bi-eye-fill text-slate-400"></i>
                        Aperçu
                    </h3>
                </div>

                <div class="p-5 space-y-3">
                    <div class="flex items-center justify-between py-2
                                border-b border-slate-100 dark:border-slate-700">
                        <span class="text-xs text-slate-500 dark:text-slate-400">Enseignant</span>
                        <span id="preview-teacher" class="text-xs font-semibold text-slate-800 dark:text-slate-200">
                            —
                        </span>
                    </div>
                    <div class="flex items-center justify-between py-2
                                border-b border-slate-100 dark:border-slate-700">
                        <span class="text-xs text-slate-500 dark:text-slate-400">Type</span>
                        <span id="preview-type" class="text-xs font-semibold text-slate-800 dark:text-slate-200">
                            —
                        </span>
                    </div>
                    <div class="flex items-center justify-between py-2
                                border-b border-slate-100 dark:border-slate-700">
                        <span class="text-xs text-slate-500 dark:text-slate-400">Statut</span>
                        <span id="preview-status" class="text-xs font-semibold text-slate-800 dark:text-slate-200">
                            —
                        </span>
                    </div>
                    <div class="flex items-center justify-between py-2
                                border-b border-slate-100 dark:border-slate-700">
                        <span class="text-xs text-slate-500 dark:text-slate-400">Salaire</span>
                        <span id="preview-salary" class="text-xs font-semibold text-emerald-600 dark:text-emerald-400">
                            —
                        </span>
                    </div>
                    <div class="flex items-center justify-between py-2">
                        <span class="text-xs text-slate-500 dark:text-slate-400">Période</span>
                        <span id="preview-period" class="text-xs font-semibold text-slate-800 dark:text-slate-200">
                            —
                        </span>
                    </div>
                </div>
            </div>

            {{-- ── Aide ─────────────────────────────────────── --}}
            <div class="rounded-2xl p-5
                        bg-blue-50 dark:bg-blue-950/30
                        border border-blue-200 dark:border-blue-800">
                <div class="flex items-start gap-3">
                    <i class="bi bi-info-circle-fill text-blue-500 shrink-0 mt-0.5"></i>
                    <div class="space-y-2 text-xs text-blue-700 dark:text-blue-300">
                        <p class="font-semibold">Informations importantes</p>
                        <ul class="space-y-1 list-disc list-inside opacity-90">
                            <li>Un contrat <strong>actif</strong> ne peut pas être supprimé.</li>
                            <li>La date de fin est <strong>optionnelle</strong> pour les CDI.</li>
                            <li>Le document PDF ne dépasse pas <strong>5 Mo</strong>.</li>
                            <li>Le numéro de contrat doit être <strong>unique</strong>.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Boutons d'action ─────────────────────────────────────── --}}
    <div class="flex flex-col-reverse sm:flex-row items-center justify-between
                gap-3 mt-6 pt-6
                border-t border-slate-200 dark:border-slate-700">

        <a href="{{ route('teacher-contracts.index') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2
                  px-5 py-2.5 rounded-xl text-sm font-medium
                  text-slate-600 dark:text-slate-400
                  border border-slate-200 dark:border-slate-700
                  hover:bg-slate-100 dark:hover:bg-slate-700
                  transition-all duration-200">
            <i class="bi bi-x-lg"></i>
            Annuler
        </a>

        <div class="w-full sm:w-auto flex items-center gap-3">
            @if($isEdit)
            {{-- Enregistrer et continuer --}}
            <button type="submit" name="_action" value="save_continue" class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2
                           px-5 py-2.5 rounded-xl text-sm font-medium
                           bg-white dark:bg-slate-800
                           border border-slate-200 dark:border-slate-700
                           text-slate-700 dark:text-slate-300
                           hover:bg-slate-50 dark:hover:bg-slate-700 transition-all">
                <i class="bi bi-check-lg"></i>
                Enregistrer
            </button>
            @endif

            {{-- Soumettre principal --}}
            <button type="submit" id="submit-btn" class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2
                           px-6 py-2.5 rounded-xl text-sm font-semibold text-white
                           bg-blue-600 hover:bg-blue-700
                           shadow-sm hover:shadow-blue-500/25
                           transition-all duration-200">
                <i class="bi bi-{{ $isEdit ? 'arrow-repeat' : 'plus-circle-fill' }}"></i>
                {{ $isEdit ? 'Mettre à jour' : 'Créer le contrat' }}
            </button>
        </div>
    </div>

</x-forms.form>

{{-- ── Scripts ─────────────────────────────────────────────────── --}}
<script>
document.addEventListener('DOMContentLoaded', () => {

    // ── Flatpickr sur les dates ──────────────────────────────
    if (typeof flatpickr !== 'undefined') {
        const fpConfig = {
            dateFormat: 'Y-m-d',
            altInput: true,
            altFormat: 'd/m/Y',
            locale: 'fr',
            allowInput: true,
        };

        const startPicker = flatpickr('#id_start_date', {
            ...fpConfig,
            onChange(selectedDates) {
                if (endPicker && selectedDates[0]) {
                    endPicker.set('minDate', selectedDates[0]);
                }
                _updateDuration();
                _updatePreview();
            },
        });

        const endPicker = flatpickr('#id_end_date', {
            ...fpConfig,
            onChange() {
                _updateDuration();
                _updatePreview();
            },
        });
    } else {
        // Fallback : inputs natifs type date
        document.querySelectorAll('.flatpickr-date').forEach(input => {
            input.type = 'date';
        });
        document.querySelectorAll('.flatpickr-date').forEach(input => {
            input.addEventListener('change', () => {
                _updateDuration();
                _updatePreview();
            });
        });
    }

    // ── Cards radio — Type de contrat ───────────────────────
    _initRadioCards('#contract-type-group', (value) => {
        const labels = {
            permanent: 'Permanent (CDI)',
            temporary: 'Temporaire (CDD)',
            part_time: 'Temps partiel',
            internship: 'Stage',
        };
        document.getElementById('preview-type').textContent = labels[value] ?? '—';

        // CDI → masquer le champ end_date
        const endDateWrap = document.querySelector('[name="end_date"]')?.closest('.w-full');
        if (endDateWrap) {
            endDateWrap.style.opacity = value === 'permanent' ? '0.5' : '1';
        }
    });

    // ── Cards radio — Statut ────────────────────────────────
    _initRadioCards('#contract-status-group', (value) => {
        const labels = {
            active: 'Actif',
            expired: 'Expiré',
            terminated: 'Résilié',
        };
        document.getElementById('preview-status').textContent = labels[value] ?? '—';
    });

    // ── Aperçu enseignant ────────────────────────────────────
    const teacherSelect = document.querySelector('[name="teacher_id"]');
    teacherSelect?.addEventListener('change', () => {
        const option = teacherSelect.options[teacherSelect.selectedIndex];
        document.getElementById('preview-teacher').textContent =
            option?.text ?? '—';
    });

    // ── Aperçu salaire ───────────────────────────────────────
    const salaryInput = document.getElementById('salary');
    const salaryPreview = document.getElementById('salary-preview');
    const salaryFormatted = document.getElementById('salary-formatted');

    salaryInput?.addEventListener('input', () => {
        const val = parseFloat(salaryInput.value);
        if (!isNaN(val) && val > 0) {
            salaryPreview?.classList.remove('hidden');
            if (salaryFormatted) {
                salaryFormatted.textContent =
                    val.toLocaleString('fr-FR') + ' FCFA / mois';
            }
            document.getElementById('preview-salary').textContent =
                val.toLocaleString('fr-FR') + ' FCFA';
        } else {
            salaryPreview?.classList.add('hidden');
            document.getElementById('preview-salary').textContent = '—';
        }
    });

    // Déclencher si valeur pré-remplie
    salaryInput?.dispatchEvent(new Event('input'));

    // ── Upload fichier ───────────────────────────────────────
    const fileInput = document.getElementById('contract_pdf_path');
    const placeholder = document.getElementById('upload-placeholder');
    const preview = document.getElementById('upload-preview');
    const filename = document.getElementById('upload-filename');
    const filesize = document.getElementById('upload-filesize');
    const clearBtn = document.getElementById('upload-clear');
    const uploadZone = document.getElementById('upload-zone');

    fileInput?.addEventListener('change', () => {
        const file = fileInput.files[0];
        if (!file) return;

        // Validation taille (5 Mo)
        if (file.size > 5 * 1024 * 1024) {
            window.showToast?.({
                type: 'error',
                title: 'Fichier trop lourd',
                message: 'Le document ne doit pas dépasser 5 Mo.',
            });
            fileInput.value = '';
            return;
        }

        placeholder?.classList.add('hidden');
        preview?.classList.remove('hidden');
        if (filename) filename.textContent = file.name;
        if (filesize) filesize.textContent = _formatBytes(file.size);
    });

    clearBtn?.addEventListener('click', (e) => {
        e.stopPropagation();
        fileInput.value = '';
        preview?.classList.add('hidden');
        placeholder?.classList.remove('hidden');
    });

    // Drag & drop sur la zone upload
    uploadZone?.addEventListener('dragover', (e) => {
        e.preventDefault();
        uploadZone.classList.add('border-blue-500', 'bg-blue-50', 'dark:bg-blue-950/20');
    });
    uploadZone?.addEventListener('dragleave', () => {
        uploadZone.classList.remove('border-blue-500', 'bg-blue-50', 'dark:bg-blue-950/20');
    });
    uploadZone?.addEventListener('drop', (e) => {
        e.preventDefault();
        uploadZone.classList.remove('border-blue-500', 'bg-blue-50', 'dark:bg-blue-950/20');
        const file = e.dataTransfer.files[0];
        if (file) {
            const dt = new DataTransfer();
            dt.items.add(file);
            fileInput.files = dt.files;
            fileInput.dispatchEvent(new Event('change'));
        }
    });

    // ── Spinner sur submit ───────────────────────────────────
    document.querySelector('form')?.addEventListener('submit', () => {
        const btn = document.getElementById('submit-btn');
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = `
                <svg class="w-4 h-4 animate-spin" viewBox="0 0 24 24" fill="none">
                    <circle class="opacity-25" cx="12" cy="12" r="10"
                            stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                          d="M4 12a8 8 0 018-8v8H4z"></path>
                </svg>
                Enregistrement…
            `;
        }
    });

    // ────────────────────────────────────────────────────────
    // Fonctions internes
    // ────────────────────────────────────────────────────────

    function _initRadioCards(groupSelector, onChange) {
        const group = document.querySelector(groupSelector);
        if (!group) return;

        const labels = group.querySelectorAll('label');
        const radios = group.querySelectorAll('input[type="radio"]');

        radios.forEach(radio => {
            radio.addEventListener('change', () => {
                // Réinitialiser toutes les cards
                labels.forEach(label => {
                    label.classList.remove(
                        'border-blue-500', 'bg-blue-50', 'dark:bg-blue-900/20'
                    );
                    label.classList.add(
                        'border-slate-200', 'dark:border-slate-700'
                    );
                });

                // Activer la card sélectionnée
                const activeLabel = radio.closest('label');
                if (activeLabel) {
                    activeLabel.classList.add(
                        'border-blue-500', 'bg-blue-50', 'dark:bg-blue-900/20'
                    );
                    activeLabel.classList.remove(
                        'border-slate-200', 'dark:border-slate-700'
                    );
                }

                onChange?.(radio.value);
            });
        });
    }

    function _updateDuration() {
        const startVal = document.querySelector('[name="start_date"]')?.value;
        const endVal = document.querySelector('[name="end_date"]')?.value;
        const display = document.getElementById('duration-text');
        if (!display) return;

        if (!startVal) {
            display.textContent = 'Calculée automatiquement';
            return;
        }

        if (!endVal) {
            display.textContent = 'Sans terme défini (CDI)';
            return;
        }

        const start = new Date(startVal);
        const end = new Date(endVal);

        if (end <= start) {
            display.textContent = '⚠ La date de fin doit être après le début';
            display.style.color = '#ef4444';
            return;
        }

        display.style.color = '';
        const days = Math.ceil((end - start) / (1000 * 60 * 60 * 24));
        const months = Math.round(days / 30);
        display.textContent = `${days} jours (≈ ${months} mois)`;
    }

    function _updatePreview() {
        const startVal = document.querySelector('[name="start_date"]')?.value;
        const endVal = document.querySelector('[name="end_date"]')?.value;
        const preview = document.getElementById('preview-period');
        if (!preview) return;

        if (startVal && endVal) {
            const fmt = (d) => new Date(d).toLocaleDateString('fr-FR');
            preview.textContent = `${fmt(startVal)} → ${fmt(endVal)}`;
        } else if (startVal) {
            const fmt = (d) => new Date(d).toLocaleDateString('fr-FR');
            preview.textContent = `Dès le ${fmt(startVal)}`;
        } else {
            preview.textContent = '—';
        }
    }

    function _formatBytes(bytes) {
        if (bytes < 1024) return bytes + ' o';
        if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' Ko';
        return (bytes / (1024 * 1024)).toFixed(1) + ' Mo';
    }

    // Init aperçu initial si édition
    @if($isEdit)
    document.getElementById('preview-teacher').textContent =
        '{{ addslashes($contract->teacher->user->name ?? '—
    ') }}';
    _updateDuration();
    _updatePreview();
    @endif
});
</script>
@endsection