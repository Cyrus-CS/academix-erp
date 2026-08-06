@extends('layouts.base')

@section('page_title', 'Modifier une présence')

@section('breadcrumb')
<span class="text-slate-300 dark:text-slate-600 font-light select-none">/</span>
<a href="{{ route('attendance.index') }}"
    class="text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
    Présences
</a>
<span class="text-slate-300 dark:text-slate-600 font-light select-none">/</span>
<span class="text-slate-400 dark:text-slate-500">Modifier</span>
@endsection

@section('content')

<div class="max-w-6xl mx-auto space-y-6">

    {{-- ══════════════════════════════════════════════════════
         INFO — Contexte de la présence
    ══════════════════════════════════════════════════════ --}}
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm
                border border-slate-200 dark:border-slate-700 overflow-hidden">

        <div class="flex items-center gap-3 px-6 py-4
                    border-b border-slate-200 dark:border-slate-700">
            <div class="w-9 h-9 rounded-xl bg-blue-100 dark:bg-blue-900/40
                        flex items-center justify-center">
                <i class="bi bi-info-circle-fill text-blue-600 dark:text-blue-400"></i>
            </div>
            <div>
                <h2 class="text-sm font-bold text-slate-800 dark:text-slate-100">
                    Informations de la session
                </h2>
                <p class="text-xs text-slate-500 dark:text-slate-400">
                    Ces données ne peuvent pas être modifiées
                </p>
            </div>
        </div>

        <dl class="divide-y divide-slate-100 dark:divide-slate-700">
            @foreach([
            ['icon' => 'bi-person-fill', 'label' => 'Élève', 'value' => $attendance->student->user->name],
            ['icon' => 'bi-building', 'label' => 'Classe', 'value' => $attendance->classe->name],
            ['icon' => 'bi-book-fill', 'label' => 'Matière', 'value' => $attendance->subject->name ?? '—'],
            ['icon' => 'bi-person-badge', 'label' => 'Enseignant', 'value' => $attendance->teacher->user->name ?? '—'],
            ['icon' => 'bi-calendar3', 'label' => 'Date', 'value' => $attendance->date->translatedFormat('l d F Y')],
            ] as $item)
            <div class="flex items-center gap-4 px-6 py-3
                        hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                <div class="w-8 h-8 rounded-lg bg-slate-100 dark:bg-slate-700
                            flex items-center justify-center shrink-0">
                    <i class="bi {{ $item['icon'] }} text-slate-500 dark:text-slate-400 text-sm"></i>
                </div>
                <dt class="text-sm text-slate-500 dark:text-slate-400 w-28 shrink-0">
                    {{ $item['label'] }}
                </dt>
                <dd class="text-sm font-semibold text-slate-800 dark:text-slate-100 truncate">
                    {{ $item['value'] }}
                </dd>
            </div>
            @endforeach
        </dl>
    </div>

    {{-- ══════════════════════════════════════════════════════
         FORMULAIRE
    ══════════════════════════════════════════════════════ --}}
    <form action="{{ route('attendance.update', $attendance) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm
                    border border-slate-200 dark:border-slate-700 overflow-hidden">

            <div class="flex items-center gap-3 px-6 py-4
                        border-b border-slate-200 dark:border-slate-700">
                <div class="w-9 h-9 rounded-xl bg-emerald-100 dark:bg-emerald-900/40
                            flex items-center justify-center">
                    <i class="bi bi-pencil-square text-emerald-600 dark:text-emerald-400"></i>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-slate-800 dark:text-slate-100">
                        Modifier la présence
                    </h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400">
                        Mettez à jour le statut et le motif
                    </p>
                </div>
            </div>

            <div class="p-6 space-y-6">

                {{-- Statut --}}
                <div>
                    <label class="flex items-center gap-1.5 text-sm font-medium
                                  text-slate-700 dark:text-slate-200 mb-3">
                        <i class="bi bi-toggle-on text-slate-400"></i>
                        Statut de présence
                        <span class="text-red-500">*</span>
                    </label>

                    <div class="grid grid-cols-3 gap-3">
                        @foreach([
                        ['value' => 'present', 'label' => 'Présent', 'icon' => 'bi-check-circle-fill', 'color' =>
                        'emerald'],
                        ['value' => 'absent', 'label' => 'Absent', 'icon' => 'bi-x-circle-fill', 'color' => 'red'],
                        ['value' => 'late', 'label' => 'Retard', 'icon' => 'bi-clock-fill', 'color' => 'amber'],
                        ] as $option)
                        <label class="cursor-pointer">
                            <input type="radio" name="status" value="{{ $option['value'] }}"
                                class="sr-only edit-status-radio"
                                {{ old('status', $attendance->status) === $option['value'] ? 'checked' : '' }}>
                            <div class="edit-status-card flex flex-col items-center gap-2 p-4
                                        rounded-xl border-2 transition-all duration-200
                                        border-slate-200 dark:border-slate-600
                                        hover:border-{{ $option['color'] }}-300
                                        cursor-pointer" data-color="{{ $option['color'] }}">
                                <i class="bi {{ $option['icon'] }} text-2xl
                                          text-slate-400 status-icon" data-color="{{ $option['color'] }}"></i>
                                <span class="text-sm font-semibold text-slate-600 dark:text-slate-400">
                                    {{ $option['label'] }}
                                </span>
                            </div>
                        </label>
                        @endforeach
                    </div>

                    @error('status')
                    <p class="mt-2 text-xs text-red-600 dark:text-red-400 flex items-center gap-1">
                        <i class="bi bi-exclamation-circle-fill"></i>
                        {{ $message }}
                    </p>
                    @enderror
                </div>

                {{-- Motif --}}
                <x-forms.textarea name="reason" label="Motif / Observation" icon="bi-chat-left-text" :rows="3"
                    :value="old('reason', $attendance->reason)" placeholder="Indiquez un motif si nécessaire..."
                    help="Champ optionnel — visible uniquement par l'administration" />

            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-end gap-3 px-6 py-4
                        bg-slate-50 dark:bg-slate-800/50
                        border-t border-slate-200 dark:border-slate-700">
                <a href="{{ route('attendance.index') }}" class="px-4 py-2 rounded-xl text-sm font-medium
                          text-slate-600 dark:text-slate-400
                          hover:bg-slate-100 dark:hover:bg-slate-700
                          transition-all duration-200">
                    Annuler
                </a>
                <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl
                               text-sm font-semibold text-white
                               bg-blue-600 hover:bg-blue-700
                               shadow-sm transition-all duration-200">
                    <i class="bi bi-check-lg"></i>
                    Enregistrer les modifications
                </button>
            </div>
        </div>
    </form>

</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {

    const colorMap = {
        emerald: {
            active: ['border-emerald-500', 'bg-emerald-50', 'dark:bg-emerald-900/30'],
            icon: ['text-emerald-500'],
            inactive: ['border-slate-200', 'dark:border-slate-600'],
        },
        red: {
            active: ['border-red-500', 'bg-red-50', 'dark:bg-red-900/30'],
            icon: ['text-red-500'],
            inactive: ['border-slate-200', 'dark:border-slate-600'],
        },
        amber: {
            active: ['border-amber-500', 'bg-amber-50', 'dark:bg-amber-900/30'],
            icon: ['text-amber-500'],
            inactive: ['border-slate-200', 'dark:border-slate-600'],
        },
    };

    const updateEditStyles = () => {
        document.querySelectorAll('.edit-status-radio').forEach(radio => {
            const card = radio.nextElementSibling;
            const icon = card.querySelector('.status-icon');
            const color = card.dataset.color;
            const map = colorMap[color];

            // Reset tout
            [...map.active, ...map.inactive].forEach(c => {
                card.classList.remove(c);
                icon?.classList.remove(c);
            });
            map.icon.forEach(c => icon?.classList.remove(c));

            if (radio.checked) {
                map.active.forEach(c => card.classList.add(c));
                map.icon.forEach(c => icon?.classList.add(c));
            } else {
                map.inactive.forEach(c => card.classList.add(c));
                icon?.classList.add('text-slate-400');
            }
        });
    };

    // Init styles
    updateEditStyles();

    // Écouter les changements
    document.querySelectorAll('.edit-status-radio').forEach(radio => {
        radio.addEventListener('change', updateEditStyles);
    });

    // Clic sur la carte entière
    document.querySelectorAll('.edit-status-card').forEach(card => {
        card.addEventListener('click', () => {
            const radio = card.previousElementSibling;
            if (radio) {
                radio.checked = true;
                updateEditStyles();
            }
        });
    });

});
</script>
@endpush

@endsection