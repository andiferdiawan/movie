@extends('layouts.app')

@section('title', __('My Favorites'))

@section('content')
<div class="container">
    <h1 class="h3 mb-4">{{ __('My Favorites') }}</h1>

    <div id="favorites-empty" class="empty-state {{ $favorites->isEmpty() ? '' : 'd-none' }}">
        <div class="empty-state-icon">♡</div>
        <h2 class="h5">{{ __('No favorites yet') }}</h2>
        <p class="text-muted">{{ __('Browse movies and tap the heart icon to save them here.') }}</p>
        <a href="{{ route('movies.index') }}" class="btn btn-primary mt-2">{{ __('Browse Movies') }}</a>
    </div>

    @if (! $favorites->isEmpty())
        <div class="row movie-grid" id="favorites-grid">
            @foreach ($favorites as $favorite)
                <div class="col-6 col-md-4 col-lg-3 movie-col" data-imdb-id="{{ $favorite->imdb_id }}">
                    <div class="movie-card">
                        <a class="movie-poster-link" href="{{ route('movies.show', $favorite->imdb_id) }}">
                            <img
                                class="movie-poster lazy"
                                loading="lazy"
                                src="{{ asset('img/poster-placeholder.svg') }}"
                                data-src="{{ ($favorite->poster && $favorite->poster !== 'N/A') ? $favorite->poster : asset('img/poster-placeholder.svg') }}"
                                alt="{{ $favorite->title }}"
                            >
                        </a>
                        <button
                            type="button"
                            class="btn-favorite active btn-remove-favorite"
                            data-imdb-id="{{ $favorite->imdb_id }}"
                            title="{{ __('Remove from Favorites') }}"
                        >
                            <span class="icon-heart">♥</span>
                        </button>
                        <div class="movie-info">
                            <h3 class="movie-title"><a href="{{ route('movies.show', $favorite->imdb_id) }}">{{ $favorite->title }}</a></h3>
                            <p class="movie-meta">{{ $favorite->year }} &middot; {{ ucfirst($favorite->type) }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
    window.MovieApp = window.MovieApp || {};
    window.MovieApp.favoritesDestroyUrlTemplate = @json(route('favorites.destroy', ':id'));
</script>
@endsection
