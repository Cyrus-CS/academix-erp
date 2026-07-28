@extends('layouts.base')

@section('page_title', 'Paramètres')

@section('breadcrumb')
<span class="text-slate-300 dark:text-slate-600 font-light select-none">/</span>
@endsection

@section('content')

<div class="max-w-6xl mx-auto space-y-6">

    {{-- ================= Header ================= --}}
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border
                border-slate-200 dark:border-slate-700 overflow-hidden">
        <div class="px-6 py-5 bg-linear-to-r from-blue-50 to-emerald-50
                    dark:from-blue-950/30 dark:to-emerald-950/30
                    border-b border-slate-200 dark:border-slate-700
                    flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-linear-to-br from-blue-600 to-emerald-500
                            flex items-center justify-center shrink-0 shadow-md">
                    <i class="bi bi-gear-fill text-white text-xl"></i>
                </div>
                <div>
                    <h1 class="text-lg font-bold text-slate-800 dark:text-slate-100">
                        Paramètres de l'établissement
                    </h1>
                    <p class="text-sm text-slate-500 dark:text-slate-400">
                        Gérez les informations, l'apparence et les préférences régionales
                    </p>
                </div>
            </div>

            {{-- Indicateur de dernière sauvegarde --}}
            <div class="flex items-center gap-2 px-3.5 py-2 rounded-xl
                        bg-white/70 dark:bg-slate-900/40
                        border border-slate-200 dark:border-slate-700 shrink-0">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span class="text-xs text-slate-500 dark:text-slate-400">
                    Configuration active
                </span>
            </div>
        </div>
    </div>

    {{-- ================= Layout principal ================= --}}
    {{-- Remplacer la ligne <form ...> par : --}}
    <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data" id="settings-form"
        novalidate data-initial-tab="{{ 
          $errors->has('logo') || $errors->has('favicon')
              ? 'branding'
              : ($errors->has('currency') || $errors->has('language') || $errors->has('timezone') || $errors->has('academic_year_format')
                  ? 'regional'
                  : 'general')
      }}" data-has-errors="{{ $errors->any() ? 'true' : 'false' }}"
        data-success="{{ session('success') ? 'true' : 'false' }}">
        @csrf
        @method('PUT')

        <div class="flex flex-col lg:flex-row gap-6">

            {{-- ================= Sidebar navigation onglets ================= --}}
            <div class="lg:w-56 shrink-0">
                <div class="lg:sticky lg:top-22">
                    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border
                                border-slate-200 dark:border-slate-700 overflow-hidden">

                        {{-- Label section --}}
                        <p class="px-4 pt-4 pb-2 text-[10px] font-semibold uppercase tracking-widest
                                  text-slate-400 dark:text-slate-500">
                            Sections
                        </p>

                        <nav class="flex lg:flex-col overflow-x-auto lg:overflow-x-visible
                                    px-2 pb-2 gap-1" id="settings-tabs">

                            @php
                            $tabs = [
                            ['id' => 'general', 'icon' => 'bi-building', 'label' => 'Établissement', 'errors' =>
                            ['school_name','school_email','school_phone','school_address','school_motto','school_website']],
                            ['id' => 'branding', 'icon' => 'bi-palette2', 'label' => 'Logo & Apparence', 'errors' =>
                            ['logo','favicon']],
                            ['id' => 'regional', 'icon' => 'bi-globe2', 'label' => 'Langue & Région', 'errors' =>
                            ['currency','language','timezone','academic_year_format']],
                            ];
                            @endphp

                            @foreach($tabs as $tab)
                            @php
                            $hasTabError = collect($tab['errors'])->contains(fn($f) => $errors->has($f));
                            @endphp
                            <button type="button" data-tab="{{ $tab['id'] }}" class="settings-tab-btn group relative flex items-center gap-3
                                           w-full px-3.5 py-3 rounded-xl text-sm font-medium
                                           text-left transition-all duration-200 whitespace-nowrap shrink-0
                                           text-slate-600 dark:text-slate-400
                                           hover:bg-slate-50 dark:hover:bg-slate-700/50
                                           hover:text-slate-800 dark:hover:text-slate-200">
                                <i class="bi {{ $tab['icon'] }} text-base w-4 text-center shrink-0
                                          transition-colors text-slate-400
                                          group-hover:text-blue-500 dark:group-hover:text-blue-400"></i>
                                <span class="flex-1 truncate">{{ $tab['label'] }}</span>
                                @if($hasTabError)
                                <span class="w-2 h-2 rounded-full bg-red-500 shrink-0"></span>
                                @endif
                            </button>
                            @endforeach
                        </nav>

                        <div class="px-2 pb-3 pt-1 border-t border-slate-100 dark:border-slate-700 mt-1">
                            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 w-full px-3.5 py-2.5 rounded-xl text-sm
                                      text-slate-400 dark:text-slate-500
                                      hover:text-slate-600 dark:hover:text-slate-300
                                      hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                                <i class="bi bi-arrow-left text-sm w-4 text-center"></i>
                                <span>Retour</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ================= Panneaux ================= --}}
            <div class="flex-1 min-w-0 space-y-5">

                {{-- ───────────────────────────────────────────
                     PANNEAU 1 : Établissement
                ─────────────────────────────────────────── --}}
                <div id="panel-general" class="settings-panel space-y-5">

                    {{-- Infos principales --}}
                    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border
                                border-slate-200 dark:border-slate-700 overflow-hidden">
                        <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700
                                    flex items-center gap-2.5">
                            <div class="w-7 h-7 rounded-lg bg-blue-50 dark:bg-blue-900/30
                                        flex items-center justify-center">
                                <i class="bi bi-building text-blue-600 dark:text-blue-400 text-sm"></i>
                            </div>
                            <div>
                                <h2 class="text-sm font-semibold text-slate-800 dark:text-slate-100">
                                    Informations générales
                                </h2>
                                <p class="text-[11px] text-slate-400 dark:text-slate-500">
                                    Nom, contact et présentation de l'école
                                </p>
                            </div>
                        </div>

                        <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-5">

                            <div class="sm:col-span-2">
                                <x-forms.input-field name="school_name" label="Nom de l'établissement"
                                    icon="bi-mortarboard-fill" :value="$settings['school_name'] ?? ''"
                                    placeholder="Ex: Collège Saint-Jean" required />
                            </div>

                            <x-forms.input-field type="email" name="school_email" label="Adresse e-mail officielle"
                                icon="bi-envelope-fill" :value="$settings['school_email'] ?? ''"
                                placeholder="contact@ecole.com" required />

                            <x-forms.input-field type="text" name="school_phone" label="Numéro de téléphone"
                                icon="bi-telephone-fill" :value="$settings['school_phone'] ?? ''"
                                placeholder="+228 90 00 00 00" />

                            <x-forms.input-field type="url" name="school_website" label="Site web" icon="bi-globe"
                                :value="$settings['school_website'] ?? ''" placeholder="https://www.ecole.com"
                                help="Doit commencer par https://" />

                            <x-forms.input-field type="text" name="school_motto" label="Devise / Slogan" icon="bi-quote"
                                :value="$settings['school_motto'] ?? ''" placeholder="Ex: Excellence et Discipline"
                                help="Affiché sur les bulletins et communications officielles." />

                            <div class="sm:col-span-2">
                                <x-forms.textarea name="school_address" label="Adresse complète" icon="bi-geo-alt-fill"
                                    :value="$settings['school_address'] ?? ''" :rows="3"
                                    placeholder="Quartier, rue, ville, pays..." />
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ───────────────────────────────────────────
                     PANNEAU 2 : Logo & Apparence
                ─────────────────────────────────────────── --}}
                <div id="panel-branding" class="settings-panel hidden space-y-5">

                    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border
                                border-slate-200 dark:border-slate-700 overflow-hidden">
                        <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700
                                    flex items-center gap-2.5">
                            <div class="w-7 h-7 rounded-lg bg-purple-50 dark:bg-purple-900/30
                                        flex items-center justify-center">
                                <i class="bi bi-palette2 text-purple-600 dark:text-purple-400 text-sm"></i>
                            </div>
                            <div>
                                <h2 class="text-sm font-semibold text-slate-800 dark:text-slate-100">
                                    Logo & identité visuelle
                                </h2>
                                <p class="text-[11px] text-slate-400 dark:text-slate-500">
                                    Apparaît sur les bulletins, reçus et l'écran de connexion
                                </p>
                            </div>
                        </div>

                        <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-8">

                            {{-- Logo --}}
                            <div>
                                <label class="flex items-center gap-1.5 text-sm font-medium
                                              text-slate-700 dark:text-slate-200 mb-3">
                                    <i class="bi bi-image text-slate-400"></i>
                                    Logo de l'établissement
                                </label>

                                {{-- Zone de drop --}}
                                <div id="logo-dropzone" class="relative flex flex-col items-center justify-center gap-3
                                            w-full h-44 rounded-2xl cursor-pointer
                                            border-2 border-dashed border-slate-300 dark:border-slate-600
                                            bg-slate-50 dark:bg-slate-900/40
                                            hover:border-blue-400 dark:hover:border-blue-500
                                            hover:bg-blue-50/50 dark:hover:bg-blue-900/10
                                            transition-all duration-200 overflow-hidden" data-dropzone="logo">

                                    {{-- Preview --}}
                                    @if(!empty($settings['logo']))
                                    <img id="logo-preview" src="{{ asset('storage/' . $settings['logo']) }}"
                                        alt="Logo actuel" class="absolute inset-0 w-full h-full object-contain p-4" />
                                    <div
                                        class="absolute inset-0 flex items-center justify-center
                                                bg-slate-900/0 hover:bg-slate-900/40 transition-all duration-200 group">
                                        <span class="opacity-0 group-hover:opacity-100 text-white text-xs font-medium
                                                     flex items-center gap-1.5 transition-opacity">
                                            <i class="bi bi-pencil-fill"></i> Modifier
                                        </span>
                                    </div>
                                    @else
                                    <img id="logo-preview" src="" alt=""
                                        class="hidden absolute inset-0 w-full h-full object-contain p-4" />
                                    <i class="bi bi-cloud-upload text-3xl text-slate-300 dark:text-slate-600"
                                        id="logo-upload-icon"></i>
                                    <div class="text-center" id="logo-upload-text">
                                        <p class="text-xs font-medium text-slate-500 dark:text-slate-400">
                                            Glissez votre logo ici
                                        </p>
                                        <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-0.5">
                                            ou cliquez pour parcourir
                                        </p>
                                    </div>
                                    @endif

                                    <input type="file" name="logo" id="logo-input" accept=".jpg,.jpeg,.png,.svg,.webp"
                                        class="absolute inset-0 opacity-0 cursor-pointer w-full h-full"
                                        data-preview="logo-preview" data-icon="logo-upload-icon"
                                        data-text="logo-upload-text" />
                                </div>

                                <div class="mt-2.5 flex items-center justify-between">
                                    <p class="text-[11px] text-slate-400 dark:text-slate-500">
                                        JPG, PNG, SVG ou WEBP · Max 2 Mo
                                    </p>
                                    @if(!empty($settings['logo']))
                                    <span class="inline-flex items-center gap-1 text-[11px]
                                                 text-emerald-600 dark:text-emerald-400 font-medium">
                                        <i class="bi bi-check-circle-fill text-xs"></i>
                                        Logo actif
                                    </span>
                                    @endif
                                </div>
                                <x-forms.error name="logo" />
                            </div>

                            {{-- Favicon --}}
                            <div>
                                <label class="flex items-center gap-1.5 text-sm font-medium
                                              text-slate-700 dark:text-slate-200 mb-3">
                                    <i class="bi bi-app text-slate-400"></i>
                                    Favicon (onglet navigateur)
                                </label>

                                <div id="favicon-dropzone" class="relative flex flex-col items-center justify-center gap-3
                                            w-full h-44 rounded-2xl cursor-pointer
                                            border-2 border-dashed border-slate-300 dark:border-slate-600
                                            bg-slate-50 dark:bg-slate-900/40
                                            hover:border-blue-400 dark:hover:border-blue-500
                                            hover:bg-blue-50/50 dark:hover:bg-blue-900/10
                                            transition-all duration-200 overflow-hidden" data-dropzone="favicon">

                                    @if(!empty($settings['favicon']))
                                    <img id="favicon-preview" src="{{ asset('storage/' . $settings['favicon']) }}"
                                        alt="Favicon actuel"
                                        class="absolute inset-0 w-full h-full object-contain p-8" />
                                    <div
                                        class="absolute inset-0 flex items-center justify-center
                                                bg-slate-900/0 hover:bg-slate-900/40 transition-all duration-200 group">
                                        <span class="opacity-0 group-hover:opacity-100 text-white text-xs font-medium
                                                     flex items-center gap-1.5 transition-opacity">
                                            <i class="bi bi-pencil-fill"></i> Modifier
                                        </span>
                                    </div>
                                    @else
                                    <img id="favicon-preview" src="" alt=""
                                        class="hidden absolute inset-0 w-full h-full object-contain p-8" />
                                    <i class="bi bi-cloud-upload text-3xl text-slate-300 dark:text-slate-600"
                                        id="favicon-upload-icon"></i>
                                    <div class="text-center" id="favicon-upload-text">
                                        <p class="text-xs font-medium text-slate-500 dark:text-slate-400">
                                            Glissez votre favicon ici
                                        </p>
                                        <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-0.5">
                                            ou cliquez pour parcourir
                                        </p>
                                    </div>
                                    @endif

                                    <input type="file" name="favicon" id="favicon-input" accept=".ico,.png,.svg"
                                        class="absolute inset-0 opacity-0 cursor-pointer w-full h-full"
                                        data-preview="favicon-preview" data-icon="favicon-upload-icon"
                                        data-text="favicon-upload-text" />
                                </div>

                                <div class="mt-2.5 flex items-center justify-between">
                                    <p class="text-[11px] text-slate-400 dark:text-slate-500">
                                        ICO, PNG ou SVG · Max 512 Ko
                                    </p>
                                    @if(!empty($settings['favicon']))
                                    <span class="inline-flex items-center gap-1 text-[11px]
                                                 text-emerald-600 dark:text-emerald-400 font-medium">
                                        <i class="bi bi-check-circle-fill text-xs"></i>
                                        Favicon actif
                                    </span>
                                    @endif
                                </div>
                                <x-forms.error name="favicon" />
                            </div>
                        </div>

                        {{-- Bandeau info --}}
                        <div class="mx-6 mb-6 flex items-start gap-3 px-4 py-3.5 rounded-xl
                                    bg-cyan-50 dark:bg-cyan-900/20
                                    border border-cyan-200 dark:border-cyan-800">
                            <i class="bi bi-info-circle-fill text-cyan-500 shrink-0 mt-0.5 text-sm"></i>
                            <p class="text-xs text-cyan-700 dark:text-cyan-400 leading-relaxed">
                                Le logo apparaît sur les <strong>bulletins PDF</strong>, les <strong>reçus de
                                    paiement</strong>
                                et la <strong>page de connexion</strong>.
                                Recommandation : format carré <strong>512×512 px</strong> en fond transparent.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- ───────────────────────────────────────────
                     PANNEAU 3 : Langue & Région
                ─────────────────────────────────────────── --}}
                <div id="panel-regional" class="settings-panel hidden space-y-5">

                    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border
                                border-slate-200 dark:border-slate-700 overflow-hidden">
                        <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700
                                    flex items-center gap-2.5">
                            <div class="w-7 h-7 rounded-lg bg-emerald-50 dark:bg-emerald-900/30
                                        flex items-center justify-center">
                                <i class="bi bi-globe2 text-emerald-600 dark:text-emerald-400 text-sm"></i>
                            </div>
                            <div>
                                <h2 class="text-sm font-semibold text-slate-800 dark:text-slate-100">
                                    Préférences régionales
                                </h2>
                                <p class="text-[11px] text-slate-400 dark:text-slate-500">
                                    Devise, langue, fuseau horaire et format des dates
                                </p>
                            </div>
                        </div>

                        <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-5">

                            <x-forms.select name="currency" label="Devise" icon="bi-currency-exchange"
                                :value="$settings['currency'] ?? 'FCFA'" :options="[
                                    'FCFA' => 'FCFA — Franc CFA',
                                    'USD'  => 'USD — Dollar américain',
                                    'EUR'  => 'EUR — Euro',
                                    'GBP'  => 'GBP — Livre sterling',
                                ]" required />

                            <x-forms.select name="language" label="Langue de l'interface" icon="bi-translate"
                                :value="$settings['language'] ?? 'fr'" :options="[
                                    'fr' => '🇫🇷 Français',
                                    'en' => '🇬🇧 English',
                                ]" required />

                            <div class="sm:col-span-2">
                                <x-forms.select name="timezone" label="Fuseau horaire" icon="bi-clock-fill"
                                    :value="$settings['timezone'] ?? 'Africa/Lome'" :options="[
                                        'Africa/Lome'        => '🌍 Lomé, Togo (GMT+0)',
                                        'Africa/Abidjan'     => '🌍 Abidjan, Côte d\'Ivoire (GMT+0)',
                                        'Africa/Dakar'       => '🌍 Dakar, Sénégal (GMT+0)',
                                        'Africa/Bamako'      => '🌍 Bamako, Mali (GMT+0)',
                                        'Africa/Ouagadougou' => '🌍 Ouagadougou, Burkina Faso (GMT+0)',
                                        'Africa/Porto-Novo'  => '🌍 Porto-Novo, Bénin (GMT+1)',
                                        'Africa/Lagos'       => '🌍 Lagos, Nigeria (GMT+1)',
                                        'Africa/Kinshasa'    => '🌍 Kinshasa, RDC (GMT+1)',
                                        'Africa/Douala'      => '🌍 Douala, Cameroun (GMT+1)',
                                        'Africa/Nairobi'     => '🌍 Nairobi, Kenya (GMT+3)',
                                        'Africa/Accra'       => '🌍 Accra, Ghana (GMT+0)',
                                        'Europe/Paris'       => '🇫🇷 Paris, France (GMT+1/+2)',
                                        'Europe/London'      => '🇬🇧 Londres, Royaume-Uni (GMT+0/+1)',
                                        'America/New_York'   => '🇺🇸 New York, USA (GMT-5/-4)',
                                        'UTC'                => '🌐 UTC (GMT+0)',
                                    ]" required />
                            </div>

                            <div class="sm:col-span-2">
                                <x-forms.input-field name="academic_year_format"
                                    label="Format d'affichage de l'année académique" icon="bi-calendar3"
                                    :value="$settings['academic_year_format'] ?? 'YYYY – YYYY'"
                                    placeholder="Ex: YYYY – YYYY"
                                    help="Exemple : 2024 – 2025. Utilisé dans les bulletins et les en-têtes." />
                            </div>
                        </div>
                    </div>

                    {{-- Aperçu régional en temps réel --}}
                    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border
                                border-slate-200 dark:border-slate-700 overflow-hidden">
                        <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700
                                    flex items-center gap-2">
                            <i class="bi bi-eye text-blue-600 dark:text-blue-400 text-sm"></i>
                            <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-100">
                                Aperçu du format
                            </h3>
                        </div>
                        <div class="p-5 grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-900/50
                                        border border-slate-200 dark:border-slate-700 text-center">
                                <p class="text-[10px] text-slate-400 uppercase tracking-wide mb-1.5">Devise</p>
                                <p class="text-sm font-bold text-slate-700 dark:text-slate-200" id="preview-currency">
                                    15 000 {{ $settings['currency'] ?? 'FCFA' }}
                                </p>
                            </div>
                            <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-900/50
                                        border border-slate-200 dark:border-slate-700 text-center">
                                <p class="text-[10px] text-slate-400 uppercase tracking-wide mb-1.5">Heure locale</p>
                                <p class="text-sm font-bold text-slate-700 dark:text-slate-200" id="preview-time">
                                    {{ now()->setTimezone($settings['timezone'] ?? 'UTC')->format('H:i') }}
                                </p>
                            </div>
                            <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-900/50
                                        border border-slate-200 dark:border-slate-700 text-center">
                                <p class="text-[10px] text-slate-400 uppercase tracking-wide mb-1.5">Année académique
                                </p>
                                <p class="text-sm font-bold text-slate-700 dark:text-slate-200" id="preview-year">
                                    {{ now()->format('Y') }} – {{ now()->addYear()->format('Y') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ================= Barre d'actions fixe ================= --}}
                <div class="sticky bottom-0 z-20">
                    <div class="bg-white/95 dark:bg-slate-800/95 backdrop-blur-md rounded-2xl shadow-lg border
                                border-slate-200 dark:border-slate-700 px-5 py-4
                                flex flex-col sm:flex-row items-center justify-between gap-3">

                        <div class="flex items-center gap-2 text-xs text-slate-400 dark:text-slate-500">
                            <i class="bi bi-shield-lock-fill text-emerald-500"></i>
                            <span>Les modifications sont appliquées immédiatement.</span>
                        </div>

                        <div class="flex items-center gap-3 w-full sm:w-auto">
                            <a href="{{ route('dashboard') }}" class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2
                                      px-4 py-2.5 rounded-xl text-sm font-medium
                                      text-slate-600 dark:text-slate-300
                                      bg-white dark:bg-slate-700
                                      border border-slate-200 dark:border-slate-600
                                      hover:bg-slate-50 dark:hover:bg-slate-600 transition-colors">
                                <i class="bi bi-x-lg text-xs"></i>
                                Annuler
                            </a>

                            <button type="submit" id="save-btn" class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2
                                           px-5 py-2.5 rounded-xl text-sm font-medium text-white
                                           bg-blue-600 hover:bg-blue-700
                                           shadow-sm shadow-blue-600/25
                                           transition-colors disabled:opacity-60 disabled:cursor-not-allowed">
                                <i class="bi bi-check-circle-fill" id="save-icon"></i>
                                <span id="save-label">Enregistrer les paramètres</span>
                            </button>
                        </div>
                    </div>
                </div>

            </div>{{-- fin panneaux --}}
        </div>{{-- fin layout flex --}}
    </form>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {

    // ══════════════════════════════════════════════════
    // 1. GESTION DES ONGLETS
    // ══════════════════════════════════════════════════
    const tabBtns = document.querySelectorAll('.settings-tab-btn');
    const panels = document.querySelectorAll('.settings-panel');
    const form = document.getElementById('settings-form');

    const activeClasses = ['bg-blue-600', 'text-white', 'shadow-sm'];
    const inactiveClasses = [
        'text-slate-600', 'dark:text-slate-400',
        'hover:bg-slate-50', 'dark:hover:bg-slate-700/50'
    ];

    function activateTab(tabId) {
        tabBtns.forEach(function(btn) {
            const isActive = btn.dataset.tab === tabId;
            activeClasses.forEach(c => btn.classList.toggle(c, isActive));
            inactiveClasses.forEach(c => btn.classList.toggle(c, !isActive));

            const icon = btn.querySelector('i.bi');
            if (icon) {
                icon.classList.toggle('text-white', isActive);
                icon.classList.toggle('text-slate-400', !isActive);
            }
        });

        panels.forEach(function(panel) {
            panel.classList.toggle('hidden', panel.id !== 'panel-' + tabId);
        });

        // Persister dans l'URL sans rechargement
        const url = new URL(window.location.href);
        url.searchParams.set('tab', tabId);
        window.history.replaceState(null, '', url.toString());
    }

    // Attacher les événements
    tabBtns.forEach(function(btn) {
        btn.addEventListener('click', function() {
            activateTab(btn.dataset.tab);
        });
    });

    // ── Déterminer l'onglet initial (data-* depuis Blade) ──────────
    const hasErrors = form?.dataset.hasErrors === 'true';
    const initialTab = form?.dataset.initialTab ?? 'general';
    const urlTab = new URLSearchParams(window.location.search).get('tab');

    if (hasErrors) {
        // Priorité aux erreurs de validation
        activateTab(initialTab);
    } else if (urlTab && ['general', 'branding', 'regional'].includes(urlTab)) {
        // Sinon : restaurer depuis l'URL
        activateTab(urlTab);
    } else {
        activateTab('general');
    }


    // ══════════════════════════════════════════════════
    // 2. PRÉVISUALISATION DES IMAGES (Drag & Drop + Click)
    // ══════════════════════════════════════════════════
    const maxSizes = {
        'logo-input': 2 * 1024 * 1024, // 2 Mo
        'favicon-input': 512 * 1024, // 512 Ko
    };

    const fileInputs = document.querySelectorAll('input[type="file"][data-preview]');

    fileInputs.forEach(function(input) {

        // ── Sélection via clic ──────────────────────────────────────
        input.addEventListener('change', function() {
            handleFile(input, input.files[0]);
        });

        // ── Drag & Drop ─────────────────────────────────────────────
        const dropzone = input.closest('[data-dropzone]');
        if (!dropzone) return;

        ['dragenter', 'dragover'].forEach(function(evt) {
            dropzone.addEventListener(evt, function(e) {
                e.preventDefault();
                dropzone.classList.add(
                    'border-blue-500',
                    'bg-blue-50',
                    'dark:bg-blue-900/10'
                );
            });
        });

        ['dragleave', 'drop'].forEach(function(evt) {
            dropzone.addEventListener(evt, function(e) {
                e.preventDefault();
                dropzone.classList.remove(
                    'border-blue-500',
                    'bg-blue-50',
                    'dark:bg-blue-900/10'
                );
            });
        });

        dropzone.addEventListener('drop', function(e) {
            e.preventDefault();
            const file = e.dataTransfer && e.dataTransfer.files[0];
            if (!file) return;

            // Assigner au input pour que le formulaire envoie le fichier
            const dt = new DataTransfer();
            dt.items.add(file);
            input.files = dt.files;

            handleFile(input, file);
        });
    });

    function handleFile(input, file) {
        if (!file) return;

        const max = maxSizes[input.id] ?? (2 * 1024 * 1024);
        const maxLabel = max >= 1024 * 1024 ?
            (max / (1024 * 1024)) + ' Mo' :
            (max / 1024) + ' Ko';

        // Validation taille
        if (file.size > max) {
            window.showToast({
                type: 'error',
                title: 'Fichier trop volumineux',
                message: 'La taille maximum autorisée est de ' + maxLabel + '.'
            });
            input.value = '';
            return;
        }

        // Validation type
        if (!file.type.startsWith('image/')) {
            window.showToast({
                type: 'error',
                title: 'Format invalide',
                message: 'Veuillez choisir un fichier image valide.'
            });
            input.value = '';
            return;
        }

        // Lecture et prévisualisation
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById(input.dataset.preview);
            const icon = document.getElementById(input.dataset.icon);
            const text = document.getElementById(input.dataset.text);

            if (preview) {
                preview.src = e.target.result;
                preview.classList.remove('hidden');
            }
            icon && icon.classList.add('hidden');
            text && text.classList.add('hidden');

            window.showToast({
                type: 'info',
                title: 'Image sélectionnée',
                message: '"' + file.name + '" prête à être enregistrée.'
            });
        };
        reader.readAsDataURL(file);
    }


    // ══════════════════════════════════════════════════
    // 3. APERÇU EN TEMPS RÉEL — Devise
    // ══════════════════════════════════════════════════
    const currencySelect = document.querySelector('select[name="currency"]');
    const previewCurrency = document.getElementById('preview-currency');

    if (currencySelect && previewCurrency) {
        currencySelect.addEventListener('change', function() {
            previewCurrency.textContent = '15 000 ' + this.value;
        });
    }


    // ══════════════════════════════════════════════════
    // 4. BOUTON SUBMIT — État loading
    // ══════════════════════════════════════════════════
    const saveBtn = document.getElementById('save-btn');
    const saveIcon = document.getElementById('save-icon');
    const saveLabel = document.getElementById('save-label');

    form && form.addEventListener('submit', function() {
        if (!saveBtn) return;
        saveBtn.disabled = true;
        saveIcon.className = 'bi bi-arrow-repeat animate-spin';
        saveLabel.textContent = 'Enregistrement…';
    });


    // ══════════════════════════════════════════════════
    // 5. GARDE NAVIGATION — Avertir si changements non sauvegardés
    // ══════════════════════════════════════════════════
    let formChanged = false;

    if (form) {
        form.querySelectorAll('input, select, textarea').forEach(function(el) {
            el.addEventListener('change', function() {
                formChanged = true;
            });
            el.addEventListener('input', function() {
                formChanged = true;
            });
        });

        form.addEventListener('submit', function() {
            formChanged = false;
        });
    }

    window.addEventListener('beforeunload', function(e) {
        if (formChanged) {
            e.preventDefault();
            e.returnValue = '';
        }
    });

});
</script>
@endpush