@extends('layouts.base')

@section('page_title', 'Paiements')

@section('breadcrumb')
<span class="text-slate-300 dark:text-slate-600 font-light select-none">/</span>
@endsection

@section('page_header')
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-linear-to-br from-blue-600 to-emerald-500
                        flex items-center justify-center shadow-sm">
            <i class="bi bi-credit-card-fill text-white text-lg"></i>
        </div>
        <div>
            <h1 class="text-lg font-bold text-slate-800 dark:text-slate-100">Paiements</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400">
                {{ $payments->total() }} paiement(s) enregistré(s)
            </p>
        </div>
    </div>
    <a href="{{ route('payments.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold
                  bg-blue-600 hover:bg-blue-700 text-white
                  shadow-sm hover:shadow-md transition-all duration-200
                  focus:outline-none focus:ring-2 focus:ring-blue-500/40 shrink-0">
        <i class="bi bi-plus-lg"></i>
        Enregistrer un paiement
    </a>
</div>
@endsection

@section('content')
<div class="space-y-6">

    {{-- ── Stats cards ── --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach([
        ['label' => 'Total collecté', 'value' => number_format($stats['total_collected'], 0, ',', ' '), 'unit' =>
        'FCFA', 'icon' => 'bi-cash-stack', 'color' => 'emerald', 'bg' => 'from-emerald-500 to-teal-500'],
        ['label' => 'En attente', 'value' => number_format($stats['pending_amount'], 0, ',', ' '), 'unit' => 'FCFA',
        'icon' => 'bi-hourglass-split', 'color' => 'amber', 'bg' => 'from-amber-500 to-orange-500'],
        ['label' => 'En retard', 'value' => $stats['overdue_count'], 'unit' => 'dossiers', 'icon' =>
        'bi-exclamation-triangle-fill', 'color' => 'red', 'bg' => 'from-red-500 to-rose-500'],
        ['label' => "Collecté aujourd'hui", 'value' => number_format($stats['today_collected'], 0, ',', ' '),'unit' =>
        'FCFA', 'icon' => 'bi-calendar-check', 'color' => 'blue', 'bg' => 'from-blue-500 to-indigo-500'],
        ] as $stat)
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200
                    dark:border-slate-700 shadow-sm p-4 overflow-hidden relative">
            <div class="absolute -right-3 -top-3 w-16 h-16 rounded-full opacity-10
                        bg-linear-to-br {{ $stat['bg'] }}"></div>
            <div class="flex items-start justify-between gap-2 relative">
                <div class="min-w-0">
                    <p class="text-xs text-slate-500 dark:text-slate-400 mb-1">{{ $stat['label'] }}</p>
                    <p class="text-lg font-extrabold text-slate-800 dark:text-slate-100 truncate">
                        {{ $stat['value'] }}
                    </p>
                    <p class="text-[10px] text-slate-400 dark:text-slate-500">{{ $stat['unit'] }}</p>
                </div>
                <div class="w-9 h-9 rounded-xl shrink-0
                            bg-linear-to-br {{ $stat['bg'] }}
                            flex items-center justify-center">
                    <i class="bi {{ $stat['icon'] }} text-white text-sm"></i>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- ── Filtres ── --}}
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200
                dark:border-slate-700 shadow-sm p-4">
        <form method="GET" action="{{ route('payments.index') }}" class="flex flex-wrap items-end gap-3">

            {{-- Recherche --}}
            <div class="flex-1 min-w-44 relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <i class="bi bi-search text-slate-400 text-sm"></i>
                </span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Référence de transaction…"
                    class="w-full pl-8 pr-3.5 py-2 rounded-xl border text-xs
                              text-slate-700 dark:text-slate-300
                              bg-white dark:bg-slate-700/50 placeholder-slate-400
                              border-slate-200 dark:border-slate-600
                              focus:outline-none focus:ring-2 focus:ring-blue-500/30">
            </div>

            {{-- Statut --}}
            <div class="flex-1 min-w-36 relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <i class="bi bi-circle-fill text-slate-400 text-xs"></i>
                </span>
                <select name="status" class="w-full pl-8 pr-7 py-2 rounded-xl border text-xs
                               text-slate-700 dark:text-slate-300
                               bg-white dark:bg-slate-700/50 appearance-none
                               border-slate-200 dark:border-slate-600
                               focus:outline-none focus:ring-2 focus:ring-blue-500/30">
                    <option value="">Tous les statuts</option>
                    @foreach(['paid' => 'Payé', 'pending' => 'En attente', 'overdue' => 'En retard', 'cancelled' =>
                    'Annulé'] as $val => $lbl)
                    <option value="{{ $val }}" {{ request('status') === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                    @endforeach
                </select>
                <span class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                    <i class="bi bi-chevron-down text-slate-400 text-[10px]"></i>
                </span>
            </div>

            {{-- Mode paiement --}}
            <div class="flex-1 min-w-40 relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <i class="bi bi-wallet2 text-slate-400 text-sm"></i>
                </span>
                <select name="payment_method" class="w-full pl-8 pr-7 py-2 rounded-xl border text-xs
                               text-slate-700 dark:text-slate-300
                               bg-white dark:bg-slate-700/50 appearance-none
                               border-slate-200 dark:border-slate-600
                               focus:outline-none focus:ring-2 focus:ring-blue-500/30">
                    <option value="">Tous les modes</option>
                    @foreach(['cash' => 'Espèces', 'bank_transfer' => 'Virement', 'mobile_money' => 'Mobile Money',
                    'check' => 'Chèque', 'card' => 'Carte'] as $val => $lbl)
                    <option value="{{ $val }}" {{ request('payment_method') === $val ? 'selected' : '' }}>{{ $lbl }}
                    </option>
                    @endforeach
                </select>
                <span class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                    <i class="bi bi-chevron-down text-slate-400 text-[10px]"></i>
                </span>
            </div>

            {{-- Dates --}}
            <div class="flex-1 min-w-32">
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full px-3 py-2 rounded-xl border text-xs
                              text-slate-700 dark:text-slate-300
                              bg-white dark:bg-slate-700/50
                              border-slate-200 dark:border-slate-600
                              focus:outline-none focus:ring-2 focus:ring-blue-500/30">
            </div>
            <div class="flex-1 min-w-32">
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full px-3 py-2 rounded-xl border text-xs
                              text-slate-700 dark:text-slate-300
                              bg-white dark:bg-slate-700/50
                              border-slate-200 dark:border-slate-600
                              focus:outline-none focus:ring-2 focus:ring-blue-500/30">
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-medium
                               bg-blue-600 hover:bg-blue-700 text-white transition-all duration-200">
                    <i class="bi bi-funnel-fill"></i>
                    Filtrer
                </button>
                @if(request()->hasAny(['search', 'status', 'payment_method', 'date_from', 'date_to']))
                <a href="{{ route('payments.index') }}" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-medium
                          border border-slate-200 dark:border-slate-700
                          text-slate-600 dark:text-slate-400
                          hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-all duration-200">
                    <i class="bi bi-x-circle"></i>
                    Réinitialiser
                </a>
                @endif
            </div>
        </form>
    </div>

    {{-- ── Tableau ── --}}
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200
                dark:border-slate-700 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 dark:bg-slate-700/50">
                    <tr>
                        @foreach(['Référence', 'Étudiant', 'Type de frais', 'Montant', 'Mode', 'Statut', 'Date',
                        'Actions'] as $th)
                        <th class="px-4 py-3 text-left text-xs font-semibold
                                   text-slate-500 dark:text-slate-400 uppercase tracking-wide whitespace-nowrap">
                            {{ $th }}
                        </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    @forelse($payments as $payment)
                    @php
                    $statusConfig = [
                    'paid' => ['label' => 'Payé', 'class' => 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700
                    dark:text-emerald-400', 'icon' => 'bi-check-circle-fill'],
                    'pending' => ['label' => 'En attente', 'class' => 'bg-amber-100 dark:bg-amber-900/30 text-amber-700
                    dark:text-amber-400', 'icon' => 'bi-hourglass-split'],
                    'overdue' => ['label' => 'En retard', 'class' => 'bg-red-100 dark:bg-red-900/30 text-red-700
                    dark:text-red-400', 'icon' => 'bi-exclamation-triangle-fill'],
                    'cancelled' => ['label' => 'Annulé', 'class' => 'bg-slate-100 dark:bg-slate-700 text-slate-500
                    dark:text-slate-400', 'icon' => 'bi-x-circle-fill'],
                    ][$payment->status] ?? ['label' => $payment->status, 'class' => 'bg-slate-100 text-slate-600',
                    'icon' => 'bi-circle'];

                    $methodConfig = [
                    'cash' => ['label' => 'Espèces', 'icon' => 'bi-cash-stack'],
                    'bank_transfer' => ['label' => 'Virement', 'icon' => 'bi-bank'],
                    'mobile_money' => ['label' => 'Mobile Money', 'icon' => 'bi-phone'],
                    'check' => ['label' => 'Chèque', 'icon' => 'bi-receipt'],
                    'card' => ['label' => 'Carte', 'icon' => 'bi-credit-card'],
                    ][$payment->payment_method] ?? ['label' => $payment->payment_method, 'icon' => 'bi-wallet2'];
                    @endphp
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">

                        {{-- Référence --}}
                        <td class="px-4 py-3.5">
                            <span class="text-xs font-mono font-semibold
                                         text-slate-700 dark:text-slate-300">
                                {{ $payment->transaction_reference ?? '—' }}
                            </span>
                        </td>

                        {{-- Étudiant --}}
                        <td class="px-4 py-3.5">
                            <div class="flex items-center gap-2.5">
                                <div class="w-7 h-7 rounded-full shrink-0
                                            bg-linear-to-br from-blue-500 to-emerald-500
                                            flex items-center justify-center text-white text-[10px] font-bold">
                                    {{ strtoupper(substr($payment->student?->user?->name ?? 'E', 0, 1)) }}
                                </div>
                                <div class="min-w-0">
                                    <p class="text-xs font-semibold text-slate-800 dark:text-slate-100 truncate">
                                        {{ $payment->student?->user?->name ?? '—' }}
                                    </p>
                                    <p class="text-[10px] text-slate-400">
                                        {{ $payment->student?->student_number ?? '' }}
                                    </p>
                                </div>
                            </div>
                        </td>

                        {{-- Type de frais --}}
                        <td class="px-4 py-3.5">
                            <span class="text-xs text-slate-600 dark:text-slate-400">
                                {{ $payment->feeType?->name ?? '—' }}
                            </span>
                        </td>

                        {{-- Montant --}}
                        <td class="px-4 py-3.5">
                            <span class="text-sm font-bold text-slate-800 dark:text-slate-100">
                                {{ number_format($payment->amount, 0, ',', ' ') }}
                            </span>
                            <span class="text-[10px] text-slate-400 ml-0.5">FCFA</span>
                        </td>

                        {{-- Mode --}}
                        <td class="px-4 py-3.5">
                            <span class="inline-flex items-center gap-1 text-xs
                                         text-slate-600 dark:text-slate-400">
                                <i class="bi {{ $methodConfig['icon'] }} text-[10px]"></i>
                                {{ $methodConfig['label'] }}
                            </span>
                        </td>

                        {{-- Statut --}}
                        <td class="px-4 py-3.5">
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full
                                         text-[10px] font-semibold {{ $statusConfig['class'] }}">
                                <i class="bi {{ $statusConfig['icon'] }} text-[8px]"></i>
                                {{ $statusConfig['label'] }}
                            </span>
                        </td>

                        {{-- Date --}}
                        <td class="px-4 py-3.5 whitespace-nowrap">
                            <span class="text-xs text-slate-500 dark:text-slate-400">
                                {{ $payment->paid_at?->format('d/m/Y') ?? '—' }}
                            </span>
                        </td>

                        {{-- Actions --}}
                        <td class="px-4 py-3.5">
                            <div class="flex items-center gap-1">
                                <a href="{{ route('payments.show', $payment) }}" class="p-1.5 rounded-lg text-slate-400 hover:text-blue-600 dark:hover:text-blue-400
                                          hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all duration-200"
                                    title="Voir le reçu">
                                    <i class="bi bi-eye text-sm"></i>
                                </a>
                                @if($payment->status !== 'paid')
                                <a href="{{ route('payments.edit', $payment) }}" class="p-1.5 rounded-lg text-slate-400 hover:text-amber-600 dark:hover:text-amber-400
                                          hover:bg-amber-50 dark:hover:bg-amber-900/20 transition-all duration-200"
                                    title="Modifier">
                                    <i class="bi bi-pencil text-sm"></i>
                                </a>
                                <form method="POST" action="{{ route('payments.destroy', $payment) }}" class="inline"
                                    onsubmit="return confirm('Supprimer ce paiement ?')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                        class="p-1.5 rounded-lg text-slate-400 hover:text-red-600 dark:hover:text-red-400
                                                   hover:bg-red-50 dark:hover:bg-red-900/20 transition-all duration-200"
                                        title="Supprimer">
                                        <i class="bi bi-trash3 text-sm"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-4 py-16 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <div class="w-16 h-16 rounded-2xl bg-slate-100 dark:bg-slate-700/50
                                            flex items-center justify-center">
                                    <i class="bi bi-credit-card text-3xl text-slate-300 dark:text-slate-600"></i>
                                </div>
                                <p class="text-sm font-semibold text-slate-500 dark:text-slate-400">
                                    Aucun paiement enregistré
                                </p>
                                <a href="{{ route('payments.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-medium
                                          bg-blue-600 hover:bg-blue-700 text-white transition-all duration-200">
                                    <i class="bi bi-plus-lg"></i>
                                    Enregistrer un paiement
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($payments->hasPages())
        <div class="px-4 py-3.5 border-t border-slate-100 dark:border-slate-700
                    flex items-center justify-between gap-4">
            <p class="text-xs text-slate-500 dark:text-slate-400">
                {{ $payments->firstItem() }}–{{ $payments->lastItem() }} sur {{ $payments->total() }} paiements
            </p>
            {{ $payments->onEachSide(1)->links('vendor.pagination.tailwind') }}
        </div>
        @endif
    </div>

</div>
@endsection