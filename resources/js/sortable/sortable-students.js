/**
 * sortable-students.js
 * Drag & Drop de la grille élèves avec SortableJS
 * JS natif — aucun framework
 */

export function initStudentsGrid() {
    const grid = document.getElementById('students-grid');

    if (!grid) return;

    // ── État de chargement ──────────────────────────────────────
    let isSaving = false;

    // ── Instance SortableJS ─────────────────────────────────────
    const sortable = Sortable.create(grid, {
        animation        : 200,
        easing           : 'cubic-bezier(0.25, 0.1, 0.25, 1)',
        ghostClass       : 'sortable-ghost',
        chosenClass      : 'sortable-chosen',
        dragClass        : 'sortable-drag',
        handle           : '.drag-handle',   // poignée dédiée
        delay            : 80,
        delayOnTouchOnly : true,
        forceFallback    : false,

        // ── Début du drag ───────────────────────────────────────
        onStart(evt) {
            document.body.classList.add('is-dragging');
            evt.item.classList.add('opacity-60');
        },

        // ── Fin du drag ─────────────────────────────────────────
        onEnd(evt) {
            document.body.classList.remove('is-dragging');
            evt.item.classList.remove('opacity-60');

            // Pas de changement de position → rien à sauvegarder
            if (evt.oldIndex === evt.newIndex) return;

            // Récupérer l'ordre actuel des IDs
            const order = [...grid.querySelectorAll('[data-id]')]
                .map(card => card.dataset.id)
                .filter(Boolean);

            _persistOrder(order);
        },
    });

    // ── Sauvegarde via fetch ────────────────────────────────────
    function _persistOrder(order) {
        if (isSaving) return;
        isSaving = true;

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

        // Indicateur visuel
        _showSavingIndicator(true);

        fetch('/students/reorder', {
            method  : 'POST',
            headers : {
                'Content-Type' : 'application/json',
                'Accept'       : 'application/json',
                'X-CSRF-TOKEN' : csrfToken,
            },
            body: JSON.stringify({ order }),
        })
        .then(res => {
            if (!res.ok) throw new Error(`HTTP ${res.status}`);
            return res.json();
        })
        .then(() => {
            window.showToast?.({
                type    : 'success',
                title   : 'Ordre mis à jour',
                message : 'La disposition des élèves a été sauvegardée.',
                delay   : 3000,
            });
        })
        .catch(err => {
            console.error('[SortableStudents] Erreur de sauvegarde :', err);

            window.showToast?.({
                type    : 'error',
                title   : 'Erreur',
                message : 'Impossible de sauvegarder l\'ordre. Réessayez.',
                delay   : 5000,
            });

            // Rétablir l'ordre initial en rechargant
            // (optionnel selon ton besoin)
            // window.location.reload();
        })
        .finally(() => {
            isSaving = false;
            _showSavingIndicator(false);
        });
    }

    // ── Indicateur de sauvegarde ────────────────────────────────
    function _showSavingIndicator(show) {
        const indicator = document.getElementById('saving-indicator');
        if (!indicator) return;
        indicator.classList.toggle('opacity-0', !show);
        indicator.classList.toggle('opacity-100', show);
    }

    return sortable;
}