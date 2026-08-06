@extends('layouts.base')

@php
$isEdit = $user->exists;
@endphp

@section('page_title', $isEdit ? 'Modifier l\'utilisateur' : 'Nouvel utilisateur')

@section('breadcrumb')
<span class="text-slate-300 dark:text-slate-600 font-light select-none">/</span>
<a href="{{ route('users.index') }}"
    class="text-slate-400 dark:text-slate-500 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
    Utilisateurs
</a>
@endsection

@section('page_header')
<div class="flex items-center gap-3">
    <a href="{{ route('users.index') }}" class="p-2 rounded-xl text-slate-400 dark:text-slate-500
                  hover:bg-slate-100 dark:hover:bg-slate-700
                  hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
        <i class="bi bi-arrow-left text-lg"></i>
    </a>
    <div>
        <h1 class="text-xl sm:text-2xl font-bold text-slate-800 dark:text-slate-100">
            {{ $isEdit ? 'Modifier l\'utilisateur' : 'Nouvel utilisateur' }}
        </h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
            {{ $isEdit ? 'Mettez à jour les informations du compte.' : 'Créez un nouveau compte utilisateur.' }}
        </p>
    </div>
</div>
@endsection

@section('content')

<x-forms.form :model="$user" resource="users" enctype="multipart/form-data" class="max-w-3xl">

    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700
                    shadow-sm divide-y divide-slate-100 dark:divide-slate-700">

        {{-- ── Section : Avatar ── --}}
        <div class="p-6 flex items-center gap-5">
            <div class="relative shrink-0">
                <img id="avatar-preview"
                    src="{{ $user->avatar ? asset('storage/' . $user->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name ?? 'U') . '&background=2563EB&color=fff' }}"
                    alt="Avatar" class="w-20 h-20 rounded-full object-cover ring-4 ring-slate-100 dark:ring-slate-700">
                <label for="avatar" class="absolute -bottom-1 -right-1 w-7 h-7 rounded-full bg-blue-600 hover:bg-blue-700
                                  flex items-center justify-center cursor-pointer shadow-md transition-colors">
                    <i class="bi bi-camera-fill text-white text-xs"></i>
                </label>
                <input type="file" id="avatar" name="avatar" accept="image/*" class="hidden">
            </div>
            <div>
                <p class="text-sm font-semibold text-slate-700 dark:text-slate-200">Photo de profil</p>
                <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">
                    JPG, PNG. Taille max 2 Mo.
                </p>
                <x-forms.error name="avatar" />
            </div>
        </div>

        {{-- ── Section : Informations ── --}}
        <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-5">

            <x-forms.input-field name="name" label="Nom complet" icon="bi-person-fill" :value="$user->name"
                placeholder="Ex : Jean Dupont" required />

            <x-forms.input-field name="email" type="email" label="Adresse e-mail" icon="bi-envelope-fill"
                :value="$user->email" placeholder="jean.dupont@ecole.com" required />

            <x-forms.input-field name="password" type="password" label="Mot de passe" icon="bi-lock-fill"
                placeholder="{{ $isEdit ? 'Laisser vide pour ne pas changer' : '••••••••' }}" :required="!$isEdit"
                :help="$isEdit ? 'Laissez vide pour conserver le mot de passe actuel.' : 'Minimum 8 caractères.'" />

            <x-forms.input-field name="password_confirmation" type="password" label="Confirmer le mot de passe"
                icon="bi-lock-fill" placeholder="••••••••" :required="!$isEdit" />

            <x-forms.select name="role" label="Rôle" icon="bi-shield-fill" :options="$roles" optionValue="name"
                optionLabel="name" :value="$user->getRoleNames()->first() ?? old('role')"
                placeholder="Sélectionner un rôle" required />

            <div class="flex items-center gap-3 sm:pt-7">
                <button type="button" id="toggle-active"
                    data-active="{{ old('is_active', $user->is_active ?? true) ? 'true' : 'false' }}"
                    class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors
                                   focus:outline-none focus:ring-2 focus:ring-blue-600/40
                                   {{ old('is_active', $user->is_active ?? true) ? 'bg-emerald-500' : 'bg-slate-300 dark:bg-slate-600' }}">
                    <span id="toggle-dot"
                        class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform
                                     {{ old('is_active', $user->is_active ?? true) ? 'translate-x-6' : 'translate-x-1' }}"></span>
                </button>
                <input type="hidden" name="is_active" id="is_active_input"
                    value="{{ old('is_active', $user->is_active ?? true) ? '1' : '0' }}">
                <label for="toggle-active" class="text-sm font-medium text-slate-700 dark:text-slate-200">
                    Compte actif
                </label>
            </div>

        </div>

    </div>

    {{-- ── Actions ── --}}
    <div class="flex items-center justify-end gap-3 mt-6">
        <a href="{{ route('users.index') }}" class="px-5 py-2.5 rounded-xl text-sm font-medium
                      text-slate-500 dark:text-slate-400
                      border border-slate-200 dark:border-slate-700
                      hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
            Annuler
        </a>
        <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl
                           bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold
                           shadow-sm hover:shadow-md transition-all duration-200
                           focus:outline-none focus:ring-2 focus:ring-blue-600/40">
            <i class="bi {{ $isEdit ? 'bi-check-lg' : 'bi-person-plus-fill' }}"></i>
            {{ $isEdit ? 'Enregistrer les modifications' : 'Créer l\'utilisateur' }}
        </button>
    </div>

</x-forms.form>

@endsection

@push('scripts')
<script>
// Prévisualisation avatar
document.getElementById('avatar')?.addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        document.getElementById('avatar-preview').src = URL.createObjectURL(file);
    }
});

// Toggle "Compte actif"
const toggleBtn = document.getElementById('toggle-active');
const toggleDot = document.getElementById('toggle-dot');
const activeInput = document.getElementById('is_active_input');

toggleBtn?.addEventListener('click', function() {
    const isActive = this.dataset.active === 'true';
    const newState = !isActive;

    this.dataset.active = newState ? 'true' : 'false';
    activeInput.value = newState ? '1' : '0';

    this.classList.toggle('bg-emerald-500', newState);
    this.classList.toggle('bg-slate-300', !newState);
    this.classList.toggle('dark:bg-slate-600', !newState);
    toggleDot.classList.toggle('translate-x-6', newState);
    toggleDot.classList.toggle('translate-x-1', !newState);
});
</script>
@endpush