@extends('layouts.base')

@section('page_title', 'Rôles & Permissions')

@section('breadcrumb')
<span class="text-slate-300 dark:text-slate-600 font-light select-none">/</span>
<a href="{{ route('roles.index') }}"
    class="text-slate-400 dark:text-slate-500 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
    Administration
</a>
@endsection

@section('page_header')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-xl sm:text-2xl font-bold text-slate-800 dark:text-slate-100">
            Rôles &amp; Permissions
        </h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
            Gérez les rôles du système et leurs permissions associées.
        </p>
    </div>

    <a href="{{ route('roles.create') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl
                  bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold
                  shadow-sm hover:shadow-md transition-all duration-200
                  focus:outline-none focus:ring-2 focus:ring-blue-600/40">
        <i class="bi bi-plus-lg"></i>
        Nouveau rôle
    </a>
</div>
@endsection

@section('content')

{{-- ── Stats cards ── --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">

    <div class="flex items-center gap-4 p-5 rounded-2xl bg-white dark:bg-slate-800
                    border border-slate-200 dark:border-slate-700 shadow-sm">
        <div class="w-11 h-11 rounded-xl bg-blue-100 dark:bg-blue-900/40
                        flex items-center justify-center shrink-0">
            <i class="bi bi-shield-lock-fill text-blue-600 dark:text-blue-400 text-xl"></i>
        </div>
        <div>
            <p class="text-2xl font-bold text-slate-800 dark:text-slate-100">
                {{ $stats['total_roles'] }}
            </p>
            <p class="text-xs text-slate-500 dark:text-slate-400">Rôles</p>
        </div>
    </div>

    <div class="flex items-center gap-4 p-5 rounded-2xl bg-white dark:bg-slate-800
                    border border-slate-200 dark:border-slate-700 shadow-sm">
        <div class="w-11 h-11 rounded-xl bg-emerald-100 dark:bg-emerald-900/40
                        flex items-center justify-center shrink-0">
            <i class="bi bi-key-fill text-emerald-600 dark:text-emerald-400 text-xl"></i>
        </div>
        <div>
            <p class="text-2xl font-bold text-slate-800 dark:text-slate-100">
                {{ $stats['total_permissions'] }}
            </p>
            <p class="text-xs text-slate-500 dark:text-slate-400">Permissions</p>
        </div>
    </div>

    <div class="flex items-center gap-4 p-5 rounded-2xl bg-white dark:bg-slate-800
                    border border-slate-200 dark:border-slate-700 shadow-sm">
        <div class="w-11 h-11 rounded-xl bg-cyan-100 dark:bg-cyan-900/40
                        flex items-center justify-center shrink-0">
            <i class="bi bi-people-fill text-cyan-600 dark:text-cyan-400 text-xl"></i>
        </div>
        <div>
            <p class="text-2xl font-bold text-slate-800 dark:text-slate-100">
                {{ $stats['total_users'] }}
            </p>
            <p class="text-xs text-slate-500 dark:text-slate-400">Utilisateurs</p>
        </div>
    </div>

</div>

{{-- ── Tableau des rôles ── --}}
<div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700
                shadow-sm overflow-hidden">

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-slate-50 dark:bg-slate-900/40 border-b border-slate-200 dark:border-slate-700">
                    <th class="px-5 py-3.5 text-left font-semibold text-slate-500 dark:text-slate-400
                                   uppercase text-xs tracking-wide">Rôle</th>
                    <th class="px-5 py-3.5 text-left font-semibold text-slate-500 dark:text-slate-400
                                   uppercase text-xs tracking-wide">Permissions</th>
                    <th class="px-5 py-3.5 text-left font-semibold text-slate-500 dark:text-slate-400
                                   uppercase text-xs tracking-wide">Utilisateurs</th>
                    <th class="px-5 py-3.5 text-right font-semibold text-slate-500 dark:text-slate-400
                                   uppercase text-xs tracking-wide">Actions</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                @forelse($roles as $role)
                @php
                $isProtected = in_array($role->name, ['Admin', 'Teacher', 'Student', 'Parent']);
                $previewPermissions = $role->permissions->take(3);
                $remainingCount = $role->permissions->count() - $previewPermissions->count();
                @endphp
                <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">

                    {{-- Nom du rôle --}}
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-lg bg-linear-to-br from-blue-600 to-emerald-500
                                                flex items-center justify-center shrink-0">
                                <i class="bi bi-shield-fill text-white text-sm"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="font-semibold text-slate-800 dark:text-slate-100">
                                    {{ $role->name }}
                                </p>
                                @if($isProtected)
                                <span
                                    class="inline-flex items-center gap-1 text-[10px] text-slate-400 dark:text-slate-500">
                                    <i class="bi bi-lock-fill"></i> Rôle système
                                </span>
                                @endif
                            </div>
                        </div>
                    </td>

                    {{-- Permissions --}}
                    <td class="px-5 py-4">
                        @if($role->permissions->isEmpty())
                        <span class="text-xs text-slate-400 dark:text-slate-500 italic">
                            Aucune permission
                        </span>
                        @else
                        <div class="flex flex-wrap items-center gap-1.5">
                            @foreach($previewPermissions as $permission)
                            <span class="px-2 py-1 rounded-md text-[11px] font-medium
                                                         bg-blue-50 dark:bg-blue-950/40
                                                         text-blue-700 dark:text-blue-300
                                                         border border-blue-100 dark:border-blue-900/50">
                                {{ $permission->name }}
                            </span>
                            @endforeach

                            @if($remainingCount > 0)
                            <span class="px-2 py-1 rounded-md text-[11px] font-medium
                                                         bg-slate-100 dark:bg-slate-700
                                                         text-slate-500 dark:text-slate-400">
                                +{{ $remainingCount }}
                            </span>
                            @endif
                        </div>
                        @endif
                    </td>

                    {{-- Utilisateurs --}}
                    <td class="px-5 py-4">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full
                                             text-xs font-semibold
                                             bg-cyan-50 dark:bg-cyan-950/40
                                             text-cyan-700 dark:text-cyan-300">
                            <i class="bi bi-person-fill"></i>
                            {{ $role->users_count }}
                        </span>
                    </td>

                    {{-- Actions --}}
                    <td class="px-5 py-4">
                        <div class="flex items-center justify-end gap-1.5">

                            <a href="{{ route('roles.show', $role) }}" class="p-2 rounded-lg text-slate-400 dark:text-slate-500
                                              hover:bg-slate-100 dark:hover:bg-slate-700
                                              hover:text-cyan-600 dark:hover:text-cyan-400
                                              transition-colors" title="Voir">
                                <i class="bi bi-eye-fill"></i>
                            </a>

                            <a href="{{ route('roles.edit', $role) }}" class="p-2 rounded-lg text-slate-400 dark:text-slate-500
                                              hover:bg-slate-100 dark:hover:bg-slate-700
                                              hover:text-blue-600 dark:hover:text-blue-400
                                              transition-colors" title="Modifier">
                                <i class="bi bi-pencil-fill"></i>
                            </a>

                            @if(!$isProtected)
                            <form action="{{ route('roles.destroy', $role) }}" method="POST"
                                onsubmit="return confirm('Supprimer le rôle « {{ $role->name }} » ? Cette action est irréversible.');">
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
                                title="Rôle système protégé">
                                <i class="bi bi-trash-fill"></i>
                            </span>
                            @endif

                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-5 py-16">
                        <div class="flex flex-col items-center justify-center text-slate-400 dark:text-slate-500">
                            <i class="bi bi-shield-slash text-4xl mb-3"></i>
                            <p class="text-sm font-medium">Aucun rôle trouvé</p>
                            <p class="text-xs mt-1">Créez votre premier rôle pour commencer.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($roles->hasPages())
    <div class="px-5 py-4 border-t border-slate-100 dark:border-slate-700">
        {{ $roles->links() }}
    </div>
    @endif

</div>

@endsection