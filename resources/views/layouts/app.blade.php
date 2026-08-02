<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', __('Movie App')) | {{ config('app.name') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito:400,600,700&display=swap" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-dark app-navbar shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ route('movies.index') }}">
                    {{ config('app.name') }}
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    @auth
                        <ul class="navbar-nav mr-auto nav-pills-custom">
                            <li class="nav-item {{ request()->routeIs('movies.*') ? 'active' : '' }}">
                                <a class="nav-link" href="{{ route('movies.index') }}">{{ __('Movies') }}</a>
                            </li>
                            <li class="nav-item {{ request()->routeIs('favorites.*') ? 'active' : '' }}">
                                <a class="nav-link" href="{{ route('favorites.index') }}">{{ __('My Favorites') }}</a>
                            </li>
                        </ul>
                    @endauth

                    <ul class="navbar-nav ml-auto align-items-md-center">
                        <li class="nav-item dropdown mr-md-2 mb-2 mb-md-0">
                            <a id="localeDropdown" class="nav-link dropdown-toggle locale-pill" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                {{ config('app.available_locales')[app()->getLocale()] ?? strtoupper(app()->getLocale()) }}
                            </a>
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="localeDropdown">
                                @foreach (config('app.available_locales') as $code => $label)
                                    <a class="dropdown-item {{ app()->getLocale() === $code ? 'active' : '' }}" href="{{ route('lang.switch', $code) }}">{{ $label }}</a>
                                @endforeach
                            </div>
                        </li>

                        @auth
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle user-pill d-flex align-items-center" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <span class="user-avatar">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
                                    {{ Auth::user()->name }}
                                </a>
                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endauth
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            @yield('content')
        </main>

        <footer class="app-footer text-center text-muted py-4">
            <small>{{ __('Movie data provided by') }} <a href="https://www.omdbapi.com/" target="_blank" rel="noopener">OMDb API</a></small>
        </footer>
    </div>

    @yield('scripts')
</body>
</html>
