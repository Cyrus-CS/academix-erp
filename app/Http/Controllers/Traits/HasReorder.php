<?php

namespace App\Http\Controllers\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

/**
 * ╔══════════════════════════════════════════════════════════════╗
 * ║              HasReorder — School ERP                         ║
 * ║  Trait réutilisable pour le drag & drop SortableJS           ║
 * ╚══════════════════════════════════════════════════════════════╝
 *
 * UTILISATION dans un Controller :
 * ────────────────────────────────
 *
 *   use App\Http\Controllers\Traits\HasReorder;
 *
 *   class SubjectController extends Controller
 *   {
 *       use HasReorder;
 *
 *       protected string $reorderModel  = Subject::class;
 *       // protected string $reorderColumn = 'position'; // optionnel
 *
 *       public function reorder(Request $request): JsonResponse
 *       {
 *           return $this->handleReorder($request);
 *       }
 *   }
 *
 * MIGRATION requise sur chaque table concernée :
 * ──────────────────────────────────────────────
 *   $table->unsignedInteger('position')->default(0)->after('id');
 *
 * ROUTE à ajouter dans web.php :
 * ────────────────────────────────
 *   Route::post('subjects/reorder', [SubjectController::class, 'reorder'])
 *        ->name('subjects.reorder');
 */
trait HasReorder
{
    /**
     * Modèle Eloquent à réordonner.
     * OBLIGATOIRE — à définir dans chaque controller.
     *
     * @example protected string $reorderModel = Subject::class;
     */
    protected string $reorderModel;

    /**
     * Colonne de position dans la table (défaut : 'position').
     * Peut être surchargée dans le controller.
     *
     * @example protected string $reorderColumn = 'sort_order';
     */
    protected string $reorderColumn = 'position';

    /**
     * Nombre maximum d'items acceptés dans un reorder.
     * Protection contre les payloads trop grands.
     */
    protected int $reorderMaxItems = 500;

    // ─────────────────────────────────────────────────────────────
    // Méthode principale
    // ─────────────────────────────────────────────────────────────

    /**
     * Persiste le nouvel ordre des items via SortableJS.
     *
     * Appelé par : POST /{resource}/reorder
     * Payload attendu : { "order": [3, 1, 4, 2, ...] }
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    protected function handleReorder(Request $request): JsonResponse
    {
        // ── 1. Validation de la requête ──────────────────────────
        $request->validate([
            'order'   => [
                'required',
                'array',
                'min:1',
                "max:{$this->reorderMaxItems}",
            ],
            'order.*' => [
                'required',
                'integer',
                'min:1',
            ],
        ]);

        // ── 2. Vérifier que $reorderModel est défini ─────────────
        if (empty($this->reorderModel)) {
            return $this->_reorderError(
                'La propriété $reorderModel n\'est pas définie dans le controller.',
                500
            );
        }

        // ── 3. Vérifier que le modèle existe ─────────────────────
        if (!class_exists($this->reorderModel)) {
            return $this->_reorderError(
                "Le modèle [{$this->reorderModel}] est introuvable.",
                500
            );
        }

        $model  = $this->reorderModel;
        $column = $this->reorderColumn;
        $order  = $request->input('order');

        // ── 4. Vérifier que les IDs existent en base ─────────────
        $existingIds = $model::whereIn('id', $order)
            ->pluck('id')
            ->map(fn($id) => (int) $id)
            ->toArray();

        $invalidIds = array_diff(
            array_map('intval', $order),
            $existingIds
        );

        if (!empty($invalidIds)) {
            return $this->_reorderError(
                'Certains identifiants sont invalides : ' . implode(', ', $invalidIds),
                422
            );
        }

        // ── 5. Mise à jour en transaction ────────────────────────
        try {
            DB::transaction(function () use ($order, $model, $column) {
                foreach ($order as $position => $id) {
                    $model::where('id', (int) $id)
                        ->update([$column => $position + 1]);
                }
            });
        } catch (\Throwable $e) {
            report($e);

            return $this->_reorderError(
                'Une erreur est survenue lors de la mise à jour de l\'ordre.',
                500
            );
        }

        // ── 6. Réponse succès ────────────────────────────────────
        return response()->json([
            'success' => true,
            'message' => 'Ordre mis à jour avec succès.',
            'count'   => count($order),
        ]);
    }

    // ─────────────────────────────────────────────────────────────
    // Helper privé
    // ─────────────────────────────────────────────────────────────

    /**
     * Retourne une réponse JSON d'erreur standardisée.
     */
    private function _reorderError(string $message, int $status = 422): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], $status);
    }
}