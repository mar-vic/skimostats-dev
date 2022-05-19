<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Skimostats') }}</title>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <script src="{{ mix('js/app.js') }}"></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
    <div id="mainapp">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'SkiMo Stats') }}
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav mr-auto">
                        @guest
                        @else
                        <li class="nav-item {{ Route::is('admin.categories.list') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('admin.categories.list') }}">{{ __('Categories') }}</a>
                        </li>
                        <li class="nav-item {{ Route::is('admin.races.list') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('admin.races.list') }}">{{ __('Races') }}</a>
                        </li>
                        <li class="nav-item {{ Route::is('admin.rankings.index') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('admin.rankings.index') }}">{{ __('Rankings') }}</a>
                        </li>
                        <li class="nav-item {{ Route::is('admin.countries') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('admin.countries') }}">{{ __('Countries') }}</a>
                        </li>
                        <li class="nav-item {{ Route::is('admin.athletes.list') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('admin.athletes.list') }}">{{ __('Athletes') }}</a>
                        </li>
                        <li class="nav-item {{ Route::is('admin.partner-categories.list') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('admin.partner-categories.list') }}">{{ __('Partners') }}</a>
                        </li>
                        {{-- <li class="nav-item {{ Route::is('admin.teams.list') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('admin.teams.list') }}">{{ __('Teams') }}</a>
                        </li> --}}
                        @endguest
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Authentication Links -->
                        @guest
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                            </li>
                            {{-- @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif --}}
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }} <span class="caret"></span>
                                </a>

                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            @yield('content')
        </main>
    </div>

    @stack('scripts')
</body>
</html>
