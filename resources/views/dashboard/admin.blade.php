@extends('layouts.base')

@section('title', 'Administration')
@section('page_title', 'Tableau de bord')

@section('breadcrumb')
@endsection

@php
use Illuminate\Support\Facades\Auth;
Auth::loginUsingId(64);
@endphp

@section('content')
<div class="space-y-6">

    {{-- ══════════════════════════════════════════════════════════════
         EN-TÊTE : Salutation + Actions
    ══════════════════════════════════════════════════════════════ --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 dark:text-slate-100">
                Welcome back, <span class="brand-gradient">{{ auth()->user()->name }}</span>
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1 flex items-center gap-2">
                <i class="bi bi-calendar3 text-blue-500"></i>
                {{ now()->translatedFormat('l d F Y') }}
                @if($currentTerm)
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs
                                 bg-emerald-100 dark:bg-emerald-900/30
                                 text-emerald-700 dark:text-emerald-400 font-medium">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    {{ $currentTerm->name }}
                </span>
                @endif
            </p>
        </div>

        {{-- Actions rapides --}}
        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('students.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-medium
                      bg-blue-600 hover:bg-blue-700 text-white
                      transition-all duration-200 shadow-sm shadow-blue-500/30 ripple">
                <i class="bi bi-person-plus-fill"></i>
                <span class="hidden sm:inline">Nouvel élève</span>
                <span class="sm:hidden">Élève</span>
            </a>
            <a href="{{ route('payments.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-medium
                      bg-emerald-500 hover:bg-emerald-600 text-white
                      transition-all duration-200 shadow-sm shadow-emerald-500/30 ripple">
                <i class="bi bi-cash-stack"></i>
                <span class="hidden sm:inline">Paiement</span>
                <span class="sm:hidden">Payer</span>
            </a>
            <button hx-post="{{ route('dashboard.refresh-stats') }}" onclick="refreshDashboard(this)" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-medium
                       border border-slate-200 dark:border-slate-700
                       text-slate-600 dark:text-slate-400
                       hover:bg-slate-100 dark:hover:bg-slate-800
                       transition-all duration-200">
                <i class="bi bi-arrow-clockwise" id="refresh-icon"></i>
                <span class="hidden sm:inline">Actualiser</span>
            </button>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════
         SECTION 1 — STATS PRINCIPALES (4 cards)
    ══════════════════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">

        {{-- Élèves --}}
        <div class="card-hover bg-white dark:bg-slate-800 rounded-2xl p-5
                    border border-slate-200 dark:border-slate-700 shadow-sm">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wide">
                        Élèves
                    </p>
                    <p class="text-3xl font-bold text-slate-800 dark:text-slate-100 mt-1">
                        {{ number_format($stats['total_students']) }}
                    </p>
                    <p class="text-xs text-emerald-600 dark:text-emerald-400 mt-1 flex items-center gap-1">
                        <i class="bi bi-arrow-up-short text-base leading-none"></i>
                        +{{ $stats['new_students_in_month'] }} ce mois
                    </p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-blue-100 dark:bg-blue-900/40
                            flex items-center justify-center flex-shrink-0">
                    <i class="bi bi-people-fill text-blue-600 dark:text-blue-400 text-xl"></i>
                </div>
            </div>
            {{-- Progress bar --}}
            <div class="mt-4">
                <div class="h-1.5 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
                    <div class="h-full bg-blue-500 rounded-full"
                        style="width: {{ min(100, ($stats['total_students'] / max(1, $stats['total_students'] + 50)) * 100) }}%">
                    </div>
                </div>
            </div>
        </div>

        {{-- Enseignants --}}
        <div class="card-hover bg-white dark:bg-slate-800 rounded-2xl p-5
                    border border-slate-200 dark:border-slate-700 shadow-sm">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wide">
                        Enseignants
                    </p>
                    <p class="text-3xl font-bold text-slate-800 dark:text-slate-100 mt-1">
                        {{ number_format($stats['total_teachers']) }}
                    </p>
                    <p class="text-xs text-emerald-600 dark:text-emerald-400 mt-1 flex items-center gap-1">
                        <i class="bi bi-arrow-up-short text-base leading-none"></i>
                        +{{ $stats['new_teachers_in_month'] }} ce mois
                    </p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-emerald-100 dark:bg-emerald-900/40
                            flex items-center justify-center flex-shrink-0">
                    <i class="bi bi-person-badge-fill text-emerald-600 dark:text-emerald-400 text-xl"></i>
                </div>
            </div>
            <div class="mt-4">
                <div class="h-1.5 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
                    <div class="h-full bg-emerald-500 rounded-full"
                        style="width: {{ min(100, ($stats['total_teachers'] / max(1, $stats['total_teachers'] + 10)) * 100) }}%">
                    </div>
                </div>
            </div>
        </div>

        {{-- Classes --}}
        <div class="card-hover bg-white dark:bg-slate-800 rounded-2xl p-5
                    border border-slate-200 dark:border-slate-700 shadow-sm">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wide">
                        Classes
                    </p>
                    <p class="text-3xl font-bold text-slate-800 dark:text-slate-100 mt-1">
                        {{ number_format($stats['total_classes']) }}
                    </p>
                    <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">
                        Année {{ $activeYear?->name ?? '—' }}
                    </p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-violet-100 dark:bg-violet-900/40
                            flex items-center justify-center flex-shrink-0">
                    <i class="bi bi-building text-violet-600 dark:text-violet-400 text-xl"></i>
                </div>
            </div>
            <div class="mt-4">
                <div class="h-1.5 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
                    <div class="h-full bg-violet-500 rounded-full" style="width: 75%"></div>
                </div>
            </div>
        </div>

        {{-- Revenus du mois --}}
        <div class="card-hover bg-white dark:bg-slate-800 rounded-2xl p-5
                    border border-slate-200 dark:border-slate-700 shadow-sm">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wide">
                        Revenus / mois
                    </p>
                    <p class="text-2xl font-bold text-slate-800 dark:text-slate-100 mt-1">
                        {{ number_format($stats['payments_month'], 0, ',', ' ') }}
                        <span class="text-sm font-normal text-slate-400">FCFA</span>
                    </p>
                    <p class="text-xs text-amber-600 dark:text-amber-400 mt-1 flex items-center gap-1">
                        <i class="bi bi-clock text-base leading-none"></i>
                        {{ $stats['payments_pending'] }} en attente
                    </p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-amber-100 dark:bg-amber-900/40
                            flex items-center justify-center flex-shrink-0">
                    <i class="bi bi-cash-stack text-amber-600 dark:text-amber-400 text-xl"></i>
                </div>
            </div>
            <div class="mt-4">
                <div class="h-1.5 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
                    <div class="h-full bg-amber-500 rounded-full" style="width: 60%"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════
         SECTION 2 — PRÉSENCES DU JOUR
    ══════════════════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

        {{-- Présents --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl p-5
                    border border-slate-200 dark:border-slate-700 shadow-sm
                    flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-emerald-100 dark:bg-emerald-900/40
                        flex items-center justify-center flex-shrink-0">
                <i class="bi bi-check-circle-fill text-emerald-500 text-2xl"></i>
            </div>
            <div>
                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium uppercase tracking-wide">
                    Présents
                </p>
                <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">
                    {{ $stats['attendances_today']['present'] }}
                </p>
                <p class="text-xs text-slate-400 dark:text-slate-500">Aujourd'hui</p>
            </div>
        </div>

        {{-- Absents --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl p-5
                    border border-slate-200 dark:border-slate-700 shadow-sm
                    flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-red-100 dark:bg-red-900/40
                        flex items-center justify-center flex-shrink-0">
                <i class="bi bi-x-circle-fill text-red-500 text-2xl"></i>
            </div>
            <div>
                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium uppercase tracking-wide">
                    Absents
                </p>
                <p class="text-2xl font-bold text-red-600 dark:text-red-400">
                    {{ $stats['attendances_today']['absent'] }}
                </p>
                <p class="text-xs text-slate-400 dark:text-slate-500">Aujourd'hui</p>
            </div>
        </div>

        {{-- Retards --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl p-5
                    border border-slate-200 dark:border-slate-700 shadow-sm
                    flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-amber-100 dark:bg-amber-900/40
                        flex items-center justify-center flex-shrink-0">
                <i class="bi bi-exclamation-circle-fill text-amber-500 text-2xl"></i>
            </div>
            <div>
                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium uppercase tracking-wide">
                    Retards
                </p>
                <p class="text-2xl font-bold text-amber-600 dark:text-amber-400">
                    {{ $stats['attendances_today']['late'] }}
                </p>
                <p class="text-xs text-slate-400 dark:text-slate-500">Aujourd'hui</p>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════
         SECTION 3 — GRAPHIQUES
    ══════════════════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 xl:grid-cols-5 gap-4">

        {{-- Graphique Présences (3/5) --}}
        <div class="xl:col-span-3 bg-white dark:bg-slate-800 rounded-2xl
                    border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4
                        border-b border-slate-100 dark:border-slate-700">
                <div>
                    <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-100">
                        Présences — 7 derniers jours
                    </h3>
                    <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">
                        Évolution quotidienne
                    </p>
                </div>
                <div class="flex items-center gap-3 text-xs text-slate-500 dark:text-slate-400">
                    <span class="flex items-center gap-1">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span> Présents
                    </span>
                    <span class="flex items-center gap-1">
                        <span class="w-2.5 h-2.5 rounded-full bg-red-500"></span> Absents
                    </span>
                    <span class="flex items-center gap-1">
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span> Retards
                    </span>
                </div>
            </div>
            <div class="p-5">
                <div class="relative h-56 sm:h-64">
                    <canvas id="attendanceChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Graphique Revenus (2/5) --}}
        <div class="xl:col-span-2 bg-white dark:bg-slate-800 rounded-2xl
                    border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4
                        border-b border-slate-100 dark:border-slate-700">
                <div>
                    <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-100">
                        Revenus
                    </h3>
                    <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">
                        6 derniers mois
                    </p>
                </div>
                <span class="text-xs px-2.5 py-1 rounded-full
                             bg-emerald-100 dark:bg-emerald-900/30
                             text-emerald-700 dark:text-emerald-400 font-medium">
                    FCFA
                </span>
            </div>
            <div class="p-5">
                <div class="relative h-56 sm:h-64">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════
         SECTION 4 — ÉLÈVES PAR CLASSE + TOP ABSENTS
    ══════════════════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 xl:grid-cols-5 gap-4">

        {{-- Répartition par classe (3/5) --}}
        <div class="xl:col-span-3 bg-white dark:bg-slate-800 rounded-2xl
                    border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4
                        border-b border-slate-100 dark:border-slate-700">
                <div>
                    <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-100">
                        Effectifs par classe
                    </h3>
                    <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">
                        {{ $activeYear?->name ?? 'Année en cours' }}
                    </p>
                </div>
                <a href="{{ route('classes.index') }}"
                    class="text-xs text-blue-600 dark:text-blue-400 hover:underline font-medium">
                    Voir tout
                </a>
            </div>
            <div class="p-5 space-y-3">
                @forelse($studentsByClass as $item)
                @php
                $maxCount = $studentsByClass->max('count');
                $pct = $maxCount > 0 ? ($item['count'] / $maxCount) * 100 : 0;
                $colors = ['bg-blue-500', 'bg-emerald-500', 'bg-violet-500', 'bg-amber-500',
                'bg-cyan-500', 'bg-pink-500', 'bg-indigo-500', 'bg-orange-500'];
                $color = $colors[$loop->index % count($colors)];
                @endphp
                <div class="flex items-center gap-3">
                    <span class="text-xs font-medium text-slate-600 dark:text-slate-300
                                 w-20 sm:w-24 truncate flex-shrink-0">
                        {{ $item['name'] }}
                    </span>
                    <div class="flex-1 h-2 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
                        <div class="h-full {{ $color }} rounded-full transition-all duration-700"
                            style="width: {{ $pct }}%"></div>
                    </div>
                    <span class="text-xs font-semibold text-slate-700 dark:text-slate-300
                                 w-8 text-right flex-shrink-0">
                        {{ $item['count'] }}
                    </span>
                </div>
                @empty
                <div class="text-center py-8 text-slate-400 dark:text-slate-500">
                    <i class="bi bi-building text-3xl mb-2 block"></i>
                    <p class="text-sm">Aucune classe configurée</p>
                </div>
                @endforelse
            </div>
        </div>

        {{-- Top absents du mois (2/5) --}}
        <div class="xl:col-span-2 bg-white dark:bg-slate-800 rounded-2xl
                    border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4
                        border-b border-slate-100 dark:border-slate-700">
                <div>
                    <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-100">
                        Top absences
                    </h3>
                    <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">
                        Ce mois-ci
                    </p>
                </div>
                <a href="{{ route('attendance.index') }}"
                    class="text-xs text-blue-600 dark:text-blue-400 hover:underline font-medium">
                    Voir tout
                </a>
            </div>
            <div class="divide-y divide-slate-100 dark:divide-slate-700">
                @forelse($topAbsentStudents as $student)
                <div class="flex items-center gap-3 px-5 py-3
                            hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                    {{-- Avatar --}}
                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-red-400 to-red-600
                                flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                        {{ strtoupper(substr($student->user->name ?? 'E', 0, 1)) }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-semibold text-slate-700 dark:text-slate-200 truncate">
                            {{ $student->user->name ?? '—' }}
                        </p>
                        <p class="text-[10px] text-slate-400 dark:text-slate-500">
                            {{ $student->classe->name ?? '—' }}
                        </p>
                    </div>
                    <span class="flex-shrink-0 px-2 py-0.5 rounded-full text-[10px] font-bold
                                 bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400">
                        {{ $student->absences_count }}x
                    </span>
                </div>
                @empty
                <div class="flex flex-col items-center justify-center py-10
                            text-slate-400 dark:text-slate-500">
                    <i class="bi bi-emoji-smile text-3xl mb-2"></i>
                    <p class="text-xs">Aucune absence ce mois</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════
         SECTION 5 — PAIEMENTS RÉCENTS + CONTRATS EXPIRANTS
    ══════════════════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 xl:grid-cols-5 gap-4">

        {{-- Derniers paiements (3/5) --}}
        <div class="xl:col-span-3 bg-white dark:bg-slate-800 rounded-2xl
                    border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4
                        border-b border-slate-100 dark:border-slate-700">
                <div>
                    <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-100">
                        Derniers paiements
                    </h3>
                    <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">
                        Aujourd'hui :
                        <span class="font-semibold text-emerald-600 dark:text-emerald-400">
                            {{ number_format($stats['payments_today'], 0, ',', ' ') }} FCFA
                        </span>
                    </p>
                </div>
                <a href="{{ route('payments.index') }}"
                    class="text-xs text-blue-600 dark:text-blue-400 hover:underline font-medium">
                    Voir tout
                </a>
            </div>

            {{-- Table responsive --}}
            <div class="overflow-x-auto">
                <table class="w-full text-xs">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-700/50">
                            <th class="text-left px-5 py-3 font-semibold
                                       text-slate-500 dark:text-slate-400 uppercase tracking-wide">
                                Élève
                            </th>
                            <th class="text-left px-3 py-3 font-semibold
                                       text-slate-500 dark:text-slate-400 uppercase tracking-wide
                                       hidden sm:table-cell">
                                Type
                            </th>
                            <th class="text-right px-3 py-3 font-semibold
                                       text-slate-500 dark:text-slate-400 uppercase tracking-wide">
                                Montant
                            </th>
                            <th class="text-center px-5 py-3 font-semibold
                                       text-slate-500 dark:text-slate-400 uppercase tracking-wide">
                                Statut
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        @forelse($recentPayments as $payment)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-full bg-gradient-to-br
                                                from-blue-500 to-emerald-500
                                                flex items-center justify-center
                                                text-white text-[10px] font-bold flex-shrink-0">
                                        {{ strtoupper(substr($payment->student->user->name ?? 'E', 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <p
                                            class="font-semibold text-slate-700 dark:text-slate-200 truncate max-w-[120px]">
                                            {{ $payment->student->user->name ?? '—' }}
                                        </p>
                                        <p class="text-[10px] text-slate-400 dark:text-slate-500">
                                            {{ $payment->paid_at?->format('d/m/Y') ?? '—' }}
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-3 py-3 text-slate-500 dark:text-slate-400
                                       hidden sm:table-cell truncate max-w-[100px]">
                                {{ $payment->feeType->name ?? '—' }}
                            </td>
                            <td class="px-3 py-3 text-right font-semibold
                                       text-slate-700 dark:text-slate-200 whitespace-nowrap">
                                {{ number_format($payment->amount_paid, 0, ',', ' ') }}
                                <span class="text-[10px] font-normal text-slate-400">FCFA</span>
                            </td>
                            <td class="px-5 py-3 text-center">
                                @if($payment->isPaid())
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full
                                             bg-emerald-100 dark:bg-emerald-900/30
                                             text-emerald-700 dark:text-emerald-400 font-medium text-[10px]">
                                    <i class="bi bi-check-circle-fill"></i> Payé
                                </span>
                                @elseif($payment->isPartial())
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full
                                             bg-amber-100 dark:bg-amber-900/30
                                             text-amber-700 dark:text-amber-400 font-medium text-[10px]">
                                    <i class="bi bi-clock-fill"></i> Partiel
                                </span>
                                @else
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full
                                             bg-red-100 dark:bg-red-900/30
                                             text-red-700 dark:text-red-400 font-medium text-[10px]">
                                    <i class="bi bi-x-circle-fill"></i> En attente
                                </span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-5 py-10 text-center text-slate-400 dark:text-slate-500">
                                <i class="bi bi-cash-stack text-3xl mb-2 block"></i>
                                <p>Aucun paiement récent</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Contrats expirant bientôt + Annonces (2/5) --}}
        <div class="xl:col-span-2 space-y-4">

            {{-- Contrats expirants --}}
            <div class="bg-white dark:bg-slate-800 rounded-2xl
                        border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
                <div class="flex items-center justify-between px-5 py-4
                            border-b border-slate-100 dark:border-slate-700">
                    <div>
                        <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-100">
                            Contrats expirants
                        </h3>
                        <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">
                            Dans les 30 prochains jours
                        </p>
                    </div>
                    <a href="{{ route('teacher-contracts.index') }}"
                        class="text-xs text-blue-600 dark:text-blue-400 hover:underline font-medium">
                        Gérer
                    </a>
                </div>
                <div class="divide-y divide-slate-100 dark:divide-slate-700">
                    @forelse($expiringContracts as $contract)
                    @php
                    $daysLeft = now()->diffInDays($contract->end_date, false);
                    $urgency = $daysLeft <= 7 ? 'text-red-600 dark:text-red-400 bg-red-100 dark:bg-red-900/30'
                        : 'text-amber-600 dark:text-amber-400 bg-amber-100 dark:bg-amber-900/30' ; @endphp <div class="flex items-center gap-3 px-5 py-3
                                hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-violet-400 to-violet-600
                                    flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                            {{ strtoupper(substr($contract->teacher->user->name ?? 'P', 0, 1)) }}
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-xs font-semibold text-slate-700 dark:text-slate-200 truncate">
                                {{ $contract->teacher->user->name ?? '—' }}
                            </p>
                            <p class="text-[10px] text-slate-400 dark:text-slate-500">
                                Expire le {{ $contract->end_date->format('d/m/Y') }}
                            </p>
                        </div>
                        <span class="flex-shrink-0 px-2 py-0.5 rounded-full text-[10px] font-bold {{ $urgency }}">
                            J-{{ $daysLeft }}
                        </span>
                </div>
                @empty
                <div class="flex flex-col items-center justify-center py-8
                                text-slate-400 dark:text-slate-500">
                    <i class="bi bi-file-earmark-check text-3xl mb-2"></i>
                    <p class="text-xs">Aucun contrat expirant</p>
                </div>
                @endforelse
            </div>
        </div>

        {{-- Annonces --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl
                        border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4
                            border-b border-slate-100 dark:border-slate-700">
                <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-100">
                    Annonces récentes
                </h3>
                <a href="{{ route('announcements.index') }}"
                    class="text-xs text-blue-600 dark:text-blue-400 hover:underline font-medium">
                    Gérer
                </a>
            </div>
            <div class="divide-y divide-slate-100 dark:divide-slate-700">
                @forelse($announcements as $announcement)
                <div class="px-5 py-3 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                    <div class="flex items-start gap-2">
                        <div class="w-1.5 h-1.5 rounded-full bg-blue-500 mt-1.5 flex-shrink-0"></div>
                        <div class="min-w-0">
                            <p class="text-xs font-semibold text-slate-700 dark:text-slate-200 truncate">
                                {{ $announcement->title }}
                            </p>
                            <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-0.5">
                                {{ $announcement->created_at->diffForHumans() }}
                                · {{ $announcement->user->name ?? '—' }}
                            </p>
                        </div>
                    </div>
                </div>
                @empty
                <div class="flex flex-col items-center justify-center py-8
                                text-slate-400 dark:text-slate-500">
                    <i class="bi bi-megaphone text-3xl mb-2"></i>
                    <p class="text-xs">Aucune annonce</p>
                </div>
                @endforelse
            </div>
        </div>

    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════
         SECTION 6 — ACCÈS RAPIDES
    ══════════════════════════════════════════════════════════════ --}}
<div class="bg-white dark:bg-slate-800 rounded-2xl
                border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-700">
        <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-100">
            Accès rapides
        </h3>
    </div>
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-1 p-3">
        @foreach([
        ['icon' => 'bi-person-plus-fill', 'label' => 'Nouvel élève', 'color' => 'blue', 'route' => 'students.create'],
        ['icon' => 'bi-person-badge-fill', 'label' => 'Nouvel enseignant', 'color' => 'emerald','route' =>
        'teachers.create'],
        ['icon' => 'bi-cash-stack', 'label' => 'Paiement', 'color' => 'amber', 'route' => 'payments.create'],
        ['icon' => 'bi-clipboard2-check-fill', 'label' => 'Présences', 'color' => 'cyan', 'route' =>
        'attendance.index'],
        ['icon' => 'bi-pencil-square', 'label' => 'Saisir notes', 'color' => 'violet', 'route' => 'grades.index'],
        ['icon' => 'bi-file-earmark-bar-graph-fill', 'label' => 'Bulletins', 'color' => 'pink', 'route' =>
        'report-cards.index'],
        ['icon' => 'bi-bar-chart-fill', 'label' => 'Statistiques', 'color' => 'indigo', 'route' => 'reports.index'],
        ['icon' => 'bi-file-earmark-spreadsheet', 'label' => 'Exporter', 'color' => 'orange', 'route' =>
        'exports.index'],
        ['icon' => 'bi-megaphone-fill', 'label' => 'Annonce', 'color' => 'red', 'route' => 'announcements.index'],
        ['icon' => 'bi-clock-history', 'label' => 'Emplois du temps', 'color' => 'teal', 'route' => 'timetables.index'],
        ['icon' => 'bi-shield-lock-fill', 'label' => 'Utilisateurs', 'color' => 'slate', 'route' => 'users.index'],
        ['icon' => 'bi-gear-wide-connected', 'label' => 'Paramètres', 'color' => 'gray', 'route' => 'settings.index'],
        ] as $item)
        <a href="{{ route($item['route']) }}" class="group flex flex-col items-center gap-2 p-4 rounded-xl
                      hover:bg-slate-50 dark:hover:bg-slate-700/50
                      transition-all duration-200 text-center">
            <div class="w-11 h-11 rounded-xl
                            bg-{{ $item['color'] }}-100 dark:bg-{{ $item['color'] }}-900/30
                            flex items-center justify-center
                            group-hover:scale-110 transition-transform duration-200">
                <i class="bi {{ $item['icon'] }}
                              text-{{ $item['color'] }}-600 dark:text-{{ $item['color'] }}-400
                              text-lg"></i>
            </div>
            <span class="text-[10px] sm:text-xs font-medium
                             text-slate-600 dark:text-slate-400
                             group-hover:text-slate-800 dark:group-hover:text-slate-200
                             leading-tight">
                {{ $item['label'] }}
            </span>
        </a>
        @endforeach
    </div>
</div>

</div>
@endsection

{{-- ══════════════════════════════════════════════════════════════
     SCRIPTS CHART.JS
══════════════════════════════════════════════════════════════ --}}
@push('scripts')
<script>
console.log('Sortable:', typeof window.Sortable);
document.addEventListener('DOMContentLoaded', function() {

    // ── Données PHP → JS ─────────────────────────────────────────
    const attendanceData = @json($attendanceChart);
    const revenueData = @json($revenueChart);

    // ── Couleurs selon le thème ───────────────────────────────────
    const isDark = () => document.documentElement.classList.contains('dark');

    const gridColor = () => isDark() ? 'rgba(51,65,85,0.6)' : 'rgba(226,232,240,0.8)';
    const labelColor = () => isDark() ? '#94a3b8' : '#64748b';

    // ════════════════════════════════════════════════════
    //  CHART 1 — Présences (Bar)
    // ════════════════════════════════════════════════════
    const attendanceCtx = document.getElementById('attendanceChart');
    if (!attendanceCtx) return;

    const attendanceChart = new Chart(attendanceCtx, {
        type: 'bar',
        data: {
            labels: attendanceData.labels,
            datasets: [{
                    label: 'Présents',
                    data: attendanceData.present,
                    backgroundColor: 'rgba(16, 185, 129, 0.85)',
                    borderRadius: 6,
                    borderSkipped: false,
                },
                {
                    label: 'Absents',
                    data: attendanceData.absent,
                    backgroundColor: 'rgba(239, 68, 68, 0.85)',
                    borderRadius: 6,
                    borderSkipped: false,
                },
                {
                    label: 'Retards',
                    data: attendanceData.late,
                    backgroundColor: 'rgba(245, 158, 11, 0.85)',
                    borderRadius: 6,
                    borderSkipped: false,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: isDark() ? '#1e293b' : '#fff',
                    titleColor: isDark() ? '#e2e8f0' : '#1e293b',
                    bodyColor: isDark() ? '#94a3b8' : '#64748b',
                    borderColor: isDark() ? '#334155' : '#e2e8f0',
                    borderWidth: 1,
                    padding: 12,
                    cornerRadius: 12,
                },
            },
            scales: {
                x: {
                    grid: {
                        color: gridColor(),
                        drawBorder: false
                    },
                    ticks: {
                        color: labelColor(),
                        font: {
                            size: 11
                        }
                    },
                },
                y: {
                    grid: {
                        color: gridColor(),
                        drawBorder: false
                    },
                    ticks: {
                        color: labelColor(),
                        font: {
                            size: 11
                        },
                        stepSize: 1,
                    },
                    beginAtZero: true,
                },
            },
        },
    });

    // ════════════════════════════════════════════════════
    //  CHART 2 — Revenus (Line)
    // ════════════════════════════════════════════════════
    const revenueCtx = document.getElementById('revenueChart');
    if (!revenueCtx) return;

    const revenueChart = new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: revenueData.labels,
            datasets: [{
                label: 'Revenus (FCFA)',
                data: revenueData.amounts,
                borderColor: '#10B981',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                borderWidth: 2.5,
                pointBackgroundColor: '#10B981',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 5,
                pointHoverRadius: 7,
                tension: 0.4,
                fill: true,
            }, ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: isDark() ? '#1e293b' : '#fff',
                    titleColor: isDark() ? '#e2e8f0' : '#1e293b',
                    bodyColor: isDark() ? '#94a3b8' : '#64748b',
                    borderColor: isDark() ? '#334155' : '#e2e8f0',
                    borderWidth: 1,
                    padding: 12,
                    cornerRadius: 12,
                    callbacks: {
                        label: ctx => ' ' + new window.Intl.NumberFormat('fr-FR').format(ctx.raw) +
                            ' FCFA',
                    },
                },
            },
            scales: {
                x: {
                    grid: {
                        color: gridColor(),
                        drawBorder: false
                    },
                    ticks: {
                        color: labelColor(),
                        font: {
                            size: 10
                        }
                    },
                },
                y: {
                    grid: {
                        color: gridColor(),
                        drawBorder: false
                    },
                    ticks: {
                        color: labelColor(),
                        font: {
                            size: 10
                        },
                        callback: v => new window.Intl.NumberFormat('fr-FR', {
                            notation: 'compact',
                        }).format(v),
                    },
                    beginAtZero: true,
                },
            },
        },
    });

    // ── Mise à jour des charts au changement de thème ─────────────
    window.addEventListener('theme-changed', ({
        detail
    }) => {
        const update = (chart) => {
            chart.options.plugins.tooltip.backgroundColor = detail.isDark ? '#1e293b' : '#fff';
            chart.options.plugins.tooltip.titleColor = detail.isDark ? '#e2e8f0' : '#1e293b';
            chart.options.plugins.tooltip.bodyColor = detail.isDark ? '#94a3b8' : '#64748b';
            chart.options.plugins.tooltip.borderColor = detail.isDark ? '#334155' : '#e2e8f0';
            chart.options.scales.x.grid.color = gridColor();
            chart.options.scales.x.ticks.color = labelColor();
            chart.options.scales.y.grid.color = gridColor();
            chart.options.scales.y.ticks.color = labelColor();
            chart.update();
        };
        update(attendanceChart);
        update(revenueChart);
    });
});

// ── Refresh dashboard ─────────────────────────────────────────────
function refreshDashboard(btn) {
    const icon = document.getElementById('refresh-icon');
    icon.classList.add('animate-spin');
    btn.disabled = true;

    fetch('{{ route("dashboard.refresh-stats") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
            },
        })
        .then(r => r.json())
        .then(() => {
            icon.classList.remove('animate-spin');
            btn.disabled = false;
            window.location.reload();
        })
        .catch(() => {
            icon.classList.remove('animate-spin');
            btn.disabled = false;
        });
}
</script>
@endpush