@extends('layouts.base')

@section('page_title', 'Paiement #' . str_pad($payment->id, 5, '0', STR_PAD_LEFT))

@section('breadcrumb')
<span class="text-slate-300 dark:text-slate-600 font-light select-none">/</span>
<a href="{{ route('payments.index') }}"
    class="text-slate-500 dark:text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
    Paiements
</a>
@endsection

@section('page_header')
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div class="flex items-center gap-3">
        <a href="{{ route('payments.index') }}" class="w-9 h-9 rounded-xl flex items-center justify-center
                  bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700
                  text-slate-500 hover:text-blue-600 hover:border-blue-300 dark:hover:border-blue-600
                  transition-all duration-200">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-800 dark:text-slate-100">
                Reçu de paiement
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                Référence #{{ str_pad($payment->id, 5, '0', STR_PAD_LEFT) }}
                • {{ $payment->created_at->format('d/m/Y') }}
            </p>
        </div>
    </div>

    <div class="flex flex-wrap items-center gap-2">
        <a href="{{ route('payments.edit', $payment) }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-medium
                  bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700
                  text-slate-700 dark:text-slate-300
                  hover:bg-slate-50 dark:hover:bg-slate-700 transition-all duration-200">
            <i class="bi bi-pencil-square"></i>
            <span class="hidden sm:inline">Modifier</span>
        </a>

        {{-- Imprimer le reçu --}}
        <button id="print-receipt-btn" type="button" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold
                       bg-blue-600 hover:bg-blue-700 text-white shadow-sm hover:shadow-blue-500/20
                       transition-all duration-200">
            <i class="bi bi-printer-fill"></i>
            <span class="hidden sm:inline">Imprimer</span>
        </button>

        <form id="delete-payment-form" action="{{ route('payments.destroy', $payment) }}" method="POST">
            @csrf
            @method('DELETE')
            <button id="delete-payment-btn" type="button" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold
                           bg-red-600 hover:bg-red-700 text-white shadow-sm hover:shadow-red-500/20
                           transition-all duration-200">
                <i class="bi bi-trash3"></i>
                <span class="hidden sm:inline">Supprimer</span>
            </button>
        </form>
    </div>
</div>
@endsection

@section('content')
@php
$status = strtolower($payment->status ?? 'pending');
$statusMap = [
'paid' => [
'label' => 'Payé',
'icon' => 'bi-check-circle-fill',
'text' => 'text-emerald-700 dark:text-emerald-300',
'bg' => 'bg-emerald-50 dark:bg-emerald-900/20',
'border' => 'border-emerald-200 dark:border-emerald-800',
'bar' => 'bg-emerald-500',
'badge' => 'bg-emerald-600 text-white',
],
'overdue' => [
'label' => 'En retard',
'icon' => 'bi-exclamation-triangle-fill',
'text' => 'text-red-700 dark:text-red-300',
'bg' => 'bg-red-50 dark:bg-red-900/20',
'border' => 'border-red-200 dark:border-red-800',
'bar' => 'bg-red-500',
'badge' => 'bg-red-600 text-white',
],
'pending' => [
'label' => 'En attente',
'icon' => 'bi-hourglass-split',
'text' => 'text-amber-700 dark:text-amber-300',
'bg' => 'bg-amber-50 dark:bg-amber-900/20',
'border' => 'border-amber-200 dark:border-amber-800',
'bar' => 'bg-amber-500',
'badge' => 'bg-amber-500 text-white',
],
];

$cfg = $statusMap[$status] ?? $statusMap['pending'];

$methodIcons = [
'cash' => 'bi-cash-stack',
'mobile_money' => 'bi-phone-fill',
'bank_transfer' => 'bi-bank',
'card' => 'bi-credit-card-2-front-fill',
'cheque' => 'bi-file-text',
];

$methodLabels = [
'cash' => 'Espèces',
'mobile_money' => 'Mobile Money',
'bank_transfer' => 'Virement bancaire',
'card' => 'Carte bancaire',
'cheque' => 'Chèque',
];

$method = strtolower($payment->payment_method ?? 'cash');
$methodIcon = $methodIcons[$method] ?? 'bi-credit-card';
$methodLabel = $methodLabels[$method] ?? ucfirst($payment->payment_method ?? 'Espèces');
$reference = $payment->transaction_reference ?? null;
$refNumber = str_pad($payment->id, 5, '0', STR_PAD_LEFT);
@endphp

{{-- ── Reçu Hero ────────────────────────────────────────────────── --}}
<div id="receipt-area" class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200
            dark:border-slate-700 shadow-sm overflow-hidden mb-6">

    {{-- Entête reçu --}}
    <div class="bg-linear-to-r from-blue-600 to-emerald-600 p-6 sm:p-8 text-white">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-white/20 backdrop-blur
                            flex items-center justify-center shadow-sm">
                    <i class="bi bi-receipt-cutoff text-white text-2xl"></i>
                </div>
                <div>
                    <p class="text-white/70 text-xs font-semibold uppercase tracking-wider">
                        Reçu de paiement
                    </p>
                    <h2 class="text-2xl font-black text-white">
                        #{{ $refNumber }}
                    </h2>
                    <p class="text-white/80 text-sm mt-0.5">
                        Émis le {{ $payment->created_at->format('d M Y à H:i') }}
                    </p>
                </div>
            </div>

            <div class="text-right">
                <p class="text-white/70 text-xs font-semibold uppercase tracking-wider">
                    Montant réglé
                </p>
                <p class="text-3xl font-black text-white">
                    {{ number_format($payment->amount ?? 0, 0, ',', ' ') }}
                </p>
                <p class="text-white/80 text-sm">FCFA</p>
            </div>
        </div>
    </div>

    {{-- Corps reçu --}}
    <div class="p-6 sm:p-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            {{-- Infos élève --}}
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-3">
                    Élève
                </p>
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-full shrink-0
                                bg-linear-to-br from-blue-600 to-emerald-500
                                flex items-center justify-center text-white font-bold text-sm">
                        {{ strtoupper(substr($payment->student->user->name ?? 'E', 0, 2)) }}
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-bold text-slate-800 dark:text-slate-100 truncate">
                            {{ $payment->student->user->name ?? 'Élève' }}
                        </p>
                        <p class="text-xs text-slate-500 dark:text-slate-400 truncate">
                            {{ $payment->student->user->email ?? 'Aucun email' }}
                        </p>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            Mat. {{ $payment->student->admission_number ?? $payment->student->matricule ?? '—' }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Infos type de frais --}}
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-3">
                    Type de frais
                </p>
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-xl shrink-0
                                bg-emerald-50 dark:bg-emerald-900/20
                                border border-emerald-200 dark:border-emerald-800
                                flex items-center justify-center">
                        <i class="bi bi-cash-coin text-emerald-600 dark:text-emerald-400 text-lg"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-bold text-slate-800 dark:text-slate-100 truncate">
                            {{ $payment->feeType->name ?? 'Frais scolaire' }}
                        </p>
                        <p class="text-xs text-slate-500 dark:text-slate-400">
                            Montant de base :
                            {{ number_format($payment->feeType->amount ?? 0, 0, ',', ' ') }} FCFA
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Séparateur pointillé --}}
        <div class="my-6 border-t border-dashed border-slate-200 dark:border-slate-700"></div>

        {{-- Détails transaction --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">

            <div class="rounded-xl bg-slate-50 dark:bg-slate-900/50
                        border border-slate-200 dark:border-slate-700 p-4">
                <p class="text-[11px] uppercase tracking-wider font-semibold text-slate-400">
                    Statut
                </p>
                <span class="mt-2 inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full
                             text-xs font-bold {{ $cfg['badge'] }}">
                    <i class="bi {{ $cfg['icon'] }}"></i>
                    {{ $cfg['label'] }}
                </span>
            </div>

            <div class="rounded-xl bg-slate-50 dark:bg-slate-900/50
                        border border-slate-200 dark:border-slate-700 p-4">
                <p class="text-[11px] uppercase tracking-wider font-semibold text-slate-400">
                    Méthode
                </p>
                <div class="mt-2 flex items-center gap-2">
                    <i class="bi {{ $methodIcon }} text-blue-500 text-base"></i>
                    <p class="text-sm font-semibold text-slate-800 dark:text-slate-100">
                        {{ $methodLabel }}
                    </p>
                </div>
            </div>

            <div class="rounded-xl bg-slate-50 dark:bg-slate-900/50
                        border border-slate-200 dark:border-slate-700 p-4">
                <p class="text-[11px] uppercase tracking-wider font-semibold text-slate-400">
                    Date
                </p>
                <p class="text-sm font-semibold text-slate-800 dark:text-slate-100 mt-2">
                    {{ $payment->created_at->format('d/m/Y') }}
                </p>
                <p class="text-xs text-slate-500 mt-0.5">
                    {{ $payment->created_at->format('H:i') }}
                </p>
            </div>

            <div class="rounded-xl bg-slate-50 dark:bg-slate-900/50
                        border border-slate-200 dark:border-slate-700 p-4">
                <p class="text-[11px] uppercase tracking-wider font-semibold text-slate-400">
                    Référence
                </p>
                <p class="text-sm font-mono font-semibold text-slate-800 dark:text-slate-100 mt-2 truncate">
                    {{ $reference ?? 'REF-'.$refNumber }}
                </p>
            </div>
        </div>

        @if($reference)
        <div class="mt-5 flex items-start gap-3 p-4 rounded-xl
                    bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800">
            <i class="bi bi-info-circle-fill text-blue-500 shrink-0 mt-0.5"></i>
            <div class="min-w-0">
                <p class="text-xs font-semibold text-blue-700 dark:text-blue-300">
                    Référence de transaction
                </p>
                <p class="text-sm font-mono text-blue-800 dark:text-blue-200 mt-0.5 break-all">
                    {{ $reference }}
                </p>
            </div>
        </div>
        @endif

        @if($payment->notes ?? null)
        <div class="mt-5 rounded-xl border border-slate-200 dark:border-slate-700
                    bg-slate-50 dark:bg-slate-900/40 p-4">
            <p class="text-[11px] uppercase tracking-wider font-semibold text-slate-400 mb-2">
                Notes
            </p>
            <p class="text-sm text-slate-700 dark:text-slate-300 leading-relaxed">
                {{ $payment->notes }}
            </p>
        </div>
        @endif
    </div>

    <div class="h-1 bg-linear-to-r from-blue-600 to-emerald-600"></div>
</div>

{{-- ── Parents liés ─────────────────────────────────────────────── --}}
@if($payment->student->parents?->count())
<div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200
            dark:border-slate-700 shadow-sm overflow-hidden mb-6">
    <div class="px-5 sm:px-6 py-4 border-b border-slate-200 dark:border-slate-700
                bg-slate-50/50 dark:bg-slate-800/50">
        <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-100 flex items-center gap-2">
            <i class="bi bi-people-fill text-blue-500"></i>
            Parents / Tuteurs de l'élève
        </h3>
    </div>
    <div class="divide-y divide-slate-100 dark:divide-slate-700/50">
        @foreach($payment->student->parents as $parent)
        <div class="p-5 flex items-center gap-4">
            <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-900/20
                        flex items-center justify-center text-blue-600 dark:text-blue-400
                        font-bold text-sm shrink-0">
                {{ strtoupper(substr($parent->user->name ?? 'P', 0, 1)) }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-slate-800 dark:text-slate-100 truncate">
                    {{ $parent->user->name ?? 'Parent' }}
                </p>
                <p class="text-xs text-slate-500 dark:text-slate-400 truncate">
                    {{ $parent->user->email ?? 'Aucun email' }}
                    @if($parent->phone ?? null) • {{ $parent->phone }} @endif
                </p>
            </div>
            <span class="px-2.5 py-1 rounded-full text-[11px] font-medium
                         bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300">
                {{ $parent->relationship ?? 'Tuteur' }}
            </span>
        </div>
        @endforeach
    </div>
</div>
@endif

{{-- ── Actions rapides ─────────────────────────────────────────── --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
    <a href="{{ route('students.show', $payment->student) }}" class="flex items-center gap-3 p-4 rounded-2xl
              bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700
              shadow-sm hover:border-blue-300 dark:hover:border-blue-600
              hover:bg-blue-50 dark:hover:bg-blue-950/20
              transition-all duration-200 group">
        <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-900/30
                    flex items-center justify-center text-blue-600 dark:text-blue-400">
            <i class="bi bi-person-fill text-base"></i>
        </div>
        <div class="min-w-0 flex-1">
            <p class="text-sm font-semibold text-slate-800 dark:text-slate-100 truncate">
                Voir l'élève
            </p>
            <p class="text-xs text-slate-500 truncate">{{ $payment->student->user->name }}</p>
        </div>
        <i class="bi bi-arrow-right-short text-slate-400 group-hover:text-blue-500 transition-colors"></i>
    </a>

    <a href="{{ route('fee-types.show', $payment->feeType) }}" class="flex items-center gap-3 p-4 rounded-2xl
              bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700
              shadow-sm hover:border-emerald-300 dark:hover:border-emerald-600
              hover:bg-emerald-50 dark:hover:bg-emerald-950/20
              transition-all duration-200 group">
        <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-900/30
                    flex items-center justify-center text-emerald-600 dark:text-emerald-400">
            <i class="bi bi-cash-coin text-base"></i>
        </div>
        <div class="min-w-0 flex-1">
            <p class="text-sm font-semibold text-slate-800 dark:text-slate-100 truncate">
                Type de frais
            </p>
            <p class="text-xs text-slate-500 truncate">{{ $payment->feeType->name ?? '—' }}</p>
        </div>
        <i class="bi bi-arrow-right-short text-slate-400 group-hover:text-emerald-500 transition-colors"></i>
    </a>

    <a href="{{ route('payments.create') }}?student_id={{ $payment->student_id }}" class="flex items-center gap-3 p-4 rounded-2xl
              bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700
              shadow-sm hover:border-cyan-300 dark:hover:border-cyan-600
              hover:bg-cyan-50 dark:hover:bg-cyan-950/20
              transition-all duration-200 group">
        <div class="w-10 h-10 rounded-xl bg-cyan-50 dark:bg-cyan-900/30
                    flex items-center justify-center text-cyan-600 dark:text-cyan-400">
            <i class="bi bi-plus-circle-fill text-base"></i>
        </div>
        <div class="min-w-0 flex-1">
            <p class="text-sm font-semibold text-slate-800 dark:text-slate-100">
                Nouveau paiement
            </p>
            <p class="text-xs text-slate-500">Pour le même élève</p>
        </div>
        <i class="bi bi-arrow-right-short text-slate-400 group-hover:text-cyan-500 transition-colors"></i>
    </a>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {

    // ── Suppression ──────────────────────────────────────────
    const deleteBtn = document.getElementById('delete-payment-btn');
    const deleteForm = document.getElementById('delete-payment-form');

    deleteBtn?.addEventListener('click', () => {
        if (confirm(
                'Supprimer définitivement ce paiement ?\n\n' +
                'Cette action est irréversible.'
            )) {
            deleteForm.submit();
        }
    });

    // ── Impression du reçu ───────────────────────────────────
    document.getElementById('print-receipt-btn')?.addEventListener('click', () => {
        window.print();
    });
});
</script>

{{-- Style impression --}}
<style>
@media print {

    #sidebar,
    header,
    #page-loader,
    [id$="-btn"],
    .no-print {
        display: none !important;
    }

    #receipt-area {
        border: none !important;
        box-shadow: none !important;
    }

    body {
        background: white !important;
        color: black !important;
    }
}
</style>
@endsection