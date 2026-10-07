/*
 * Stable masonry layout for photo grids ([data-masonry]).
 *
 * Plain CSS columns re-balance every photo whenever new ones are added, so photos jump
 * between columns while infinite scrolling loads more. Here each photo is placed once,
 * into the currently shortest column, and stays there. New photos are only added below.
 *
 * Heights come from data-ratio (height / width, sent by the server), so photos can be
 * placed before their images have loaded. Without JavaScript the CSS columns still work.
 */

// Column count per screen width, the same steps as the Tailwind classes on the grid
const BREAKPOINTS = [
    [1280, 6],
    [1024, 5],
    [768, 4],
    [640, 3],
    [0, 2],
];

function columnCount(grid) {
    const max = Number(grid.dataset.masonryMax || 6);
    const width = window.innerWidth;
    const count = BREAKPOINTS.find(([minWidth]) => width >= minWidth)[1];
    return Math.min(count, max);
}

function setupMasonry(grid) {
    let columns = [];
    let heights = [];
    let order = 0;

    function place(tile) {
        // Remember the reading order (newest first), e.g. for browsing in the lightbox
        if (!tile.dataset.order) {
            tile.dataset.order = order++;
        }

        const shortest = heights.indexOf(Math.min(...heights));
        columns.at(shortest).append(tile);
        // Column width is the same for all columns, so the ratio is enough to compare heights
        heights[shortest] += Number(tile.dataset.ratio || 0.75) + 0.05;
    }

    function build() {
        const tiles = [...grid.querySelectorAll('[data-photo-card]')]
            .sort((a, b) => Number(a.dataset.order ?? 0) - Number(b.dataset.order ?? 0));
        const count = columnCount(grid);

        grid.replaceChildren();
        columns = Array.from({ length: count }, () => {
            const column = document.createElement('div');
            column.className = 'flex min-w-0 flex-1 flex-col';
            grid.append(column);
            return column;
        });
        heights = Array(count).fill(0);

        tiles.forEach(place);
        grid.dataset.columns = count;
    }

    // Switch from CSS columns to fixed column containers
    grid.className = grid.className.replace(/\b(\w+:)?columns-\d+\b/g, '').trim() + ' flex items-start gap-2';
    build();

    // New photos from infinite scrolling: only add them, never move the existing ones
    grid.addEventListener('masonry:append', (event) => {
        event.preventDefault();
        event.detail.items.forEach(place);
    });

    // Rebuild only when the number of columns really changes (e.g. rotating a tablet)
    let resizeTimer;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(() => {
            if (columnCount(grid) !== Number(grid.dataset.columns)) {
                build();
            }
        }, 150);
    });
}

document.querySelectorAll('[data-masonry]').forEach(setupMasonry);
