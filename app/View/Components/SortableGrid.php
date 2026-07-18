<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

/**
 * ╔══════════════════════════════════════════════════════════════╗
 * ║              x-sortable-grid — School ERP                    ║
 * ║  Conteneur réutilisable pour toutes les grilles/listes        ║
 * ║  réorganisables via SortableJS (auto-init dans app.js).       ║
 * ╚══════════════════════════════════════════════════════════════╝
 *
 * USAGE MINIMAL :
 * ────────────────
 *   <x-sortable-grid resource="students">
 *       @foreach($students as $student)
 *           <x-sortable-item :id="$student->id">
 *               ... contenu de la carte ...
 *           </x-sortable-item>
 *       @endforeach
 *   </x-sortable-grid>
 *
 * → génère automatiquement :
 *   id="students-grid"
 *   data-sortable-url="{{ route('students.reorder') }}"
 *
 * OPTIONS :
 * ─────────
 *   :id           Surcharge l'ID du conteneur (défaut : "{resource}-grid")
 *   :route-name   Surcharge le nom de la route (défaut : "{resource}.reorder")
 *   :handle       Sélecteur CSS de la poignée de drag (ex: ".drag-handle")
 *   class         Classes Tailwind du conteneur (défaut : grille responsive)
 */
class SortableGrid extends Component
{
    public string $gridId;
    public string $sortableUrl;

    public function __construct(
        public string $resource,
        ?string $id = null,
        ?string $routeName = null,
        public ?string $handle = null,
        public string $class = 'grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4',
    ) {
        $this->gridId = $id ?? "{$resource}-grid";
        $this->sortableUrl = route($routeName ?? "{$resource}.reorder");
    }

    public function render(): View|Closure|string
    {
        return view('components.sortable-grid');
    }
}