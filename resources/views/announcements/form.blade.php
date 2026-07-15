@extends('layouts.base')

@section('page_title', $announcement->exists ? 'Modifier l\'annonce' : 'Nouvelle annonce')

@section('breadcrumb')
<span class="text-slate-300 dark:text-slate-600 font-light select-none">/</span>
<a href="{{ route('announcements.index') }}"
    class="text-slate-400 dark:text-slate-500 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
    Annonces
</a>
@endsection

@section('page_header')
<div class="flex items-center justify-between gap-4">
    <div class="flex items-center gap-3 min-w-0">
        <div class="w-10 h-10 rounded-xl bg-linear-to-br from-blue-600 to-emerald-500
                        flex items-center justify-center shadow-sm shrink-0">
            <i class="bi bi-megaphone-fill text-white text-lg"></i>
        </div>
        <div class="min-w-0">
            <h1 class="text-lg font-bold text-slate-800 dark:text-slate-100 truncate">
                {{ $announcement->exists ? 'Modifier : ' . $announcement->title : 'Nouvelle annonce' }}
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400">
                {{ $announcement->exists
                        ? 'Modifiez les informations de cette annonce'
                        : 'Publiez une nouvelle annonce pour votre audience' }}
            </p>
        </div>
    </div>
    <a href="{{ route('announcements.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-medium
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
<x-forms.form :model="$announcement" resource="announcements" class="space-y-6 max-w-4xl mx-auto">

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ── Colonne principale ── --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Contenu --}}
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200
                        dark:border-slate-700 shadow-sm overflow-hidden">

                <div class="flex items-center gap-3 px-6 py-4
                            border-b border-slate-100 dark:border-slate-700
                            bg-slate-50/60 dark:bg-slate-700/30">
                    <div class="w-7 h-7 rounded-lg bg-blue-100 dark:bg-blue-900/40
                                flex items-center justify-center">
                        <i class="bi bi-file-text-fill text-blue-600 dark:text-blue-400 text-sm"></i>
                    </div>
                    <h2 class="text-sm font-semibold text-slate-700 dark:text-slate-200">
                        Contenu de l'annonce
                    </h2>
                </div>

                <div class="p-6 space-y-5">

                    {{-- Titre --}}
                    <div class="space-y-1.5">
                        <label for="title" class="flex items-center gap-1.5 text-sm font-medium
                                      text-slate-700 dark:text-slate-200">
                            <i class="bi bi-type-h1 text-slate-400"></i>
                            Titre
                            <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="title" id="title" value="{{ old('title', $announcement->title) }}"
                            placeholder="Titre de l'annonce…" maxlength="200"
                            class="w-full px-3.5 py-2.5 rounded-xl border text-sm
                                      text-slate-800 dark:text-slate-100
                                      bg-white dark:bg-slate-700/50 placeholder-slate-400
                                      focus:outline-none focus:ring-2 transition-all duration-200
                                      {{ $errors->has('title')
                                          ? 'border-red-500 focus:ring-red-500/40'
                                          : 'border-slate-200 dark:border-slate-600 focus:ring-blue-500/30 focus:border-blue-500' }}" required>
                        <div class="flex items-center justify-between">
                            @error('title')
                            <p class="text-xs text-red-600 dark:text-red-400 flex items-center gap-1">
                                <i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
                            </p>
                            @else
                            <span></span>
                            @enderror
                            <span id="title-count" class="text-xs text-slate-400 dark:text-slate-500">
                                0 / 200
                            </span>
                        </div>
                    </div>

                    {{-- Contenu --}}
                    <div class="space-y-1.5">
                        <label for="content" class="flex items-center gap-1.5 text-sm font-medium
                                      text-slate-700 dark:text-slate-200">
                            <i class="bi bi-text-paragraph text-slate-400"></i>
                            Contenu
                            <span class="text-red-500">*</span>
                        </label>
                        <textarea name="content" id="content" rows="8" placeholder="Rédigez votre annonce ici…"
                            class="w-full px-3.5 py-2.5 rounded-xl border text-sm
                                         text-slate-800 dark:text-slate-100
                                         bg-white dark:bg-slate-700/50 placeholder-slate-400
                                         focus:outline-none focus:ring-2 transition-all duration-200 resize-y
                                         {{ $errors->has('content')
                                             ? 'border-red-500 focus:ring-red-500/40'
                                             : 'border-slate-200 dark:border-slate-600 focus:ring-blue-500/30 focus:border-blue-500' }}" required>{{ old('content', $announcement->content) }}</textarea>
                        @error('content')
                        <p class="text-xs text-red-600 dark:text-red-400 flex items-center gap-1">
                            <i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
                        </p>
                        @enderror
                    </div>

                </div>
            </div>
        </div>

        {{-- ── Colonne latérale ── --}}
        <div class="space-y-6">

            {{-- Paramètres --}}
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200
                        dark:border-slate-700 shadow-sm overflow-hidden">

                <div class="flex items-center gap-3 px-5 py-4
                            border-b border-slate-100 dark:border-slate-700
                            bg-slate-50/60 dark:bg-slate-700/30">
                    <div class="w-7 h-7 rounded-lg bg-emerald-100 dark:bg-emerald-900/40
                                flex items-center justify-center">
                        <i class="bi bi-gear-fill text-emerald-600 dark:text-emerald-400 text-sm"></i>
                    </div>
                    <h2 class="text-sm font-semibold text-slate-700 dark:text-slate-200">
                        Paramètres
                    </h2>
                </div>

                <div class="p-5 space-y-5">

                    {{-- Audience --}}
                    <div class="space-y-2">
                        <label class="flex items-center gap-1.5 text-sm font-medium
                                      text-slate-700 dark:text-slate-200">
                            <i class="bi bi-people text-slate-400"></i>
                            Audience
                            <span class="text-red-500">*</span>
                        </label>
                        <div class="space-y-2">
                            @foreach($audiences as $value => $label)
                            @php
                            $icons = [
                            'all' => ['icon' => 'bi-globe', 'color' => 'blue'],
                            'teachers' => ['icon' => 'bi-person-badge', 'color' => 'emerald'],
                            'students' => ['icon' => 'bi-mortarboard', 'color' => 'amber'],
                            'parents' => ['icon' => 'bi-people-fill', 'color' => 'cyan'],
                            ];
                            $cfg = $icons[$value];
                            $isSelected = old('audience', $announcement->audience) === $value;
                            @endphp
                            <label class="audience-option flex items-center gap-3 px-3.5 py-2.5
                                          rounded-xl border-2 cursor-pointer transition-all duration-200
                                          {{ $isSelected
                                              ? 'border-' . $cfg['color'] . '-500 bg-' . $cfg['color'] . '-50 dark:bg-' . $cfg['color'] . '-900/20'
                                              : 'border-slate-200 dark:border-slate-600 hover:border-slate-300' }}">
                                <input type="radio" name="audience" value="{{ $value }}"
                                    {{ $isSelected ? 'checked' : '' }} class="sr-only">
                                <div class="w-7 h-7 rounded-lg
                                            bg-{{ $cfg['color'] }}-100 dark:bg-{{ $cfg['color'] }}-900/30
                                            flex items-center justify-center shrink-0">
                                    <i class="bi {{ $cfg['icon'] }}
                                              text-{{ $cfg['color'] }}-600 dark:text-{{ $cfg['color'] }}-400
                                              text-sm"></i>
                                </div>
                                <span class="text-sm font-medium text-slate-700 dark:text-slate-200">
                                    {{ $label }}
                                </span>
                            </label>
                            @endforeach
                        </div>
                        @error('audience')
                        <p class="text-xs text-red-600 dark:text-red-400 flex items-center gap-1">
                            <i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
                        </p>
                        @enderror
                    </div>

                    {{-- Date d'expiration --}}
                    <div class="space-y-1.5">
                        <label for="expires_at" class="flex items-center gap-1.5 text-sm font-medium
                                      text-slate-700 dark:text-slate-200">
                            <i class="bi bi-calendar-x text-slate-400"></i>
                            Date d'expiration
                        </label>
                        <input type="text" name="expires_at" id="expires_at"
                            value="{{ old('expires_at', $announcement->expires_at?->format('Y-m-d')) }}"
                            placeholder="Aucune expiration" class="w-full px-3.5 py-2.5 rounded-xl border text-sm
                                      text-slate-800 dark:text-slate-100
                                      bg-white dark:bg-slate-700/50 placeholder-slate-400
                                      focus:outline-none focus:ring-2 transition-all duration-200
                                      border-slate-200 dark:border-slate-600
                                      focus:ring-blue-500/30 focus:border-blue-500">
                        <p class="text-xs text-slate-400 dark:text-slate-500">
                            Laissez vide pour une annonce permanente
                        </p>
                        @error('expires_at')
                        <p class="text-xs text-red-600 dark:text-red-400 flex items-center gap-1">
                            <i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
                        </p>
                        @enderror
                    </div>

                    {{-- Épingler --}}
                    <label class="flex items-center gap-3 px-3.5 py-3 rounded-xl border cursor-pointer
                                  border-slate-200 dark:border-slate-600
                                  hover:border-amber-400 dark:hover:border-amber-500
                                  bg-white dark:bg-slate-700/50
                                  transition-all duration-200">
                        <input type="hidden" name="is_pinned" value="0">
                        <input type="checkbox" name="is_pinned" id="is_pinned" value="1"
                            {{ old('is_pinned', $announcement->is_pinned ?? false) ? 'checked' : '' }}
                            class="w-4 h-4 rounded accent-amber-500 cursor-pointer">
                        <div>
                            <p class="text-sm font-medium text-slate-700 dark:text-slate-200 flex items-center gap-1.5">
                                <i class="bi bi-pin-angle-fill text-amber-500 text-xs"></i>
                                Épingler l'annonce
                            </p>
                            <p class="text-xs text-slate-400 dark:text-slate-500">
                                Apparaît en tête de liste
                            </p>
                        </div>
                    </label>

                </div>
            </div>

            {{-- Aperçu --}}
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200
                        dark:border-slate-700 shadow-sm overflow-hidden">
                <div class="flex items-center gap-3 px-5 py-4
                            border-b border-slate-100 dark:border-slate-700
                            bg-slate-50/60 dark:bg-slate-700/30">
                    <div class="w-7 h-7 rounded-lg bg-cyan-100 dark:bg-cyan-900/40
                                flex items-center justify-center">
                        <i class="bi bi-eye-fill text-cyan-600 dark:text-cyan-400 text-sm"></i>
                    </div>
                    <h2 class="text-sm font-semibold text-slate-700 dark:text-slate-200">
                        Aperçu
                    </h2>
                </div>
                <div class="p-5">
                    <div class="rounded-xl border border-slate-200 dark:border-slate-600
                                bg-slate-50 dark:bg-slate-700/30 p-4 space-y-2">
                        <div class="flex items-start gap-2">
                            <i class="bi bi-pin-angle-fill text-amber-500 text-xs mt-0.5
                                      {{ old('is_pinned', $announcement->is_pinned) ? '' : 'hidden' }}"
                                id="preview-pin"></i>
                            <p id="preview-title"
                                class="text-sm font-bold text-slate-800 dark:text-slate-100 leading-snug">
                                {{ $announcement->title ?: 'Titre de l\'annonce…' }}
                            </p>
                        </div>
                        <p id="preview-content"
                            class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed line-clamp-3">
                            {{ $announcement->content ?: 'Le contenu de votre annonce apparaîtra ici…' }}
                        </p>
                        <div class="flex items-center gap-2 pt-1">
                            <span id="preview-audience" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full
                                         text-[10px] font-semibold
                                         bg-blue-100 dark:bg-blue-900/30
                                         text-blue-700 dark:text-blue-400">
                                <i class="bi bi-globe text-[8px]"></i>
                                Tous
                            </span>
                            <span id="preview-expiry" class="text-[10px] text-slate-400 dark:text-slate-500"></span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="space-y-2">
                <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-6 py-3
                               rounded-xl text-sm font-semibold
                               bg-blue-600 hover:bg-blue-700 text-white
                               shadow-sm hover:shadow-md transition-all duration-200
                               focus:outline-none focus:ring-2 focus:ring-blue-500/40">
                    <i class="bi {{ $announcement->exists ? 'bi-check-lg' : 'bi-megaphone' }}"></i>
                    {{ $announcement->exists ? 'Enregistrer les modifications' : 'Publier l\'annonce' }}
                </button>
                <a href="{{ route('announcements.index') }}" class="w-full inline-flex items-center justify-center gap-2 px-6 py-2.5
                          rounded-xl text-sm font-medium
                          border border-slate-200 dark:border-slate-700
                          text-slate-700 dark:text-slate-300
                          hover:bg-slate-50 dark:hover:bg-slate-700/50
                          transition-all duration-200">
                    <i class="bi bi-x-lg"></i>
                    Annuler
                </a>
            </div>

        </div>
    </div>

</x-forms.form>
@endsection

@push('scripts')
<script>
(() => {
    // ── Compteur titre ────────────────────────────────────────────
    const titleInput = document.getElementById('title');
    const titleCount = document.getElementById('title-count');
    const previewTitle = document.getElementById('preview-title');

    titleInput?.addEventListener('input', () => {
        const len = titleInput.value.length;
        if (titleCount) titleCount.textContent = `${len} / 200`;
        if (previewTitle) previewTitle.textContent = titleInput.value || 'Titre de l\'annonce…';
    });

    // ── Aperçu contenu ────────────────────────────────────────────
    const contentTA = document.getElementById('content');
    const previewContent = document.getElementById('preview-content');

    contentTA?.addEventListener('input', () => {
        if (previewContent) {
            previewContent.textContent = contentTA.value || 'Le contenu de votre annonce apparaîtra ici…';
        }
    });

    // ── Aperçu audience ───────────────────────────────────────────
    const audienceLabels = {
        all: {
            label: 'Tous',
            icon: 'bi-globe',
            color: 'blue'
        },
        teachers: {
            label: 'Enseignants',
            icon: 'bi-person-badge',
            color: 'emerald'
        },
        students: {
            label: 'Élèves',
            icon: 'bi-mortarboard',
            color: 'amber'
        },
        parents: {
            label: 'Parents',
            icon: 'bi-people-fill',
            color: 'cyan'
        },
    };

    const audienceColors = {
        blue: 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400',
        emerald: 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400',
        amber: 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400',
        cyan: 'bg-cyan-100 dark:bg-cyan-900/30 text-cyan-700 dark:text-cyan-400',
    };

    const previewAudience = document.getElementById('preview-audience');

    document.querySelectorAll('.audience-option').forEach(label => {
        label.addEventListener('click', () => {
            const radio = label.querySelector('input[type="radio"]');
            const val = radio?.value;
            const cfg = audienceLabels[val];
            const color = audienceColors[cfg?.color ?? 'blue'];

            // Styling options
            document.querySelectorAll('.audience-option').forEach(l => {
                l.className = l.className
                    .replace(/border-\w+-500/g, 'border-slate-200 dark:border-slate-600')
                    .replace(/bg-\w+-50|dark:bg-\w+-900\/20/g, '');
            });

            const c = cfg?.color ?? 'blue';
            label.classList.add(`border-${c}-500`, `bg-${c}-50`, `dark:bg-${c}-900/20`);

            // Aperçu
            if (previewAudience && cfg) {
                previewAudience.className =
                    `inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold ${color}`;
                previewAudience.innerHTML =
                `<i class="bi ${cfg.icon} text-[8px]"></i> ${cfg.label}`;
            }
        });
    });

    // ── Aperçu épingle ────────────────────────────────────────────
    const pinCheckbox = document.getElementById('is_pinned');
    const previewPin = document.getElementById('preview-pin');

    pinCheckbox?.addEventListener('change', () => {
        previewPin?.classList.toggle('hidden', !pinCheckbox.checked);
    });

    // ── Aperçu expiration ─────────────────────────────────────────
    const expiresInput = document.getElementById('expires_at');
    const previewExpiry = document.getElementById('preview-expiry');

    flatpickr('#expires_at', {
        dateFormat: 'Y-m-d',
        altInput: true,
        altFormat: 'd/m/Y',
        locale: 'fr',
        minDate: 'today',
        onChange: ([date]) => {
            if (previewExpiry && date) {
                previewExpiry.textContent = `Expire le ${date.toLocaleDateString('fr-FR')}`;
            } else if (previewExpiry) {
                previewExpiry.textContent = '';
            }
        },
    });

    // Init
    if (titleInput && titleCount) {
        titleCount.textContent = `${titleInput.value.length} / 200`;
    }
})();
</script>
@endpush