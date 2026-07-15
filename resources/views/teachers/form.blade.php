@extends('layouts.base')

@section('page_title', $teacher->exists ? 'Modifier l\'enseignant' : 'Nouvel enseignant')

@section('breadcrumb')
<span class="text-slate-300 dark:text-slate-600 font-light select-none">/</span>
<a href="{{ route('teachers.index') }}"
    class="text-slate-400 dark:text-slate-500 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
    Enseignants
</a>
@endsection

@section('page_header')
<div class="flex items-center justify-between gap-4">
    <div class="flex items-center gap-3 min-w-0">
        <div class="w-10 h-10 rounded-xl bg-linear-to-br from-blue-600 to-emerald-500
                        flex items-center justify-center shadow-sm shrink-0">
            <i class="bi bi-person-badge-fill text-white text-lg"></i>
        </div>
        <div class="min-w-0">
            <h1 class="text-lg font-bold text-slate-800 dark:text-slate-100 truncate">
                {{ $teacher->exists ? 'Modifier : ' . $teacher->user->name : 'Nouvel enseignant' }}
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400">
                {{ $teacher->exists
                        ? 'N° ' . $teacher->employee_number
                        : 'Créez un nouveau profil enseignant' }}
            </p>
        </div>
    </div>
    <a href="{{ route('teachers.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-medium
                  border border-slate-200 dark:border-slate-700
                  text-slate-700 dark:text-slate-300
                  hover:bg-slate-50 dark:hover:bg-slate-700/50
                  transition-all duration-200 shrink-0">
        <i class="bi bi-arrow-left"></i>
        <span class="hidden sm:inline">Retour</span>
    </a>
</div>
@endsection

@section('content')
<x-forms.form :model="$teacher" resource="teachers" enctype="multipart/form-data" class="space-y-6 max-w-4xl mx-auto">

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ── Colonne principale ── --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Compte utilisateur --}}
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200
                        dark:border-slate-700 shadow-sm overflow-hidden">

                <div class="flex items-center gap-3 px-6 py-4
                            border-b border-slate-100 dark:border-slate-700
                            bg-slate-50/60 dark:bg-slate-700/30">
                    <div class="w-7 h-7 rounded-lg bg-blue-100 dark:bg-blue-900/40
                                flex items-center justify-center">
                        <i class="bi bi-person-circle text-blue-600 dark:text-blue-400 text-sm"></i>
                    </div>
                    <h2 class="text-sm font-semibold text-slate-700 dark:text-slate-200">
                        Compte utilisateur
                    </h2>
                </div>

                <div class="p-6 space-y-5">

                    {{-- Nom complet --}}
                    <x-forms.input-field name="name" label="Nom complet" type="text"
                        :value="old('name', $teacher->user?->name)" placeholder="Prénom et nom" icon="bi-person"
                        required />

                    {{-- Email + N° Employé --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <x-forms.input-field name="email" label="Adresse email" type="email"
                            :value="old('email', $teacher->user?->email)" placeholder="email@ecole.com"
                            icon="bi-envelope" required />

                        <x-forms.input-field name="employee_number" label="N° Employé" type="text"
                            :value="old('employee_number', $teacher->employee_number)" placeholder="EMP-001"
                            icon="bi-badge-sd" required />
                    </div>

                    {{-- Mot de passe --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div class="space-y-1.5">
                            <label for="password" class="flex items-center gap-1.5 text-sm font-medium
                                          text-slate-700 dark:text-slate-200">
                                <i class="bi bi-lock text-slate-400"></i>
                                Mot de passe
                                @if(!$teacher->exists)
                                <span class="text-red-500">*</span>
                                @endif
                            </label>
                            <div class="relative">
                                <input type="password" name="password" id="password"
                                    placeholder="{{ $teacher->exists ? 'Laisser vide pour ne pas modifier' : 'Mot de passe…' }}"
                                    class="w-full pl-3.5 pr-10 py-2.5 rounded-xl border text-sm
                                              text-slate-800 dark:text-slate-100
                                              bg-white dark:bg-slate-700/50 placeholder-slate-400
                                              focus:outline-none focus:ring-2 transition-all duration-200
                                              {{ $errors->has('password')
                                                  ? 'border-red-500 focus:ring-red-500/40'
                                                  : 'border-slate-200 dark:border-slate-600 focus:ring-blue-500/30 focus:border-blue-500' }}"
                                    {{ !$teacher->exists ? 'required' : '' }}>
                                <button type="button" id="toggle-password" class="absolute inset-y-0 right-0 flex items-center pr-3.5
                                               text-slate-400 hover:text-slate-600 dark:hover:text-slate-300
                                               transition-colors focus:outline-none">
                                    <i class="bi bi-eye" id="pwd-eye-icon"></i>
                                </button>
                            </div>
                            @error('password')
                            <p class="mt-1.5 text-xs text-red-600 dark:text-red-400 flex items-center gap-1">
                                <i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
                            </p>
                            @enderror
                        </div>

                        <div class="space-y-1.5">
                            <label for="password_confirmation" class="flex items-center gap-1.5 text-sm font-medium
                                          text-slate-700 dark:text-slate-200">
                                <i class="bi bi-lock-fill text-slate-400"></i>
                                Confirmer le mot de passe
                                @if(!$teacher->exists)
                                <span class="text-red-500">*</span>
                                @endif
                            </label>
                            <input type="password" name="password_confirmation" id="password_confirmation"
                                placeholder="Confirmer…" class="w-full px-3.5 py-2.5 rounded-xl border text-sm
                                          text-slate-800 dark:text-slate-100
                                          bg-white dark:bg-slate-700/50 placeholder-slate-400
                                          focus:outline-none focus:ring-2 transition-all duration-200
                                          border-slate-200 dark:border-slate-600
                                          focus:ring-blue-500/30 focus:border-blue-500"
                                {{ !$teacher->exists ? 'required' : '' }}>
                            {{-- Force du mot de passe --}}
                            <div id="pwd-strength" class="hidden mt-2 space-y-1">
                                <div class="flex gap-1">
                                    @foreach(range(1, 4) as $i)
                                    <div id="pwd-bar-{{ $i }}" class="h-1 flex-1 rounded-full bg-slate-200 dark:bg-slate-700
                                                transition-all duration-300"></div>
                                    @endforeach
                                </div>
                                <p id="pwd-strength-label" class="text-xs text-slate-500 dark:text-slate-400"></p>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            {{-- Informations professionnelles --}}
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200
                        dark:border-slate-700 shadow-sm overflow-hidden">

                <div class="flex items-center gap-3 px-6 py-4
                            border-b border-slate-100 dark:border-slate-700
                            bg-slate-50/60 dark:bg-slate-700/30">
                    <div class="w-7 h-7 rounded-lg bg-emerald-100 dark:bg-emerald-900/40
                                flex items-center justify-center">
                        <i class="bi bi-briefcase-fill text-emerald-600 dark:text-emerald-400 text-sm"></i>
                    </div>
                    <h2 class="text-sm font-semibold text-slate-700 dark:text-slate-200">
                        Informations professionnelles
                    </h2>
                </div>

                <div class="p-6 space-y-5">

                    {{-- Qualification --}}
                    <x-forms.input-field name="qualification" label="Qualification" type="text"
                        :value="old('qualification', $teacher->qualification)"
                        placeholder="Ex : Master en Mathématiques, Licence en Physique…" icon="bi-award" required />

                    {{-- Spécialisation + Statut --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <x-forms.input-field name="specialization" label="Spécialisation" type="text"
                            :value="old('specialization', $teacher->specialization)"
                            placeholder="Ex : Algèbre, Physique quantique…" icon="bi-stars" />

                        <x-forms.select name="status" label="Statut" icon="bi-circle-fill" :options="[
                                'active'   => 'Actif',
                                'inactive' => 'Inactif',
                                'on_leave' => 'En congé',
                            ]" :value="old('status', $teacher->status ?? 'active')" required />
                    </div>

                    {{-- Date d'embauche + Genre --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <x-forms.input-field name="hire_date" label="Date d'embauche" type="text"
                            :value="old('hire_date', $teacher->hire_date?->format('Y-m-d'))" icon="bi-calendar-event"
                            class="flatpickr-date" required />

                        <x-forms.select name="gender" label="Genre" icon="bi-gender-ambiguous"
                            :options="['male' => 'Masculin', 'female' => 'Féminin']"
                            :value="old('gender', $teacher->gender)" placeholder="Sélectionner…" required />
                    </div>

                </div>
            </div>

            {{-- Informations personnelles --}}
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200
                        dark:border-slate-700 shadow-sm overflow-hidden">

                <div class="flex items-center gap-3 px-6 py-4
                            border-b border-slate-100 dark:border-slate-700
                            bg-slate-50/60 dark:bg-slate-700/30">
                    <div class="w-7 h-7 rounded-lg bg-cyan-100 dark:bg-cyan-900/40
                                flex items-center justify-center">
                        <i class="bi bi-info-circle-fill text-cyan-600 dark:text-cyan-400 text-sm"></i>
                    </div>
                    <h2 class="text-sm font-semibold text-slate-700 dark:text-slate-200">
                        Informations personnelles
                    </h2>
                </div>

                <div class="p-6 space-y-5">

                    {{-- Date de naissance + Nationalité --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <x-forms.input-field name="date_of_birth" label="Date de naissance" type="text"
                            :value="old('date_of_birth', $teacher->date_of_birth?->format('Y-m-d'))" icon="bi-cake"
                            class="flatpickr-date" help="Optionnel." />

                        <x-forms.input-field name="nationality" label="Nationalité" type="text"
                            :value="old('nationality', $teacher->nationality)" placeholder="Ex : Togolaise, Française…"
                            icon="bi-flag" help="Optionnel." />
                    </div>

                    {{-- Téléphone + Adresse --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <x-forms.input-field name="phone" label="Téléphone" type="tel"
                            :value="old('phone', $teacher->phone)" placeholder="+228 90 00 00 00" icon="bi-telephone" />

                        <x-forms.input-field name="address" label="Adresse" type="text"
                            :value="old('address', $teacher->address)" placeholder="Adresse complète…"
                            icon="bi-geo-alt" />
                    </div>

                    {{-- Bio --}}
                    <x-forms.textarea name="bio" label="Biographie" :value="old('bio', $teacher->bio)"
                        placeholder="Courte biographie de l'enseignant…" rows="4"
                        help="Optionnel. Maximum 1000 caractères." />

                </div>
            </div>

        </div>

        {{-- ── Colonne latérale ── --}}
        <div class="space-y-6">

            {{-- Photo de profil --}}
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200
                        dark:border-slate-700 shadow-sm overflow-hidden">

                <div class="flex items-center gap-3 px-5 py-4
                            border-b border-slate-100 dark:border-slate-700
                            bg-slate-50/60 dark:bg-slate-700/30">
                    <div class="w-7 h-7 rounded-lg bg-blue-100 dark:bg-blue-900/40
                                flex items-center justify-center">
                        <i class="bi bi-camera-fill text-blue-600 dark:text-blue-400 text-sm"></i>
                    </div>
                    <h2 class="text-sm font-semibold text-slate-700 dark:text-slate-200">
                        Photo de profil
                    </h2>
                </div>

                <div class="p-5 space-y-4">
                    {{-- Aperçu --}}
                    <div class="flex justify-center">
                        <div class="relative group cursor-pointer" id="avatar-wrapper">
                            <div id="avatar-preview" class="w-28 h-28 rounded-2xl overflow-hidden
                                        border-2 border-dashed border-slate-300 dark:border-slate-600
                                        bg-slate-50 dark:bg-slate-700/50
                                        flex items-center justify-center">
                                @if($teacher->exists && $teacher->user?->avatar)
                                <img src="{{ asset('storage/' . $teacher->user->avatar) }}"
                                    alt="{{ $teacher->user->name }}" id="avatar-img" class="w-full h-full object-cover">
                                @else
                                <div id="avatar-placeholder" class="flex flex-col items-center gap-1 text-slate-400">
                                    <i class="bi bi-person-fill text-4xl"></i>
                                    <span class="text-xs">Aucune photo</span>
                                </div>
                                @endif
                            </div>
                            {{-- Overlay upload --}}
                            <label for="avatar" class="absolute inset-0 flex items-center justify-center
                                          bg-slate-900/50 rounded-2xl opacity-0
                                          group-hover:opacity-100 transition-opacity cursor-pointer">
                                <i class="bi bi-camera text-white text-2xl"></i>
                            </label>
                        </div>
                    </div>

                    {{-- Input file --}}
                    <div>
                        <input type="file" name="avatar" id="avatar" accept="image/*" class="hidden">
                        <label for="avatar" class="flex items-center justify-center gap-2 w-full
                                      px-4 py-2.5 rounded-xl border border-dashed
                                      border-slate-300 dark:border-slate-600
                                      text-sm text-slate-500 dark:text-slate-400
                                      hover:border-blue-400 hover:text-blue-600
                                      dark:hover:border-blue-500 dark:hover:text-blue-400
                                      cursor-pointer transition-all duration-200">
                            <i class="bi bi-upload"></i>
                            Choisir une photo
                        </label>
                        <p class="text-[10px] text-slate-400 dark:text-slate-500 text-center mt-1.5">
                            JPG, PNG, WEBP · Max 2 Mo
                        </p>
                    </div>

                    @error('avatar')
                    <p class="text-xs text-red-600 dark:text-red-400 flex items-center gap-1">
                        <i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
                    </p>
                    @enderror
                </div>
            </div>

            {{-- Carte aperçu profil --}}
            <div class="bg-linear-to-br from-blue-600 to-emerald-500 rounded-2xl p-5 shadow-sm">
                <p class="text-xs font-semibold text-blue-100 uppercase tracking-wide mb-4">
                    Aperçu du profil
                </p>
                <div class="flex items-center gap-3 mb-4">
                    <div id="card-avatar" class="w-12 h-12 rounded-xl bg-white/20 flex items-center justify-center
                                text-white font-bold text-lg shrink-0">
                        <i class="bi bi-person"></i>
                    </div>
                    <div class="min-w-0">
                        <p id="card-name" class="font-bold text-white truncate">—</p>
                        <p id="card-number" class="text-xs text-blue-200">—</p>
                    </div>
                </div>
                <div class="space-y-2">
                    <div class="flex items-center gap-2 text-xs text-blue-100">
                        <i class="bi bi-award w-4 text-center"></i>
                        <span id="card-qualification" class="truncate">—</span>
                    </div>
                    <div class="flex items-center gap-2 text-xs text-blue-100">
                        <i class="bi bi-envelope w-4 text-center"></i>
                        <span id="card-email" class="truncate">—</span>
                    </div>
                    <div class="flex items-center gap-2 text-xs text-blue-100">
                        <i class="bi bi-telephone w-4 text-center"></i>
                        <span id="card-phone" class="truncate">—</span>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- ── Actions ── --}}
    <div class="flex items-center justify-between gap-3
                bg-white dark:bg-slate-800 rounded-2xl border border-slate-200
                dark:border-slate-700 shadow-sm px-6 py-4">
        <a href="{{ route('teachers.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-medium
                  border border-slate-200 dark:border-slate-700
                  text-slate-700 dark:text-slate-300
                  hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-all duration-200">
            <i class="bi bi-x-lg"></i>
            Annuler
        </a>
        <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl text-sm font-semibold
                       bg-blue-600 hover:bg-blue-700 text-white
                       shadow-sm hover:shadow-md transition-all duration-200
                       focus:outline-none focus:ring-2 focus:ring-blue-500/40">
            <i class="bi {{ $teacher->exists ? 'bi-check-lg' : 'bi-plus-lg' }}"></i>
            {{ $teacher->exists ? 'Enregistrer les modifications' : 'Créer l\'enseignant' }}
        </button>
    </div>

</x-forms.form>
@endsection

@push('scripts')
<script>
(() => {
    // ── Flatpickr ─────────────────────────────────────────────────
    document.querySelectorAll('.flatpickr-date').forEach(el => {
        flatpickr(el, {
            dateFormat: 'Y-m-d',
            altInput: true,
            altFormat: 'd/m/Y',
            locale: 'fr',
        });
    });

    // ── Aperçu avatar ─────────────────────────────────────────────
    const avatarInput = document.getElementById('avatar');
    const avatarPreview = document.getElementById('avatar-preview');
    const placeholder = document.getElementById('avatar-placeholder');
    const cardAvatar = document.getElementById('card-avatar');

    avatarInput?.addEventListener('change', (e) => {
        const file = e.target.files[0];
        if (!file) return;

        const reader = new window.FileReader();
        reader.onload = (ev) => {
            // Preview principal
            if (placeholder) placeholder.remove();
            let img = avatarPreview.querySelector('img');
            if (!img) {
                img = document.createElement('img');
                img.className = 'w-full h-full object-cover';
                avatarPreview.appendChild(img);
            }
            img.src = ev.target.result;

            // Card avatar
            if (cardAvatar) {
                cardAvatar.innerHTML = `<img src="${ev.target.result}"
                    class="w-full h-full object-cover rounded-xl" alt="Avatar">`;
            }
        };
        reader.readAsDataURL(file);
    });

    // ── Aperçu carte profil ───────────────────────────────────────
    const nameInput = document.querySelector('[name="name"]');
    const empInput = document.querySelector('[name="employee_number"]');
    const emailInput = document.querySelector('[name="email"]');
    const phoneInput = document.querySelector('[name="phone"]');
    const qualInput = document.querySelector('[name="qualification"]');

    const cardName = document.getElementById('card-name');
    const cardNumber = document.getElementById('card-number');
    const cardEmail = document.getElementById('card-email');
    const cardPhone = document.getElementById('card-phone');
    const cardQual = document.getElementById('card-qualification');

    function updateCard() {
        if (cardName) cardName.textContent = nameInput?.value || '—';
        if (cardNumber) cardNumber.textContent = empInput?.value ? `N° ${empInput.value}` : '—';
        if (cardEmail) cardEmail.textContent = emailInput?.value || '—';
        if (cardPhone) cardPhone.textContent = phoneInput?.value || '—';
        if (cardQual) cardQual.textContent = qualInput?.value || '—';
    }

    [nameInput, empInput, emailInput, phoneInput, qualInput].forEach(el => {
        el?.addEventListener('input', updateCard);
    });

    updateCard(); // init

    // ── Toggle password visibility ────────────────────────────────
    const toggleBtn = document.getElementById('toggle-password');
    const pwdInput = document.getElementById('password');
    const pwdEyeIcon = document.getElementById('pwd-eye-icon');

    toggleBtn?.addEventListener('click', () => {
        const isText = pwdInput?.type === 'text';
        if (pwdInput) pwdInput.type = isText ? 'password' : 'text';
        if (pwdEyeIcon) {
            pwdEyeIcon.className = isText ? 'bi bi-eye' : 'bi bi-eye-slash';
        }
    });

    // ── Force du mot de passe ─────────────────────────────────────
    const pwdStrength = document.getElementById('pwd-strength');
    const pwdLabel = document.getElementById('pwd-strength-label');
    const bars = [1, 2, 3, 4].map(i => document.getElementById(`pwd-bar-${i}`));

    const strengthLevels = [{
            score: 1,
            label: 'Très faible',
            color: 'bg-red-500'
        },
        {
            score: 2,
            label: 'Faible',
            color: 'bg-amber-500'
        },
        {
            score: 3,
            label: 'Moyen',
            color: 'bg-blue-500'
        },
        {
            score: 4,
            label: 'Fort',
            color: 'bg-emerald-500'
        },
    ];

    function getStrengthScore(pwd) {
        let score = 0;
        if (pwd.length >= 8) score++;
        if (/[A-Z]/.test(pwd)) score++;
        if (/[0-9]/.test(pwd)) score++;
        if (/[^A-Za-z0-9]/.test(pwd)) score++;
        return score;
    }

    pwdInput?.addEventListener('input', () => {
        const val = pwdInput.value;
        if (!val) {
            pwdStrength?.classList.add('hidden');
            return;
        }

        pwdStrength?.classList.remove('hidden');
        const score = getStrengthScore(val);
        const level = strengthLevels[score - 1] ?? strengthLevels[0];

        bars.forEach((bar, idx) => {
            if (!bar) return;
            bar.className = `h-1 flex-1 rounded-full transition-all duration-300 ${
                idx < score ? level.color : 'bg-slate-200 dark:bg-slate-700'
            }`;
        });

        if (pwdLabel) {
            pwdLabel.textContent = level.label;
            pwdLabel.className = `text-xs ${level.color.replace('bg-', 'text-')}`;
        }
    });
})();
</script>
@endpush