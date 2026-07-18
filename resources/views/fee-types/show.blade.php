@extends('layouts.base')

@section('page_title', 'Type de frais : ' . $feeType->name)

@section('breadcrumb')
<span class="text-slate-300 dark:text-slate-600 font-light select-none">/</span>
<a href="{{ route('fee-types.index') }}"
    class="text-slate-500 dark:text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
    Types de frais
</a>
@endsection

@section('page_header')
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div class="flex items-center gap-3">
        <a href="{{ route('fee-types.index') }}" class="w-9 h-9 rounded-xl flex items-center justify-center
                  bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700
                  text-slate-500 hover:text-blue-600 hover:border-blue-300 dark:hover:border-blue-600
                  transition-all duration-200">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-800 dark:text-slate-100">
                {{ $feeType->name }}
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                Montant de base :
                <span class="font-semibold text-slate-700 dark:text-slate-300">
                    {{ number_format($feeType->amount, 0, ',', ' ') }} FCFA
                </span>
            </p>
        </div>
    </div>

    <div class="flex items-center gap-2">
        <a href="{{ route('fee-types.edit', $feeType) }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-medium
                  bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700
                  text-slate-700 dark:text-slate-300
                  hover:bg-slate-50 dark:hover:bg-slate-700 transition-all duration-200">
            <i class="bi bi-pencil-square"></i>
            <span class="hidden sm:inline">Modifier</span>
        </a>

        <form id="delete-feetype-form" action="{{ route('fee-types.destroy', $feeType) }}" method="POST">
            @csrf
            @method('DELETE')
            <button id="delete-feetype-btn" type="button" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold
                           bg-red-600 hover:bg-red-700 text-white
                           shadow-sm hover:shadow-red-500/20 transition-all duration-200">
                <i class="bi bi-trash3"></i>
                <span class="hidden sm:inline">Supprimer</span>
            </button>
        </form>
    </div>
</div>
@endsection

@section('content')
@php
$totalAmount = $stats['total_amount'] ?? 0;
$totalCount = $stats['total_payments'] ?? 0;
$paidCount = $stats['paid_count'] ?? 0;
$pendingCount = $stats['pending_count'] ?? 0;
$overdueCount = $stats['overdue_count'] ?? 0;

$collectionRate = $totalCount > 0
? round(($paidCount / $totalCount) * 100)
: 0;

$expectedTotal = $totalCount * ($feeType->amount ?? 0);
@endphp

{{-- ── Hero ─────────────────────────────────────────────────────── --}}
<div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200
            dark:border-slate-700 shadow-sm overflow-hidden mb-6">
    <div class="p-6 sm:p-8">
        <div class="flex flex-col lg:flex-row gap-6 lg:items-start">

            {{-- Icône --}}
            <div class="w-16 h-16 rounded-2xl bg-linear-to-br from-emerald-500 to-teal-600
                        flex items-center justify-center shadow-lg shadow-emerald-500/20 shrink-0">
                <i class="bi bi-cash-coin text-white text-2xl"></i>
            </div>

            <div class="flex-1 min-w-0">
                <div class="flex flex-wrap items-center gap-2.5 mb-3">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full
                                 text-xs font-bold bg-emerald-600 text-white shadow-sm">
                        <i class="bi bi-tag-fill"></i>
                        {{ $feeType->category ?? 'Frais scolaire' }}
                    </span>

                    @if($feeType->is_required ?? true)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full
                                 text-xs font-semibold
                                 bg-amber-50 dark:bg-amber-900/20
                                 text-amber-700 dark:text-amber-300
                                 border border-amber-200 dark:border-amber-800">
                        <i class="bi bi-exclamation-circle-fill"></i>
                        Obligatoire
                    </span>
                    @else
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full
                                 text-xs font-medium
                                 bg-slate-100 dark:bg-slate-700
                                 text-slate-600 dark:text-slate-300
                                 border border-slate-200 dark:border-slate-600">
                        <i class="bi bi-check-circle"></i>
                        Facultatif
                    </span>
                    @endif

                    @if($feeType->frequency ?? null)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full
                                 text-xs font-medium
                                 bg-blue-50 dark:bg-blue-900/20
                                 text-blue-700 dark:text-blue-300
                                 border border-blue-200 dark:border-blue-800">
                        <i class="bi bi-arrow-repeat"></i>
                        {{ ucfirst($feeType->frequency) }}
                    </span>
                    @endif
                </div>

                <h2 class="text-2xl font-bold text-slate-800 dark:text-slate-100">
                    {{ $feeType->name }}
                </h2>

                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1 leading-relaxed max-w-2xl">
                    {{ $feeType->description ?? 'Aucune description définie pour ce type de frais.' }}
                </p>

                {{-- Montant & Taux de collecte --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-6">
                    <div class="rounded-xl bg-slate-50 dark:bg-slate-900/50
                                border border-slate-200 dark:border-slate-700 p-4">
                        <p class="text-[11px] uppercase tracking-wider font-semibold text-slate-400">
                            Montant unitaire
                        </p>
                        <p class="text-lg font-black text-emerald-600 dark:text-emerald-400 mt-1">
                            {{ number_format($feeType->amount ?? 0, 0, ',', ' ') }}
                            <span class="text-xs font-semibold text-slate-500">FCFA</span>
                        </p>
                    </div>

                    <div class="rounded-xl bg-slate-50 dark:bg-slate-900/50
                                border border-slate-200 dark:border-slate-700 p-4">
                        <p class="text-[11px] uppercase tracking-wider font-semibold text-slate-400">
                            Total collecté
                        </p>
                        <p class="text-lg font-black text-blue-600 dark:text-blue-400 mt-1">
                            {{ number_format($totalAmount, 0, ',', ' ') }}
                            <span class="text-xs font-semibold text-slate-500">FCFA</span>
                        </p>
                    </div>

                    <div class="rounded-xl bg-slate-50 dark:bg-slate-900/50
                                border border-slate-200 dark:border-slate-700 p-4">
                        <p class="text-[11px] uppercase tracking-wider font-semibold text-slate-400">
                            Taux de collecte
                        </p>
                        <p
                            class="text-lg font-black mt-1
                            {{ $collectionRate >= 75 ? 'text-emerald-600 dark:text-emerald-400' : ($collectionRate >= 50 ? 'text-amber-600 dark:text-amber-400' : 'text-red-600 dark:text-red-400') }}">
                            {{ $collectionRate }}%
                        </p>
                        <div class="mt-2 h-1.5 w-full bg-slate-200 dark:bg-slate-700 rounded-full overflow-hidden">
                            <div class="h-full rounded-full transition-all duration-700
                                {{ $collectionRate >= 75 ? 'bg-emerald-500' : ($collectionRate >= 50 ? 'bg-amber-500' : 'bg-red-500') }}"
                                style="width: {{ $collectionRate }}%">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="h-1 w-full bg-linear-to-r from-emerald-500 to-teal-600"></div>
</div>

{{-- ── Stats ─────────────────────────────────────────────────────── --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    @foreach([
    [
    'label' => 'Total paiements',
    'value' => $totalCount,
    'sub' => 'Toutes opérations',
    'icon' => 'bi-receipt',
    'color' => 'blue',
    ],
    [
    'label' => 'Payés',
    'value' => $paidCount,
    'sub' => 'Soldés',
    'icon' => 'bi-check-circle-fill',
    'color' => 'emerald',
    ],
    [
    'label' => 'En attente',
    'value' => $pendingCount,
    'sub' => 'À régler',
    'icon' => 'bi-hourglass-split',
    'color' => 'amber',
    ],
    [
    'label' => 'En retard',
    'value' => $overdueCount,
    'sub' => 'Délai dépassé',
    'icon' => 'bi-exclamation-triangle-fill',
    'color' => 'red',
    ],
    ] as $s)
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200
                dark:border-slate-700 p-5 shadow-sm">
        <div class="flex items-start justify-between gap-2">
            <div class="min-w-0">
                <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">
                    {{ $s['label'] }}
                </p>
                <p class="text-2xl font-bold text-slate-800 dark:text-slate-100 mt-1.5">
                    {{ $s['value'] }}
                </p>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">{{ $s['sub'] }}</p>
            </div>
            <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0
                {{ $s['color'] === 'blue'    ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400' : '' }}
                {{ $s['color'] === 'emerald' ? 'bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400' : '' }}
                {{ $s['color'] === 'amber'   ? 'bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400' : '' }}
                {{ $s['color'] === 'red'     ? 'bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400' : '' }}">
                <i class="bi {{ $s['icon'] }} text-lg"></i>
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- ── Paiements récents + Sidebar ─────────────────────────────── --}}
<div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

    {{-- Tableau paiements --}}
    <div class="lg:col-span-8 bg-white dark:bg-slate-800 rounded-2xl border
                border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
        <div class="px-5 sm:px-6 py-4 border-b border-slate-200 dark:border-slate-700
                    bg-slate-50/50 dark:bg-slate-800/50
                    flex items-center justify-between">
            <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-100
                       flex items-center gap-2">
                <i class="bi bi-clock-history text-emerald-500"></i>
                Derniers paiements
            </h3>
            <a href="{{ route('payments.index') }}?fee_type_id={{ $feeType->id }}"
                class="text-xs font-medium text-blue-600 dark:text-blue-400 hover:underline">
                Tout voir
            </a>
        </div>

        @if($feeType->payments->count())

        {{-- Desktop --}}
        <div class="hidden sm:block overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="text-[11px] uppercase font-semibold tracking-wider
                                  text-slate-400 dark:text-slate-500
                                  bg-slate-50 dark:bg-slate-900/50">
                    <tr>
                        <th class="px-5 py-3 text-left">Élève</th>
                        <th class="px-5 py-3 text-left">Montant</th>
                        <th class="px-5 py-3 text-left">Méthode</th>
                        <th class="px-5 py-3 text-left">Statut</th>
                        <th class="px-5 py-3 text-left">Date</th>
                        <th class="px-5 py-3 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50">
                    @foreach($feeType->payments as $payment)
                    @php
                    $pStatus = strtolower($payment->status ?? 'pending');
                    $pCfg = match($pStatus) {
                    'paid' => ['text' => 'text-emerald-700 dark:text-emerald-300', 'bg' => 'bg-emerald-50
                    dark:bg-emerald-900/20', 'border' => 'border-emerald-200 dark:border-emerald-800', 'label' =>
                    'Payé', 'icon' => 'bi-check-circle-fill'],
                    'overdue' => ['text' => 'text-red-700 dark:text-red-300', 'bg' => 'bg-red-50 dark:bg-red-900/20',
                    'border' => 'border-red-200 dark:border-red-800', 'label' => 'En retard', 'icon' =>
                    'bi-exclamation-triangle-fill'],
                    default => ['text' => 'text-amber-700 dark:text-amber-300', 'bg' => 'bg-amber-50
                    dark:bg-amber-900/20', 'border' => 'border-amber-200 dark:border-amber-800', 'label' => 'En
                    attente', 'icon' => 'bi-hourglass-split'],
                    };
                    @endphp
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-2.5">
                                <div class="w-7 h-7 rounded-full shrink-0
                                                bg-linear-to-br from-blue-500 to-emerald-500
                                                flex items-center justify-center text-white text-[10px] font-bold">
                                    {{ strtoupper(substr($payment->student->user->name ?? 'E', 0, 2)) }}
                                </div>
                                <p class="font-medium text-slate-800 dark:text-slate-200 truncate max-w-32">
                                    {{ $payment->student->user->name ?? '—' }}
                                </p>
                            </div>
                        </td>
                        <td class="px-5 py-3 font-semibold text-slate-800 dark:text-slate-100">
                            {{ number_format($payment->amount ?? 0, 0, ',', ' ') }}
                            <span class="text-[10px] font-normal text-slate-400">FCFA</span>
                        </td>
                        <td class="px-5 py-3 text-slate-500 dark:text-slate-400 capitalize">
                            {{ $payment->payment_method ?? '—' }}
                        </td>
                        <td class="px-5 py-3">
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full
                                             text-[11px] font-semibold border
                                             {{ $pCfg['bg'] }} {{ $pCfg['text'] }} {{ $pCfg['border'] }}">
                                <i class="bi {{ $pCfg['icon'] }}"></i>
                                {{ $pCfg['label'] }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-slate-500 dark:text-slate-400 text-xs">
                            {{ $payment->created_at->format('d/m/Y') }}
                        </td>
                        <td class="px-5 py-3 text-right">
                            <a href="{{ route('payments.show', $payment) }}" class="inline-flex w-7 h-7 items-center justify-center rounded-lg
                                          border border-slate-200 dark:border-slate-600
                                          text-slate-500 hover:text-blue-600
                                          hover:border-blue-300 dark:hover:border-blue-500
                                          transition-all">
                                <i class="bi bi-eye text-xs"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Mobile --}}
        <div class="sm:hidden divide-y divide-slate-100 dark:divide-slate-700">
            @foreach($feeType->payments as $payment)
            @php
            $pStatus = strtolower($payment->status ?? 'pending');
            $pCfg = match($pStatus) {
            'paid' => ['text' => 'text-emerald-600 dark:text-emerald-400', 'label' => 'Payé'],
            'overdue' => ['text' => 'text-red-600 dark:text-red-400', 'label' => 'En retard'],
            default => ['text' => 'text-amber-600 dark:text-amber-400', 'label' => 'En attente'],
            };
            @endphp
            <div class="p-4 flex items-center gap-3">
                <div class="w-10 h-10 rounded-full shrink-0
                                bg-linear-to-br from-blue-500 to-emerald-500
                                flex items-center justify-center text-white text-xs font-bold">
                    {{ strtoupper(substr($payment->student->user->name ?? 'E', 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-slate-800 dark:text-slate-200 truncate">
                        {{ $payment->student->user->name ?? '—' }}
                    </p>
                    <p class="text-xs text-slate-500">
                        {{ number_format($payment->amount ?? 0, 0, ',', ' ') }} FCFA •
                        <span class="{{ $pCfg['text'] }} font-medium">{{ $pCfg['label'] }}</span>
                    </p>
                </div>
                <a href="{{ route('payments.show', $payment) }}" class="w-8 h-8 rounded-xl bg-slate-50 dark:bg-slate-700
                              flex items-center justify-center
                              text-slate-400 hover:text-blue-600 transition-colors">
                    <i class="bi bi-chevron-right text-xs"></i>
                </a>
            </div>
            @endforeach
        </div>

        @else
        <div class="py-16 text-center">
            <div class="w-14 h-14 mx-auto rounded-2xl bg-slate-100 dark:bg-slate-700
                            flex items-center justify-center mb-3">
                <i class="bi bi-receipt text-2xl text-slate-400"></i>
            </div>
            <p class="text-sm font-medium text-slate-600 dark:text-slate-300">
                Aucun paiement enregistré
            </p>
            <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">
                Les paiements liés à ce type de frais apparaîtront ici.
            </p>
            <a href="{{ route('payments.create') }}?fee_type_id={{ $feeType->id }}" class="mt-4 inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold
                          bg-emerald-600 hover:bg-emerald-700 text-white transition-all">
                <i class="bi bi-plus-circle"></i>
                Enregistrer un paiement
            </a>
        </div>
        @endif
    </div>

    {{-- Sidebar --}}
    <div class="lg:col-span-4 space-y-6">

        {{-- Détail frais --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200
                    dark:border-slate-700 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-700
                        bg-slate-50/50 dark:bg-slate-800/50">
                <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-100
                           flex items-center gap-2">
                    <i class="bi bi-info-circle text-blue-500"></i>
                    Informations
                </h3>
            </div>
            <div class="p-5 space-y-4">
                @foreach([
                ['icon' => 'bi-tag', 'label' => 'Nom', 'value' => $feeType->name, 'color' => 'blue'],
                ['icon' => 'bi-currency-dollar', 'label' => 'Montant', 'value' => number_format($feeType->amount ?? 0,
                0, ',', ' ').' FCFA', 'color' => 'emerald'],
                ['icon' => 'bi-folder', 'label' => 'Catégorie', 'value' => $feeType->category ?? '—', 'color' =>
                'amber'],
                ['icon' => 'bi-arrow-repeat', 'label' => 'Fréquence', 'value' => ucfirst($feeType->frequency ??
                'Unique'), 'color' => 'cyan'],
                ['icon' => 'bi-calendar-plus', 'label' => 'Créé le', 'value' => $feeType->created_at->format('d/m/Y'),
                'color' => 'slate'],
                ] as $info)
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0
                        {{ $info['color'] === 'blue'    ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-500' : '' }}
                        {{ $info['color'] === 'emerald' ? 'bg-emerald-50 dark:bg-emerald-900/30 text-emerald-500' : '' }}
                        {{ $info['color'] === 'amber'   ? 'bg-amber-50 dark:bg-amber-900/30 text-amber-500' : '' }}
                        {{ $info['color'] === 'cyan'    ? 'bg-cyan-50 dark:bg-cyan-900/30 text-cyan-500' : '' }}
                        {{ $info['color'] === 'slate'   ? 'bg-slate-100 dark:bg-slate-700 text-slate-500' : '' }}">
                        <i class="bi {{ $info['icon'] }} text-sm"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">
                            {{ $info['label'] }}
                        </p>
                        <p class="text-sm font-medium text-slate-800 dark:text-slate-200 truncate">
                            {{ $info['value'] }}
                        </p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Répartition statuts --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200
                    dark:border-slate-700 shadow-sm p-5">
            <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-100
                       flex items-center gap-2 mb-4">
                <i class="bi bi-pie-chart-fill text-emerald-500"></i>
                Répartition des statuts
            </h3>

            @if($totalCount > 0)
            <div class="space-y-3">
                @foreach([
                ['label' => 'Payés', 'count' => $paidCount, 'color' => 'bg-emerald-500', 'text' => 'text-emerald-700
                dark:text-emerald-300'],
                ['label' => 'En attente', 'count' => $pendingCount, 'color' => 'bg-amber-500', 'text' => 'text-amber-700
                dark:text-amber-300'],
                ['label' => 'En retard', 'count' => $overdueCount, 'color' => 'bg-red-500', 'text' => 'text-red-700
                dark:text-red-300'],
                ] as $bar)
                @php $pct = $totalCount > 0 ? round(($bar['count'] / $totalCount) * 100) : 0; @endphp
                <div>
                    <div class="flex items-center justify-between text-xs mb-1.5">
                        <span class="font-medium text-slate-600 dark:text-slate-300">{{ $bar['label'] }}</span>
                        <span class="font-bold {{ $bar['text'] }}">{{ $bar['count'] }} ({{ $pct }}%)</span>
                    </div>
                    <div class="h-2 w-full bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
                        <div class="h-full {{ $bar['color'] }} rounded-full transition-all duration-700"
                            style="width: {{ $pct }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <p class="text-xs text-slate-400 dark:text-slate-500 text-center py-4">
                Aucune donnée disponible
            </p>
            @endif
        </div>

        {{-- Lien ajout paiement --}}
        <a href="{{ route('payments.create') }}?fee_type_id={{ $feeType->id }}" class="flex items-center justify-between w-full p-4 rounded-2xl
                  bg-linear-to-r from-emerald-600 to-teal-600
                  text-white shadow-lg shadow-emerald-500/20
                  hover:shadow-emerald-500/30 hover:scale-[1.01]
                  transition-all duration-200 group">
            <div class="flex items-center gap-3">
                <i class="bi bi-plus-circle-fill text-xl"></i>
                <div>
                    <p class="text-sm font-bold">Nouveau paiement</p>
                    <p class="text-xs text-emerald-100">Enregistrer un paiement</p>
                </div>
            </div>
            <i class="bi bi-arrow-right-short text-xl opacity-70
                      group-hover:translate-x-1 transition-transform"></i>
        </a>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const btn = document.getElementById('delete-feetype-btn');
    const form = document.getElementById('delete-feetype-form');

    btn?.addEventListener('click', () => {
        if (confirm(
                'Supprimer le type de frais "{{ addslashes($feeType->name) }}" ?\n\n' +
                'Attention : tous les paiements liés seront également affectés.'
            )) {
            form.submit();
        }
    });
});
</script>
@endsection