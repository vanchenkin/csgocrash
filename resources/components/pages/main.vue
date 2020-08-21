<template>
    <div class="content-block">
    	<div class="upper">
            <div class="left">
                <div class="graph">
                    <div class="ct-chart graph-chart" id="graph"></div>
                    <div class="graph-number">{{ number.toFixed(2) }}</div>
                </div>
                <div class="stats">
                    <div class="stats-text">СТАТИСТИКА</div>
                    <div class="stats-right">
                        <div class="stat">
                            <i class="fas fa-dollar-sign stat-icon"></i>
                            <div class="stat-block">
                                <div class="stat-text">УЧАСТВУЕТ</div>
                                <div class="stat-value">$ <animatedNumber :number="sum"></animatedNumber></div>
                            </div>
                        </div>
                        <div class="stat">
                            <i class="fas fa-user stat-icon"></i>
                            <div class="stat-block">
                                <div class="stat-text">УЧАСТНИКОВ</div>
                                <div class="stat-value"><animatedNumber :number="bets.length"></animatedNumber></div>
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
                    <input class="bet-input" placeholder="0.00" autocomplete="off" id="bet-input" v-model="auto">
                    <div class="button bet-button">
                        <div class="bet-button-text">{{ game.status=='current'?"ОСТАНОВИТЬ":"НАЧАТЬ" }}</div>
                        <div class="bet-button-money">$ <animatedNumber :number="betSum"></animatedNumber></div>
                    </div>
                </div>
                <div class="autos">
                    <div class="auto" :class="{ ac: auto == 0 }" @click="auto = 0">НЕТ</div>
                    <div class="auto" :class="{ ac: auto == 1.2 }" @click="auto = 1.2">x1.2</div>
                    <div class="auto" :class="{ ac: auto == 1.3 }" @click="auto = 1.3">x1.3</div>
                    <div class="auto" :class="{ ac: auto == 1.5 }" @click="auto = 1.5">x1.5</div>
                    <div class="auto" :class="{ ac: auto == 2 }" @click="auto = 2">x2</div>
                </div>
            </div>
        </div>
        <div class="games">
            <router-link v-for="game in games" :key="game.id" class="game" :class="getGameColor(game.number)" :to="{ name: 'game', params: { id: game.id } }">{{ game.number.toFixed(2) }}x</router-link>
        </div>
        <div class="bets">
            <div class="bet" v-for="bet in bets" :key="bet.id" :class="bet.status">
                <router-link :to="{ name: 'user', params: { id: bet.user.id } }"><img class="bet-image" :src="bet.user.image"></router-link>
                <div class="bet-money">$ {{ bet.sum }}</div>
                <div class="bet-skins">
                    <div class="bet-skins-item" v-for="skin in bet.skins.slice(0, 3)" :key="skin.id">
                        <img class="bet-skin" :class="'r-'+skin.skin.rarity" :src="skin.skin.image">
                        <div class="tooltip">{{ getFull(skin.skin) }}</div>
                    </div>
                    <div class="bet-addskin" v-if="bet.skins.length > 3">+{{ bet.skins.length-3 }}</div>
                </div>
                <div class="bet-coef">{{ bet.status=='ingame'?"В ИГРЕ":bet.status=="lose"?"КРАШ":bet.number }}</div>
                <div class="bet-win">$ {{ bet.win.price }}</div>
                <div class="bet-win-item" v-for="skin in bet.skins.slice(0, 3)" :key="skin.id">
                    <img class="bet-win-image" :class="'r-'+bet.win.rarity" :src="bet.win.image">
                    <div class="tooltip">{{ getFull(bet.win) }}</div>
                </div>
            </div>
        </div>
        <div class="footer">
            <div class="footer-left">
                <div class="salt">Соль: {{ game.salt?game.salt:"недоступно" }}</div>
                <div class="hash">Хэш раунда: {{ game.hash }}</div>
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
            number: 1,
            sum: 0,
            betSum: 0,
            auto: 0,
        }),
        mounted() {
            paintGraph(this.number);
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
                //paintCrashGraphic(data.x, data.number);
            });

            socket.on('newBet', (data) => {
                store.commit('addBet', data);
                console.log(data);
            });

            socket.on('crashGraph', (x) => {
                //paintCrashGraphic(x);
                $('.cg_graph_block .time_left').addClass('disable');
                cgBetStop();
            });
        },
        watch: {
            bets(nv, ov){
                this.sum = 0;
                for(var i of nv)
                    this.sum += i.sum;
            },
            checked(nv, ov){
                if(this.game.status == 'created'){
                    this.betSum = 0;
                    for(var i of nv)
                        this.betSum += this.skins[i].price;
                }
            },
            number(){
                paintGraph(this.number);
            }
        },
        computed: {
            ...mapState(['role', 'skins', 'bets', 'games', 'game', 'checked']),
        },
        methods: {
            setChecked: function(checked){
                this.checked = checked;
            },
            getFull: function(skin){
                return (skin.stattrak?"StatTrak™":"") + skin.weapon + ' | ' + skin.name + "(" + skin.quality + ")"
            },
            getGameColor: function(number){
                if(number < 1.2) return 'cf-red';
                if(number < 2) return 'cf-orange';
                if(number < 4) return 'cf-pink';
                if(number < 10) return 'cf-green';
                return 'cf-gold';
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