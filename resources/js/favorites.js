import { removeFavorite } from './favorite';

/**
 * Favorites page: removing a favorite updates the grid in place and
 * reveals the empty-state panel once the last item is gone.
 */
document.addEventListener('DOMContentLoaded', () => {
    const grid = document.getElementById('favorites-grid');
    const emptyState = document.getElementById('favorites-empty');

    if (!grid) {
        return; // Not on the favorites page.
    }

    grid.addEventListener('click', (event) => {
        const btn = event.target.closest('.btn-remove-favorite');

        if (!btn) {
            return;
        }

        event.preventDefault();
        const imdbId = btn.dataset.imdbId;
        btn.disabled = true;

        removeFavorite(imdbId)
            .then(() => {
                const col = btn.closest('.movie-col');
                if (col) {
                    col.remove();
                }

                if (!grid.querySelector('.movie-col')) {
                    grid.classList.add('d-none');
                    if (emptyState) {
                        emptyState.classList.remove('d-none');
                    }
                }
            })
            .catch(() => {
                btn.disabled = false;
            });
    });
});
