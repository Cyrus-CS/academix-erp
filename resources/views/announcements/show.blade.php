@extends('layouts.base')

@section('page_title', 'Annonce : ' . Str::limit($announcement->title, 30))

@section('breadcrumb')
<span class="text-slate-300 dark:text-slate-600">/</span>
<a href="{{ route('announcements.index') }}"
    class="text-slate-500 dark:text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
    Annonces
</a>
@endsection

@section('page_header')
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div class="flex items-center gap-3">
        <a href="{{ route('announcements.index') }}" class="w-9 h-9 rounded-xl flex items-center justify-center
                  bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700
                  text-slate-500 hover:text-blue-600 hover:border-blue-300
                  dark:hover:border-blue-600 transition-all">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-800 dark:text-slate-100">
                Détail de l'annonce
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                Publiée par {{ $announcement->user->name ?? 'Administrateur' }}
                &bull; {{ $announcement->created_at->diffForHumans() }}
            </p>
        </div>
    </div>
    <div class="flex flex-wrap items-center gap-2">
        {{-- Renouveler si expirée --}}
        @if($announcement->expires_at && $announcement->expires_at->isPast())
        <form action="{{ route('announcements.renew', $announcement) }}" method="POST">
            @csrf @method('PATCH')
            <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-medium
                           bg-cyan-600 hover:bg-cyan-700 text-white shadow-sm transition-all">
                <i class="bi bi-arrow-clockwise"></i>
                <span class="hidden sm:inline">Renouveler</span>
            </button>
        </form>
        @endif
        <a href="{{ route('announcements.edit', $announcement) }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-medium
                  bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700
                  text-slate-700 dark:text-slate-300
                  hover:bg-slate-50 dark:hover:bg-slate-700 transition-all">
            <i class="bi bi-pencil-square"></i>
            <span class="hidden sm:inline">Modifier</span>
        </a>
        <form id="delete-announcement-form" action="{{ route('announcements.destroy', $announcement) }}" method="POST">
            @csrf @method('DELETE')
            <button id="delete-announcement-btn" type="button" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold
                           bg-red-600 hover:bg-red-700 text-white
                           shadow-sm hover:shadow-red-500/20 transition-all">
                <i class="bi bi-trash3"></i>
                <span class="hidden sm:inline">Supprimer</span>
            </button>
        </form>
    </div>
</div>
@endsection

@section('content')
@php
$isExpired = $announcement->expires_at && $announcement->expires_at->isPast();
$isActive = !$isExpired;
$expiresAt = $announcement->expires_at ? \Carbon\Carbon::parse($announcement->expires_at) : null;
$typeColors = [
'info' => ['bg' => 'bg-cyan-50 dark:bg-cyan-900/20', 'text' => 'text-cyan-700 dark:text-cyan-300', 'border' =>
'border-cyan-200 dark:border-cyan-800', 'icon' => 'bi-info-circle-fill', 'iconColor' => 'text-cyan-500', 'bar' =>
'from-cyan-500 to-blue-500'],
'warning' => ['bg' => 'bg-amber-50 dark:bg-amber-900/20', 'text' => 'text-amber-700 dark:text-amber-300', 'border' =>
'border-amber-200 dark:border-amber-800', 'icon' => 'bi-exclamation-triangle-fill', 'iconColor' => 'text-amber-500',
'bar' => 'from-amber-500 to-orange-500'],
'success' => ['bg' => 'bg-emerald-50 dark:bg-emerald-900/20', 'text' => 'text-emerald-700 dark:text-emerald-300',
'border' => 'border-emerald-200 dark:border-emerald-800', 'icon' => 'bi-check-circle-fill', 'iconColor' =>
'text-emerald-500', 'bar' => 'from-emerald-500 to-teal-500'],
'danger' => ['bg' => 'bg-red-50 dark:bg-red-900/20', 'text' => 'text-red-700 dark:text-red-300', 'border' =>
'border-red-200 dark:border-red-800', 'icon' => 'bi-x-octagon-fill', 'iconColor' => 'text-red-500', 'bar' =>
'from-red-500 to-rose-500'],
'default' => ['bg' => 'bg-blue-50 dark:bg-blue-900/20', 'text' => 'text-blue-700 dark:text-blue-300', 'border' =>
'border-blue-200 dark:border-blue-800', 'icon' => 'bi-megaphone-fill', 'iconColor' => 'text-blue-500', 'bar' =>
'from-blue-600 to-emerald-500'],
];
$type = $typeColors[$announcement->type ?? 'default'] ?? $typeColors['default'];
@endphp

{{-- ── Banner si expirée ───────────────────────────────────────── --}}
@if($isExpired)
<div class="mb-5 flex items-start gap-3 px-4 py-3.5 rounded-xl
            bg-red-50 dark:bg-red-900/20
            border border-red-200 dark:border-red-800
            text-red-700 dark:text-red-400">
    <i class="bi bi-exclamation-circle-fill text-lg shrink-0 mt-0.5"></i>
    <div>
        <p class="text-sm font-semibold">Annonce expirée</p>
        <p class="text-xs mt-0.5">Cette annonce a expiré le {{ $expiresAt?->format('d/m/Y à H:i') }}.
            Vous pouvez la renouveler depuis le bouton ci-dessus.</p>
    </div>
</div>
@endif

{{-- ── Contenu annonce ─────────────────────────────────────────── --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Article principal --}}
    <div class="lg:col-span-2 space-y-5">
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200
                    dark:border-slate-700 shadow-sm overflow-hidden">

            {{-- Barre de couleur selon le type --}}
            <div class="h-1.5 w-full bg-gradient-to-r {{ $type['bar'] }}"></div>

            <div class="p-6 sm:p-8">
                {{-- Type badge --}}
                <div class="flex flex-wrap items-center gap-3 mb-5">
                    <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-semibold
                                 {{ $type['bg'] }} {{ $type['text'] }} border {{ $type['border'] }}">
                        <i class="bi {{ $type['icon'] }}"></i>
                        {{ ucfirst($announcement->type ?? 'Annonce') }}
                    </span>
                    @if($isActive && $expiresAt)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-medium
                                 bg-slate-100 dark:bg-slate-700
                                 text-slate-600 dark:text-slate-300
                                 border border-slate-200 dark:border-slate-600">
                        <i class="bi bi-clock"></i>
                        Expire le {{ $expiresAt->format('d/m/Y') }}
                    </span>
                    @endif
                    @if(!$expiresAt)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-medium
                                 bg-emerald-50 dark:bg-emerald-900/20
                                 text-emerald-700 dark:text-emerald-300
                                 border border-emerald-200 dark:border-emerald-800">
                        <i class="bi bi-infinity"></i> Permanente
                    </span>
                    @endif
                </div>

                {{-- Titre --}}
                <h2 class="text-xl sm:text-2xl font-bold text-slate-800 dark:text-slate-100 leading-snug mb-5">
                    {{ $announcement->title }}
                </h2>

                {{-- Séparateur --}}
                <div class="h-px bg-slate-100 dark:bg-slate-700 mb-5"></div>

                {{-- Corps --}}
                <div class="prose prose-sm prose-slate dark:prose-invert max-w-none
                            text-slate-700 dark:text-slate-300 leading-relaxed">
                    {!! nl2br(e($announcement->content)) !!}
                </div>
            </div>

            {{-- Footer de la carte --}}
            <div class="px-6 sm:px-8 py-4 border-t border-slate-100 dark:border-slate-700
                        bg-slate-50/50 dark:bg-slate-800/50
                        flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full
                                bg-gradient-to-br from-blue-600 to-emerald-500
                                flex items-center justify-center text-white text-xs font-bold">
                        {{ strtoupper(substr($announcement->user->name ?? 'A', 0, 1)) }}
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-slate-700 dark:text-slate-200">
                            {{ $announcement->user->name ?? 'Administrateur' }}
                        </p>
                        <p class="text-[11px] text-slate-400">
                            {{ $announcement->user?->getRoleNames()->first() ?? 'Admin' }}
                        </p>
                    </div>
                </div>
                <p class="text-xs text-slate-400">
                    Publiée le {{ $announcement->created_at->format('d/m/Y à H:i') }}
                </p>
            </div>
        </div>
    </div>

    {{-- Sidebar --}}
    <div class="space-y-5">

        {{-- Méta-infos --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200
                    dark:border-slate-700 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-700
                        bg-slate-50/50 dark:bg-slate-800/50">
                <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-100 flex items-center gap-2">
                    <i class="bi bi-info-circle text-blue-500"></i> Informations
                </h3>
            </div>
            <div class="p-5 space-y-4">
                @foreach([
                ['icon' => 'bi-tag', 'label' => 'Type', 'value' => ucfirst($announcement->type ?? 'Général'), 'color' =>
                'blue'],
                ['icon' => 'bi-person-circle', 'label' => 'Auteur', 'value' => $announcement->user->name ?? 'Admin',
                'color' => 'emerald'],
                ['icon' => 'bi-calendar-plus', 'label' => 'Création', 'value' =>
                $announcement->created_at->format('d/m/Y'), 'color' => 'slate'],
                ['icon' => 'bi-calendar-x', 'label' => 'Expiration', 'value' => $expiresAt ? $expiresAt->format('d/m/Y')
                : 'Aucune', 'color' => $isExpired ? 'red' : 'slate'],
                ['icon' => 'bi-pencil', 'label' => 'Modifié', 'value' => $announcement->updated_at->diffForHumans(),
                'color' => 'slate'],
                ] as $meta)
                <div class="flex items-start gap-3">
                    <div
                        class="w-8 h-8 rounded-lg shrink-0 flex items-center justify-center
                        {{ $meta['color'] === 'blue' ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-500' : '' }}
                        {{ $meta['color'] === 'emerald' ? 'bg-emerald-50 dark:bg-emerald-900/30 text-emerald-500' : '' }}
                        {{ $meta['color'] === 'red' ? 'bg-red-50 dark:bg-red-900/30 text-red-500' : '' }}
                        {{ $meta['color'] === 'slate' ? 'bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400' : '' }}">
                        <i class="bi {{ $meta['icon'] }} text-sm"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">
                            {{ $meta['label'] }}</p>
                        <p class="text-sm font-medium text-slate-800 dark:text-slate-200">{{ $meta['value'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Statut visuel --}}
        <div class="rounded-2xl overflow-hidden border
            {{ $isActive ? 'border-emerald-200 dark:border-emerald-800 bg-gradient-to-br from-emerald-50 to-teal-50 dark:from-emerald-950/30 dark:to-teal-950/20' : 'border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50' }}
            p-5">
            <div class="flex items-center gap-3 mb-3">
                <div
                    class="w-10 h-10 rounded-xl flex items-center justify-center
                    {{ $isActive ? 'bg-emerald-100 dark:bg-emerald-900/40 text-emerald-600 dark:text-emerald-400' : 'bg-slate-200 dark:bg-slate-700 text-slate-500' }}">
                    <i class="bi {{ $isActive ? 'bi-broadcast' : 'bi-broadcast-pin' }} text-lg"></i>
                </div>
                <div>
                    <p
                        class="text-sm font-bold {{ $isActive ? 'text-emerald-700 dark:text-emerald-300' : 'text-slate-600 dark:text-slate-400' }}">
                        {{ $isActive ? 'Annonce active' : 'Annonce expirée' }}
                    </p>
                    <p class="text-xs {{ $isActive ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-500' }}">
                        {{ $isActive ? 'Visible par les utilisateurs' : 'Non visible actuellement' }}
                    </p>
                </div>
            </div>
            @if($expiresAt && $isActive)
            <div class="mt-3 space-y-1.5">
                <div class="flex justify-between text-[11px] font-medium">
                    <span class="text-slate-500">Temps restant</span>
                    <span class="text-emerald-600 dark:text-emerald-400">
                        {{ $expiresAt->diffForHumans() }}
                    </span>
                </div>
                @php
                $created = $announcement->created_at;
                $totalSecs = $created->diffInSeconds($expiresAt);
                $passedSecs = $created->diffInSeconds(now());
                $pct = $totalSecs > 0 ? min(100, round(($passedSecs / $totalSecs) * 100)) : 100;
                @endphp
                <div class="w-full h-2 bg-emerald-100 dark:bg-emerald-900/30 rounded-full overflow-hidden">
                    <div class="h-full bg-emerald-500 rounded-full transition-all" style="width: {{ $pct }}%"></div>
                </div>
            </div>
            @endif
        </div>

        {{-- Actions rapides --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200
                    dark:border-slate-700 shadow-sm p-4 space-y-2">
            <p class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">Actions rapides</p>
            <a href="{{ route('announcements.create') }}" class="flex items-center gap-3 p-3 rounded-xl text-sm
                      text-slate-700 dark:text-slate-300
                      border border-slate-100 dark:border-slate-700
                      hover:bg-blue-50 dark:hover:bg-blue-950/20
                      hover:border-blue-200 dark:hover:border-blue-800
                      hover:text-blue-600 dark:hover:text-blue-400
                      transition-all duration-200 group">
                <i class="bi bi-plus-circle text-base"></i>
                Nouvelle annonce
                <i class="bi bi-arrow-right-short ml-auto opacity-0 group-hover:opacity-100 transition-opacity"></i>
            </a>
            <a href="{{ route('announcements.index') }}" class="flex items-center gap-3 p-3 rounded-xl text-sm
                      text-slate-700 dark:text-slate-300
                      border border-slate-100 dark:border-slate-700
                      hover:bg-slate-50 dark:hover:bg-slate-700/50
                      transition-all duration-200 group">
                <i class="bi bi-list-ul text-base"></i>
                Toutes les annonces
                <i class="bi bi-arrow-right-short ml-auto opacity-0 group-hover:opacity-100 transition-opacity"></i>
            </a>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const btn = document.getElementById('delete-announcement-btn');
    const form = document.getElementById('delete-announcement-form');
    btn?.addEventListener('click', () => {
        if (confirm('Supprimer définitivement cette annonce ?')) form.submit();
    });
});
</script>
@endsection