/*
 * Admins: sort photos in the gallery by dragging them onto a category or subcategory.
 *
 * - "Select photos" (or Shift-click) marks several photos; dragging one of them drags the whole group.
 * - Category pills ([data-category-drop]) and subcategory pills ([data-subcategory-drop]) are drop targets.
 * - All photos of a group are moved with one request to admin.photos.sort.
 */

const SORT_TYPE = 'application/x-wedspiration-photos';
const csrf = document.querySelector('meta[name="csrf-token"]').content;
const grid = document.querySelector('[data-infinite]');
const sortUrl = document.querySelector('[data-sort-url]')?.dataset.sortUrl;
const toggle = document.querySelector('[data-select-toggle]');
const info = document.querySelector('[data-selection-info]');
const counter = document.querySelector('[data-selection-count]');

const selected = new Set();
let selecting = false;
let draggedTiles = [];

// ---- Selecting -------------------------------------------------------------

function setSelected(tile, on) {
    tile.dataset.selected = on ? 'true' : 'false';
    on ? selected.add(tile) : selected.delete(tile);
    counter.textContent = selected.size;
    info.hidden = selected.size === 0;
}

function clearSelection() {
    [...selected].forEach((tile) => setSelected(tile, false));
}

function setSelecting(on) {
    selecting = on;
    toggle.setAttribute('aria-pressed', on ? 'true' : 'false');
    toggle.textContent = on ? 'Done selecting' : 'Select photos';
}

toggle?.addEventListener('click', () => setSelecting(!selecting));
document.querySelector('[data-selection-clear]')?.addEventListener('click', clearSelection);

// Runs before the lightbox's click handler (capture phase), so a click selects instead of opening
document.addEventListener('click', (event) => {
    const tile = event.target.closest('[data-photo-card][data-photo-id]');
    if (!tile || event.target.closest('form')) {
        return; // not a tile, or the Save / Delete button on it
    }

    if (selecting || event.shiftKey) {
        event.preventDefault();
        event.stopPropagation();
        setSelecting(true);
        setSelected(tile, tile.dataset.selected !== 'true');
    }
}, true);

document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape' && selecting) {
        clearSelection();
        setSelecting(false);
    }
});

// ---- Dragging --------------------------------------------------------------

document.addEventListener('dragstart', (event) => {
    const tile = event.target.closest('[data-photo-card][data-photo-id]');
    if (!tile) {
        return;
    }

    // Dragging a selected photo takes the whole selection along; any other photo goes alone
    draggedTiles = tile.dataset.selected === 'true' ? [...selected] : [tile];
    event.dataTransfer.setData(SORT_TYPE, JSON.stringify(draggedTiles.map((t) => Number(t.dataset.photoId))));

    if (draggedTiles.length > 1) {
        // A small badge instead of one photo, so it is clear the whole group is moving
        const badge = document.createElement('div');
        badge.textContent = `${draggedTiles.length} photos`;
        badge.style.cssText = 'position:absolute;top:-1000px;padding:6px 14px;border-radius:9999px;background:#be123c;color:#fff;font:600 14px sans-serif';
        document.body.append(badge);
        event.dataTransfer.setDragImage(badge, 20, 16);
        setTimeout(() => badge.remove());
    }
});

function toast(message) {
    const box = document.createElement('div');
    box.className = 'fixed top-4 left-1/2 z-50 -translate-x-1/2 rounded-full bg-stone-900 px-5 py-2 text-sm text-white shadow-lg';
    box.setAttribute('role', 'status');
    box.textContent = message;
    document.body.append(box);
    setTimeout(() => box.remove(), 2500);
}

function fadeOut(tile) {
    selected.delete(tile);
    tile.style.transition = 'opacity 200ms';
    tile.style.opacity = '0';
    setTimeout(() => tile.remove(), 200);
}

async function drop(pill, target, event) {
    event.preventDefault();
    pill.dataset.over = 'false';

    const ids = JSON.parse(event.dataTransfer.getData(SORT_TYPE) || '[]');
    const tiles = draggedTiles;

    const response = await fetch(sortUrl, {
        method: 'PATCH',
        headers: { 'Content-Type': 'application/json', Accept: 'application/json', 'X-CSRF-TOKEN': csrf },
        body: JSON.stringify({ photos: ids, ...target }),
    });

    if (!response.ok) {
        toast('Could not move the photos.');
        return;
    }

    const result = await response.json();
    toast(result.message);

    for (const [id, count] of Object.entries(result.counts)) {
        const label = document.querySelector(`[data-subcategory-count="${id}"]`);
        if (label) {
            label.textContent = count;
        }
    }

    // Photos that no longer match the current filter leave the page
    const activeSubcategory = grid?.dataset.activeSubcategory;
    const activeCategory = grid?.dataset.activeCategory;
    const leaves = (target.subcategory_id && activeSubcategory && String(target.subcategory_id) !== activeSubcategory)
        || (target.category && activeCategory && target.category !== activeCategory)
        || (target.remove_subcategory && activeSubcategory);

    tiles.forEach((tile) => (leaves ? fadeOut(tile) : setSelected(tile, false)));
}

function makeDropTarget(pill, target) {
    pill.addEventListener('dragover', (event) => {
        if (!event.dataTransfer.types.includes(SORT_TYPE)) {
            return; // not photos from this gallery
        }
        event.preventDefault();
        pill.dataset.over = 'true';
    });
    pill.addEventListener('dragleave', () => (pill.dataset.over = 'false'));
    pill.addEventListener('drop', (event) => drop(pill, target, event));
}

document.querySelectorAll('[data-subcategory-drop]').forEach((pill) => {
    makeDropTarget(pill, { subcategory_id: Number(pill.dataset.subcategoryDrop) });
});

// "All" in the subcategory row: take the photos out of their subcategory (they stay in the gallery)
document.querySelectorAll('[data-subcategory-clear]').forEach((pill) => {
    makeDropTarget(pill, { remove_subcategory: true });
});

document.querySelectorAll('[data-category-drop]').forEach((pill) => {
    makeDropTarget(pill, { category: pill.dataset.categoryDrop });
});
