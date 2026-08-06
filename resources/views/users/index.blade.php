@extends('layouts.base')

@section('page_title', 'Utilisateurs')

@section('breadcrumb')
<span class="text-slate-300 dark:text-slate-600 font-light select-none">/</span>
<a href="{{ route('users.index') }}"
    class="text-slate-400 dark:text-slate-500 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
    Administration
</a>
@endsection

@section('page_header')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-xl sm:text-2xl font-bold text-slate-800 dark:text-slate-100">
            Utilisateurs
        </h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
            Gérez les comptes utilisateurs et leurs rôles d'accès.
        </p>
    </div>

    <a href="{{ route('users.create') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl
                  bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold
                  shadow-sm hover:shadow-md transition-all duration-200
                  focus:outline-none focus:ring-2 focus:ring-blue-600/40">
        <i class="bi bi-person-plus-fill"></i>
        Nouvel utilisateur
    </a>
</div>
@endsection

@section('content')

{{-- ── Barre de recherche & filtres ── --}}
<div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700
                shadow-sm p-4 mb-6">
    <form method="GET" action="{{ route('users.index') }}" class="flex flex-col sm:flex-row items-center gap-3">

        <div class="relative flex-1 w-full">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
                <i class="bi bi-search text-slate-400"></i>
            </span>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher par nom ou e-mail…"
                class="w-full rounded-lg border border-slate-300 dark:border-slate-600 text-sm
                              bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100
                              placeholder:text-slate-400 pl-10 pr-3.5 py-2.5
                              focus:outline-none focus:ring-2 focus:ring-blue-600/40 focus:border-blue-600">
        </div>

        <select name="role" class="w-full sm:w-48 rounded-lg border border-slate-300 dark:border-slate-600 text-sm
                           bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 px-3.5 py-2.5
                           focus:outline-none focus:ring-2 focus:ring-blue-600/40 focus:border-blue-600">
            <option value="">Tous les rôles</option>
            @foreach($roles as $role)
            <option value="{{ $role->name }}" @selected(request('role')===$role->name)>
                {{ $role->name }}
            </option>
            @endforeach
        </select>

        <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl
                           bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 text-sm font-medium
                           hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors">
            <i class="bi bi-funnel-fill"></i>
            Filtrer
        </button>
    </form>
</div>

{{-- ── Tableau des utilisateurs ── --}}
<div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700
                shadow-sm overflow-hidden">

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-slate-50 dark:bg-slate-900/40 border-b border-slate-200 dark:border-slate-700">
                    <th class="px-5 py-3.5 text-left font-semibold text-slate-500 dark:text-slate-400
                                   uppercase text-xs tracking-wide">Utilisateur</th>
                    <th class="px-5 py-3.5 text-left font-semibold text-slate-500 dark:text-slate-400
                                   uppercase text-xs tracking-wide">Rôle</th>
                    <th class="px-5 py-3.5 text-left font-semibold text-slate-500 dark:text-slate-400
                                   uppercase text-xs tracking-wide">Statut</th>
                    <th class="px-5 py-3.5 text-left font-semibold text-slate-500 dark:text-slate-400
                                   uppercase text-xs tracking-wide">Inscrit le</th>
                    <th class="px-5 py-3.5 text-right font-semibold text-slate-500 dark:text-slate-400
                                   uppercase text-xs tracking-wide">Actions</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                @forelse($users as $user)
                <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">

                    {{-- Avatar + Nom + Email --}}
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-3">
                            @if($user->avatar)
                            <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}"
                                class="w-9 h-9 rounded-full object-cover ring-2 ring-blue-500/20 shrink-0" />
                            @else
                            <div
                                class="w-9 h-9 rounded-full bg-linear-to-br from-blue-500 to-emerald-500
                                                    flex items-center justify-center text-white text-xs font-bold shrink-0">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            @endif
                            <div class="min-w-0">
                                <p class="font-semibold text-slate-800 dark:text-slate-100 truncate">
                                    {{ $user->name }}
                                </p>
                                <p class="text-xs text-slate-400 dark:text-slate-500 truncate">
                                    {{ $user->email }}
                                </p>
                            </div>
                        </div>
                    </td>

                    {{-- Rôle --}}
                    <td class="px-5 py-4">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full
                                             text-xs font-semibold
                                             bg-blue-50 dark:bg-blue-950/40
                                             text-blue-700 dark:text-blue-300">
                            <i class="bi bi-shield-fill text-[10px]"></i>
                            {{ $user->getRoleNames()->first() ?? '—' }}
                        </span>
                    </td>

                    {{-- Statut --}}
                    <td class="px-5 py-4">
                        @if($user->is_active)
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full
                                                 text-xs font-semibold
                                                 bg-emerald-50 dark:bg-emerald-950/40
                                                 text-emerald-700 dark:text-emerald-400">
                            <i class="bi bi-circle-fill text-[6px]"></i>
                            Actif
                        </span>
                        @else
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full
                                                 text-xs font-semibold
                                                 bg-slate-100 dark:bg-slate-700
                                                 text-slate-500 dark:text-slate-400">
                            <i class="bi bi-circle-fill text-[6px]"></i>
                            Inactif
                        </span>
                        @endif
                    </td>

                    {{-- Date d'inscription --}}
                    <td class="px-5 py-4 text-slate-500 dark:text-slate-400">
                        {{ $user->created_at->format('d/m/Y') }}
                    </td>

                    {{-- Actions --}}
                    <td class="px-5 py-4">
                        <div class="flex items-center justify-end gap-1.5">

                            <a href="{{ route('users.edit', $user) }}" class="p-2 rounded-lg text-slate-400 dark:text-slate-500
                                              hover:bg-slate-100 dark:hover:bg-slate-700
                                              hover:text-blue-600 dark:hover:text-blue-400
                                              transition-colors" title="Modifier">
                                <i class="bi bi-pencil-fill"></i>
                            </a>

                            @unless($user->id === auth()->id())
                            <form action="{{ route('users.destroy', $user) }}" method="POST"
                                onsubmit="return confirm('Supprimer l\'utilisateur « {{ $user->name }} » ? Cette action est irréversible.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 rounded-lg text-slate-400 dark:text-slate-500
                                                           hover:bg-red-50 dark:hover:bg-red-900/20
                                                           hover:text-red-600 dark:hover:text-red-400
                                                           transition-colors" title="Supprimer">
                                    <i class="bi bi-trash-fill"></i>
                                </button>
                            </form>
                            @else
                            <span class="p-2 rounded-lg text-slate-300 dark:text-slate-600 cursor-not-allowed"
                                title="Vous ne pouvez pas supprimer votre propre compte">
                                <i class="bi bi-trash-fill"></i>
                            </span>
                            @endunless

                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-5 py-16">
                        <div class="flex flex-col items-center justify-center text-slate-400 dark:text-slate-500">
                            <i class="bi bi-person-x text-4xl mb-3"></i>
                            <p class="text-sm font-medium">Aucun utilisateur trouvé</p>
                            <p class="text-xs mt-1">Ajustez vos filtres ou créez un nouvel utilisateur.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($users->hasPages())
    <div class="px-5 py-4 border-t border-slate-100 dark:border-slate-700">
        {{ $users->links() }}
    </div>
    @endif

</div>

@endsection