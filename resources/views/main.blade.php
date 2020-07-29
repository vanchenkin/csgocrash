<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>CSGOCRASH</title>
    <script src="{{ asset('js/app.js') }}" defer></script>
    <script> window.APP_REL_URL = "{{ env('APP_REL_URL') }}" ;
            window.APP_URL = "{{ env('APP_URL') }}"; </script>
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/main.css') }}" rel="stylesheet">
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">CSGOCRASH</a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav mr-auto">
                    	<li class="nav-item"><router-link class="nav-link" :to="{ path: '/' }">Home</router-link></li>
                    	<li class="nav-item"><router-link class="nav-link" :to="{ path: '/faq' }">Faq</router-link></li>
                        <li class="nav-item"><router-link class="nav-link" :to="{ path: '/support' }">Support</router-link></li>
                    </ul>
                    <ul class="navbar-nav ml-auto">
                        @guest
                            <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">LOGIN via STEAM</a></li>
                        @else
                            <li class="nav-item nav-link">Hi, {{ Auth::user()->username }}</li>
                            <li class="nav-item"><a class="nav-link" href="{{ route('logout') }}">Logout</a></li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main>
            <router-view></router-view>
        </main>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            @auth
                store.commit('setAuth');
            @endauth
            store.commit('setSkins', @json($skins));
            store.commit('setMoney', @json($money));
            store.commit('setBets', @json($bets));
            store.commit('setGames', @json($games));
            store.commit('setGameid', @json($gameid));
        });
    </script>
</body>
</html>