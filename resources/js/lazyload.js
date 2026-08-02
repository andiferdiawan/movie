/**
 * Lazy-loads movie poster images.
 *
 * Images start out pointing at a lightweight placeholder (see
 * public/img/poster-placeholder.svg) with the real poster URL stashed in
 * data-src. When an <img class="lazy"> element scrolls near the viewport,
 * IntersectionObserver swaps data-src into src. Browsers without
 * IntersectionObserver support fall back to loading everything immediately.
 */

let observer = null;

function getObserver() {
    if (observer) {
        return observer;
    }

    observer = new IntersectionObserver(
        (entries, obs) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) {
                    return;
                }

                const img = entry.target;
                loadImage(img);
                obs.unobserve(img);
            });
        },
        { rootMargin: '250px 0px', threshold: 0.01 }
    );

    return observer;
}

function loadImage(img) {
    const src = img.getAttribute('data-src');

    if (!src) {
        return;
    }

    // Whatever the <img> was showing before the swap is the placeholder —
    // fall back to it if the real poster URL 404s or otherwise fails to load.
    const fallback = img.getAttribute('src');

    img.onerror = () => {
        img.onerror = null;
        img.src = fallback;
    };

    img.src = src;
    img.removeAttribute('data-src');
    img.classList.remove('lazy');
    img.classList.add('lazy-loaded');
}

/**
 * Observe every not-yet-loaded lazy image inside the given container.
 * Call this again after appending new movie cards (e.g. via infinite scroll).
 *
 * @param {ParentNode} container
 */
export function initLazyLoad(container = document) {
    const images = container.querySelectorAll('img.lazy[data-src]');

    if (!images.length) {
        return;
    }

    if (!('IntersectionObserver' in window)) {
        images.forEach(loadImage);
        return;
    }

    const obs = getObserver();
    images.forEach((img) => obs.observe(img));
}

document.addEventListener('DOMContentLoaded', () => initLazyLoad());
