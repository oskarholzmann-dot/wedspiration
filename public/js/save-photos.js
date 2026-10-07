/*
 * Save photos into your own folder without reloading the page.
 *
 * - Every "Save" button (form[data-save-form]) toggles between saved and not saved.
 * - Photo cards ([data-photo-card]) can be dragged onto the drop zone ([data-drop-zone]),
 *   which appears at the bottom of the screen while you drag.
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

document.querySelectorAll('form[data-save-form]').forEach((form) => {
    form.addEventListener('submit', async (event) => {
        event.preventDefault();

        const saved = form.querySelector('button').dataset.saved === 'true';

        try {
            const result = await send(form.action, saved ? 'DELETE' : 'POST');
            markSaved(form.action, result.saved);
        } catch (error) {
            alert(error.message);
        }
    });
});

const dropZone = document.querySelector('[data-drop-zone]');

if (dropZone) {
    const message = dropZone.querySelector('[data-drop-message]');
    const defaultText = message.textContent;

    document.querySelectorAll('[data-photo-card]').forEach((card) => {
        card.addEventListener('dragstart', (event) => {
            event.dataTransfer.setData('text/plain', card.dataset.saveUrl);
            event.dataTransfer.effectAllowed = 'copy';
            dropZone.hidden = false;
        });

        card.addEventListener('dragend', () => {
            // Leave a moment to read the confirmation before the zone disappears
            setTimeout(() => {
                dropZone.hidden = true;
                dropZone.dataset.over = 'false';
                message.textContent = defaultText;
            }, 1200);
        });
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
