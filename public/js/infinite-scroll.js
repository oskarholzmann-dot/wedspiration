/*
 * Infinite scrolling for paginated lists.
 *
 * The server still sends one page at a time (fast, and the page links keep working
 * without JavaScript). When the reader nears the end of the list, this script loads
 * the next page in the background and appends its items.
 *
 * Markup: <div data-infinite data-next="{{ next page url or empty }}"> items… </div>
 *         <div data-pager> page links (hidden by this script) </div>
 */

const list = document.querySelector('[data-infinite]');

if (list) {
    let nextUrl = list.dataset.next || null;
    let loading = false;

    document.querySelectorAll('[data-pager]').forEach((pager) => (pager.hidden = true));

    // An invisible marker after the list: when it comes near the screen, load more
    const sentinel = document.createElement('div');
    sentinel.className = 'py-8 text-center text-sm text-stone-500';
    sentinel.setAttribute('aria-live', 'polite');
    list.after(sentinel);

    const nearBottom = () => sentinel.getBoundingClientRect().top < window.innerHeight + 800;

    async function loadMore() {
        if (loading || !nextUrl) {
            return;
        }

        loading = true;
        sentinel.textContent = 'Loading more photos…';

        try {
            const response = await fetch(nextUrl);
            const page = new DOMParser().parseFromString(await response.text(), 'text/html');
            const nextList = page.querySelector('[data-infinite]');

            list.append(...nextList.children);
            nextUrl = nextList.dataset.next || null;
            sentinel.textContent = '';
        } catch {
            sentinel.textContent = 'Could not load more photos. Scroll again to retry.';
        }

        loading = false;

        if (!nextUrl) {
            observer.disconnect();
            sentinel.remove();
        } else if (nearBottom()) {
            // The new items did not fill the screen yet: keep going
            loadMore();
        }
    }

    const observer = new IntersectionObserver((entries) => {
        if (entries[0].isIntersecting) {
            loadMore();
        }
    }, { rootMargin: '800px' });

    if (nextUrl) {
        observer.observe(sentinel);
    }
}
