@extends('layouts.base')

@section('page_title', $feeType->exists ? 'Modifier le type de frais' : 'Nouveau type de frais')

@section('breadcrumb')
<span class="text-slate-300 dark:text-slate-600 font-light select-none">/</span>
<a href="{{ route('fee-types.index') }}"
    class="text-slate-400 dark:text-slate-500 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
    Types de frais
</a>
@endsection

@section('page_header')
<div class="flex items-center justify-between gap-4">
    <div class="flex items-center gap-3 min-w-0">
        <div class="w-10 h-10 rounded-xl bg-linear-to-br from-blue-600 to-emerald-500
                        flex items-center justify-center shadow-sm shrink-0">
            <i class="bi bi-tag-fill text-white text-lg"></i>
        </div>
        <div class="min-w-0">
            <h1 class="text-lg font-bold text-slate-800 dark:text-slate-100 truncate">
                {{ $feeType->exists ? 'Modifier : ' . $feeType->name : 'Nouveau type de frais' }}
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400">
                {{ $feeType->exists
                        ? 'Modifiez les informations de ce type de frais'
                        : 'Créez un nouveau type de frais scolaires' }}
            </p>
        </div>
    </div>
    <a href="{{ route('fee-types.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-medium
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
<x-forms.form :model="$feeType" resource="fee-types" class="space-y-6 max-w-4xl mx-auto">

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

                    {{-- Nom --}}
                    <x-forms.input-field name="name" label="Nom du type de frais" type="text"
                        :value="old('name', $feeType->name)" placeholder="Ex : Frais de scolarité, Frais d'inscription…"
                        icon="bi-tag" required />

                    {{-- Montant + Fréquence --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

                        {{-- Montant --}}
                        <div class="space-y-1.5">
                            <label for="amount" class="flex items-center gap-1.5 text-sm font-medium
                                          text-slate-700 dark:text-slate-200">
                                <i class="bi bi-cash text-slate-400"></i>
                                Montant
                                <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="number" name="amount" id="amount" min="0" step="500"
                                    value="{{ old('amount', $feeType->amount) }}" placeholder="0"
                                    class="w-full pl-3.5 pr-20 py-2.5 rounded-xl border text-sm font-semibold
                                              text-slate-800 dark:text-slate-100
                                              bg-white dark:bg-slate-700/50 placeholder-slate-400
                                              focus:outline-none focus:ring-2 transition-all duration-200
                                              {{ $errors->has('amount')
                                                  ? 'border-red-500 focus:ring-red-500/40'
                                                  : 'border-slate-200 dark:border-slate-600 focus:ring-blue-500/30 focus:border-blue-500' }}" required>
                                <span class="absolute inset-y-0 right-0 flex items-center px-3
                                             text-xs font-semibold text-slate-500 dark:text-slate-400
                                             bg-slate-50 dark:bg-slate-700
                                             border-l border-slate-200 dark:border-slate-600
                                             rounded-r-xl pointer-events-none select-none">
                                    FCFA
                                </span>
                            </div>
                            @error('amount')
                            <p class="mt-1.5 text-xs text-red-600 dark:text-red-400 flex items-center gap-1">
                                <i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
                            </p>
                            @enderror
                        </div>

                        {{-- Fréquence --}}
                        <x-forms.select name="frequency" label="Fréquence" icon="bi-arrow-repeat" :options="[
                                'monthly'   => 'Mensuel',
                                'quarterly' => 'Trimestriel',
                                'yearly'    => 'Annuel',
                                'one_time'  => 'Unique',
                            ]" :value="old('frequency', $feeType->frequency)" placeholder="Sélectionner une fréquence…"
                            required />
                    </div>

                    {{-- Description --}}
                    <x-forms.textarea name="description" label="Description"
                        :value="old('description', $feeType->description)" placeholder="Décrivez ce type de frais…"
                        rows="4" help="Optionnel. Maximum 500 caractères." />

                </div>
            </div>

            {{-- Statut --}}
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200
                        dark:border-slate-700 shadow-sm overflow-hidden">

                <div class="flex items-center gap-3 px-6 py-4
                            border-b border-slate-100 dark:border-slate-700
                            bg-slate-50/60 dark:bg-slate-700/30">
                    <div class="w-7 h-7 rounded-lg bg-emerald-100 dark:bg-emerald-900/40
                                flex items-center justify-center">
                        <i class="bi bi-toggle-on text-emerald-600 dark:text-emerald-400 text-sm"></i>
                    </div>
                    <h2 class="text-sm font-semibold text-slate-700 dark:text-slate-200">
                        Statut
                    </h2>
                </div>

                <div class="p-6">
                    <label class="flex items-center gap-4 px-4 py-4 rounded-xl border-2 cursor-pointer
                                  transition-all duration-200
                                  {{ old('is_active', $feeType->is_active ?? true)
                                      ? 'border-emerald-400 dark:border-emerald-600 bg-emerald-50 dark:bg-emerald-900/20'
                                      : 'border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700/50' }}"
                        id="status-toggle-label">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" id="is_active" value="1"
                            {{ old('is_active', $feeType->is_active ?? true) ? 'checked' : '' }} class="sr-only">

                        {{-- Toggle visuel --}}
                        <div id="toggle-track" class="relative w-11 h-6 rounded-full transition-colors duration-200 shrink-0
                                    {{ old('is_active', $feeType->is_active ?? true)
                                        ? 'bg-emerald-500'
                                        : 'bg-slate-300 dark:bg-slate-600' }}">
                            <div id="toggle-thumb" class="absolute top-0.5 h-5 w-5 rounded-full bg-white shadow
                                        transition-transform duration-200
                                        {{ old('is_active', $feeType->is_active ?? true)
                                            ? 'translate-x-5'
                                            : 'translate-x-0.5' }}">
                            </div>
                        </div>

                        <div class="min-w-0 flex-1">
                            <p id="toggle-label" class="text-sm font-semibold
                                      {{ old('is_active', $feeType->is_active ?? true)
                                          ? 'text-emerald-700 dark:text-emerald-400'
                                          : 'text-slate-600 dark:text-slate-400' }}">
                                {{ old('is_active', $feeType->is_active ?? true) ? 'Type de frais actif' : 'Type de frais inactif' }}
                            </p>
                            <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">
                                {{ old('is_active', $feeType->is_active ?? true)
                                    ? 'Ce type sera disponible lors des enregistrements de paiements'
                                    : 'Ce type ne sera pas proposé lors des paiements' }}
                            </p>
                        </div>
                    </label>
                </div>
            </div>

        </div>

        {{-- ── Colonne latérale ── --}}
        <div class="space-y-6">

            {{-- Aperçu carte --}}
            <div class="bg-linear-to-br from-blue-600 to-emerald-500 rounded-2xl p-5 shadow-sm">
                <p class="text-[10px] font-semibold text-blue-200 uppercase tracking-widest mb-4">
                    Aperçu de la carte
                </p>

                <div class="space-y-4">
                    {{-- Badge fréquence --}}
                    <div class="flex items-center justify-between">
                        <span id="preview-freq-badge" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full
                                     text-[10px] font-semibold bg-white/20 text-white">
                            <i class="bi bi-arrow-repeat text-[10px]"></i>
                            <span id="preview-freq-label">—</span>
                        </span>
                        <span id="preview-status-badge" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full
                                     text-[10px] font-semibold bg-emerald-400/20 text-emerald-100">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-300"></span>
                            Actif
                        </span>
                    </div>

                    {{-- Nom --}}
                    <div>
                        <p id="preview-name" class="font-bold text-white text-lg leading-tight truncate">
                            {{ $feeType->name ?: 'Nom du type de frais' }}
                        </p>
                        <p id="preview-description"
                            class="text-xs text-blue-200 mt-1 line-clamp-2 leading-relaxed min-h-4">
                            {{ $feeType->description ?: '' }}
                        </p>
                    </div>

                    {{-- Montant --}}
                    <div class="bg-white/15 backdrop-blur-sm rounded-xl px-4 py-3">
                        <p class="text-[10px] text-blue-200 uppercase tracking-wide mb-0.5">
                            Montant
                        </p>
                        <p class="text-2xl font-extrabold text-white leading-none">
                            <span id="preview-amount">
                                {{ $feeType->amount
                                    ? number_format($feeType->amount, 0, ',', ' ')
                                    : '0' }}
                            </span>
                            <span class="text-sm font-normal text-blue-200 ml-1">FCFA</span>
                        </p>
                    </div>
                </div>
            </div>

            {{-- Fréquences disponibles --}}
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200
                        dark:border-slate-700 shadow-sm p-5">
                <p class="text-xs font-semibold text-slate-500 dark:text-slate-400
                           uppercase tracking-wide mb-3">
                    Fréquences disponibles
                </p>
                <div class="space-y-2">
                    @foreach([
                    ['value' => 'monthly', 'label' => 'Mensuel', 'icon' => 'bi-calendar-month', 'desc' => 'Prélevé
                    chaque mois'],
                    ['value' => 'quarterly', 'label' => 'Trimestriel', 'icon' => 'bi-calendar3', 'desc' => 'Prélevé par
                    trimestre'],
                    ['value' => 'yearly', 'label' => 'Annuel', 'icon' => 'bi-calendar-year', 'desc' => 'Prélevé une fois
                    par an'],
                    ['value' => 'one_time', 'label' => 'Unique', 'icon' => 'bi-1-circle', 'desc' => 'Paiement unique'],
                    ] as $freq)
                    <div class="freq-info-item flex items-center gap-3 px-3 py-2 rounded-xl
                                transition-all duration-200
                                {{ old('frequency', $feeType->frequency) === $freq['value']
                                    ? 'bg-blue-50 dark:bg-blue-950/30 border border-blue-200 dark:border-blue-800'
                                    : 'hover:bg-slate-50 dark:hover:bg-slate-700/30' }}"
                        data-value="{{ $freq['value'] }}">
                        <div class="w-7 h-7 rounded-lg bg-slate-100 dark:bg-slate-700
                                    flex items-center justify-center shrink-0">
                            <i class="bi {{ $freq['icon'] }}
                                      text-slate-500 dark:text-slate-400 text-xs"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-semibold text-slate-700 dark:text-slate-300">
                                {{ $freq['label'] }}
                            </p>
                            <p class="text-[10px] text-slate-400 dark:text-slate-500">
                                {{ $freq['desc'] }}
                            </p>
                        </div>
                        <i class="bi bi-check-circle-fill text-blue-500 text-sm ml-auto shrink-0
                                  {{ old('frequency', $feeType->frequency) === $freq['value'] ? '' : 'hidden' }}"
                            data-check="{{ $freq['value'] }}"></i>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Actions --}}
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200
                        dark:border-slate-700 shadow-sm px-5 py-4 flex items-center justify-between gap-3">
                <a href="{{ route('fee-types.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-medium
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
                    <i class="bi {{ $feeType->exists ? 'bi-check-lg' : 'bi-plus-lg' }}"></i>
                    {{ $feeType->exists ? 'Enregistrer' : 'Créer' }}
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
    const amountInput = document.getElementById('amount');
    const freqSelect = document.querySelector('[name="frequency"]');
    const descTA = document.querySelector('[name="description"]');
    const checkbox = document.getElementById('is_active');
    const label = document.getElementById('status-toggle-label');
    const track = document.getElementById('toggle-track');
    const thumb = document.getElementById('toggle-thumb');
    const toggleLabel = document.getElementById('toggle-label');

    // Aperçu
    const previewName = document.getElementById('preview-name');
    const previewAmount = document.getElementById('preview-amount');
    const previewFreq = document.getElementById('preview-freq-label');
    const previewDesc = document.getElementById('preview-description');
    const previewStatus = document.getElementById('preview-status-badge');

    const freqLabels = {
        monthly: 'Mensuel',
        quarterly: 'Trimestriel',
        yearly: 'Annuel',
        one_time: 'Unique',
    };

    // ── Mise à jour aperçu ────────────────────────────────────────
    function updatePreview() {
        if (previewName) {
            previewName.textContent = nameInput?.value || 'Nom du type de frais';
        }

        if (previewAmount) {
            const val = parseInt(amountInput?.value || 0);
            previewAmount.textContent = val.toLocaleString('fr-FR');
        }

        if (previewFreq) {
            previewFreq.textContent = freqLabels[freqSelect?.value] || '—';
        }

        if (previewDesc) {
            previewDesc.textContent = descTA?.value || '';
        }
    }

    nameInput?.addEventListener('input', updatePreview);
    amountInput?.addEventListener('input', updatePreview);
    freqSelect?.addEventListener('change', () => {
        updatePreview();
        updateFreqHighlight();
    });
    descTA?.addEventListener('input', updatePreview);

    // ── Highlight fréquence sélectionnée ─────────────────────────
    function updateFreqHighlight() {
        const val = freqSelect?.value;

        document.querySelectorAll('.freq-info-item').forEach(item => {
            const isSelected = item.dataset.value === val;
            const check = item.querySelector('[data-check]');

            // Reset
            item.classList.remove(
                'bg-blue-50', 'dark:bg-blue-950/30',
                'border', 'border-blue-200', 'dark:border-blue-800'
            );
            item.classList.add('hover:bg-slate-50', 'dark:hover:bg-slate-700/30');

            if (isSelected) {
                item.classList.remove('hover:bg-slate-50', 'dark:hover:bg-slate-700/30');
                item.classList.add(
                    'bg-blue-50', 'dark:bg-blue-950/30',
                    'border', 'border-blue-200', 'dark:border-blue-800'
                );
                check?.classList.remove('hidden');
            } else {
                check?.classList.add('hidden');
            }
        });
    }

    // ── Toggle statut ─────────────────────────────────────────────
    label?.addEventListener('click', () => {
        const isChecked = !checkbox.checked;
        checkbox.checked = isChecked;

        // Track
        track?.classList.toggle('bg-emerald-500', isChecked);
        track?.classList.toggle('bg-slate-300', !isChecked);
        track?.classList.toggle('dark:bg-slate-600', !isChecked);

        // Thumb
        thumb?.classList.toggle('translate-x-5', isChecked);
        thumb?.classList.toggle('translate-x-0.5', !isChecked);

        // Label wrapper
        label?.classList.toggle('border-emerald-400', isChecked);
        label?.classList.toggle('dark:border-emerald-600', isChecked);
        label?.classList.toggle('bg-emerald-50', isChecked);
        label?.classList.toggle('dark:bg-emerald-900/20', isChecked);
        label?.classList.toggle('border-slate-200', !isChecked);
        label?.classList.toggle('dark:border-slate-600', !isChecked);
        label?.classList.toggle('bg-white', !isChecked);
        label?.classList.toggle('dark:bg-slate-700/50', !isChecked);

        // Texte label
        if (toggleLabel) {
            toggleLabel.textContent = isChecked ? 'Type de frais actif' : 'Type de frais inactif';
            toggleLabel.className = `text-sm font-semibold ${
                isChecked
                    ? 'text-emerald-700 dark:text-emerald-400'
                    : 'text-slate-600 dark:text-slate-400'
            }`;
        }

        // Badge aperçu
        if (previewStatus) {
            previewStatus.innerHTML = isChecked ?
                '<span class="w-1.5 h-1.5 rounded-full bg-emerald-300"></span> Actif' :
                '<span class="w-1.5 h-1.5 rounded-full bg-white/50"></span> Inactif';
            previewStatus.className = `inline-flex items-center gap-1 px-2 py-0.5 rounded-full
                text-[10px] font-semibold ${
                    isChecked
                        ? 'bg-emerald-400/20 text-emerald-100'
                        : 'bg-white/10 text-white/70'
                }`;
        }
    });

    // ── Init ──────────────────────────────────────────────────────
    updatePreview();
    updateFreqHighlight();
})();
</script>
@endpush