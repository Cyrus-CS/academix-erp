@extends('layouts.base')

@section('page_title', 'Modifier le profil')

@section('breadcrumb')
<span class="text-slate-300 dark:text-slate-600 font-light select-none">/</span>
<a href="{{ route('profile.index') }}"
    class="text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
    Mon profil
</a>
<span class="text-slate-300 dark:text-slate-600 font-light select-none">/</span>
<span class="text-slate-400 dark:text-slate-500">Modifier</span>
@endsection

@section('content')

<div class="max-w-5xl mx-auto space-y-6">

    {{-- ══════════════════════════════════════════════════════
         HEADER
    ══════════════════════════════════════════════════════ --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 dark:text-slate-100">
                Paramètres du profil
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                Gérez vos informations personnelles et vos préférences de sécurité
            </p>
        </div>
        <a href="{{ route('profile.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-medium
                  border border-slate-200 dark:border-slate-700
                  text-slate-600 dark:text-slate-400
                  hover:border-slate-300 dark:hover:border-slate-600
                  hover:bg-slate-50 dark:hover:bg-slate-800
                  transition-all duration-200">
            <i class="bi bi-arrow-left"></i>
            Retour au profil
        </a>
    </div>

    {{-- ══════════════════════════════════════════════════════
         TABS DE NAVIGATION
    ══════════════════════════════════════════════════════ --}}
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm 
                border border-slate-200 dark:border-slate-700 p-1.5 
                sticky top-20 z-20 backdrop-blur-md">
        <nav class="flex items-center gap-1 overflow-x-auto scrollbar-none" id="profile-tabs">
            @foreach([
            ['id' => 'info', 'icon' => 'bi-person-fill', 'label' => 'Informations'],
            ['id' => 'password', 'icon' => 'bi-shield-lock-fill', 'label' => 'Mot de passe'],
            ['id' => 'danger', 'icon' => 'bi-exclamation-triangle-fill', 'label' => 'Zone dangereuse'],
            ] as $index => $tab)
            <button type="button" data-tab-target="{{ $tab['id'] }}" class="tab-btn flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-medium
                           whitespace-nowrap transition-all duration-200 
                           {{ $index === 0 
                              ? 'bg-blue-600 text-white shadow-sm' 
                              : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700' }}">
                <i class="bi {{ $tab['icon'] }}"></i>
                {{ $tab['label'] }}
            </button>
            @endforeach
        </nav>
    </div>

    {{-- ══════════════════════════════════════════════════════
         SECTION 1 — Informations personnelles
    ══════════════════════════════════════════════════════ --}}
    <section id="tab-info" class="tab-panel space-y-6">
        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" autocomplete="off"
            class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm 
                     border border-slate-200 dark:border-slate-700 overflow-hidden">
            @csrf
            @method('PATCH')

            {{-- Header --}}
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
                        Mettez à jour vos informations de contact
                    </p>
                </div>
            </div>

            <div class="p-6 space-y-6">

                {{-- ─── Avatar upload ─── --}}
                <div class="flex flex-col sm:flex-row sm:items-center gap-6 pb-6 
                            border-b border-slate-200 dark:border-slate-700">
                    <div class="shrink-0 relative group">
                        @if($user->avatar)
                        <img id="avatar-preview" src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}"
                            class="w-24 h-24 rounded-2xl object-cover 
                                        ring-4 ring-slate-100 dark:ring-slate-700" />
                        @else
                        <div id="avatar-preview" class="w-24 h-24 rounded-2xl 
                                        bg-linear-to-br from-blue-500 to-emerald-500 
                                        flex items-center justify-center 
                                        text-white text-3xl font-bold 
                                        ring-4 ring-slate-100 dark:ring-slate-700">
                            {{ strtoupper(substr($user->name, 0, 1)) }}{{ strtoupper(substr(explode(' ', $user->name)[1] ?? '', 0, 1)) }}
                        </div>
                        @endif

                        {{-- Overlay hover --}}
                        <label for="avatar-input" class="absolute inset-0 rounded-2xl bg-black/50 
                                      flex items-center justify-center opacity-0 
                                      group-hover:opacity-100 transition-opacity cursor-pointer">
                            <i class="bi bi-camera-fill text-white text-xl"></i>
                        </label>
                    </div>

                    <div class="flex-1 min-w-0">
                        <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-100 mb-1">
                            Photo de profil
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mb-4">
                            JPG, PNG ou WebP. Taille maximale : 2 Mo
                        </p>

                        <div class="flex flex-wrap items-center gap-2">
                            <label for="avatar-input" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg 
                                          text-xs font-semibold cursor-pointer
                                          bg-blue-600 hover:bg-blue-700 text-white 
                                          transition-colors">
                                <i class="bi bi-upload"></i>
                                Choisir une image
                            </label>
                            <input type="file" name="avatar" id="avatar-input" accept="image/jpeg,image/png,image/webp"
                                class="hidden" />

                            @if($user->avatar)
                            <button type="button" data-open-modal="delete-avatar-modal" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg 
                                           text-xs font-semibold
                                           border border-red-200 dark:border-red-800
                                           text-red-600 dark:text-red-400 
                                           hover:bg-red-50 dark:hover:bg-red-900/20 
                                           transition-colors">
                                <i class="bi bi-trash3"></i>
                                Supprimer
                            </button>
                            @endif
                        </div>

                        @error('avatar')
                        <p class="mt-2 text-xs text-red-600 dark:text-red-400 flex items-center gap-1">
                            <i class="bi bi-exclamation-circle-fill"></i>
                            {{ $message }}
                        </p>
                        @enderror
                    </div>
                </div>

                {{-- ─── Champs ─── --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <x-forms.input-field name="name" label="Nom complet" :required="true" icon="bi-person"
                        :value="$user->name" placeholder="Ex. Jean Dupont" />

                    <x-forms.input-field name="email" type="email" label="Adresse email" :required="true"
                        icon="bi-envelope" :value="$user->email" placeholder="exemple@ecole.com" />

                    <x-forms.input-field name="phone" type="tel" label="Numéro de téléphone" icon="bi-telephone"
                        :value="$user->phone" placeholder="+229 XX XX XX XX" help="Format international recommandé" />
                </div>

                {{-- Avertissement email --}}
                @unless($user->email_verified_at)
                <div class="flex items-start gap-3 px-4 py-3.5 rounded-xl
                            bg-amber-50 dark:bg-amber-900/20 
                            border border-amber-200 dark:border-amber-800">
                    <i class="bi bi-exclamation-triangle-fill text-amber-500 text-lg shrink-0 mt-0.5"></i>
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-amber-700 dark:text-amber-400">
                            Votre email n'est pas vérifié
                        </p>
                        <p class="text-xs text-amber-600 dark:text-amber-400/80 mt-0.5">
                            Modifier votre email nécessitera une nouvelle vérification.
                        </p>
                    </div>
                </div>
                @endunless
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-end gap-3 px-6 py-4 
                        bg-slate-50 dark:bg-slate-800/50 
                        border-t border-slate-200 dark:border-slate-700">
                <a href="{{ route('profile.index') }}" class="px-4 py-2 rounded-xl text-sm font-medium
                          text-slate-600 dark:text-slate-400
                          hover:bg-slate-100 dark:hover:bg-slate-700
                          transition-all duration-200">
                    Annuler
                </a>
                <button type="submit" class="inline-flex items-center gap-2 px-5 py-2 rounded-xl 
                               text-sm font-semibold text-white
                               bg-blue-600 hover:bg-blue-700 
                               shadow-sm transition-all duration-200">
                    <i class="bi bi-check-lg"></i>
                    Enregistrer les modifications
                </button>
            </div>
        </form>
    </section>

    {{-- ══════════════════════════════════════════════════════
         SECTION 2 — Mot de passe
    ══════════════════════════════════════════════════════ --}}
    <section id="tab-password" class="tab-panel hidden space-y-6">
        <form action="{{ route('profile.password.update') }}" method="POST" autocomplete="off" class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm 
                     border border-slate-200 dark:border-slate-700 overflow-hidden">
            @csrf
            @method('PATCH')

            <div class="flex items-center gap-3 px-6 py-4 
                        border-b border-slate-200 dark:border-slate-700">
                <div class="w-9 h-9 rounded-xl bg-emerald-100 dark:bg-emerald-900/40 
                            flex items-center justify-center">
                    <i class="bi bi-shield-lock-fill text-emerald-600 dark:text-emerald-400"></i>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-slate-800 dark:text-slate-100">
                        Modifier le mot de passe
                    </h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400">
                        Utilisez un mot de passe fort et unique
                    </p>
                </div>
            </div>

            <div class="p-6 space-y-5">

                <x-forms.input-field name="current_password" type="password" label="Mot de passe actuel"
                    :required="true" icon="bi-lock" placeholder="••••••••" />

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <x-forms.input-field name="password" type="password" label="Nouveau mot de passe" :required="true"
                        icon="bi-shield-lock" placeholder="••••••••"
                        help="Min. 8 caractères, majuscules, chiffres, symboles" />

                    <x-forms.input-field name="password_confirmation" type="password" label="Confirmer le mot de passe"
                        :required="true" icon="bi-shield-check" placeholder="••••••••" />
                </div>

                {{-- Indicateurs de sécurité --}}
                <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-700/30 
                            border border-slate-200 dark:border-slate-700">
                    <p class="text-xs font-semibold text-slate-700 dark:text-slate-300 mb-3 flex items-center gap-1.5">
                        <i class="bi bi-info-circle-fill text-blue-500"></i>
                        Critères de sécurité recommandés
                    </p>
                    <ul class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs 
                               text-slate-600 dark:text-slate-400">
                        <li class="flex items-center gap-1.5">
                            <i class="bi bi-check-circle text-emerald-500"></i>
                            8 caractères minimum
                        </li>
                        <li class="flex items-center gap-1.5">
                            <i class="bi bi-check-circle text-emerald-500"></i>
                            Majuscules et minuscules
                        </li>
                        <li class="flex items-center gap-1.5">
                            <i class="bi bi-check-circle text-emerald-500"></i>
                            Au moins un chiffre
                        </li>
                        <li class="flex items-center gap-1.5">
                            <i class="bi bi-check-circle text-emerald-500"></i>
                            Un caractère spécial (@ # $ !)
                        </li>
                    </ul>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 px-6 py-4 
                        bg-slate-50 dark:bg-slate-800/50 
                        border-t border-slate-200 dark:border-slate-700">
                <button type="reset" class="px-4 py-2 rounded-xl text-sm font-medium
                               text-slate-600 dark:text-slate-400
                               hover:bg-slate-100 dark:hover:bg-slate-700
                               transition-all duration-200">
                    Réinitialiser
                </button>
                <button type="submit" class="inline-flex items-center gap-2 px-5 py-2 rounded-xl 
                               text-sm font-semibold text-white
                               bg-emerald-600 hover:bg-emerald-700 
                               shadow-sm transition-all duration-200">
                    <i class="bi bi-shield-check"></i>
                    Mettre à jour le mot de passe
                </button>
            </div>
        </form>
    </section>

    {{-- ══════════════════════════════════════════════════════
         SECTION 3 — Zone dangereuse
    ══════════════════════════════════════════════════════ --}}
    <section id="tab-danger" class="tab-panel hidden space-y-6">
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm 
                    border-2 border-red-200 dark:border-red-900/50 overflow-hidden">
            <div class="flex items-center gap-3 px-6 py-4 
                        border-b border-red-200 dark:border-red-900/50 
                        bg-red-50 dark:bg-red-900/20">
                <div class="w-9 h-9 rounded-xl bg-red-100 dark:bg-red-900/40 
                            flex items-center justify-center">
                    <i class="bi bi-exclamation-triangle-fill text-red-600 dark:text-red-400"></i>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-red-700 dark:text-red-400">
                        Zone dangereuse
                    </h2>
                    <p class="text-xs text-red-600/70 dark:text-red-400/70">
                        Les actions ci-dessous sont irréversibles
                    </p>
                </div>
            </div>

            <div class="p-6">
                <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
                    <div class="flex-1">
                        <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-100 mb-1.5">
                            Supprimer mon compte
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                            Une fois votre compte supprimé, toutes vos ressources et
                            données seront définitivement effacées. Cette action ne
                            peut pas être annulée. Veuillez confirmer avant de continuer.
                        </p>
                    </div>
                    <button type="button" data-open-modal="delete-account-modal" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl 
                                   text-sm font-semibold text-white shrink-0
                                   bg-red-600 hover:bg-red-700 
                                   shadow-sm transition-all duration-200">
                        <i class="bi bi-trash3-fill"></i>
                        Supprimer le compte
                    </button>
                </div>
            </div>
        </div>
    </section>

</div>

{{-- ══════════════════════════════════════════════════════
     MODAL — Confirmer suppression avatar
══════════════════════════════════════════════════════ --}}
<div id="delete-avatar-modal" class="hidden fixed inset-0 z-50 items-center justify-center p-4">
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" data-close-modal></div>
    <div class="relative bg-white dark:bg-slate-800 rounded-2xl shadow-xl 
                border border-slate-200 dark:border-slate-700 
                max-w-md w-full overflow-hidden">
        <div class="p-6">
            <div class="w-12 h-12 rounded-2xl bg-red-100 dark:bg-red-900/40 
                        flex items-center justify-center mb-4">
                <i class="bi bi-trash3-fill text-red-600 dark:text-red-400 text-xl"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100 mb-2">
                Supprimer la photo de profil ?
            </h3>
            <p class="text-sm text-slate-500 dark:text-slate-400 mb-6">
                Votre photo actuelle sera supprimée. Vous pourrez toujours en
                ajouter une nouvelle plus tard.
            </p>

            <div class="flex items-center justify-end gap-3">
                <button type="button" data-close-modal class="px-4 py-2 rounded-xl text-sm font-medium
                               text-slate-600 dark:text-slate-400
                               hover:bg-slate-100 dark:hover:bg-slate-700
                               transition-all duration-200">
                    Annuler
                </button>
                <form action="{{ route('profile.avatar.destroy') }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl 
                                   text-sm font-semibold text-white
                                   bg-red-600 hover:bg-red-700 transition-all duration-200">
                        <i class="bi bi-trash3"></i>
                        Confirmer
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════
     MODAL — Confirmer suppression compte
══════════════════════════════════════════════════════ --}}
<div id="delete-account-modal" class="hidden fixed inset-0 z-50 items-center justify-center p-4">
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" data-close-modal></div>
    <div class="relative bg-white dark:bg-slate-800 rounded-2xl shadow-xl 
                border border-slate-200 dark:border-slate-700 
                max-w-lg w-full overflow-hidden">
        <form action="{{ route('profile.destroy') }}" method="POST">
            @csrf
            @method('DELETE')

            <div class="p-6">
                <div class="w-12 h-12 rounded-2xl bg-red-100 dark:bg-red-900/40 
                            flex items-center justify-center mb-4">
                    <i class="bi bi-exclamation-triangle-fill text-red-600 dark:text-red-400 text-xl"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100 mb-2">
                    Supprimer définitivement votre compte ?
                </h3>
                <p class="text-sm text-slate-500 dark:text-slate-400 mb-5 leading-relaxed">
                    Cette action est <strong class="text-red-600 dark:text-red-400">irréversible</strong>.
                    Toutes vos données personnelles, historiques et fichiers
                    seront supprimés définitivement.
                </p>

                <div class="p-3 rounded-xl bg-red-50 dark:bg-red-900/20 
                            border border-red-200 dark:border-red-900/50 mb-5">
                    <p class="text-xs text-red-700 dark:text-red-400 flex items-start gap-2">
                        <i class="bi bi-info-circle-fill mt-0.5"></i>
                        <span>Pour confirmer, veuillez saisir votre mot de passe actuel ci-dessous.</span>
                    </p>
                </div>

                <x-forms.input-field name="password" type="password" label="Mot de passe actuel" :required="true"
                    icon="bi-lock" placeholder="••••••••" />
            </div>

            <div class="flex items-center justify-end gap-3 px-6 py-4 
                        bg-slate-50 dark:bg-slate-800/50 
                        border-t border-slate-200 dark:border-slate-700">
                <button type="button" data-close-modal class="px-4 py-2 rounded-xl text-sm font-medium
                               text-slate-600 dark:text-slate-400
                               hover:bg-slate-100 dark:hover:bg-slate-700
                               transition-all duration-200">
                    Annuler
                </button>
                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl 
                               text-sm font-semibold text-white
                               bg-red-600 hover:bg-red-700 transition-all duration-200">
                    <i class="bi bi-trash3-fill"></i>
                    Supprimer définitivement
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════
     SCRIPTS
══════════════════════════════════════════════════════ --}}
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {

    // ── Système d'onglets ────────────────────────────────
    const tabButtons = document.querySelectorAll('.tab-btn');
    const tabPanels = document.querySelectorAll('.tab-panel');

    const activateTab = (target) => {
        tabButtons.forEach(btn => {
            const isActive = btn.dataset.tabTarget === target;
            btn.classList.toggle('bg-blue-600', isActive);
            btn.classList.toggle('text-white', isActive);
            btn.classList.toggle('shadow-sm', isActive);
            btn.classList.toggle('text-slate-600', !isActive);
            btn.classList.toggle('dark:text-slate-400', !isActive);
            btn.classList.toggle('hover:bg-slate-100', !isActive);
            btn.classList.toggle('dark:hover:bg-slate-700', !isActive);
        });

        tabPanels.forEach(panel => {
            panel.classList.toggle('hidden', panel.id !== `tab-${target}`);
        });
    };

    tabButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            activateTab(btn.dataset.tabTarget);
            history.replaceState(null, '', `#${btn.dataset.tabTarget}`);
        });
    });

    // Activer l'onglet depuis l'ancre URL
    const hash = window.location.hash.replace('#', '');
    if (hash && ['info', 'password', 'danger'].includes(hash)) {
        activateTab(hash);
    }

    // ── Preview avatar ────────────────────────────────────
    const avatarInput = document.getElementById('avatar-input');
    const avatarPreview = document.getElementById('avatar-preview');

    avatarInput?.addEventListener('change', (e) => {
        const file = e.target.files[0];
        if (!file) return;

        // Validation taille (2 Mo)
        if (file.size > 2 * 1024 * 1024) {
            window.showToast?.({
                type: 'error',
                title: 'Fichier trop volumineux',
                message: 'La photo doit faire moins de 2 Mo.',
            });
            avatarInput.value = '';
            return;
        }

        const reader = new window.FileReader();
        reader.onload = (ev) => {
            if (avatarPreview.tagName === 'IMG') {
                avatarPreview.src = ev.target.result;
            } else {
                // Remplacer le div par une image
                const img = document.createElement('img');
                img.id = 'avatar-preview';
                img.src = ev.target.result;
                img.alt = 'Aperçu';
                img.className =
                    'w-24 h-24 rounded-2xl object-cover ring-4 ring-slate-100 dark:ring-slate-700';
                avatarPreview.replaceWith(img);
            }
        };
        reader.readAsDataURL(file);
    });

    // ── Système de modals ────────────────────────────────
    document.querySelectorAll('[data-open-modal]').forEach(btn => {
        btn.addEventListener('click', () => {
            const modal = document.getElementById(btn.dataset.openModal);
            if (!modal) return;
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';
        });
    });

    document.querySelectorAll('[data-close-modal]').forEach(el => {
        el.addEventListener('click', () => {
            const modal = el.closest('[id$="-modal"]');
            if (!modal) return;
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = '';
        });
    });

    // Fermer avec Escape
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            document.querySelectorAll('[id$="-modal"]:not(.hidden)').forEach(modal => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                document.body.style.overflow = '';
            });
        }
    });
});
</script>
@endpush

@endsection