<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

/**
 * x-sortable-item — Élément individuel d'une grille Sortable.
 *
 * USAGE :
 * ────────
 *   <x-sortable-item :id="$student->id" class="student-card ...">
 *       ... contenu ...
 *   </x-sortable-item>
 */
class SortableItem extends Component
{
    public function __construct(
        public int|string $id,
    ) {}

    public function render(): View|Closure|string
    {
        return view('components.sortable-item');
    }
}