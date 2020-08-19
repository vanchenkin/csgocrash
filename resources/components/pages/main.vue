<template>
    <div class="content-block">
    	<div class="upper">
            <div class="left">
                <div class="graph"></div>
                <div class="stats">
                    <div class="stats-text">СТАТИСТИКА</div>
                    <div class="stats-right">
                        <div class="stat">
                            <i class="fas fa-dollar-sign stat-icon"></i>
                            <div class="stat-block">
                                <div class="stat-text">УЧАСТВУЕТ</div>
                                <div class="stat-value">$25.45</div>
                            </div>
                        </div>
                        <div class="stat">
                            <i class="fas fa-user stat-icon"></i>
                            <div class="stat-block">
                                <div class="stat-text">УЧАСТНИКОВ</div>
                                <div class="stat-value">10</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="right">
                <div class="skin">
                    <div class="skin-circle skin-i1"></div>
                    <div class="skin-circle skin-i2"></div>
                    <div class="skin-circle skin-i3"></div>
                    <div class="skin-circle skin-i4"></div>
                    <img class="skin-image r-knife" src="img/skin.png">
                </div>
                <div class="auto-text">АВТО-ВЫВОД</div>
                <div class="bet">
                    <input class="bet-input" placeholder="0.00" autocomplete="off" id="bet-input">
                    <div class="bet-button">
                        <div class="bet-button-text">НАЧАТЬ</div>
                        <div class="bet-button-money">$45.33</div>
                    </div>
                </div>
                <div class="autos">
                    <div class="auto">НЕТ</div>
                    <div class="auto">x1.2</div>
                    <div class="auto">x1.3</div>
                    <div class="auto">x1.5</div>
                    <div class="auto">x2</div>
                </div>
            </div>
        </div>
        <div class="games">
            <a class="game" href="#">1.23x</a>
            <a class="game" href="#">1.23x</a>
            <a class="game" href="#">1.23x</a>
        </div>
        <div class="bets">
            <div class="bet">
                <img class="bet-image" src="img/ava.jpg">
                <div class="bet-money">$45.12</div>
                <div class="bet-skins">
                    <img class="bet-skin r-white" src="img/skin.png">
                    <img class="bet-skin" src="img/skin.png">
                    <div class="bet-addskin">+3</div>
                </div>
                <div class="bet-coef">В ИГРЕ</div>
                <div class="bet-win">$48.12</div>
                <img class="bet-win-image" src="img/skin.png">
            </div>
            <div class="bet win">
                <img class="bet-image" src="img/ava.jpg">
                <div class="bet-money">$45.12</div>
                <div class="bet-skins">
                    <img class="bet-skin" src="img/skin.png">
                    <img class="bet-skin" src="img/skin.png">
                    <div class="bet-addskin">+3</div>
                </div>
                <div class="bet-coef">1.2x</div>
                <div class="bet-win">$48.12</div>
                <img class="bet-win-image r-white" src="img/skin.png">
            </div>
            <div class="bet lose">
                <img class="bet-image" src="img/ava.jpg">
                <div class="bet-money">$45.12</div>
                <div class="bet-skins">
                    <img class="bet-skin" src="img/skin.png">
                    <img class="bet-skin" src="img/skin.png">
                    <div class="bet-addskin">+3</div>
                </div>
                <div class="bet-coef">КРАШ</div>
                <div class="bet-win">$0</div>
                <img class="bet-win-image" src="img/skin.png">
            </div>
        </div>
        <div class="footer">
            <div class="footer-left">
                <div class="salt">Соль: ab432bab32</div>
                <div class="hash">Хэш раунда: 6641d30b1d826dc42f31556dcb922aa6fa12cb49e08418a3ccf9d7437b107b42</div>
            </div>
            <div class="footer-right">
                <router-link class="agreement" :to="{ path: '/agreement' }">Пользовательское соглашение</router-link>
            </div>
        </div>
    </div>
</template>

<script>
    import { mapState } from 'vuex';
    export default {
        data: () => ({
            checked: [],
            number: 2,
        }),
        mounted() {
            socket.on('timeLeft', (data) =>  {
                $('.cg_graph_block .time_left').removeClass('disable');
                $('.cg_graph_block .time_left span').html(data);
            });

            socket.on('startGame', (data) => { 
                startX = 0;
                scaleX = 20;
                scaleY = 150;
                cgBetStop();
                $('#gameid').html(data.id);
                $('.cg_hash_secret .secret').html('Скрыто');
                $('.cg_hash_secret .hash').html(data.hash);
            });

            socket.on('endGame', (data) => { 
                paintCrashGraphic(data.x, data.number);
            });

            socket.on('newBet', (data) => {
                store.commit('addBet', data);
                console.log(data);
            });

            socket.on('crashGraph', (x) => {
                paintCrashGraphic(x);
                $('.cg_graph_block .time_left').addClass('disable');
                cgBetStop();
            });
        },
        computed: {
            ...mapState(['isAuth', 'skins', 'bets', 'games', 'gameid']),
        },
        methods: {
            setChecked: function(checked){
                this.checked = checked;
            },
            newBet: function(){
                var tBet = [];
                for(var i in this.checked)
                    tBet.push(this.skins[this.checked[i]].id);
                axios.get(APP_URL+'/api/crash/bet', {
                    params: {
                        skins: JSON.stringify(tBet),
                        number: this.number,
                    }
                })
                .then((req) => {
                    $.notify(req.data.text);
                })
                .catch(() => {
                    $.notify('error with api');
                });
            }
        }
    }
</script>