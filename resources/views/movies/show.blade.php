@extends('layouts.app')

@section('title', $movie['Title'] ?? __('Movie Detail'))

@section('content')
<div class="container">
    <a href="{{ route('movies.index') }}" id="back-link" class="back-link mb-3">
        <span class="back-icon">&larr;</span> {{ __('Back') }}
    </a>

    <div class="movie-detail card">
        <div class="row no-gutters">
            <div class="col-md-4">
                @php
                    $poster = ($movie['Poster'] ?? 'N/A') !== 'N/A' ? $movie['Poster'] : asset('img/poster-placeholder.svg');
                @endphp
                <img
                    src="{{ $poster }}"
                    alt="{{ $movie['Title'] ?? '' }}"
                    class="movie-detail-poster"
                    loading="lazy"
                    onerror="this.onerror=null; this.src='{{ asset('img/poster-placeholder.svg') }}';"
                >
            </div>
            <div class="col-md-8">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start flex-wrap">
                        <h1 class="h3 mb-1">{{ $movie['Title'] ?? '' }}</h1>
                        <button
                            type="button"
                            id="favorite-toggle"
                            class="btn-favorite-lg {{ $isFavorite ? 'active' : '' }}"
                            data-imdb-id="{{ $movie['imdbID'] ?? '' }}"
                            data-title="{{ $movie['Title'] ?? '' }}"
                            data-year="{{ $movie['Year'] ?? '' }}"
                            data-type="{{ $movie['Type'] ?? '' }}"
                            data-poster="{{ $movie['Poster'] ?? '' }}"
                        >
                            <span class="icon-heart">{{ $isFavorite ? '♥' : '♡' }}</span>
                            <span class="favorite-label">{{ $isFavorite ? __('Remove from Favorites') : __('Add to Favorites') }}</span>
                        </button>
                    </div>

                    <p class="text-muted mb-3">
                        {{ $movie['Year'] ?? '' }}
                        @if(($movie['Rated'] ?? 'N/A') !== 'N/A') &middot; {{ $movie['Rated'] }} @endif
                        @if(($movie['Runtime'] ?? 'N/A') !== 'N/A') &middot; {{ $movie['Runtime'] }} @endif
                        @if(($movie['Genre'] ?? 'N/A') !== 'N/A') &middot; {{ $movie['Genre'] }} @endif
                    </p>

                    @if(($movie['imdbRating'] ?? 'N/A') !== 'N/A')
                        <div class="imdb-rating mb-3">
                            <span class="badge badge-warning">&#9733; {{ $movie['imdbRating'] }}/10</span>
                            <span class="text-muted small">({{ $movie['imdbVotes'] ?? '0' }} {{ __('votes') }})</span>
                        </div>
                    @endif

                    @if(($movie['Plot'] ?? 'N/A') !== 'N/A')
                        <p class="movie-plot">{{ $movie['Plot'] }}</p>
                    @endif

                    <dl class="movie-meta-list">
                        @if(($movie['Director'] ?? 'N/A') !== 'N/A')
                            <dt>{{ __('Director') }}</dt>
                            <dd>{{ $movie['Director'] }}</dd>
                        @endif
                        @if(($movie['Writer'] ?? 'N/A') !== 'N/A')
                            <dt>{{ __('Writer') }}</dt>
                            <dd>{{ $movie['Writer'] }}</dd>
                        @endif
                        @if(($movie['Actors'] ?? 'N/A') !== 'N/A')
                            <dt>{{ __('Actors') }}</dt>
                            <dd>{{ $movie['Actors'] }}</dd>
                        @endif
                        @if(($movie['Language'] ?? 'N/A') !== 'N/A')
                            <dt>{{ __('Language') }}</dt>
                            <dd>{{ $movie['Language'] }}</dd>
                        @endif
                        @if(($movie['Country'] ?? 'N/A') !== 'N/A')
                            <dt>{{ __('Country') }}</dt>
                            <dd>{{ $movie['Country'] }}</dd>
                        @endif
                        @if(($movie['Awards'] ?? 'N/A') !== 'N/A')
                            <dt>{{ __('Awards') }}</dt>
                            <dd>{{ $movie['Awards'] }}</dd>
                        @endif
                        @if(($movie['BoxOffice'] ?? 'N/A') !== 'N/A')
                            <dt>{{ __('Box Office') }}</dt>
                            <dd>{{ $movie['BoxOffice'] }}</dd>
                        @endif
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    window.MovieApp = window.MovieApp || {};
    window.MovieApp.favoritesUrl = @json(route('favorites.store'));
    window.MovieApp.favoritesDestroyUrlTemplate = @json(route('favorites.destroy', ':id'));
    window.MovieApp.i18n = {
        removeFromFavorites: @json(__('Remove from Favorites')),
        addToFavorites: @json(__('Add to Favorites')),
    };
</script>
@endsection
