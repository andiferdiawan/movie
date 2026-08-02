@extends('layouts.app')

@section('title', __('Not Found'))

@section('content')
<div class="container">
    <div class="empty-state">
        <div class="empty-state-icon">🎞️</div>
        <h2 class="h5">{{ __('Movie not found') }}</h2>
        <p class="text-muted">{{ $exception->getMessage() ?: __('The movie you are looking for could not be found.') }}</p>
        <a href="{{ route('movies.index') }}" class="btn btn-primary mt-2">{{ __('Back to Movies') }}</a>
    </div>
</div>
@endsection
