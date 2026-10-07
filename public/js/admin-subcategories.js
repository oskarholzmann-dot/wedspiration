/*
 * Admins: drag a photo tile onto a subcategory in the gallery's filter bar to move it there.
 *
 * The tile carries the address to send it to (data-assign-url); each subcategory
 * pill is a drop target (data-subcategory-drop="<id>").
 */

const ASSIGN_TYPE = 'application/x-wedspiration-assign';
const csrf = document.querySelector('meta[name="csrf-token"]').content;
const grid = document.querySelector('[data-infinite]');
let draggedTile = null;

// Remember which tile is dragged (works for tiles added later by infinite scrolling)
document.addEventListener('dragstart', (event) => {
    const tile = event.target.closest('[data-photo-card][data-assign-url]');
    if (!tile) {
        return;
    }

    draggedTile = tile;
    event.dataTransfer.setData(ASSIGN_TYPE, tile.dataset.assignUrl);
});

document.addEventListener('dragend', () => (draggedTile = null));

function toast(message) {
    const box = document.createElement('div');
    box.className = 'fixed top-4 left-1/2 z-50 -translate-x-1/2 rounded-full bg-stone-900 px-5 py-2 text-sm text-white shadow-lg';
    box.setAttribute('role', 'status');
    box.textContent = message;
    document.body.append(box);
    setTimeout(() => box.remove(), 2000);
}

document.querySelectorAll('[data-subcategory-drop]').forEach((pill) => {
    pill.addEventListener('dragover', (event) => {
        if (!event.dataTransfer.types.includes(ASSIGN_TYPE)) {
            return; // not a photo tile
        }
        event.preventDefault(); // allow the drop
        pill.dataset.over = 'true';
    });

    pill.addEventListener('dragleave', () => (pill.dataset.over = 'false'));

    pill.addEventListener('drop', async (event) => {
        event.preventDefault();
        pill.dataset.over = 'false';

        const url = event.dataTransfer.getData(ASSIGN_TYPE);
        const tile = draggedTile;

        const response = await fetch(url, {
            method: 'PATCH',
            headers: { 'Content-Type': 'application/json', Accept: 'application/json', 'X-CSRF-TOKEN': csrf },
            body: JSON.stringify({ subcategory_id: Number(pill.dataset.subcategoryDrop) }),
        });

        if (!response.ok) {
            toast('Could not move the photo.');
            return;
        }

        const result = await response.json();
        toast(result.message);

        // New photo counts on every pill
        for (const [id, count] of Object.entries(result.counts)) {
            const counter = document.querySelector(`[data-subcategory-count="${id}"]`);
            if (counter) {
                counter.textContent = count;
            }
        }

        // Viewing another subcategory: the photo no longer belongs on this page
        const active = grid?.dataset.activeSubcategory;
        if (tile && active && active !== pill.dataset.subcategoryDrop) {
            tile.style.transition = 'opacity 200ms';
            tile.style.opacity = '0';
            setTimeout(() => tile.remove(), 200);
        }
    });
});
