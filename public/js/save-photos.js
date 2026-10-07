/*
 * Save photos into your own folder without reloading the page.
 *
 * - Every "Save" button (form[data-save-form]) toggles between saved and not saved.
 * - Photo cards ([data-photo-card]) can be dragged onto the drop zone ([data-drop-zone]),
 *   which appears at the bottom of the screen while you drag.
 *
 * The listeners sit on the whole document ("event delegation"), so they also work
 * for photos that infinite scrolling adds later.
 */

const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

async function send(url, method) {
    const response = await fetch(url, {
        method,
        headers: { Accept: 'application/json', 'X-CSRF-TOKEN': csrfToken },
    });

    if (!response.ok) {
        throw new Error('Could not save the photo.');
    }

    return response.json();
}

// Show the new state on every button for this photo (a photo can appear twice on a page)
function markSaved(url, saved) {
    document.querySelectorAll(`form[data-save-form][action="${url}"]`).forEach((form) => {
        const button = form.querySelector('button');
        button.dataset.saved = saved ? 'true' : 'false';
        button.textContent = saved ? 'Saved' : 'Save';
        button.title = saved ? 'Remove from your folder' : 'Save to your folder';
    });
}

document.addEventListener('submit', async (event) => {
    const form = event.target.closest('form[data-save-form]');
    if (!form) {
        return;
    }

    event.preventDefault();
    const saved = form.querySelector('button').dataset.saved === 'true';

    try {
        const result = await send(form.action, saved ? 'DELETE' : 'POST');
        markSaved(form.action, result.saved);
    } catch (error) {
        alert(error.message);
    }
});

// Admins: delete a photo straight from the gallery and remove its tile
document.addEventListener('submit', async (event) => {
    const form = event.target.closest('form[data-admin-delete]');
    // defaultPrevented = the admin clicked "Cancel" in the confirmation
    if (!form || event.defaultPrevented) {
        return;
    }

    event.preventDefault();

    try {
        await send(form.action, 'DELETE');
        const tile = form.closest('[data-photo-card]');
        tile.style.transition = 'opacity 200ms';
        tile.style.opacity = '0';
        setTimeout(() => tile.remove(), 200);
    } catch {
        alert('Could not delete the photo.');
    }
});

const dropZone = document.querySelector('[data-drop-zone]');

if (dropZone) {
    const message = dropZone.querySelector('[data-drop-message]');
    const defaultText = message.textContent;
    let hideTimer = null;

    document.addEventListener('dragstart', (event) => {
        const card = event.target.closest('[data-photo-card]');
        if (!card) {
            return;
        }

        event.dataTransfer.setData('text/plain', card.dataset.saveUrl);
        event.dataTransfer.effectAllowed = 'copy';
        clearTimeout(hideTimer);
        message.textContent = defaultText;
        dropZone.hidden = false;
    });

    document.addEventListener('dragend', (event) => {
        if (!event.target.closest('[data-photo-card]')) {
            return;
        }

        // Leave a moment to read the confirmation before the zone disappears
        hideTimer = setTimeout(() => {
            dropZone.hidden = true;
            dropZone.dataset.over = 'false';
            message.textContent = defaultText;
        }, 1200);
    });

    dropZone.addEventListener('dragover', (event) => {
        event.preventDefault(); // needed, or the browser does not allow a drop here
        dropZone.dataset.over = 'true';
    });

    dropZone.addEventListener('dragleave', () => {
        dropZone.dataset.over = 'false';
    });

    dropZone.addEventListener('drop', async (event) => {
        event.preventDefault();
        const url = event.dataTransfer.getData('text/plain');

        try {
            const result = await send(url, 'POST');
            markSaved(url, true);
            message.textContent = result.message;
        } catch (error) {
            message.textContent = error.message;
        }
    });
}
