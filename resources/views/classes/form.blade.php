@extends('layouts.base')

@section('page_title', $classe->exists ? 'Modifier la classe' : 'Nouvelle classe')

@section('breadcrumb')
<span class="text-slate-300 dark:text-slate-600 font-light select-none">/</span>
<a href="{{ route('classes.index') }}"
    class="text-slate-400 dark:text-slate-500 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
    Classes
</a>
@endsection

@section('page_header')
<div class="flex items-center justify-between gap-4">
    <div class="flex items-center gap-3 min-w-0">
        <div class="w-10 h-10 rounded-xl bg-linear-to-br from-blue-600 to-emerald-500
                        flex items-center justify-center shadow-sm shrink-0">
            <i class="bi bi-building text-white text-lg"></i>
        </div>
        <div class="min-w-0">
            <h1 class="text-lg font-bold text-slate-800 dark:text-slate-100 truncate">
                {{ $classe->exists ? 'Modifier : ' . $classe->name : 'Nouvelle classe' }}
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400">
                {{ $classe->exists
                        ? 'Modifiez les informations de cette classe'
                        : 'Créez une nouvelle classe pour l\'année en cours' }}
            </p>
        </div>
    </div>
    <a href="{{ route('classes.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-medium
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
<x-forms.form :model="$classe" resource="classes" class="space-y-6 max-w-4xl mx-auto">

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ── Colonne principale ── --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Informations générales --}}
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200
                        dark:border-slate-700 shadow-sm overflow-hidden">

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

                    {{-- Nom + Niveau --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <x-forms.input-field name="name" label="Nom de la classe" type="text"
                            :value="old('name', $classe->name)" placeholder="Ex : Terminale A, Licence 3…"
                            icon="bi-building" required />

                        <x-forms.input-field name="level" label="Niveau" type="text"
                            :value="old('level', $classe->level)" placeholder="Ex : Lycée, Licence, Master…"
                            icon="bi-bar-chart-steps" required />
                    </div>

                    {{-- Capacité --}}
                    <div class="space-y-1.5">
                        <label for="capacity" class="flex items-center gap-1.5 text-sm font-medium
                                      text-slate-700 dark:text-slate-200">
                            <i class="bi bi-people text-slate-400"></i>
                            Capacité maximale
                            <span class="text-red-500">*</span>
                        </label>
                        <div class="flex items-center gap-4">
                            {{-- Slider --}}
                            <input type="range" id="capacity_range" min="5" max="100" step="5"
                                value="{{ old('capacity', $classe->capacity ?? 30) }}" class="flex-1 h-2 rounded-full accent-blue-600
                                          bg-slate-200 dark:bg-slate-700 cursor-pointer">
                            {{-- Valeur --}}
                            <div class="relative shrink-0">
                                <input type="number" name="capacity" id="capacity" min="5" max="100"
                                    value="{{ old('capacity', $classe->capacity ?? 30) }}" class="w-24 px-3 py-2.5 rounded-xl border text-sm text-center font-bold
                                              text-slate-800 dark:text-slate-100
                                              bg-white dark:bg-slate-700/50
                                              border-slate-200 dark:border-slate-600
                                              focus:outline-none focus:ring-2 focus:ring-blue-500/30
                                              focus:border-blue-500 transition-all duration-200
                                              {{ $errors->has('capacity') ? 'border-red-500' : '' }}" required>
                                <span class="absolute inset-y-0 right-0 flex items-center pr-2.5
                                             text-[10px] text-slate-400 pointer-events-none">
                                    élèves
                                </span>
                            </div>
                        </div>

                        {{-- Barre visuelle occupation --}}
                        @if($classe->exists)
                        @php
                        $occupied = $classe->students_count ?? 0;
                        $cap = $classe->capacity ?? 1;
                        $pct = min(100, round(($occupied / $cap) * 100));
                        $barColor = $pct >= 90
                        ? 'bg-red-500'
                        : ($pct >= 70 ? 'bg-amber-500' : 'bg-emerald-500');
                        @endphp
                        <div class="mt-2 space-y-1">
                            <div class="flex items-center justify-between text-xs text-slate-500 dark:text-slate-400">
                                <span>Occupation actuelle</span>
                                <span class="font-semibold">{{ $occupied }} / {{ $cap }} ({{ $pct }}%)</span>
                            </div>
                            <div class="h-2 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
                                <div class="h-full {{ $barColor }} rounded-full transition-all duration-500"
                                    style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                        @endif

                        @error('capacity')
                        <p class="text-xs text-red-600 dark:text-red-400 flex items-center gap-1">
                            <i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
                        </p>
                        @enderror
                    </div>

                    {{-- Description --}}
                    <x-forms.textarea name="description" label="Description"
                        :value="old('description', $classe->description)"
                        placeholder="Informations complémentaires sur cette classe…" rows="3"
                        help="Optionnel. Maximum 500 caractères." />

                </div>
            </div>

            {{-- Année académique --}}
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200
                        dark:border-slate-700 shadow-sm overflow-hidden">

                <div class="flex items-center gap-3 px-6 py-4
                            border-b border-slate-100 dark:border-slate-700
                            bg-slate-50/60 dark:bg-slate-700/30">
                    <div class="w-7 h-7 rounded-lg bg-emerald-100 dark:bg-emerald-900/40
                                flex items-center justify-center">
                        <i class="bi bi-mortarboard-fill text-emerald-600 dark:text-emerald-400 text-sm"></i>
                    </div>
                    <h2 class="text-sm font-semibold text-slate-700 dark:text-slate-200">
                        Année académique
                    </h2>
                </div>

                <div class="p-6">
                    <x-forms.select name="academic_year_id" label="Année académique" icon="bi-calendar-check"
                        :options="$academicYears" option-value="id" option-label="name"
                        :value="old('academic_year_id', $classe->academic_year_id ?? $activeYear?->id)"
                        placeholder="Sélectionner une année…" required />

                    @if($activeYear)
                    <div class="mt-3 flex items-center gap-2 px-3 py-2.5 rounded-xl
                                bg-blue-50 dark:bg-blue-950/30
                                border border-blue-100 dark:border-blue-900/50">
                        <i class="bi bi-info-circle text-blue-500 text-sm shrink-0"></i>
                        <p class="text-xs text-blue-700 dark:text-blue-400">
                            Année active : <span class="font-semibold">{{ $activeYear->name }}</span>
                        </p>
                    </div>
                    @endif
                </div>
            </div>

        </div>

        {{-- ── Colonne latérale ── --}}
        <div class="space-y-6">

            {{-- Aperçu carte classe --}}
            <div class="bg-linear-to-br from-blue-600 to-emerald-500 rounded-2xl p-5 shadow-sm">
                <p class="text-[10px] font-semibold text-blue-200 uppercase tracking-widest mb-4">
                    Aperçu
                </p>

                {{-- Icône + Nom --}}
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-12 h-12 rounded-xl bg-white/20 backdrop-blur-sm
                                flex items-center justify-center shrink-0">
                        <i class="bi bi-building text-white text-xl"></i>
                    </div>
                    <div class="min-w-0">
                        <p id="preview-name" class="font-bold text-white text-base truncate leading-tight">
                            {{ $classe->name ?: 'Nom de la classe' }}
                        </p>
                        <p id="preview-level" class="text-xs text-blue-200 mt-0.5 truncate">
                            {{ $classe->level ?: 'Niveau' }}
                        </p>
                    </div>
                </div>

                {{-- Stats --}}
                <div class="grid grid-cols-2 gap-3">
                    <div class="bg-white/15 backdrop-blur-sm rounded-xl px-3 py-2.5">
                        <p class="text-[10px] text-blue-200 uppercase tracking-wide mb-0.5">
                            Capacité
                        </p>
                        <p class="text-xl font-extrabold text-white leading-none">
                            <span id="preview-capacity">
                                {{ $classe->capacity ?? 30 }}
                            </span>
                            <span class="text-xs font-normal text-blue-200 ml-0.5">élèves</span>
                        </p>
                    </div>

                    <div class="bg-white/15 backdrop-blur-sm rounded-xl px-3 py-2.5">
                        <p class="text-[10px] text-blue-200 uppercase tracking-wide mb-0.5">
                            Inscrits
                        </p>
                        <p class="text-xl font-extrabold text-white leading-none">
                            {{ $classe->students_count ?? 0 }}
                            <span class="text-xs font-normal text-blue-200 ml-0.5">élèves</span>
                        </p>
                    </div>
                </div>

                {{-- Barre capacité --}}
                @if($classe->exists)
                <div class="mt-4 space-y-1.5">
                    <div class="flex items-center justify-between text-[10px] text-blue-200">
                        <span>Taux d'occupation</span>
                        <span id="preview-pct" class="font-bold text-white">{{ $pct ?? 0 }}%</span>
                    </div>
                    <div class="h-2 bg-white/20 rounded-full overflow-hidden">
                        <div id="preview-bar" class="h-full bg-white rounded-full transition-all duration-500"
                            style="width: {{ $pct ?? 0 }}%"></div>
                    </div>
                </div>
                @endif
            </div>

            {{-- Indicateur capacité --}}
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200
                        dark:border-slate-700 shadow-sm p-5">
                <p class="text-xs font-semibold text-slate-500 dark:text-slate-400
                           uppercase tracking-wide mb-4">
                    Indicateurs capacité
                </p>
                <div class="space-y-3">
                    @foreach([
                    ['min' => 5, 'max' => 20, 'label' => 'Petite classe', 'color' => 'emerald', 'icon' => 'bi-people'],
                    ['min' => 21, 'max' => 40, 'label' => 'Classe moyenne', 'color' => 'blue', 'icon' =>
                    'bi-people-fill'],
                    ['min' => 41, 'max' => 70, 'label' => 'Grande classe', 'color' => 'amber', 'icon' =>
                    'bi-diagram-3'],
                    ['min' => 71, 'max' => 100, 'label' => 'Très grande', 'color' => 'red', 'icon' =>
                    'bi-diagram-3-fill'],
                    ] as $indicator)
                    @php
                    $cap = old('capacity', $classe->capacity ?? 30);
                    $isActive = $cap >= $indicator['min'] && $cap <= $indicator['max']; @endphp <div class="capacity-indicator flex items-center gap-3 px-3 py-2 rounded-xl
                                transition-all duration-200
                                {{ $isActive
                                    ? 'bg-' . $indicator['color'] . '-50 dark:bg-' . $indicator['color'] . '-900/20 border border-' . $indicator['color'] . '-200 dark:border-' . $indicator['color'] . '-800'
                                    : '' }}" data-min="{{ $indicator['min'] }}" data-max="{{ $indicator['max'] }}"
                        data-color="{{ $indicator['color'] }}">
                        <div class="w-7 h-7 rounded-lg shrink-0
                                    {{ $isActive
                                        ? 'bg-' . $indicator['color'] . '-100 dark:bg-' . $indicator['color'] . '-900/40'
                                        : 'bg-slate-100 dark:bg-slate-700' }}
                                    flex items-center justify-center">
                            <i class="bi {{ $indicator['icon'] }} text-xs
                                      {{ $isActive
                                          ? 'text-' . $indicator['color'] . '-600 dark:text-' . $indicator['color'] . '-400'
                                          : 'text-slate-400 dark:text-slate-500' }}"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-semibold
                                      {{ $isActive
                                          ? 'text-' . $indicator['color'] . '-700 dark:text-' . $indicator['color'] . '-300'
                                          : 'text-slate-600 dark:text-slate-400' }}">
                                {{ $indicator['label'] }}
                            </p>
                            <p class="text-[10px] text-slate-400 dark:text-slate-500">
                                {{ $indicator['min'] }} – {{ $indicator['max'] }} élèves
                            </p>
                        </div>
                        <i class="bi bi-check-circle-fill ml-auto text-sm shrink-0
                                  text-{{ $indicator['color'] }}-500
                                  {{ $isActive ? '' : 'hidden' }}" data-check="{{ $indicator['min'] }}"></i>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Actions --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200
                        dark:border-slate-700 shadow-sm px-5 py-4
                        flex items-center justify-between gap-3">
            <a href="{{ route('classes.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-medium
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
                <i class="bi {{ $classe->exists ? 'bi-check-lg' : 'bi-plus-lg' }}"></i>
                {{ $classe->exists ? 'Enregistrer' : 'Créer la classe' }}
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
    const nameInput = document.querySelector('[name="name"]');
    const levelInput = document.querySelector('[name="level"]');
    const capInput = document.getElementById('capacity');
    const capRange = document.getElementById('capacity_range');

    const previewName = document.getElementById('preview-name');
    const previewLevel = document.getElementById('preview-level');
    const previewCap = document.getElementById('preview-capacity');

    // ── Sync slider ↔ input ───────────────────────────────────────
    capRange?.addEventListener('input', () => {
        if (capInput) capInput.value = capRange.value;
        updateCapacityUI(parseInt(capRange.value));
    });

    capInput?.addEventListener('input', () => {
        const val = Math.min(100, Math.max(5, parseInt(capInput.value) || 5));
        capInput.value = val;
        if (capRange) capRange.value = val;
        updateCapacityUI(val);
    });

    // ── Aperçu nom + niveau ───────────────────────────────────────
    nameInput?.addEventListener('input', () => {
        if (previewName) {
            previewName.textContent = nameInput.value || 'Nom de la classe';
        }
    });

    levelInput?.addEventListener('input', () => {
        if (previewLevel) {
            previewLevel.textContent = levelInput.value || 'Niveau';
        }
    });

    // ── Mise à jour indicateurs capacité ─────────────────────────
    function updateCapacityUI(val) {
        // Aperçu
        if (previewCap) previewCap.textContent = val;

        // Indicateurs
        document.querySelectorAll('.capacity-indicator').forEach(item => {
            const min = parseInt(item.dataset.min);
            const max = parseInt(item.dataset.max);
            const color = item.dataset.color;
            const check = item.querySelector('[data-check]');
            const icon = item.querySelector('i.bi:not([data-check])');
            const texts = item.querySelectorAll('p');
            const isActive = val >= min && val <= max;

            // Reset classes de l'item
            item.className = item.className
                .replace(/bg-\w+-\d+|dark:bg-\w+-\d+\/\d+/g, '')
                .replace(/border\s+border-\w+-\d+|dark:border-\w+-\d+/g, '')
                .trim();

            item.classList.add(
                'capacity-indicator', 'flex', 'items-center', 'gap-3',
                'px-3', 'py-2', 'rounded-xl', 'transition-all', 'duration-200'
            );

            if (isActive) {
                item.classList.add(
                    `bg-${color}-50`, `dark:bg-${color}-900/20`,
                    'border', `border-${color}-200`, `dark:border-${color}-800`
                );
            }

            // Icône container
            const iconContainer = icon?.parentElement;
            if (iconContainer) {
                iconContainer.className = `w-7 h-7 rounded-lg shrink-0 flex items-center justify-center ${
                    isActive
                        ? `bg-${color}-100 dark:bg-${color}-900/40`
                        : 'bg-slate-100 dark:bg-slate-700'
                }`;
            }

            // Icône
            if (icon) {
                icon.className = icon.className
                    .replace(/text-\w+-\d+|dark:text-\w+-\d+/g, '')
                    .trim();
                icon.classList.add(
                    'text-xs',
                    ...(isActive ?
                        [`text-${color}-600`, `dark:text-${color}-400`] :
                        ['text-slate-400', 'dark:text-slate-500'])
                );
            }

            // Texte label
            if (texts[0]) {
                texts[0].className = `text-xs font-semibold ${
                    isActive
                        ? `text-${color}-700 dark:text-${color}-300`
                        : 'text-slate-600 dark:text-slate-400'
                }`;
            }

            // Check
            if (check) {
                check.className = `bi bi-check-circle-fill ml-auto text-sm shrink-0 text-${color}-500 ${
                    isActive ? '' : 'hidden'
                }`;
            }
        });
    }

    // ── Init ──────────────────────────────────────────────────────
    updateCapacityUI(parseInt(capInput?.value || 30));
})();
</script>
@endpush