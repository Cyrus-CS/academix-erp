@extends('layouts.base')

@section('page_title', $parent->name)

@section('breadcrumb')
<a href="{{ route('parents.index') }}" class="text-slate-400 dark:text-slate-500
          hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
    Parents
</a>
<span class="text-slate-300 dark:text-slate-600 font-light select-none">/</span>
@endsection

@section('content')

@php
// Charger les relations nécessaires
$parent->loadMissing(['students.classe', 'students.user']);

$studentCount = $parent->students->count();
@endphp

<div class="max-w-5xl mx-auto space-y-6">

    {{-- ════════════════════ HEADER ════════════════════ --}}
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border
                border-slate-200 dark:border-slate-700 overflow-hidden">
        <div class="px-6 py-5 bg-gradient-to-r from-blue-50 to-emerald-50
                    dark:from-blue-950/30 dark:to-emerald-950/30
                    border-b border-slate-200 dark:border-slate-700
                    flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">

            <div class="flex items-center gap-4 min-w-0">
                {{-- Avatar --}}
                @if($parent->avatar)
                <img src="{{ asset('storage/' . $parent->avatar) }}" alt="{{ $parent->name }}" class="w-16 h-16 rounded-2xl object-cover shrink-0
                            ring-2 ring-blue-500/20 shadow-md" />
                @else
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-blue-500 to-emerald-500
                            flex items-center justify-center text-white text-2xl font-bold
                            shrink-0 ring-2 ring-blue-500/20 shadow-md">
                    {{ strtoupper(substr($parent->name, 0, 1)) }}
                </div>
                @endif

                <div class="min-w-0">
                    <div class="flex items-center gap-2.5 flex-wrap">
                        <h1 class="text-xl font-bold text-slate-800 dark:text-slate-100 truncate">
                            {{ $parent->name }}
                        </h1>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full
                                     text-xs font-semibold shrink-0
                                     {{ $studentCount > 0
                                         ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400'
                                         : 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400' }}">
                            <span class="w-1.5 h-1.5 rounded-full
                                         {{ $studentCount > 0 ? 'bg-emerald-500' : 'bg-amber-500' }}">
                            </span>
                            {{ $studentCount > 0 ? 'Actif' : 'Non lié' }}
                        </span>
                    </div>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                        Parent ·
                        {{ $studentCount }} élève{{ $studentCount > 1 ? 's' : '' }}
                        associé{{ $studentCount > 1 ? 's' : '' }}
                        · Inscrit le {{ $parent->created_at->translatedFormat('d F Y') }}
                    </p>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex items-center gap-2 shrink-0">
                <a href="{{ route('parents.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-medium
                          text-slate-600 dark:text-slate-300
                          bg-white dark:bg-slate-700
                          border border-slate-200 dark:border-slate-600
                          hover:bg-slate-50 dark:hover:bg-slate-600 transition-colors">
                    <i class="bi bi-arrow-left"></i>
                    <span class="hidden sm:inline">Retour</span>
                </a>
                <a href="{{ route('parents.edit', $parent) }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-medium
                          text-white bg-blue-600 hover:bg-blue-700
                          shadow-sm shadow-blue-600/20 transition-colors">
                    <i class="bi bi-pencil-fill"></i>
                    <span class="hidden sm:inline">Modifier</span>
                </a>
                <button type="button" data-open-modal="delete-modal" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-medium
                               text-red-600 dark:text-red-400
                               bg-red-50 dark:bg-red-900/20
                               border border-red-200 dark:border-red-800
                               hover:bg-red-100 dark:hover:bg-red-900/40 transition-colors">
                    <i class="bi bi-trash3-fill"></i>
                    <span class="hidden sm:inline">Supprimer</span>
                </button>
            </div>
        </div>
    </div>

    {{-- ════════════════════ CONTENU ════════════════════ --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ── Colonne principale ── --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Élèves associés --}}
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border
                        border-slate-200 dark:border-slate-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700
                            flex items-center justify-between gap-3">
                    <div class="flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-lg bg-blue-50 dark:bg-blue-900/30
                                    flex items-center justify-center">
                            <i class="bi bi-mortarboard-fill text-blue-600 dark:text-blue-400 text-sm"></i>
                        </div>
                        <h2 class="text-sm font-semibold text-slate-800 dark:text-slate-100">
                            Élèves associés
                        </h2>
                    </div>
                    <span class="text-xs px-2 py-1 rounded-full font-medium
                                 bg-blue-100 dark:bg-blue-900/40
                                 text-blue-600 dark:text-blue-400">
                        {{ $studentCount }}
                    </span>
                </div>

                @forelse($parent->students as $student)
                <div class="flex items-center gap-4 px-6 py-4
                            border-b border-slate-100 dark:border-slate-700/50 last:border-0
                            hover:bg-slate-50/50 dark:hover:bg-slate-700/20 transition-colors group">

                    {{-- Avatar étudiant --}}
                    @if($student->user?->avatar)
                    <img src="{{ asset('storage/' . $student->user->avatar) }}" alt="{{ $student->user->name }}"
                        class="w-11 h-11 rounded-xl object-cover shrink-0 ring-2 ring-blue-500/10" />
                    @else
                    <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-blue-400 to-emerald-400
                                flex items-center justify-center text-white text-sm font-bold shrink-0
                                ring-2 ring-blue-500/10">
                        {{ strtoupper(substr($student->user?->name ?? 'E', 0, 1)) }}
                    </div>
                    @endif

                    {{-- Infos étudiant --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <p class="text-sm font-semibold text-slate-700 dark:text-slate-200 truncate">
                                {{ $student->user?->name ?? 'Nom inconnu' }}
                            </p>
                            @if($student->classe)
                            <span class="text-[11px] px-2 py-0.5 rounded-lg font-medium shrink-0
                                         bg-slate-100 dark:bg-slate-700
                                         text-slate-600 dark:text-slate-300">
                                {{ $student->classe->name }}
                            </span>
                            @endif
                        </div>
                        <div class="flex items-center gap-3 mt-1 flex-wrap">
                            <p class="text-xs text-slate-400 dark:text-slate-500">
                                <i class="bi bi-hash text-[10px]"></i>
                                {{ $student->matricule }}
                            </p>
                            @if($student->user?->email)
                            <p class="text-xs text-slate-400 dark:text-slate-500 truncate">
                                <i class="bi bi-envelope text-[10px]"></i>
                                {{ $student->user->email }}
                            </p>
                            @endif
                        </div>
                    </div>

                    {{-- Lien vers le profil --}}
                    <a href="{{ route('students.show', $student) }}" class="shrink-0 p-2 rounded-lg text-slate-400 dark:text-slate-500
                              opacity-0 group-hover:opacity-100
                              hover:bg-blue-50 dark:hover:bg-blue-900/30
                              hover:text-blue-600 dark:hover:text-blue-400
                              transition-all duration-200" title="Voir le profil">
                        <i class="bi bi-arrow-right text-sm"></i>
                    </a>
                </div>
                @empty
                <div class="flex flex-col items-center justify-center py-12
                            text-slate-400 dark:text-slate-500">
                    <div class="w-14 h-14 rounded-2xl bg-slate-100 dark:bg-slate-700/50
                                flex items-center justify-center mb-3">
                        <i class="bi bi-mortarboard text-2xl"></i>
                    </div>
                    <p class="text-sm font-medium">Aucun élève associé</p>
                    <p class="text-xs mt-0.5">Modifiez ce parent pour associer des élèves</p>
                    <a href="{{ route('parents.edit', $parent) }}" class="mt-4 inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm
                              font-medium text-blue-600 dark:text-blue-400
                              bg-blue-50 dark:bg-blue-900/20 hover:bg-blue-100
                              dark:hover:bg-blue-900/40 transition-colors">
                        <i class="bi bi-plus-lg"></i>
                        Associer des élèves
                    </a>
                </div>
                @endforelse
            </div>

            {{-- Notifications récentes --}}
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border
                        border-slate-200 dark:border-slate-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700
                            flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-lg bg-cyan-50 dark:bg-cyan-900/30
                                flex items-center justify-center">
                        <i class="bi bi-bell-fill text-cyan-600 dark:text-cyan-400 text-sm"></i>
                    </div>
                    <h2 class="text-sm font-semibold text-slate-800 dark:text-slate-100">
                        Notifications reçues
                    </h2>
                </div>

                @php
                $notifications = $parent->notifications()->latest()->take(5)->get();
                @endphp

                @forelse($notifications as $notif)
                <div class="flex items-start gap-3 px-6 py-3.5
                            border-b border-slate-100 dark:border-slate-700/50 last:border-0">
                    <div class="w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900/40
                                flex items-center justify-center shrink-0 mt-0.5">
                        <i class="bi bi-info-circle-fill text-blue-600 dark:text-blue-400 text-xs"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-medium text-slate-700 dark:text-slate-200 leading-snug">
                            {{ $notif->data['message'] ?? 'Notification' }}
                        </p>
                        <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-0.5">
                            {{ $notif->created_at->diffForHumans() }}
                        </p>
                    </div>
                    @if(!$notif->read_at)
                    <span class="w-2 h-2 rounded-full bg-blue-500 shrink-0 mt-1.5"></span>
                    @endif
                </div>
                @empty
                <div class="flex flex-col items-center justify-center py-8
                            text-slate-400 dark:text-slate-500">
                    <i class="bi bi-bell-slash text-3xl mb-2 opacity-50"></i>
                    <p class="text-xs">Aucune notification reçue</p>
                </div>
                @endforelse
            </div>
        </div>

        {{-- ── Colonne latérale ── --}}
        <div class="space-y-5">

            {{-- Informations de contact --}}
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border
                        border-slate-200 dark:border-slate-700 overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-700
                            flex items-center gap-2">
                    <i class="bi bi-person-lines-fill text-blue-600 dark:text-blue-400 text-sm"></i>
                    <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-100">
                        Contact
                    </h3>
                </div>
                <div class="p-5 space-y-4">

                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-lg bg-slate-100 dark:bg-slate-700
                                    flex items-center justify-center shrink-0">
                            <i class="bi bi-envelope text-slate-500 dark:text-slate-400 text-sm"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="text-[10px] font-semibold uppercase tracking-wide
                                       text-slate-400 dark:text-slate-500 mb-0.5">
                                Email
                            </p>
                            <a href="mailto:{{ $parent->email }}" class="text-sm font-medium text-blue-600 dark:text-blue-400
                                      hover:underline truncate block">
                                {{ $parent->email }}
                            </a>
                        </div>
                    </div>

                    @if($parent->phone)
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-lg bg-slate-100 dark:bg-slate-700
                                    flex items-center justify-center shrink-0">
                            <i class="bi bi-telephone text-slate-500 dark:text-slate-400 text-sm"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-semibold uppercase tracking-wide
                                       text-slate-400 dark:text-slate-500 mb-0.5">
                                Téléphone
                            </p>
                            <a href="tel:{{ $parent->phone }}"
                                class="text-sm font-medium text-slate-700 dark:text-slate-200 hover:underline">
                                {{ $parent->phone }}
                            </a>
                        </div>
                    </div>
                    @endif

                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-lg bg-slate-100 dark:bg-slate-700
                                    flex items-center justify-center shrink-0">
                            <i class="bi bi-calendar3 text-slate-500 dark:text-slate-400 text-sm"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-semibold uppercase tracking-wide
                                       text-slate-400 dark:text-slate-500 mb-0.5">
                                Inscrit le
                            </p>
                            <p class="text-sm font-medium text-slate-700 dark:text-slate-200">
                                {{ $parent->created_at->translatedFormat('d F Y') }}
                            </p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-lg bg-slate-100 dark:bg-slate-700
                                    flex items-center justify-center shrink-0">
                            <i class="bi bi-shield-check text-slate-500 dark:text-slate-400 text-sm"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-semibold uppercase tracking-wide
                                       text-slate-400 dark:text-slate-500 mb-0.5">
                                Email vérifié
                            </p>
                            <p class="text-sm font-medium">
                                @if($parent->email_verified_at)
                                <span class="text-emerald-600 dark:text-emerald-400">
                                    <i class="bi bi-check-circle-fill text-xs mr-1"></i>
                                    {{ $parent->email_verified_at->translatedFormat('d M Y') }}
                                </span>
                                @else
                                <span class="text-amber-600 dark:text-amber-400">
                                    <i class="bi bi-exclamation-circle-fill text-xs mr-1"></i>
                                    Non vérifié
                                </span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Actions rapides --}}
                <div class="px-5 pb-5 space-y-2">
                    <a href="mailto:{{ $parent->email }}" class="w-full inline-flex items-center justify-center gap-2
                              px-4 py-2.5 rounded-xl text-sm font-medium
                              text-blue-600 dark:text-blue-400
                              bg-blue-50 dark:bg-blue-900/20
                              hover:bg-blue-100 dark:hover:bg-blue-900/40 transition-colors">
                        <i class="bi bi-envelope-fill"></i>
                        Envoyer un email
                    </a>
                    @if($parent->phone)
                    <a href="tel:{{ $parent->phone }}" class="w-full inline-flex items-center justify-center gap-2
                              px-4 py-2.5 rounded-xl text-sm font-medium
                              text-emerald-600 dark:text-emerald-400
                              bg-emerald-50 dark:bg-emerald-900/20
                              hover:bg-emerald-100 dark:hover:bg-emerald-900/40 transition-colors">
                        <i class="bi bi-telephone-fill"></i>
                        Appeler
                    </a>
                    @endif
                </div>
            </div>

            {{-- Statistiques --}}
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border
                        border-slate-200 dark:border-slate-700 overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-700
                            flex items-center gap-2">
                    <i class="bi bi-bar-chart-fill text-blue-600 dark:text-blue-400 text-sm"></i>
                    <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-100">
                        Statistiques
                    </h3>
                </div>
                <div class="p-5 space-y-3.5">
                    @php
                    $unreadNotifs = $parent->unreadNotifications()->count();
                    @endphp

                    <div class="flex items-center justify-between">
                        <span class="text-sm text-slate-500 dark:text-slate-400 flex items-center gap-2">
                            <i class="bi bi-mortarboard text-slate-400 text-xs w-4"></i>
                            Élèves suivis
                        </span>
                        <span class="text-sm font-bold text-blue-600 dark:text-blue-400">
                            {{ $studentCount }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-sm text-slate-500 dark:text-slate-400 flex items-center gap-2">
                            <i class="bi bi-bell text-slate-400 text-xs w-4"></i>
                            Notifs non lues
                        </span>
                        <span
                            class="text-sm font-bold
                                     {{ $unreadNotifs > 0 ? 'text-red-600 dark:text-red-400' : 'text-slate-500 dark:text-slate-400' }}">
                            {{ $unreadNotifs }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-sm text-slate-500 dark:text-slate-400 flex items-center gap-2">
                            <i class="bi bi-clock-history text-slate-400 text-xs w-4"></i>
                            Dernière activité
                        </span>
                        <span class="text-xs font-medium text-slate-600 dark:text-slate-300">
                            {{ $parent->updated_at->diffForHumans() }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Historique --}}
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border
                        border-slate-200 dark:border-slate-700 overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-700
                            flex items-center gap-2">
                    <i class="bi bi-clock-history text-blue-600 dark:text-blue-400 text-sm"></i>
                    <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-100">
                        Historique
                    </h3>
                </div>
                <div class="p-5 space-y-4">
                    <div class="flex gap-3">
                        <div class="flex flex-col items-center">
                            <div class="w-2.5 h-2.5 rounded-full bg-blue-600 mt-0.5 shrink-0"></div>
                            <div class="w-px flex-1 bg-slate-200 dark:bg-slate-700 my-1"></div>
                        </div>
                        <div class="pb-2">
                            <p class="text-xs font-semibold text-slate-700 dark:text-slate-200">
                                Compte créé
                            </p>
                            <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-0.5">
                                {{ $parent->created_at->translatedFormat('d F Y à H:i') }}
                            </p>
                        </div>
                    </div>
                    <div class="flex gap-3">
                        <div class="flex flex-col items-center">
                            <div class="w-2.5 h-2.5 rounded-full bg-emerald-500 mt-0.5 shrink-0"></div>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-slate-700 dark:text-slate-200">
                                Dernière modification
                            </p>
                            <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-0.5">
                                {{ $parent->updated_at->diffForHumans() }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ════════════════════ MODAL SUPPRESSION ════════════════════ --}}
<div id="delete-modal" class="hidden fixed inset-0 z-[100] flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" data-close-modal="delete-modal"></div>

    <div class="relative bg-white dark:bg-slate-800 rounded-2xl shadow-2xl border
                border-slate-200 dark:border-slate-700 w-full max-w-md overflow-hidden">
        <div class="p-6">
            <div class="w-12 h-12 rounded-2xl bg-red-100 dark:bg-red-900/30
                        flex items-center justify-center mb-4">
                <i class="bi bi-exclamation-triangle-fill text-red-600 dark:text-red-400 text-xl"></i>
            </div>
            <h3 class="text-base font-bold text-slate-800 dark:text-slate-100 mb-1.5">
                Supprimer ce parent ?
            </h3>
            <p class="text-sm text-slate-500 dark:text-slate-400">
                Le compte de
                <strong class="text-slate-700 dark:text-slate-200">{{ $parent->name }}</strong>
                sera définitivement supprimé ainsi que toutes ses associations avec des élèves.
                @if($studentCount > 0)
                <span class="block mt-2 text-amber-600 dark:text-amber-400 font-medium">
                    <i class="bi bi-exclamation-triangle-fill text-xs mr-1"></i>
                    {{ $studentCount }} élève{{ $studentCount > 1 ? 's' : '' }} sera{{ $studentCount > 1 ? 'ont' : '' }}
                    dissocié{{ $studentCount > 1 ? 's' : '' }}.
                </span>
                @endif
            </p>
        </div>
        <div class="px-6 py-4 bg-slate-50 dark:bg-slate-900/50 border-t
                    border-slate-200 dark:border-slate-700 flex justify-end gap-3">
            <button type="button" data-close-modal="delete-modal" class="px-4 py-2.5 rounded-xl text-sm font-medium
                           text-slate-600 dark:text-slate-300
                           bg-white dark:bg-slate-700
                           border border-slate-200 dark:border-slate-600
                           hover:bg-slate-50 dark:hover:bg-slate-600 transition-colors">
                Annuler
            </button>
            <form action="{{ route('parents.destroy', $parent) }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2.5 rounded-xl text-sm font-medium text-white
                               bg-red-600 hover:bg-red-700
                               shadow-sm shadow-red-600/20 transition-colors">
                    <i class="bi bi-trash3-fill mr-1.5"></i>
                    Supprimer définitivement
                </button>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('[data-open-modal]').forEach(function(btn) {
        btn.addEventListener('click', function() {
            document.getElementById(btn.dataset.openModal)?.classList.remove('hidden');
        });
    });
    document.querySelectorAll('[data-close-modal]').forEach(function(el) {
        el.addEventListener('click', function() {
            document.getElementById(el.dataset.closeModal)?.classList.add('hidden');
        });
    });
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            document.getElementById('delete-modal')?.classList.add('hidden');
        }
    });
});
</script>
@endpush