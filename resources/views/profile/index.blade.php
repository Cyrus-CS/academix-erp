@extends('layouts.base')

@section('page_title', 'Mon profil')

@section('breadcrumb')
<span class="text-slate-300 dark:text-slate-600 font-light select-none">/</span>
<span class="text-slate-400 dark:text-slate-500">Mon profil</span>
@endsection

@section('content')

<div class="max-w-6xl mx-auto space-y-6">

    {{-- ══════════════════════════════════════════════════════
         HERO — Bannière profil
    ══════════════════════════════════════════════════════ --}}
    <div class="relative overflow-hidden rounded-2xl 
                bg-white dark:bg-slate-800 
                border border-slate-200 dark:border-slate-700 
                shadow-sm">

        {{-- Fond dégradé décoratif --}}
        <div class="absolute inset-x-0 top-0 h-32 
                    bg-linear-to-r from-blue-600 via-blue-500 to-emerald-500">
            <div class="absolute inset-0 bg-linear-to-t from-black/10 to-transparent"></div>
            {{-- Motif décoratif --}}
            <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(circle at 20% 50%, white 1px, transparent 1px), 
                                          radial-gradient(circle at 80% 30%, white 1px, transparent 1px);
                        background-size: 40px 40px;">
            </div>
        </div>

        <div class="relative px-6 pt-20 pb-6 sm:px-8 sm:pt-24">
            <div class="flex flex-col sm:flex-row sm:items-end gap-5">

                {{-- Avatar --}}
                <div class="shrink-0">
                    @if($user->avatar)
                    <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}" class="w-28 h-28 sm:w-32 sm:h-32 rounded-2xl object-cover 
                                    ring-4 ring-white dark:ring-slate-800 shadow-lg" />
                    @else
                    <div class="w-28 h-28 sm:w-32 sm:h-32 rounded-2xl 
                                    bg-linear-to-br from-blue-500 to-emerald-500 
                                    flex items-center justify-center 
                                    text-white text-4xl font-bold 
                                    ring-4 ring-white dark:ring-slate-800 shadow-lg">
                        {{ strtoupper(substr($user->name, 0, 1)) }}{{ strtoupper(substr(explode(' ', $user->name)[1] ?? '', 0, 1)) }}
                    </div>
                    @endif
                </div>

                {{-- Infos + Actions --}}
                <div class="flex-1 min-w-0 flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
                    <div class="min-w-0">
                        <h1 class="text-2xl sm:text-3xl font-bold text-slate-800 dark:text-slate-100 truncate">
                            {{ $user->name }}
                        </h1>
                        <div class="flex flex-wrap items-center gap-2 mt-2">
                            {{-- Rôle --}}
                            @php $role = $user->getRoleNames()->first() ?? 'Utilisateur'; @endphp
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full
                                         text-xs font-semibold 
                                         bg-blue-100 dark:bg-blue-900/40 
                                         text-blue-700 dark:text-blue-300">
                                <i class="bi bi-shield-fill-check"></i>
                                {{ $role }}
                            </span>

                            {{-- Statut vérification email --}}
                            @if($user->email_verified_at)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full
                                             text-xs font-semibold
                                             bg-emerald-100 dark:bg-emerald-900/40 
                                             text-emerald-700 dark:text-emerald-300">
                                <i class="bi bi-patch-check-fill"></i>
                                Email vérifié
                            </span>
                            @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full
                                             text-xs font-semibold
                                             bg-amber-100 dark:bg-amber-900/40 
                                             text-amber-700 dark:text-amber-300">
                                <i class="bi bi-exclamation-triangle-fill"></i>
                                Non vérifié
                            </span>
                            @endif
                        </div>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-2 flex items-center gap-1.5">
                            <i class="bi bi-envelope-fill"></i>
                            {{ $user->email }}
                        </p>
                    </div>

                    {{-- Bouton Modifier --}}
                    <a href="{{ route('profile.edit') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl
                              text-sm font-semibold text-white
                              bg-blue-600 hover:bg-blue-700 
                              shadow-sm hover:shadow-md
                              transition-all duration-200 shrink-0">
                        <i class="bi bi-pencil-square"></i>
                        Modifier le profil
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════
         GRID — Cartes d'information
    ══════════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ─── Colonne principale (2/3) ──────────────────── --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- ─── Informations personnelles ─── --}}
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm 
                        border border-slate-200 dark:border-slate-700 
                        overflow-hidden">
                <div class="flex items-center gap-3 px-6 py-4 
                            border-b border-slate-200 dark:border-slate-700">
                    <div class="w-9 h-9 rounded-xl bg-blue-100 dark:bg-blue-900/40 
                                flex items-center justify-center">
                        <i class="bi bi-person-vcard-fill text-blue-600 dark:text-blue-400"></i>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-slate-800 dark:text-slate-100">
                            Informations personnelles
                        </h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400">
                            Détails de votre compte
                        </p>
                    </div>
                </div>

                <dl class="divide-y divide-slate-100 dark:divide-slate-700">
                    @foreach([
                    ['icon' => 'bi-person-fill', 'label' => 'Nom complet', 'value' => $user->name],
                    ['icon' => 'bi-envelope-fill', 'label' => 'Email', 'value' => $user->email],
                    ['icon' => 'bi-telephone-fill', 'label' => 'Téléphone', 'value' => $user->phone ?? '—'],
                    ['icon' => 'bi-shield-fill-check', 'label' => 'Rôle', 'value' => $role],
                    ] as $item)
                    <div class="flex items-center gap-4 px-6 py-3.5 
                                hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                        <div class="w-8 h-8 rounded-lg bg-slate-100 dark:bg-slate-700 
                                    flex items-center justify-center shrink-0">
                            <i class="bi {{ $item['icon'] }} text-slate-500 dark:text-slate-400 text-sm"></i>
                        </div>
                        <dt class="text-sm text-slate-500 dark:text-slate-400 w-32 shrink-0">
                            {{ $item['label'] }}
                        </dt>
                        <dd class="text-sm font-medium text-slate-800 dark:text-slate-100 truncate">
                            {{ $item['value'] }}
                        </dd>
                    </div>
                    @endforeach
                </dl>
            </div>

            {{-- ─── Sécurité ─── --}}
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm 
                        border border-slate-200 dark:border-slate-700 
                        overflow-hidden">
                <div class="flex items-center gap-3 px-6 py-4 
                            border-b border-slate-200 dark:border-slate-700">
                    <div class="w-9 h-9 rounded-xl bg-emerald-100 dark:bg-emerald-900/40 
                                flex items-center justify-center">
                        <i class="bi bi-shield-lock-fill text-emerald-600 dark:text-emerald-400"></i>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-slate-800 dark:text-slate-100">
                            Sécurité du compte
                        </h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400">
                            État de sécurité de votre compte
                        </p>
                    </div>
                </div>

                <div class="p-6 space-y-4">

                    {{-- Mot de passe --}}
                    <div class="flex items-center justify-between gap-4 p-4 rounded-xl 
                                bg-slate-50 dark:bg-slate-700/30 
                                border border-slate-100 dark:border-slate-700">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-10 h-10 rounded-xl bg-blue-100 dark:bg-blue-900/40 
                                        flex items-center justify-center shrink-0">
                                <i class="bi bi-key-fill text-blue-600 dark:text-blue-400"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-slate-800 dark:text-slate-100">
                                    Mot de passe
                                </p>
                                <p class="text-xs text-slate-500 dark:text-slate-400">
                                    Modifié il y a
                                    {{ $user->updated_at->diffForHumans(null, true) }}
                                </p>
                            </div>
                        </div>
                        <a href="{{ route('profile.edit') }}#password" class="text-xs font-semibold text-blue-600 dark:text-blue-400 
                                  hover:underline shrink-0">
                            Changer
                        </a>
                    </div>

                    {{-- Vérification email --}}
                    <div class="flex items-center justify-between gap-4 p-4 rounded-xl 
                                bg-slate-50 dark:bg-slate-700/30 
                                border border-slate-100 dark:border-slate-700">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-10 h-10 rounded-xl 
                                        {{ $user->email_verified_at ? 'bg-emerald-100 dark:bg-emerald-900/40' : 'bg-amber-100 dark:bg-amber-900/40' }}
                                        flex items-center justify-center shrink-0">
                                <i
                                    class="bi {{ $user->email_verified_at ? 'bi-patch-check-fill text-emerald-600 dark:text-emerald-400' : 'bi-exclamation-triangle-fill text-amber-600 dark:text-amber-400' }}"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-slate-800 dark:text-slate-100">
                                    Vérification email
                                </p>
                                <p class="text-xs text-slate-500 dark:text-slate-400">
                                    {{ $user->email_verified_at 
                                        ? 'Vérifié le ' . $user->email_verified_at->format('d/m/Y')
                                        : 'Email non vérifié' }}
                                </p>
                            </div>
                        </div>
                        @if(!$user->email_verified_at)
                        <form method="POST" action="{{ route('verification.send') }}">
                            @csrf
                            <button type="submit" class="text-xs font-semibold text-amber-600 dark:text-amber-400 
                                               hover:underline shrink-0">
                                Vérifier
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- ─── Colonne latérale (1/3) ──────────────────── --}}
        <div class="space-y-6">

            {{-- ─── Statistiques du compte ─── --}}
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm 
                        border border-slate-200 dark:border-slate-700 
                        overflow-hidden">
                <div class="flex items-center gap-3 px-5 py-4 
                            border-b border-slate-200 dark:border-slate-700">
                    <div class="w-9 h-9 rounded-xl bg-cyan-100 dark:bg-cyan-900/40 
                                flex items-center justify-center">
                        <i class="bi bi-activity text-cyan-600 dark:text-cyan-400"></i>
                    </div>
                    <h2 class="text-sm font-bold text-slate-800 dark:text-slate-100">
                        Activité du compte
                    </h2>
                </div>

                <div class="p-5 space-y-4">
                    <div>
                        <p class="text-[10px] uppercase tracking-wider font-semibold 
                                  text-slate-400 dark:text-slate-500">
                            Membre depuis
                        </p>
                        <p class="text-sm font-semibold text-slate-800 dark:text-slate-100 mt-1">
                            {{ $user->created_at->format('d F Y') }}
                        </p>
                        <p class="text-xs text-slate-500 dark:text-slate-400">
                            {{ $user->created_at->diffForHumans() }}
                        </p>
                    </div>

                    <div class="pt-4 border-t border-slate-100 dark:border-slate-700">
                        <p class="text-[10px] uppercase tracking-wider font-semibold 
                                  text-slate-400 dark:text-slate-500">
                            Dernière mise à jour
                        </p>
                        <p class="text-sm font-semibold text-slate-800 dark:text-slate-100 mt-1">
                            {{ $user->updated_at->format('d F Y') }}
                        </p>
                        <p class="text-xs text-slate-500 dark:text-slate-400">
                            {{ $user->updated_at->diffForHumans() }}
                        </p>
                    </div>

                    <div class="pt-4 border-t border-slate-100 dark:border-slate-700">
                        <p class="text-[10px] uppercase tracking-wider font-semibold 
                                  text-slate-400 dark:text-slate-500">
                            Identifiant
                        </p>
                        <p class="text-sm font-mono font-semibold text-slate-800 dark:text-slate-100 mt-1">
                            #{{ str_pad($user->id, 6, '0', STR_PAD_LEFT) }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- ─── Actions rapides ─── --}}
            <div class="bg-linear-to-br from-blue-600 to-emerald-500 
                        rounded-2xl shadow-sm overflow-hidden p-6 text-white">
                <div class="w-11 h-11 rounded-xl bg-white/20 backdrop-blur 
                            flex items-center justify-center mb-4">
                    <i class="bi bi-lightning-charge-fill text-xl"></i>
                </div>
                <h3 class="text-base font-bold mb-2">
                    Personnalisez votre expérience
                </h3>
                <p class="text-xs text-white/80 mb-5 leading-relaxed">
                    Mettez à jour vos informations, changez votre mot de passe
                    ou personnalisez votre photo de profil.
                </p>
                <a href="{{ route('profile.edit') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl 
                          bg-white text-blue-600 text-xs font-bold 
                          hover:bg-slate-50 transition-colors">
                    <i class="bi bi-gear-fill"></i>
                    Gérer mon profil
                </a>
            </div>
        </div>
    </div>
</div>

@endsection