import { toggleFavorite } from './favorite';

/**
 * Movie detail page: the large favorite toggle button and the Back link.
 */
document.addEventListener('DOMContentLoaded', () => {
    const favoriteBtn = document.getElementById('favorite-toggle');

    if (favoriteBtn) {
        favoriteBtn.addEventListener('click', (event) => {
            event.preventDefault();
            toggleFavorite(favoriteBtn).catch(() => {
                // Leave the button state untouched; a real app would surface a toast here.
            });
        });
    }

    const backLink = document.getElementById('back-link');

    if (backLink) {
        backLink.addEventListener('click', (event) => {
            // If we arrived here via in-app navigation (list, favorites, hero
            // banner…), prefer native browser back so the previous page keeps
            // its exact state (search results, scroll position). Otherwise —
            // direct link, bookmark, new tab — fall back to the Movies list,
            // which is the link's href.
            const cameFromThisApp = document.referrer.startsWith(window.location.origin);

            if (cameFromThisApp && window.history.length > 1) {
                event.preventDefault();
                window.history.back();
            }
        });
    }
});
