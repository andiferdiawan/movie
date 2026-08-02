/**
 * Shared helpers for adding/removing a movie from the signed-in user's
 * favorites. Used by the movie list, movie detail and favorites pages.
 */

export function favoriteDestroyUrl(imdbId) {
    return window.MovieApp.favoritesDestroyUrlTemplate
        ? window.MovieApp.favoritesDestroyUrlTemplate.replace(':id', imdbId)
        : `/favorites/${imdbId}`;
}

export function addFavorite(payload) {
    return axios.post(window.MovieApp.favoritesUrl, payload);
}

export function removeFavorite(imdbId) {
    return axios.delete(favoriteDestroyUrl(imdbId));
}

/**
 * Toggle a favorite button's add/remove state, calling the OMDb-favorites
 * API and updating the button's icon/label once the request resolves.
 *
 * @param {HTMLElement} btn
 * @param {{onAdded?: Function, onRemoved?: Function}} callbacks
 */
export function toggleFavorite(btn, callbacks = {}) {
    const isFavorite = btn.classList.contains('active');
    const imdbId = btn.dataset.imdbId;

    btn.disabled = true;

    const request = isFavorite
        ? removeFavorite(imdbId)
        : addFavorite({
              imdb_id: imdbId,
              title: btn.dataset.title,
              year: btn.dataset.year,
              type: btn.dataset.type,
              poster: btn.dataset.poster,
          });

    return request
        .then(() => {
            applyFavoriteButtonState(btn, !isFavorite);

            if (isFavorite && callbacks.onRemoved) {
                callbacks.onRemoved();
            } else if (!isFavorite && callbacks.onAdded) {
                callbacks.onAdded();
            }
        })
        .finally(() => {
            btn.disabled = false;
        });
}

export function applyFavoriteButtonState(btn, isFavorite) {
    const i18n = window.MovieApp.i18n || {};

    btn.classList.toggle('active', isFavorite);

    const icon = btn.querySelector('.icon-heart');
    if (icon) {
        icon.textContent = isFavorite ? '♥' : '♡';
    }

    const label = btn.querySelector('.favorite-label');
    if (label) {
        label.textContent = isFavorite ? i18n.removeFromFavorites : i18n.addToFavorites;
    }

    btn.title = isFavorite ? i18n.removeFromFavorites : i18n.addToFavorites;
}
