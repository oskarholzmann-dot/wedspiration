/*
 * Photo upload helpers.
 *
 * PHP rejects any request larger than 2 MB before Laravel even sees it, so big photos
 * are shrunk here, in the browser, before they are sent.
 *
 * - <form data-multi-upload>: uploads every selected photo in its own request,
 *   so many photos together never hit the 2 MB limit.
 * - <form data-compress-images>: a normal form; the chosen photo is shrunk, then the form is sent.
 */

// Stay a bit below 2 MB: the request also carries the other form fields
const MAX_BYTES = 1.8 * 1024 * 1024;
// Longest side in pixels; plenty for a gallery and keeps files small
const MAX_SIDE = 2400;

async function compressImage(file) {
    if (file.size <= MAX_BYTES) {
        return file;
    }

    let bitmap;
    try {
        bitmap = await createImageBitmap(file);
    } catch {
        throw new Error('this file type cannot be read by your browser (try JPG or PNG)');
    }

    let scale = Math.min(1, MAX_SIDE / Math.max(bitmap.width, bitmap.height));
    let quality = 0.85;

    // Try lower quality first, then smaller dimensions, until the photo fits
    for (let attempt = 0; attempt < 10; attempt++) {
        const canvas = document.createElement('canvas');
        canvas.width = Math.round(bitmap.width * scale);
        canvas.height = Math.round(bitmap.height * scale);
        canvas.getContext('2d').drawImage(bitmap, 0, 0, canvas.width, canvas.height);

        const blob = await new Promise((resolve) => canvas.toBlob(resolve, 'image/jpeg', quality));

        if (blob && blob.size <= MAX_BYTES) {
            const name = file.name.replace(/\.[^.]+$/, '') + '.jpg';
            return new File([blob], name, { type: 'image/jpeg' });
        }

        if (quality > 0.6) {
            quality -= 0.1;
        } else {
            scale *= 0.8;
        }
    }

    throw new Error('could not be made small enough');
}

// Only real photos: skip hidden files (.DS_Store), sidecar files (.xmp), RAW files and so on
function isPhoto(file) {
    return !file.name.startsWith('.')
        && (file.type.startsWith('image/') || /\.(jpe?g|png|gif|webp|heic|heif)$/i.test(file.name));
}

// A dropped folder: read every file inside it, including subfolders
async function filesFromEntry(entry) {
    if (entry.isFile) {
        return [await new Promise((resolve, reject) => entry.file(resolve, reject))];
    }

    const reader = entry.createReader();
    const files = [];

    // readEntries returns the folder's content in batches until a batch is empty
    while (true) {
        const batch = await new Promise((resolve, reject) => reader.readEntries(resolve, reject));
        if (batch.length === 0) {
            break;
        }
        for (const child of batch) {
            files.push(...await filesFromEntry(child));
        }
    }

    return files;
}

function setupMultiUpload(form) {
    const inputs = form.querySelectorAll('input[type="file"]');
    const button = form.querySelector('button[type="submit"]');
    const status = form.querySelector('[data-upload-status]');
    const dropArea = form.querySelector('[data-upload-drop]');
    const summary = form.querySelector('[data-upload-summary]');

    // The photos to upload, from either button or from dragging
    let files = [];

    function select(chosen) {
        const all = [...chosen];
        // Upload in file name order: "photo 2" before "photo 10"
        files = all.filter(isPhoto)
            .sort((a, b) => a.name.localeCompare(b.name, undefined, { numeric: true }));
        const skipped = all.length - files.length;

        summary.textContent = files.length === 0
            ? 'No photos found in your selection.'
            : `${files.length} ${files.length === 1 ? 'photo' : 'photos'} selected`
                + (skipped > 0 ? ` (${skipped} other ${skipped === 1 ? 'file' : 'files'} skipped)` : '');
        summary.hidden = false;
    }

    inputs.forEach((input) => input.addEventListener('change', () => select(input.files)));

    // Dropping a file next to the drop area would make the browser open it and leave the page
    window.addEventListener('dragover', (event) => event.preventDefault());
    window.addEventListener('drop', (event) => event.preventDefault());

    dropArea.addEventListener('dragover', (event) => {
        event.preventDefault();
        dropArea.dataset.over = 'true';
    });
    dropArea.addEventListener('dragleave', () => (dropArea.dataset.over = 'false'));
    dropArea.addEventListener('drop', async (event) => {
        event.preventDefault();
        dropArea.dataset.over = 'false';

        // The entries must be read right away, before the first "await"
        const entries = [...event.dataTransfer.items]
            .map((item) => item.webkitGetAsEntry?.())
            .filter(Boolean);

        summary.hidden = false;
        summary.textContent = 'Reading folder…';

        const dropped = [];
        for (const entry of entries) {
            dropped.push(...await filesFromEntry(entry));
        }
        select(dropped.length > 0 ? dropped : event.dataTransfer.files);
    });

    form.addEventListener('submit', async (event) => {
        event.preventDefault();

        if (files.length === 0) {
            summary.hidden = false;
            summary.textContent = 'Please choose photos or a folder first.';
            return;
        }

        button.disabled = true;
        status.hidden = false;

        const errors = [];
        let redirect = null;

        for (const [index, file] of files.entries()) {
            status.textContent = `Uploading ${index + 1} of ${files.length}: ${file.name}`;

            try {
                const image = await compressImage(file);

                const data = new FormData(form);
                data.delete('image');
                data.append('image', image, image.name);

                const response = await fetch(form.action, {
                    method: 'POST',
                    body: data,
                    headers: { Accept: 'application/json' },
                });
                const json = await response.json().catch(() => ({}));

                if (response.ok) {
                    redirect = json.redirect;
                } else {
                    errors.push(`${file.name}: ${json.message ?? 'upload failed'}`);
                }
            } catch (error) {
                errors.push(`${file.name}: ${error.message}`);
            }
        }

        if (errors.length === 0 && redirect) {
            window.location = redirect;
            return;
        }

        // Show what went wrong; photos that did upload are already in the folder
        const uploaded = files.length - errors.length;
        status.textContent = `${uploaded} of ${files.length} photos uploaded. Not uploaded:`;
        const list = document.createElement('ul');
        list.className = 'mt-2 list-disc ps-5 text-red-600';
        for (const message of errors) {
            const item = document.createElement('li');
            item.textContent = message;
            list.append(item);
        }
        status.append(list);

        if (redirect) {
            const link = document.createElement('a');
            link.href = redirect;
            link.className = 'mt-2 inline-block underline';
            link.textContent = 'Go to your folder';
            status.append(link);
        }

        button.disabled = false;
    });
}

function setupCompressedForm(form) {
    const input = form.querySelector('input[type="file"]');

    form.addEventListener('submit', async (event) => {
        const file = input.files[0];
        if (!file || file.size <= MAX_BYTES) {
            return; // nothing to shrink: send the form as usual
        }

        event.preventDefault();

        try {
            const image = await compressImage(file);
            const files = new DataTransfer();
            files.items.add(image);
            input.files = files.files;
            form.submit();
        } catch (error) {
            alert(`${file.name}: ${error.message}`);
        }
    });
}

document.querySelectorAll('form[data-multi-upload]').forEach(setupMultiUpload);
document.querySelectorAll('form[data-compress-images]').forEach(setupCompressedForm);
