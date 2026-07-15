@extends('layouts.base')

@section('title', 'Élèves')
@section('page_title', $student->exists ? "Modifier l'élève" : 'Nouvel élève')

@section('breadcrumb')
<span class="text-slate-300 dark:text-slate-600">/</span>
<a href="{{ route('students.index') }}"
    class="text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
    Élèves
</a>
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    {{-- ── En-tête ── --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-800 dark:text-slate-100">
                {{ $student->exists ? "Modifier un élève" : "Inscrire un nouvel élève" }}
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                {{ $student->exists ? 'Modifiez les informations de cet élève.' : 'Remplissez tous les champs obligatoires.' }}
                <span class="text-red-500">*</span>
            </p>
        </div>
        <a href="{{ route('students.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-medium
                  border border-slate-200 dark:border-slate-700
                  text-slate-600 dark:text-slate-400
                  hover:bg-slate-100 dark:hover:bg-slate-800 transition-all">
            <i class="bi bi-arrow-left"></i>
            Retour
        </a>
    </div>

    {{-- ── Formulaire ── --}}
    <x-forms.form :model="$student" resource="students" enctype="multipart/form-data" id="student-form">

        {{-- ════════════════════════════════════
             SECTION 1 : Compte utilisateur
        ════════════════════════════════════ --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl
                    border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">

            {{-- Header section --}}
            <div class="flex items-center gap-3 px-6 py-4
                        border-b border-slate-100 dark:border-slate-700
                        bg-slate-50/50 dark:bg-slate-700/20">
                <div class="w-8 h-8 rounded-lg bg-blue-100 dark:bg-blue-900/40
                            flex items-center justify-center">
                    <i class="bi bi-person-fill text-blue-600 dark:text-blue-400 text-sm"></i>
                </div>
                <div>
                    <h2 class="text-sm font-semibold text-slate-800 dark:text-slate-100">
                        Informations du compte
                    </h2>
                    <p class="text-xs text-slate-400 dark:text-slate-500">
                        Identifiants de connexion de l'élève
                    </p>
                </div>
            </div>

            <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-5">
                <x-forms.input-field name="name" label="Nom complet" placeholder="Ex : Kofi Mensah" icon="bi-person"
                    :required="true" class="sm:col-span-2" :value="$student->user?->name" />

                <x-forms.input-field type="email" name="email" label="Adresse email" placeholder="eleve@school.edu"
                    icon="bi-envelope" :required="true" :value="$student->user?->email" />

                <x-forms.input-field type="tel" name="phone" label="Téléphone" placeholder="+228 90 10 70 30"
                    icon="bi-telephone" help="Numéro de l'élève ou du tuteur" :value="$student->user?->phone" />

                @if(!$student->exists)
                <x-forms.input-field type="password" name="password" label="Mot de passe"
                    placeholder="Minimum 8 caractères" icon="bi-lock" :required="true" />

                <x-forms.input-field type="password" name="password_confirmation" label="Confirmer le mot de passe"
                    placeholder="Répéter le mot de passe" icon="bi-lock-fill" :required="true" />
                @endif

            </div>
        </div>

        {{-- ════════════════════════════════════
             SECTION 2 : Informations personnelles
        ════════════════════════════════════ --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl
                    border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">

            <div class="flex items-center gap-3 px-6 py-4
                        border-b border-slate-100 dark:border-slate-700
                        bg-slate-50/50 dark:bg-slate-700/20">
                <div class="w-8 h-8 rounded-lg bg-emerald-100 dark:bg-emerald-900/40
                            flex items-center justify-center">
                    <i class="bi bi-card-list text-emerald-600 dark:text-emerald-400 text-sm"></i>
                </div>
                <div>
                    <h2 class="text-sm font-semibold text-slate-800 dark:text-slate-100">
                        Informations personnelles
                    </h2>
                    <p class="text-xs text-slate-400 dark:text-slate-500">
                        Données civiles de l'élève
                    </p>
                </div>
            </div>

            <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-5">
                <x-forms.input-field type="date" name="birth_date" label="Date de naissance" icon="bi-calendar3"
                    :required="true" data-datepicker='{"maxDate": "today"}' :value="$student->birth_date" />

                <x-forms.select name="gender" label="Genre" icon="bi-gender-ambiguous" :required="true"
                    :options="['male' => 'Masculin', 'female' => 'Féminin']" placeholder="Sélectionner le genre"
                    :value="$student->gender" />

                <x-forms.textarea name="address" label="Adresse" placeholder="Quartier, ville..." icon="bi-geo-alt"
                    :rows="3" wrapperClass="sm:col-span-2" :value="$student->gender" />
            </div>
        </div>

        {{-- ════════════════════════════════════
             SECTION 3 : Scolarité
        ════════════════════════════════════ --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl
                    border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">

            <div class="flex items-center gap-3 px-6 py-4
                        border-b border-slate-100 dark:border-slate-700
                        bg-slate-50/50 dark:bg-slate-700/20">
                <div class="w-8 h-8 rounded-lg bg-violet-100 dark:bg-violet-900/40
                            flex items-center justify-center">
                    <i class="bi bi-mortarboard-fill text-violet-600 dark:text-violet-400 text-sm"></i>
                </div>
                <div>
                    <h2 class="text-sm font-semibold text-slate-800 dark:text-slate-100">
                        Scolarité
                    </h2>
                    <p class="text-xs text-slate-400 dark:text-slate-500">
                        Affectation académique de l'élève
                    </p>
                </div>
            </div>

            <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-5">
                <x-forms.select name="academic_year_id" label="Année académique" icon="bi-calendar-range"
                    :required="true" :options="$academicYears" optionValue="id" optionLabel="name"
                    :value="$activeYear?->id" placeholder="Sélectionner l'année" />

                <x-forms.select name="class_id" label="Classe" icon="bi-building" :required="true" :options="$classes"
                    optionValue="id" optionLabel="name" placeholder="Sélectionner la classe"
                    :value="$student->class_id" />

                <x-forms.input-field type="tel" name="guardian_phone" label="Téléphone du Parent / Tuteur"
                    placeholder="+228 90 10 70 30" icon="bi-telephone" help="Numéro du parent ou du tuteur"
                    :value="$student->guardian_phone" />

                <x-forms.input-field name="guardian_name" label="Nom et Prenom de votre Parent / Tuteur"
                    icon="bi-person-heart" placeholder="Comment s'appelle votre parent" wrapperClass="sm:col-span-2"
                    help="Vous pouvez aussi donner le nom du tuteur." :value="$student->guardian_name" />
            </div>
        </div>

        {{-- ════════════════════════════════════
             SECTION 4 : Photo
        ════════════════════════════════════ --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl
                    border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">

            <div class="flex items-center gap-3 px-6 py-4
                        border-b border-slate-100 dark:border-slate-700
                        bg-slate-50/50 dark:bg-slate-700/20">
                <div class="w-8 h-8 rounded-lg bg-amber-100 dark:bg-amber-900/40
                            flex items-center justify-center">
                    <i class="bi bi-image-fill text-amber-600 dark:text-amber-400 text-sm"></i>
                </div>
                <div>
                    <h2 class="text-sm font-semibold text-slate-800 dark:text-slate-100">
                        Photo de profil
                    </h2>
                    <p class="text-xs text-slate-400 dark:text-slate-500">
                        JPG, PNG ou WEBP | Max 5 Mo
                    </p>
                </div>
            </div>

            <div class="p-6">
                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-5">

                    {{-- Preview --}}
                    <div class="shrink-0">
                        <div id="photo-preview" class="w-24 h-24 rounded-2xl bg-slate-100 dark:bg-slate-700
                                    border-2 border-dashed border-slate-300 dark:border-slate-600
                                    flex items-center justify-center overflow-hidden">
                            @if($student->exists && $student->photo)
                            <img id="photo-img" src="{{ Storage::url($student->photo) }}" alt="Preview"
                                class="hidden w-full h-full object-cover" />
                            @else
                            <i class="bi bi-person text-4xl text-slate-300 dark:text-slate-500"
                                id="photo-placeholder"></i>
                            @endif
                        </div>
                    </div>

                    {{-- Input --}}
                    <div class="flex-1 w-full">
                        <label for="photo" class="flex flex-col items-center justify-center w-full h-28
                                      border-2 border-dashed rounded-xl cursor-pointer
                                      border-slate-300 dark:border-slate-600
                                      hover:border-blue-400 dark:hover:border-blue-500
                                      bg-slate-50 dark:bg-slate-800/50
                                      hover:bg-blue-50/50 dark:hover:bg-blue-950/20
                                      transition-all duration-200">
                            <div class="flex flex-col items-center justify-center gap-1.5">
                                <i class="bi bi-cloud-upload text-2xl
                                          text-slate-400 dark:text-slate-500"></i>
                                <p class="text-sm font-medium text-slate-600 dark:text-slate-400">
                                    Cliquez pour choisir une photo
                                </p>
                                <p class="text-xs text-slate-400 dark:text-slate-500">
                                    ou glissez-déposez ici
                                </p>
                            </div>
                            <input id="photo" name="photo" type="file" accept="image/jpeg,image/png,image/webp"
                                class="hidden" />
                        </label>
                        @error('photo')
                        <p class="mt-1.5 text-xs text-red-600 dark:text-red-400 flex items-center gap-1">
                            <i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
                        </p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- ════════════════════════════════════
             ACTIONS
        ════════════════════════════════════ --}}
        <div class="flex flex-col-reverse sm:flex-row items-center justify-end gap-3
                    bg-white dark:bg-slate-800 rounded-2xl
                    border border-slate-200 dark:border-slate-700
                    shadow-sm px-6 py-4">

            <a href="{{ route('students.index') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2
                      px-5 py-2.5 rounded-xl text-sm font-medium
                      border border-slate-200 dark:border-slate-700
                      text-slate-600 dark:text-slate-400
                      hover:bg-slate-100 dark:hover:bg-slate-700
                      transition-all duration-200">
                <i class="bi bi-x-lg"></i>
                Annuler
            </a>

            <button type="submit" id="submit-btn" class="w-full sm:w-auto inline-flex items-center justify-center gap-2
                           px-6 py-2.5 rounded-xl text-sm font-semibold
                           bg-blue-600 hover:bg-blue-700 text-white
                           transition-all duration-200 shadow-sm shadow-blue-500/30
                           disabled:opacity-60 disabled:cursor-not-allowed" data-update-label="Mise à jour..."
                data-create-label="Enregistrement...">
                <i class="bi bi-person-check-fill"></i>
                <span id="submit-label">{{ $student->exists ? "Mettre à jour" : "Inscrire l'élève" }}</span>
                <svg id="submit-spinner" class="hidden animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                </svg>
            </button>
        </div>

    </x-forms.form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {

    // ── Preview photo ──────────────────────────────────────────
    const photoInput = document.getElementById('photo');
    const photoImg = document.getElementById('photo-img');
    const photoPlaceholder = document.getElementById('photo-placeholder');

    photoInput?.addEventListener('change', function() {
        const file = this.files?. [0];
        if (!file) return;

        const reader = new window.FileReader();
        reader.onload = (e) => {
            photoImg.src = e.target.result;
            photoImg.classList.remove('hidden');
            photoPlaceholder.classList.add('hidden');
        };
        reader.readAsDataURL(file);
    });

    // ── Spinner au submit ─────────────────────────────────────
    const form = document.getElementById('student-form');
    const btn = document.getElementById('submit-btn');
    const label = document.getElementById('submit-label');
    const spinner = document.getElementById('submit-spinner');

    form.addEventListener('submit', () => {

        btn.disabled = true;

        const mode = form.dataset.mode;

        label.textContent = mode === 'edit' ?
            btn.dataset.updateLabel :
            btn.dataset.createLabel;
        spinner.classList.remove('hidden');

    });
});
</script>
@endpush