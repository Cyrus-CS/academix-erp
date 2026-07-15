@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const list = document.getElementById('test-sortable');
    if (!list) return;

    // Désactiver sélection texte sur le conteneur
    list.style.userSelect = 'none';
    list.style.webkitUserSelect = 'none';

    Sortable.create(list, {
        animation: 200,
        ghostClass: 'sortable-ghost',
        chosenClass: 'sortable-chosen',
        dragClass: 'sortable-drag',
        forceFallback: false,
        scroll: true,

        onStart(evt) {
            document.body.style.userSelect = 'none';
            document.body.style.webkitUserSelect = 'none';
            document.body.style.cursor = 'grabbing';
        },

        onEnd(evt) {
            document.body.style.userSelect = '';
            document.body.style.webkitUserSelect = '';
            document.body.style.cursor = '';
            console.log('Déplacé de', evt.oldIndex, 'vers', evt.newIndex);
        },
    });
});
</script>
@endpush

{{-- Structure HTML correcte --}}
<div class="sortable-container">
    <ul id="test-sortable" style="list-style:none; padding:0; margin:0; min-height:50px;">
        <li data-id="1" style="padding:12px 16px; margin-bottom:8px;
                   background:#fff; border:1px solid #e2e8f0;
                   border-radius:12px; cursor:grab;
                   user-select:none;">
            Item 1
        </li>
        <li data-id="2" style="padding:12px 16px; margin-bottom:8px;
                   background:#fff; border:1px solid #e2e8f0;
                   border-radius:12px; cursor:grab;
                   user-select:none;">
            Item 2
        </li>
        <li data-id="3" style="padding:12px 16px; margin-bottom:8px;
                   background:#fff; border:1px solid #e2e8f0;
                   border-radius:12px; cursor:grab;
                   user-select:none;">
            Item 3
        </li>
    </ul>
</div>