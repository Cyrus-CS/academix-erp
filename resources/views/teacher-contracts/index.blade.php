@extends('layouts.base')

@section('title', 'Contrats enseignants')
@section('page_title', 'Contrats des enseignants')

@section('breadcrumb')
<span class="text-slate-300 dark:text-slate-600">/</span>
<span class="font-semibold text-slate-700 dark:text-slate-200">Contrats</span>
@endsection

@section('content')
<div class="space-y-5">

    {{-- EN-TÊTE --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-800 dark:text-slate-100">Contrats enseignants</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                <span class="font-semibold text-blue-600 dark:text-blue-400">{{ $stats['total'] }}</span>
                contrat{{ $stats['total'] > 1 ? 's' : '' }}
            </p>
        </div>
        <a href="{{ route('teacher-contracts.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold
                  bg-blue-600 hover:bg-blue-700 text-white transition-all shadow-sm shadow-blue-500/30">
            <i class="bi bi-plus-lg"></i>
            Nouveau contrat
        </a>
    </div>

    {{-- STATS --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        @foreach([
        ['label' => 'Total', 'value' => $stats['total'], 'icon' => 'bi-file-earmark-text-fill', 'color' => 'blue'],
        ['label' => 'Actifs', 'value' => $stats['active'], 'icon' => 'bi-check-circle-fill', 'color' => 'emerald'],
        ['label' => 'Expirés', 'value' => $stats['expired'], 'icon' => 'bi-x-circle-fill', 'color' => 'red'],
        ['label' => 'Expirent bientôt', 'value' => $stats['expiring'], 'icon' => 'bi-exclamation-triangle-fill', 'color'
        => 'amber'],
        ] as $card)
        <div class="bg-white dark:bg-slate-800 rounded-2xl px-4 py-3.5
                    border border-slate-200 dark:border-slate-700 shadow-sm
                    flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl shrink-0
                        bg-{{ $card['color'] }}-100 dark:bg-{{ $card['color'] }}-900/30
                        flex items-center justify-center">
                <i
                    class="bi {{ $card['icon'] }} text-{{ $card['color'] }}-600 dark:text-{{ $card['color'] }}-400 text-lg"></i>
            </div>
            <div class="min-w-0">
                <p class="text-xl font-bold text-slate-800 dark:text-slate-100 leading-tight">{{ $card['value'] }}</p>
                <p class="text-xs text-slate-500 dark:text-slate-400 truncate">{{ $card['label'] }}</p>
            </div>
        </div>
        @endforeach
    </div>

    {{-- FILTRES --}}
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm">
        <form method="GET" action="{{ route('teacher-contracts.index') }}"
            class="grid grid-cols-1 sm:grid-cols-3 gap-3 p-4">

            <div>
                <label class="text-xs font-medium text-slate-500 dark:text-slate-400 mb-1.5 block">Enseignant</label>
                <select name="teacher_id" onchange="this.form.submit()" class="w-full px-3.5 py-2.5 text-sm rounded-xl
                           border border-slate-200 dark:border-slate-700
                           bg-slate-50 dark:bg-slate-900/50 text-slate-800 dark:text-slate-100
                           focus:outline-none focus:ring-2 focus:ring-blue-600/40 focus:border-blue-600 transition">
                    <option value="">Tous</option>
                    @foreach($teachers as $teacher)
                    <option value="{{ $teacher->id }}" {{ request('teacher_id') == $teacher->id ? 'selected' : '' }}>
                        {{ $teacher->user->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="text-xs font-medium text-slate-500 dark:text-slate-400 mb-1.5 block">Statut</label>
                <select name="status" onchange="this.form.submit()" class="w-full px-3.5 py-2.5 text-sm rounded-xl
                           border border-slate-200 dark:border-slate-700
                           bg-slate-50 dark:bg-slate-900/50 text-slate-800 dark:text-slate-100
                           focus:outline-none focus:ring-2 focus:ring-blue-600/40 focus:border-blue-600 transition">
                    <option value="">Tous</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Actif</option>
                    <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expiré</option>
                    <option value="terminated" {{ request('status') === 'terminated' ? 'selected' : '' }}>Résilié
                    </option>
                </select>
            </div>

            <div>
                <label class="text-xs font-medium text-slate-500 dark:text-slate-400 mb-1.5 block">Type</label>
                <select name="type" onchange="this.form.submit()" class="w-full px-3.5 py-2.5 text-sm rounded-xl
                           border border-slate-200 dark:border-slate-700
                           bg-slate-50 dark:bg-slate-900/50 text-slate-800 dark:text-slate-100
                           focus:outline-none focus:ring-2 focus:ring-blue-600/40 focus:border-blue-600 transition">
                    <option value="">Tous</option>
                    <option value="permanent" {{ request('type') === 'permanent' ? 'selected' : '' }}>CDI</option>
                    <option value="temporary" {{ request('type') === 'temporary' ? 'selected' : '' }}>CDD</option>
                    <option value="part_time" {{ request('type') === 'part_time' ? 'selected' : '' }}>Temps partiel
                    </option>
                    <option value="internship" {{ request('type') === 'internship' ? 'selected' : '' }}>Stage</option>
                </select>
            </div>
        </form>
    </div>

    {{-- TABLEAU --}}
    <div
        class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-700/50 border-b border-slate-200 dark:border-slate-700">
                        <th
                            class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                            Enseignant</th>
                        <th
                            class="text-left px-4 py-3.5 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                            Type</th>
                        <th
                            class="text-left px-4 py-3.5 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider hidden sm:table-cell">
                            Période</th>
                        <th
                            class="text-left px-4 py-3.5 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider hidden md:table-cell">
                            Salaire</th>
                        <th
                            class="text-left px-4 py-3.5 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                            Statut</th>
                        <th
                            class="text-right px-5 py-3.5 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                            Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    @forelse($contracts as $contract)
                    <tr class="group hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                        <td class="px-5 py-3.5">
                            <p class="text-sm font-semibold text-slate-700 dark:text-slate-200">
                                {{ $contract->teacher->user->name ?? '—' }}
                            </p>
                        </td>
                        <td class="px-4 py-3.5">
                            <span class="text-xs font-medium text-slate-600 dark:text-slate-300">
                                {{ ['permanent' => 'CDI', 'temporary' => 'CDD', 'part_time' => 'Temps partiel', 'internship' => 'Stage'][$contract->contract_type] ?? $contract->type }}
                            </span>
                        </td>
                        <td class="px-4 py-3.5 hidden sm:table-cell">
                            <span class="text-xs text-slate-500 dark:text-slate-400">
                                {{ $contract->start_date?->format('d/m/Y') }}
                                @if($contract->end_date) – {{ $contract->end_date->format('d/m/Y') }} @endif
                            </span>
                        </td>
                        <td class="px-4 py-3.5 hidden md:table-cell">
                            <span class="text-xs font-semibold text-slate-700 dark:text-slate-200">
                                {{ number_format($contract->salary, 0, ',', ' ') }} FCFA
                            </span>
                        </td>
                        <td class="px-4 py-3.5">
                            @php
                            $statusColors = ['active' => 'emerald', 'expired' => 'red', 'terminated' => 'slate'];
                            $statusLabels = ['active' => 'Actif', 'expired' => 'Expiré', 'terminated' => 'Résilié'];
                            $sc = $statusColors[$contract->status] ?? 'slate';
                            @endphp
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold
                                         bg-{{ $sc }}-100 dark:bg-{{ $sc }}-900/30 text-{{ $sc }}-700 dark:text-{{ $sc }}-400">
                                {{ $statusLabels[$contract->status] ?? $contract->status }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5 text-right">
                            <div
                                class="flex items-center justify-end gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
                                <a href="{{ route('teacher-contracts.edit', $contract) }}" class="w-8 h-8 rounded-lg flex items-center justify-center
                                          bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300
                                          hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors">
                                    <i class="bi bi-pencil-fill text-sm"></i>
                                </a>
                                <button onclick="deleteContract({{ $contract->id }})"
                                    class="w-8 h-8 rounded-lg flex items-center justify-center
                                               bg-red-50 dark:bg-red-900/20 text-red-500 dark:text-red-400
                                               hover:bg-red-100 dark:hover:bg-red-900/40 transition-colors focus:outline-none">
                                    <i class="bi bi-trash3-fill text-sm"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-5 py-16 text-center">
                            <i
                                class="bi bi-file-earmark-text text-4xl text-slate-300 dark:text-slate-600 block mb-3"></i>
                            <p class="text-sm text-slate-500 dark:text-slate-400">Aucun contrat trouvé</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($contracts->hasPages())
    <div class="flex justify-center">{{ $contracts->links() }}</div>
    @endif

</div>

<form id="delete-form" method="POST" class="hidden">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('scripts')
<script>
function deleteContract(id) {
    if (!confirm('Supprimer ce contrat ? Cette action est irréversible.')) return;
    const form = document.getElementById('delete-form');
    form.action = `/teacher-contracts/${id}`;
    form.submit();
}
</script>
@endpush