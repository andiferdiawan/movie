import { initLazyLoad } from './lazyload';
import { toggleFavorite, applyFavoriteButtonState } from './favorite';

/**
 * Movie list page: search form, infinite scroll pagination and the
 * favorite toggle button rendered on each card.
 */
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('search-form');
    const grid = document.getElementById('movies-grid');

    if (!form || !grid) {
        return; // Not on the movie list page.
    }

    const template = document.getElementById('movie-card-template');
    const sentinel = document.getElementById('scroll-sentinel');
    const loadingEl = document.getElementById('movies-loading');
    const errorEl = document.getElementById('movies-error');
    const emptyStartEl = document.getElementById('movies-empty');
    const emptyResultsEl = document.getElementById('movies-no-results');
    const endEl = document.getElementById('movies-end');

    const heroEl = document.getElementById('movies-hero');
    const heroBg = document.getElementById('movies-hero-bg');
    const heroTitle = document.getElementById('movies-hero-title');
    const heroMeta = document.getElementById('movies-hero-meta');
    const heroLink = document.getElementById('movies-hero-link');
    const heroFavoriteBtn = document.getElementById('movies-hero-favorite');

    const qInput = document.getElementById('q');
    const typeInput = document.getElementById('type');
    const yearInput = document.getElementById('year');

    const initial = window.MovieApp.initialFilters || {};
    const defaultQuery = window.MovieApp.defaultQuery || '';

    const state = {
        page: 1,
        hasMore: false,
        loading: false,
        query: initial.q || defaultQuery,
        type: initial.type || '',
        year: initial.year || '',
    };

    let debounceTimer = null;
    let scrollObserver = null;

    function hide(el) {
        if (el) el.classList.add('d-none');
    }

    function show(el) {
        if (el) el.classList.remove('d-none');
    }

    function showUrlFor(imdbId) {
        return window.MovieApp.showUrlTemplate.replace(':id', imdbId);
    }

    function renderCard(movie) {
        const node = template.content
            ? template.content.cloneNode(true)
            : document.importNode(template.content, true);

        const col = node.querySelector('.movie-col');
        col.dataset.imdbId = movie.imdbID;

        const posterLink = node.querySelector('.movie-poster-link');
        posterLink.href = showUrlFor(movie.imdbID);

        const img = node.querySelector('.movie-poster');
        const poster = movie.Poster && movie.Poster !== 'N/A' ? movie.Poster : null;
        if (poster) {
            img.setAttribute('data-src', poster);
        } else {
            img.src = img.getAttribute('src'); // keep placeholder
            img.classList.remove('lazy');
        }
        img.alt = movie.Title;

        const titleLink = node.querySelector('.movie-title a');
        titleLink.href = showUrlFor(movie.imdbID);
        titleLink.textContent = movie.Title;

        node.querySelector('.movie-year').textContent = movie.Year || '';
        node.querySelector('.movie-type').textContent = movie.Type
            ? movie.Type.charAt(0).toUpperCase() + movie.Type.slice(1)
            : '';

        const favBtn = node.querySelector('.btn-favorite');
        favBtn.dataset.imdbId = movie.imdbID;
        favBtn.dataset.title = movie.Title;
        favBtn.dataset.year = movie.Year;
        favBtn.dataset.type = movie.Type;
        favBtn.dataset.poster = movie.Poster;
        applyFavoriteButtonState(favBtn, !!movie.is_favorite);

        return node;
    }

    grid.addEventListener('click', (event) => {
        const btn = event.target.closest('.btn-favorite');
        if (btn) {
            event.preventDefault();
            toggleFavorite(btn).catch(() => {
                // Leave the button state untouched; a real app would surface a toast here.
            });
        }
    });

    function setHero(movie) {
        if (!heroEl) {
            return;
        }

        const poster = movie.Poster && movie.Poster !== 'N/A' ? movie.Poster : null;
        heroBg.style.backgroundImage = poster ? `url("${poster}")` : 'none';
        heroTitle.textContent = movie.Title;

        const type = movie.Type ? movie.Type.charAt(0).toUpperCase() + movie.Type.slice(1) : '';
        heroMeta.textContent = [movie.Year, type].filter(Boolean).join(' · ');
        heroLink.href = showUrlFor(movie.imdbID);

        heroFavoriteBtn.dataset.imdbId = movie.imdbID;
        heroFavoriteBtn.dataset.title = movie.Title;
        heroFavoriteBtn.dataset.year = movie.Year;
        heroFavoriteBtn.dataset.type = movie.Type;
        heroFavoriteBtn.dataset.poster = movie.Poster;
        applyFavoriteButtonState(heroFavoriteBtn, !!movie.is_favorite);

        show(heroEl);
    }

    function hideHero() {
        hide(heroEl);
    }

    if (heroFavoriteBtn) {
        heroFavoriteBtn.addEventListener('click', (event) => {
            event.preventDefault();
            toggleFavorite(heroFavoriteBtn).catch(() => {
                // Leave the button state untouched; a real app would surface a toast here.
            });
        });
    }

    function resetEmptyStates() {
        hide(emptyStartEl);
        hide(emptyResultsEl);
        hide(endEl);
        hide(errorEl);
    }

    function syncUrl() {
        // Keep the address bar in sync with the active search so that browser
        // back/forward (and bookmarking/sharing a URL) reproduces the same
        // results — Laravel's responses aren't cached by the browser, so a
        // history navigation always re-fetches the page rather than restoring
        // in-memory JS state; syncing the URL is what makes that re-fetch land
        // on the right search instead of resetting to the default keyword.
        const params = new URLSearchParams();
        if (state.query) params.set('q', state.query);
        if (state.type) params.set('type', state.type);
        if (state.year) params.set('year', state.year);

        const query = params.toString();
        const newUrl = window.location.pathname + (query ? `?${query}` : '');
        window.history.replaceState(null, '', newUrl);
    }

    function loadResults(reset) {
        if (state.loading) {
            return;
        }

        if (reset) {
            state.page = 1;
            grid.innerHTML = '';
            resetEmptyStates();
            syncUrl();

            if (!state.query) {
                show(emptyStartEl);
                hideHero();
                return;
            }
        }

        state.loading = true;
        show(loadingEl);
        hide(errorEl);

        axios
            .get(window.MovieApp.searchUrl, {
                params: {
                    q: state.query,
                    type: state.type || undefined,
                    year: state.year || undefined,
                    page: state.page,
                },
            })
            .then(({ data }) => {
                state.hasMore = !!data.has_more;
                const isEmptyFirstPage = state.page === 1 && data.results.length === 0;

                if (isEmptyFirstPage) {
                    // "No results" already explains the empty grid; showing OMDb's raw
                    // "Movie not found!" error on top of it would just be redundant.
                    show(emptyResultsEl);
                } else if (data.error) {
                    show(errorEl);
                    errorEl.textContent = data.error;
                }

                if (reset) {
                    if (data.results.length > 0) {
                        setHero(data.results[0]);
                    } else {
                        hideHero();
                    }
                }

                data.results.forEach((movie) => grid.appendChild(renderCard(movie)));
                initLazyLoad(grid);

                if (!state.hasMore && data.results.length > 0) {
                    show(endEl);
                }
            })
            .catch(() => {
                show(errorEl);
                errorEl.textContent = window.MovieApp.i18n.searchError || 'Something went wrong. Please try again.';
            })
            .finally(() => {
                state.loading = false;
                hide(loadingEl);
            });
    }

    function loadNextPage() {
        if (!state.hasMore || state.loading || !state.query) {
            return;
        }

        state.page += 1;
        loadResults(false);
    }

    function setupScrollObserver() {
        if (!sentinel || !('IntersectionObserver' in window)) {
            return;
        }

        scrollObserver = new IntersectionObserver(
            (entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        loadNextPage();
                    }
                });
            },
            { rootMargin: '400px 0px' }
        );

        scrollObserver.observe(sentinel);
    }

    function triggerSearch() {
        // An emptied keyword field falls back to the default keyword rather
        // than showing a "type something" state, so the grid never goes blank.
        state.query = qInput.value.trim() || defaultQuery;
        state.type = typeInput.value;
        state.year = yearInput.value.trim();
        loadResults(true);
    }

    form.addEventListener('submit', (event) => {
        event.preventDefault();
        clearTimeout(debounceTimer);
        triggerSearch();
    });

    qInput.addEventListener('input', () => {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(triggerSearch, 500);
    });

    typeInput.addEventListener('change', triggerSearch);
    yearInput.addEventListener('change', triggerSearch);

    setupScrollObserver();

    // state.query is always populated (typed filter or the default keyword),
    // unless no default keyword is configured at all — in which case fall
    // back to the "start your search" empty state.
    if (state.query) {
        loadResults(true);
    } else {
        show(emptyStartEl);
    }
});
