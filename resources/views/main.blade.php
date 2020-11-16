<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <title>CSGOCRASH</title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <script src="{{ asset('js/app.js') }}" defer></script>
        <script> window.APP_REL_URL = "{{ env('APP_REL_URL') }}" ;
            window.APP_URL = "{{ env('APP_URL') }}"; </script>
        <link href="{{ asset('css/app.css') }}" rel="stylesheet">
        <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
        <link rel="stylesheet" href="{{ asset('css/fa.min.css') }}">
    </head>
    <body class="preload">
        <noscript>You need to enable JavaScript to run this app.</noscript>
        <div class="wrapper" id="app">
            <header>
                <div class="header-logo">
                    <router-link :to="{ path: '/' }" class="logo"><img class="logo-img" src="{{ asset('img/logo.png') }}"></router-link>
                    <div class="logo-elems">
                        <div class="logo-elem" onclick="turnSound()"><i class="fas logo-volume" :class="{ 'fa-volume-up':bSound,'fa-volume-mute':!bSound  }"></i></div>
                        <div class="logo-elem">
                            RU <i class="fas fa-sort-down logo-icon"></i>
                            <div class="logo-langs"></div>
                        </div>
                    </div>
                </div>
                <div class="header-main">
                    <nav class="nav">
                        <router-link class="nav-item" :to="{ path: '/' }">
                            <span class="nav-item-text"><i class="fas fa-home nav-icon"></i> ГЛАВНАЯ</span>
                        </router-link>
                        <router-link class="nav-item" :to="{ path: '/faq' }">
                            <span class="nav-item-text"><i class="fas fa-question-circle nav-icon"></i> FAQ</span>
                        </router-link>
                        <router-link class="nav-item" :to="{ path: '/support' }">
                            <span class="nav-item-text"><i class="fas fa-headset nav-icon"></i> ПОДДЕРЖКА</span>
                        </router-link>
                        <router-link class="nav-item" :to="{ path: '/fair' }">
                            <span class="nav-item-text"><i class="fas fa-lock nav-icon"></i> ЧЕСТНОСТЬ</span>
                        </router-link>
                    </nav>
                    <div class="header-soc">
                        <a class="header-soc-item" href="/"><i class="fab fa-vk"></i></a>
                        <a class="header-soc-item" href="/"><i class="fab fa-telegram"></i></a>
                        <div class="header-soc-online">
                            <span class="online-number"><i class="fas fa-circle online-circle" id="online-circle"></i> <span id="online">0</span></span>
                            <span class="online">ONLINE</span>
                        </div>
                    </div>
                </div>
                <div class="header-right">
                    @if(!Auth::check())
                        <a class="steam-button" href="{{ route('login') }}"><i class="fab fa-steam-symbol"></i><span>ВОЙТИ ЧЕРЕЗ STEAM<span></a>
                    @else
                        <div class="button header-right-button">ПОПОЛНИТЬ</div>
                        <router-link :to="{ name: 'user', params: { id: {{ Auth::user()->id }} } }" class="header-right-user">
                            <div class="header-right-nameblock">
                                <div class="header-right-name">{{ Auth::user()->name }}</div>
                                <div class="header-right-money">$<span id="money"></span></div>
                            </div>
                            <img class="header-right-image" src="{{ Auth::user()->image }}">
                        </router-link>
                        <a href="{{ route('logout') }}"><i class="fas fa-sign-out-alt header-right-icon"></i></a>
                    @endif
                </div>
            </header>
            <main>
                <div class="left">
                    <div class="skins">
                        @if(!Auth::check())
                            <div class="skins-guest">
                                <i class="fas fa-lock skins-guest-icon"></i>
                                <div class="skins-guest-text">ИНВЕНТАРЬ НЕДОСТУПЕН</div>
                                <div class="skins-guest-stext">войдите на сайт</div>
                                <a class="steam-button" href="{{ route('login') }}"><i class="fab fa-steam-symbol"></i><span>ВОЙТИ ЧЕРЕЗ STEAM<span></a>
                            </div>
                        @else
                            <skins></skins>
                        @endif
                    </div>
                    <draw></draw>
                </div>
                    <router-view></router-view>
                <chat></chat>
            </main>
        </div>
        <script>
            document.addEventListener("DOMContentLoaded", () => {
                $(function() {
                    $("body").removeClass("preload");
                });
                @if(Auth::check())
                    store.commit('setUser', {
                        auth: true,
                        role: '{{ Auth::user()->role }}',
                        id: {{ Auth::user()->id }},
                    });
                @else
                    store.commit('setUser', {
                        auth: false,
                        id: -1,
                    });
                @endif
                store.commit('setMoney', parseFloat(@json($money)));
                store.commit('setBets', @json($bets));
                store.commit('setGames', @json($games));
                store.commit('setGame', @json($game));
                store.commit('setDraw', @json($draw));
                store.commit('setSkins', @json($skins));
                store.commit('setMessages', @json($messages));
                store.commit('setTickets', @json($tickets));
            });
        </script>
    </body>
</html>