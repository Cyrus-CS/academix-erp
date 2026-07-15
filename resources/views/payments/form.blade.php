@extends('layouts.base')

@section('page_title', $payment->exists ? 'Modifier le paiement' : 'Enregistrer un paiement')

@section('breadcrumb')
<span class="text-slate-300 dark:text-slate-600 font-light select-none">/</span>
<a href="{{ route('payments.index') }}"
    class="text-slate-400 dark:text-slate-500 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
    Paiements
</a>
@endsection

@section('page_header')
<div class="flex items-center justify-between gap-4">
    <div class="flex items-center gap-3 min-w-0">
        <div class="w-10 h-10 rounded-xl bg-linear-to-br from-blue-600 to-emerald-500
                        flex items-center justify-center shadow-sm shrink-0">
            <i class="bi bi-credit-card-fill text-white text-lg"></i>
        </div>
        <div class="min-w-0">
            <h1 class="text-lg font-bold text-slate-800 dark:text-slate-100 truncate">
                {{ $payment->exists ? 'Modifier le paiement' : 'Enregistrer un paiement' }}
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400">
                {{ $payment->exists
                        ? 'Réf : ' . $payment->transaction_reference
                        : 'Saisissez les informations du nouveau paiement' }}
            </p>
        </div>
    </div>
    <a href="{{ route('payments.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-medium
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
<x-forms.form :model="$payment" resource="payments" class="space-y-6 max-w-4xl mx-auto">

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ── Colonne principale ── --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Étudiant & Frais --}}
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
                        Étudiant & Type de frais
                    </h2>
                </div>

                <div class="p-6 space-y-5">

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
                                <i class="bi bi-search text-slate-400"></i>
                            </span>
                            <select name="student_id" id="student_id" required
                                class="w-full pl-10 pr-9 py-2.5 rounded-xl border text-sm
                                           text-slate-800 dark:text-slate-100
                                           bg-white dark:bg-slate-700/50 appearance-none
                                           focus:outline-none focus:ring-2 transition-all duration-200
                                           {{ $errors->has('student_id')
                                               ? 'border-red-500 focus:ring-red-500/40'
                                               : 'border-slate-200 dark:border-slate-600 focus:ring-blue-500/30 focus:border-blue-500' }}">
                                <option value="">Sélectionner un étudiant…</option>
                                @foreach($students as $student)
                                <option value="{{ $student->id }}"
                                    {{ old('student_id', $payment->student_id) == $student->id ? 'selected' : '' }}>
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

                    {{-- Type de frais --}}
                    <div class="space-y-1.5">
                        <label for="fee_type_id" class="flex items-center gap-1.5 text-sm font-medium
                                      text-slate-700 dark:text-slate-200">
                            <i class="bi bi-tag text-slate-400"></i>
                            Type de frais
                            <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none z-10">
                                <i class="bi bi-tag text-slate-400"></i>
                            </span>
                            <select name="fee_type_id" id="fee_type_id" required
                                class="w-full pl-10 pr-9 py-2.5 rounded-xl border text-sm
                                           text-slate-800 dark:text-slate-100
                                           bg-white dark:bg-slate-700/50 appearance-none
                                           focus:outline-none focus:ring-2 transition-all duration-200
                                           {{ $errors->has('fee_type_id')
                                               ? 'border-red-500 focus:ring-red-500/40'
                                               : 'border-slate-200 dark:border-slate-600 focus:ring-blue-500/30 focus:border-blue-500' }}">
                                <option value="">Sélectionner un type de frais…</option>
                                @foreach($feeTypes as $feeType)
                                <option value="{{ $feeType->id }}" data-amount="{{ $feeType->amount }}"
                                    {{ old('fee_type_id', $payment->fee_type_id) == $feeType->id ? 'selected' : '' }}>
                                    {{ $feeType->name }} — {{ number_format($feeType->amount, 0, ',', ' ') }} FCFA
                                </option>
                                @endforeach
                            </select>
                            <span class="absolute inset-y-0 right-0 flex items-center pr-3.5 pointer-events-none">
                                <i class="bi bi-chevron-down text-slate-400 text-xs"></i>
                            </span>
                        </div>
                        @error('fee_type_id')
                        <p class="mt-1.5 text-xs text-red-600 dark:text-red-400 flex items-center gap-1">
                            <i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
                        </p>
                        @enderror
                    </div>

                    {{-- Montant --}}
                    <div class="space-y-1.5">
                        <label for="amount" class="flex items-center gap-1.5 text-sm font-medium
                                      text-slate-700 dark:text-slate-200">
                            <i class="bi bi-cash text-slate-400"></i>
                            Montant payé
                            <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="number" name="amount" id="amount" min="0" step="100"
                                value="{{ old('amount', $payment->amount) }}" placeholder="0"
                                class="w-full pl-3.5 pr-24 py-2.5 rounded-xl border text-sm font-semibold
                                          text-slate-800 dark:text-slate-100
                                          bg-white dark:bg-slate-700/50 placeholder-slate-400
                                          focus:outline-none focus:ring-2 transition-all duration-200
                                          {{ $errors->has('amount')
                                              ? 'border-red-500 focus:ring-red-500/40'
                                              : 'border-slate-200 dark:border-slate-600 focus:ring-blue-500/30 focus:border-blue-500' }}" required>
                            <span class="absolute inset-y-0 right-0 flex items-center pr-3.5
                                         text-xs font-semibold text-slate-500 dark:text-slate-400
                                         pointer-events-none bg-slate-50 dark:bg-slate-700
                                         rounded-r-xl border-l border-slate-200 dark:border-slate-600 px-3">
                                FCFA
                            </span>
                        </div>
                        {{-- Suggestion montant --}}
                        <p id="amount-suggestion" class="hidden text-xs text-blue-600 dark:text-blue-400
                                                          flex items-center gap-1">
                            <i class="bi bi-lightbulb"></i>
                            <span id="suggestion-text"></span>
                        </p>
                        @error('amount')
                        <p class="mt-1.5 text-xs text-red-600 dark:text-red-400 flex items-center gap-1">
                            <i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
                        </p>
                        @enderror
                    </div>

                </div>
            </div>

            {{-- Détails du paiement --}}
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200
                        dark:border-slate-700 shadow-sm overflow-hidden">

                <div class="flex items-center gap-3 px-6 py-4
                            border-b border-slate-100 dark:border-slate-700
                            bg-slate-50/60 dark:bg-slate-700/30">
                    <div class="w-7 h-7 rounded-lg bg-emerald-100 dark:bg-emerald-900/40
                                flex items-center justify-center">
                        <i class="bi bi-credit-card text-emerald-600 dark:text-emerald-400 text-sm"></i>
                    </div>
                    <h2 class="text-sm font-semibold text-slate-700 dark:text-slate-200">
                        Détails du paiement
                    </h2>
                </div>

                <div class="p-6 space-y-5">

                    {{-- Mode de paiement --}}
                    <div class="space-y-2">
                        <label class="flex items-center gap-1.5 text-sm font-medium
                                      text-slate-700 dark:text-slate-200">
                            <i class="bi bi-wallet2 text-slate-400"></i>
                            Mode de paiement
                            <span class="text-red-500">*</span>
                        </label>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                            @foreach([
                            ['value' => 'cash', 'label' => 'Espèces', 'icon' => 'bi-cash-stack', 'color' => 'emerald'],
                            ['value' => 'bank_transfer', 'label' => 'Virement', 'icon' => 'bi-bank', 'color' => 'blue'],
                            ['value' => 'mobile_money', 'label' => 'Mobile Money', 'icon' => 'bi-phone', 'color' =>
                            'amber'],
                            ['value' => 'check', 'label' => 'Chèque', 'icon' => 'bi-receipt', 'color' => 'cyan'],
                            ['value' => 'card', 'label' => 'Carte bancaire', 'icon' => 'bi-credit-card', 'color' =>
                            'purple'],
                            ] as $method)
                            @php $isSelected = old('payment_method', $payment->payment_method) === $method['value'];
                            @endphp
                            <label class="payment-method-option flex items-center gap-2.5 p-3 rounded-xl border-2
                                          cursor-pointer transition-all duration-200
                                          {{ $isSelected
                                              ? 'border-' . $method['color'] . '-500 bg-' . $method['color'] . '-50 dark:bg-' . $method['color'] . '-900/20'
                                              : 'border-slate-200 dark:border-slate-600 hover:border-slate-300' }}">
                                <input type="radio" name="payment_method" value="{{ $method['value'] }}"
                                    {{ $isSelected ? 'checked' : '' }} class="sr-only">
                                <i class="bi {{ $method['icon'] }} text-lg
                                          {{ $isSelected
                                              ? 'text-' . $method['color'] . '-600 dark:text-' . $method['color'] . '-400'
                                              : 'text-slate-400' }}"></i>
                                <span class="text-xs font-medium
                                             {{ $isSelected
                                                 ? 'text-' . $method['color'] . '-700 dark:text-' . $method['color'] . '-300'
                                                 : 'text-slate-600 dark:text-slate-400' }}">
                                    {{ $method['label'] }}
                                </span>
                            </label>
                            @endforeach
                        </div>
                        @error('payment_method')
                        <p class="text-xs text-red-600 dark:text-red-400 flex items-center gap-1">
                            <i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
                        </p>
                        @enderror
                    </div>

                    {{-- Référence + Statut --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div class="space-y-1.5">
                            <label for="transaction_reference" class="flex items-center gap-1.5 text-sm font-medium
                                          text-slate-700 dark:text-slate-200">
                                <i class="bi bi-hash text-slate-400"></i>
                                Référence de transaction
                            </label>
                            <div class="flex gap-2">
                                <input type="text" name="transaction_reference" id="transaction_reference"
                                    value="{{ old('transaction_reference', $payment->transaction_reference) }}"
                                    placeholder="PAY-XXXXXXXX-YYMMDD" class="flex-1 px-3.5 py-2.5 rounded-xl border text-sm font-mono
                                              text-slate-800 dark:text-slate-100
                                              bg-white dark:bg-slate-700/50 placeholder-slate-400
                                              focus:outline-none focus:ring-2 transition-all duration-200
                                              border-slate-200 dark:border-slate-600
                                              focus:ring-blue-500/30 focus:border-blue-500">
                                <button type="button" id="generate-ref" class="px-3 py-2.5 rounded-xl text-sm font-medium
                                               bg-slate-100 dark:bg-slate-700 border border-slate-200 dark:border-slate-600
                                               text-slate-600 dark:text-slate-400
                                               hover:bg-blue-50 hover:text-blue-600 hover:border-blue-300
                                               dark:hover:bg-blue-900/20 dark:hover:text-blue-400
                                               transition-all duration-200" title="Générer une référence automatique">
                                    <i class="bi bi-magic"></i>
                                </button>
                            </div>
                            <p class="text-xs text-slate-400 dark:text-slate-500">
                                Laissez vide pour génération automatique
                            </p>
                        </div>

                        <x-forms.select name="status" label="Statut" icon="bi-circle-fill" :options="[
                                'paid'      => '✅ Payé',
                                'pending'   => '⏳ En attente',
                                'overdue'   => '⚠️ En retard',
                                'cancelled' => '❌ Annulé',
                            ]" :value="old('status', $payment->status ?? 'paid')" required />
                    </div>

                    {{-- Date de paiement --}}
                    <x-forms.input-field name="paid_at" label="Date de paiement" type="text"
                        :value="old('paid_at', $payment->paid_at?->format('Y-m-d') ?? today()->format('Y-m-d'))"
                        icon="bi-calendar-event" class="flatpickr-date" required />

                    {{-- Notes --}}
                    <x-forms.textarea name="notes" label="Notes" :value="old('notes', $payment->notes)"
                        placeholder="Remarques ou informations complémentaires…" rows="2"
                        help="Optionnel. Maximum 500 caractères." />

                </div>
            </div>
        </div>

        {{-- ── Colonne latérale : Récapitulatif ── --}}
        <div class="space-y-6">

            {{-- Carte récapitulatif --}}
            <div class="bg-linear-to-br from-blue-600 to-emerald-500 rounded-2xl p-5 shadow-sm">
                <p class="text-xs font-semibold text-blue-100 uppercase tracking-wide mb-4">
                    Récapitulatif
                </p>
                <div class="space-y-3">
                    <div class="bg-white/15 rounded-xl p-3">
                        <p class="text-[10px] text-blue-200 uppercase tracking-wide mb-0.5">Étudiant</p>
                        <p id="recap-student" class="text-sm font-semibold text-white truncate">—</p>
                    </div>
                    <div class="bg-white/15 rounded-xl p-3">
                        <p class="text-[10px] text-blue-200 uppercase tracking-wide mb-0.5">Type de frais</p>
                        <p id="recap-feetype" class="text-sm font-semibold text-white truncate">—</p>
                    </div>
                    <div class="bg-white/15 rounded-xl p-3">
                        <p class="text-[10px] text-blue-200 uppercase tracking-wide mb-0.5">Montant</p>
                        <p id="recap-amount" class="text-2xl font-extrabold text-white">
                            0 <span class="text-sm font-normal text-blue-200">FCFA</span>
                        </p>
                    </div>
                    <div class="bg-white/15 rounded-xl p-3">
                        <p class="text-[10px] text-blue-200 uppercase tracking-wide mb-0.5">Mode</p>
                        <p id="recap-method" class="text-sm font-semibold text-white">—</p>
                    </div>
                    <div class="bg-white/15 rounded-xl p-3">
                        <p class="text-[10px] text-blue-200 uppercase tracking-wide mb-0.5">Statut</p>
                        <p id="recap-status" class="text-sm font-semibold text-white">—</p>
                    </div>
                </div>
            </div>

            {{-- Info QR Code --}}
            @if(!$payment->exists)
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200
                        dark:border-slate-700 shadow-sm p-4">
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-lg bg-cyan-100 dark:bg-cyan-900/30
                                flex items-center justify-center shrink-0">
                        <i class="bi bi-qr-code text-cyan-600 dark:text-cyan-400"></i>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-slate-700 dark:text-slate-200 mb-1">
                            QR Code automatique
                        </p>
                        <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                            Un QR Code de vérification sera généré automatiquement et joint au reçu PDF.
                        </p>
                    </div>
                </div>
            </div>
            @else
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200
                        dark:border-slate-700 shadow-sm p-4 text-center">
                @if($payment->qr_code)
                <p class="text-xs font-semibold text-slate-700 dark:text-slate-200 mb-3">
                    QR Code de vérification
                </p>
                <img src="data:image/png;base64,{{ $payment->qr_code }}" alt="QR Code"
                    class="w-28 h-28 mx-auto rounded-xl border border-slate-200 dark:border-slate-700">
                <p class="text-[10px] text-slate-400 mt-2">
                    Réf : {{ $payment->transaction_reference }}
                </p>
                @endif
            </div>
            @endif

        </div>
    </div>

    {{-- ── Actions ── --}}
    <div class="flex items-center justify-between gap-3
                bg-white dark:bg-slate-800 rounded-2xl border border-slate-200
                dark:border-slate-700 shadow-sm px-6 py-4">
        <a href="{{ route('payments.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-medium
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
            <i class="bi {{ $payment->exists ? 'bi-check-lg' : 'bi-save' }}"></i>
            {{ $payment->exists ? 'Enregistrer les modifications' : 'Enregistrer le paiement' }}
        </button>
    </div>

</x-forms.form>
@endsection

@push('scripts')
<script>
(() => {
    // ── Flatpickr ─────────────────────────────────────────────────
    flatpickr('[name="paid_at"]', {
        dateFormat: 'Y-m-d',
        altInput: true,
        altFormat: 'd/m/Y',
        locale: 'fr',
        defaultDate: '{{ old("paid_at", today()->format("Y-m-d")) }}',
    });

    // ── Refs DOM ──────────────────────────────────────────────────
    const studentSel = document.getElementById('student_id');
    const feeTypeSel = document.getElementById('fee_type_id');
    const amountInput = document.getElementById('amount');
    const methodOpts = document.querySelectorAll('.payment-method-option');
    const statusSel = document.querySelector('[name="status"]');
    const refInput = document.getElementById('transaction_reference');
    const genBtn = document.getElementById('generate-ref');
    const suggestion = document.getElementById('amount-suggestion');
    const suggTxt = document.getElementById('suggestion-text');

    const recapStudent = document.getElementById('recap-student');
    const recapFeeType = document.getElementById('recap-feetype');
    const recapAmount = document.getElementById('recap-amount');
    const recapMethod = document.getElementById('recap-method');
    const recapStatus = document.getElementById('recap-status');

    const methodLabels = {
        cash: 'Espèces',
        bank_transfer: 'Virement bancaire',
        mobile_money: 'Mobile Money',
        check: 'Chèque',
        card: 'Carte bancaire',
    };

    const statusLabels = {
        paid: '✅ Payé',
        pending: '⏳ En attente',
        overdue: '⚠️ En retard',
        cancelled: '❌ Annulé',
    };

    // ── Auto-remplir montant depuis fee type ───────────────────────
    feeTypeSel?.addEventListener('change', () => {
        const opt = feeTypeSel.options[feeTypeSel.selectedIndex];
        const amt = opt?.dataset?.amount;

        if (amt && amountInput && !amountInput.value) {
            amountInput.value = amt;
            if (suggestion && suggTxt) {
                suggTxt.textContent = `Montant suggéré : ${Number(amt).toLocaleString('fr-FR')} FCFA`;
                suggestion.classList.remove('hidden');
                suggestion.classList.add('flex');
            }
        }
        updateRecap();
    });

    amountInput?.addEventListener('input', () => {
        suggestion?.classList.add('hidden');
        suggestion?.classList.remove('flex');
        updateRecap();
    });

    // ── Mode de paiement styling ───────────────────────────────────
    const methodColors = {
        cash: 'emerald',
        bank_transfer: 'blue',
        mobile_money: 'amber',
        check: 'cyan',
        card: 'purple',
    };

    methodOpts.forEach(label => {
        label.addEventListener('click', () => {
            const radio = label.querySelector('input[type="radio"]');
            const val = radio?.value;
            const color = methodColors[val] ?? 'blue';

            // Reset
            methodOpts.forEach(l => {
                l.className = l.className
                    .replace(/border-\w+-500/g, 'border-slate-200 dark:border-slate-600')
                    .replace(/bg-\w+-50|dark:bg-\w+-900\/20/g, '');
                const i = l.querySelector('i');
                const span = l.querySelector('span');
                if (i) i.className = i.className.replace(/text-\w+-\d+/g, 'text-slate-400');
                if (span) span.className = span.className.replace(/text-\w+-\d+/g,
                    'text-slate-600 dark:text-slate-400');
            });

            // Activer
            label.classList.add(`border-${color}-500`, `bg-${color}-50`, `dark:bg-${color}-900/20`);
            const i = label.querySelector('i');
            const span = label.querySelector('span');
            if (i) i.classList.add(`text-${color}-600`, `dark:text-${color}-400`);
            if (span) span.classList.add(`text-${color}-700`, `dark:text-${color}-300`);

            updateRecap();
        });
    });

    // ── Générer référence ──────────────────────────────────────────
    genBtn?.addEventListener('click', () => {
        const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        const rand = Array.from({
                length: 8
            }, () =>
            chars.charAt(Math.floor(Math.random() * chars.length))
        ).join('');
        const date = new Date().toISOString().slice(2, 10).replace(/-/g, '');
        if (refInput) refInput.value = `PAY-${rand}-${date}`;
    });

    // ── Mise à jour récapitulatif ──────────────────────────────────
    function updateRecap() {
        if (recapStudent && studentSel) {
            const opt = studentSel.options[studentSel.selectedIndex];
            recapStudent.textContent = opt?.value ? opt.text : '—';
        }

        if (recapFeeType && feeTypeSel) {
            const opt = feeTypeSel.options[feeTypeSel.selectedIndex];
            recapFeeType.textContent = opt?.value ? opt.text.split(' — ')[0] : '—';
        }

        if (recapAmount && amountInput) {
            const val = Number(amountInput.value) || 0;
            recapAmount.innerHTML =
                `${val.toLocaleString('fr-FR')} <span class="text-sm font-normal text-blue-200">FCFA</span>`;
        }

        if (recapMethod) {
            const checked = document.querySelector('[name="payment_method"]:checked');
            recapMethod.textContent = methodLabels[checked?.value] ?? '—';
        }

        if (recapStatus && statusSel) {
            recapStatus.textContent = statusLabels[statusSel.value] ?? '—';
        }
    }

    // Listeners récapitulatif
    studentSel?.addEventListener('change', updateRecap);
    statusSel?.addEventListener('change', updateRecap);

    updateRecap(); // init
})();
</script>
@endpush