/*
 * Lightbox: a click on a photo tile shows the photo large over the page.
 *
 * - ‹ › buttons or the arrow keys browse through the tiles on the page
 *   (including tiles that infinite scrolling added later).
 * - Esc, the × button or a click on the dark background closes it.
 * - "Save" and dragging the large photo onto the drop zone work as on the tiles.
 */

const box = document.querySelector('[data-lightbox]');

if (box) {
    const image = box.querySelector('[data-lightbox-image]');
    const dragArea = box.querySelector('[data-lightbox-drag]');
    const title = box.querySelector('[data-lightbox-title]');
    const category = box.querySelector('[data-lightbox-category]');
    const meta = box.querySelector('[data-lightbox-meta]');
    const pageLink = box.querySelector('[data-lightbox-page]');
    const position = box.querySelector('[data-lightbox-position]');
    const prevButton = box.querySelector('[data-lightbox-prev]');
    const nextButton = box.querySelector('[data-lightbox-next]');
    const closeButton = box.querySelector('[data-lightbox-close]');
    const saveForm = box.querySelector('[data-lightbox-save]');
    const deleteButton = box.querySelector('[data-lightbox-delete]');

    let tiles = [];
    let index = 0;
    let openedFrom = null;

    function show(newIndex) {
        index = newIndex;
        const tile = tiles.at(index);

        image.src = tile.dataset.lightboxSrc;
        image.alt = tile.dataset.title;
        title.textContent = tile.dataset.title;
        category.textContent = tile.dataset.category;
        meta.textContent = tile.dataset.meta;
        pageLink.href = tile.dataset.pageUrl;
        position.textContent = `${index + 1} / ${tiles.length}`;
        prevButton.disabled = index === 0;
        nextButton.disabled = index === tiles.length - 1;

        if (deleteButton) {
            deleteButton.hidden = !tile.dataset.deleteUrl;
        }

        if (saveForm && tile.dataset.saveUrl) {
            // Same action as the tile's form, so save-photos.js keeps both buttons in sync
            const tileButton = tile.querySelector('form[data-save-form] button');
            const button = saveForm.querySelector('button');
            saveForm.action = tile.dataset.saveUrl;
            button.dataset.saved = tileButton.dataset.saved;
            button.textContent = tileButton.textContent.trim();

            dragArea.setAttribute('data-photo-card', '');
            dragArea.dataset.saveUrl = tile.dataset.saveUrl;
            dragArea.draggable = true;
        }
    }

    function open(tile) {
        // In the masonry layout the page order is per column, so use the photos' reading order
        tiles = [...document.querySelectorAll('[data-photo-card][data-lightbox-src]')]
            .sort((a, b) => Number(a.dataset.order ?? 0) - Number(b.dataset.order ?? 0));
        openedFrom = document.activeElement;
        show(tiles.indexOf(tile));
        box.hidden = false;
        document.body.style.overflow = 'hidden';
        closeButton.focus();
    }

    function close() {
        box.hidden = true;
        image.src = '';
        document.body.style.overflow = '';
        openedFrom?.focus();
    }

    document.addEventListener('click', (event) => {
        const link = event.target.closest('[data-lightbox-open]');
        // Ctrl/Cmd-click still opens the photo page in a new tab
        if (!link || event.metaKey || event.ctrlKey || event.shiftKey) {
            return;
        }

        event.preventDefault();
        open(link.closest('[data-photo-card]'));
    });

    // Admins: delete the photo shown, remove its tile and move on to the next one
    deleteButton?.addEventListener('click', async () => {
        const tile = tiles.at(index);
        if (!confirm(`Delete "${tile.dataset.title}" for everyone? This cannot be undone.`)) {
            return;
        }

        const response = await fetch(tile.dataset.deleteUrl, {
            method: 'DELETE',
            headers: {
                Accept: 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
        });

        if (!response.ok) {
            alert('Could not delete the photo.');
            return;
        }

        tile.remove();
        tiles.splice(index, 1);

        if (tiles.length === 0) {
            close();
        } else {
            show(Math.min(index, tiles.length - 1));
        }
    });

    prevButton.addEventListener('click', () => index > 0 && show(index - 1));
    nextButton.addEventListener('click', () => index < tiles.length - 1 && show(index + 1));
    closeButton.addEventListener('click', close);

    // A click on the dark background (not on the photo or a button) closes it
    box.addEventListener('click', (event) => {
        if (event.target === box || event.target === dragArea.parentElement) {
            close();
        }
    });

    document.addEventListener('keydown', (event) => {
        if (box.hidden) {
            return;
        }

        if (event.key === 'Escape') {
            close();
        } else if (event.key === 'ArrowLeft' && index > 0) {
            show(index - 1);
        } else if (event.key === 'ArrowRight' && index < tiles.length - 1) {
            show(index + 1);
        }
    });
}
