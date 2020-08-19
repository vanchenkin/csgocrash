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
                        <div class="logo-elem"><i class="fas fa-volume-up"></i></div>
                        <div class="logo-elem">RU <i class="fas fa-sort-down logo-icon"></i></div>
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
                            <span class="online-number"><i class="fas fa-circle online-circle" id="online-circle"></i> 2323</span>
                            <span class="online">ONLINE</span>
                        </div>
                    </div>
                </div>
                <div class="header-right">
                    @if(!Auth::check())
                        <a class="steam-button" href="{{ route('login') }}"><i class="fab fa-steam-symbol"></i><span>ВОЙТИ ЧЕРЕЗ STEAM<span></a>
                    @else
                        <div class="header-right-button">ПОПОЛНИТЬ</div>
                        <div class="header-right-nameblock">
                            <div class="header-right-name">{{ Auth::user()->username }}</div>
                            <div class="header-right-money">$<span id="money"></span></div>
                        </div>
                        <img class="header-right-image" src="{{ Auth::user()->avatar }}">
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
                    <div class="draw">
                        <div class="draw-header">
                            <i class="fas fa-trophy draw-header-icon"></i>
                            <div class="draw-header-text">БЕСПЛАТНЫЙ РОЗЫГРЫШ</div>
                            <div class="draw-header-time">12:23</div>
                        </div>
                        <div class="draw-body">
                            <img class="draw-body-image" src="img/draw.png">
                            <div class="draw-body-r">
                                <div class="draw-body-text">AWP</div>
                                <div class="draw-body-stext">ИСТОРИЯ О ДРАКОНЕ</div>
                                <div class="draw-body-ttext">(WELL-WORN)</div>
                                <div class="draw-body-button">УЧАСТВОВАТЬ</div>
                            </div>
                        </div>
                        <div class="draw-count">
                            <div class="draw-count-text">УЧАСТНИКОВ</div>
                            <div class="draw-count-c">12</div>
                        </div>
                    </div>
                </div>

                <router-view></router-view>

                <div class="chat">
                    <div class="chat-header">
                        <i class="fas fa-comments chat-header-icon"></i>
                        <div class="chat-header-text">ОНЛАЙН ЧАТ</div>
                        <div class="chat-header-rules">правила</div>
                    </div>
                    <div class="chat-body">
                        <div class="chat-inner">
                            <div class="chat-message">
                                <a href="#"><img class="chat-image" src="img/ava.jpg"></a>
                                <div class="chat-block">
                                    <div class="chat-nameblock">
                                        <a class="chat-name" href="#">vanchenkin</a>
                                        <div class="chat-time">17:27</div>
                                    </div>
                                    <div class="chat-text">Всем привет!</div>
                                </div>
                            </div>
                            <div class="chat-message reversed">
                                <img class="chat-image" src="img/ava.jpg">
                                <div class="chat-block">
                                    <div class="chat-nameblock">
                                        <div class="chat-name">vanchenkin</div>
                                        <div class="chat-time">17:27</div>
                                    </div>
                                    <div class="chat-text">Всем привет!</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="chat-send">
                        <input type="text" name="message" class="chat-send-input" placeholder="Введите сообщение" autocomplete="off">
                        <div class="chat-send-button"><i class="fas fa-arrow-right chat-send-icon"></i></div>
                    </div>
                </div>
            </main>
        </div>
        <script>
            document.addEventListener("DOMContentLoaded", () => {
                $(function() {
                    $("body").removeClass("preload");
                });
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