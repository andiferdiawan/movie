@extends('layouts.app')

@section('title', __('Movies'))

@section('content')
<div class="container">
    <div id="movies-hero" class="movies-hero d-none">
        <div class="movies-hero-bg" id="movies-hero-bg"></div>
        <div class="movies-hero-overlay"></div>
        <div class="movies-hero-content">
            <span class="movies-hero-badge">{{ __('Top Result') }}</span>
            <h2 id="movies-hero-title" class="movies-hero-title"></h2>
            <p id="movies-hero-meta" class="movies-hero-meta"></p>
            <div class="movies-hero-actions">
                <a id="movies-hero-link" href="#" class="btn btn-light btn-hero">{{ __('View Details') }}</a>
                <button type="button" id="movies-hero-favorite" class="btn-hero-favorite btn-favorite-lg">
                    <span class="icon-heart">♡</span>
                    <span class="favorite-label">{{ __('Add to Favorites') }}</span>
                </button>
            </div>
        </div>
    </div>

    <div class="page-header mb-4">
        <h1 class="h3 mb-3">{{ __('Search Movies') }}</h1>

        <form id="search-form" class="search-form">
            <div class="form-row align-items-end">
                <div class="col-12 col-md-6 form-group mb-2">
                    <label for="q" class="small text-muted mb-1">{{ __('Keyword') }}</label>
                    <input type="text" id="q" name="q" class="form-control" placeholder="{{ __('e.g. Batman, Inception, Friends') }}" value="{{ $filters['q'] ?? '' }}" autocomplete="off">
                </div>
                <div class="col-6 col-md-3 form-group mb-2">
                    <label for="type" class="small text-muted mb-1">{{ __('Type') }}</label>
                    <select id="type" name="type" class="form-control">
                        <option value="">{{ __('All Types') }}</option>
                        <option value="movie" {{ ($filters['type'] ?? '') === 'movie' ? 'selected' : '' }}>{{ __('Movie') }}</option>
                        <option value="series" {{ ($filters['type'] ?? '') === 'series' ? 'selected' : '' }}>{{ __('Series') }}</option>
                        <option value="episode" {{ ($filters['type'] ?? '') === 'episode' ? 'selected' : '' }}>{{ __('Episode') }}</option>
                    </select>
                </div>
                <div class="col-6 col-md-2 form-group mb-2">
                    <label for="year" class="small text-muted mb-1">{{ __('Year') }}</label>
                    <input type="text" id="year" name="year" class="form-control" placeholder="{{ __('e.g. 2020') }}" maxlength="4" value="{{ $filters['year'] ?? '' }}">
                </div>
                <div class="col-12 col-md-1 form-group mb-2">
                    <button type="submit" class="btn btn-primary btn-block">{{ __('Search') }}</button>
                </div>
            </div>
        </form>
    </div>

    <div id="movies-error" class="alert alert-warning d-none" role="alert"></div>

    <div id="movies-grid" class="row movie-grid"></div>

    <div id="scroll-sentinel" aria-hidden="true"></div>

    <div id="movies-loading" class="text-center py-4 d-none">
        <div class="spinner-border text-primary" role="status">
            <span class="sr-only">{{ __('Loading...') }}</span>
        </div>
    </div>

    <div id="movies-empty" class="empty-state d-none">
        <div class="empty-state-icon">🔍</div>
        <h2 class="h5">{{ __('Start your search') }}</h2>
        <p class="text-muted">{{ __('Type a movie, series, or episode title above to see results here.') }}</p>
    </div>

    <div id="movies-no-results" class="empty-state d-none">
        <div class="empty-state-icon">🎞️</div>
        <h2 class="h5">{{ __('No movies found') }}</h2>
        <p class="text-muted">{{ __('Try a different keyword or adjust your filters.') }}</p>
    </div>

    <div id="movies-end" class="text-center text-muted py-3 d-none">
        <small>{{ __("You've reached the end of the results.") }}</small>
    </div>
</div>

<template id="movie-card-template">
    <div class="col-6 col-md-4 col-lg-3 movie-col">
        <div class="movie-card">
            <a class="movie-poster-link" href="#">
                <img class="movie-poster lazy" loading="lazy" src="{{ asset('img/poster-placeholder.svg') }}" data-src="" alt="">
            </a>
            <button type="button" class="btn-favorite" title="{{ __('Add to Favorites') }}">
                <span class="icon-heart">♡</span>
            </button>
            <div class="movie-info">
                <h3 class="movie-title"><a href="#"></a></h3>
                <p class="movie-meta"><span class="movie-year"></span> &middot; <span class="movie-type"></span></p>
            </div>
        </div>
    </div>
</template>
@endsection

@section('scripts')
<script>
    window.MovieApp = window.MovieApp || {};
    window.MovieApp.searchUrl = @json(route('movies.search'));
    window.MovieApp.favoritesUrl = @json(route('favorites.store'));
    window.MovieApp.showUrlTemplate = @json(route('movies.show', ':id'));
    window.MovieApp.initialFilters = @json($filters);
    window.MovieApp.defaultQuery = @json(config('services.omdb.default_query'));
    window.MovieApp.favoritesDestroyUrlTemplate = @json(route('favorites.destroy', ':id'));
    window.MovieApp.i18n = {
        removeFromFavorites: @json(__('Remove from Favorites')),
        addToFavorites: @json(__('Add to Favorites')),
        searchError: @json(__('Something went wrong. Please try again.')),
    };
</script>
@endsection
