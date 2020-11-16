<template>
    <div class="content-block">
    	<div class="upper">
            <div class="left">
                <div class="graph">
                    <div class="ct-chart graph-chart" id="graph"></div>
                    <div class="graph-number" :class="{ lose: game.status=='finished' }">
                        <template v-if="game.status != 'created'">
                            {{ number.toFixed(2) }}x
                        </template>
                        <template v-else>
                            <animatedNumber :number="timeLeft*100" :tof="2" :back="false"></animatedNumber>
                        </template>
                    </div>
                </div>
                <div class="stats">
                    <div class="stats-text">СТАТИСТИКА</div>
                    <div class="stats-right">
                        <div class="stat">
                            <i class="fas fa-dollar-sign stat-icon"></i>
                            <div class="stat-block">
                                <div class="stat-text">УЧАСТВУЕТ</div>
                                <div class="stat-value">$ <animatedNumber :number="sum*100" :tof="2"></animatedNumber></div>
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
                    <template v-if="winSkin">
                        <img class="skin-image" :class="'r-'+winSkin.rarity" :src="winSkin.image">
                        <div class="tooltip">{{ getFull(winSkin) }}</div>
                    </template>
                    <template v-else>
                        <img class="skin-image r-knife" src="img/skin.png">
                    </template>
                </div>
                <div class="auto-text">АВТО-ВЫВОД</div>
                <div class="bet">
                    <input class="bet-input" placeholder="0.00" autocomplete="off" id="bet-input" :disabled="game.status != 'created'" v-model="auto">
                    <div @click="press" class="button bet-button" :class="{ 'button-disabled':getButtonDisabled()}">
                        <div class="bet-button-text">
                            {{ getButtonText() }}
                        </div>
                        <div class="bet-button-money">$ <animatedNumber :number="betSum*100" :tof="2"></animatedNumber></div>
                    </div>
                </div>
                <div class="autos">
                    <div class="auto" :class="{ ac: auto == 0 }" @click="putAuto(0)">НЕТ</div>
                    <div class="auto" :class="{ ac: auto == 1.2 }" @click="putAuto(1.2)">x1.2</div>
                    <div class="auto" :class="{ ac: auto == 1.3 }" @click="putAuto(1.3)">x1.3</div>
                    <div class="auto" :class="{ ac: auto == 1.5 }" @click="putAuto(1.5)">x1.5</div>
                    <div class="auto" :class="{ ac: auto == 2 }" @click="putAuto(2)">x2</div>
                </div>
            </div>
        </div>
        <div class="games">
            <router-link v-for="game in games" :key="game.id" class="game" :class="getGameColor(game.number)" :to="{ name: 'game', params: { id: game.id } }">{{ game.number.toFixed(2) }}x</router-link>
        </div>
        <div class="bets">
            <div class="bet" v-for="b in bets" :key="b.id" :class="b.status">
                <router-link :to="{ name: 'user', params: { id: b.user.id } }"><img class="bet-image" :src="b.user.image"></router-link>
                <div class="bet-money">$ {{ b.sum.toFixed(2) }}</div>
                <div class="bet-skins">
                    <div class="bet-skins-item" v-for="skin in b.skins.slice(0, 3)" :key="skin.id">
                        <img class="bet-skin" :class="'r-'+skin.rarity" :src="skin.image">
                        <div class="tooltip">{{ getFull(skin) }}</div>
                    </div>
                    <div class="bet-addskin" v-if="b.skins.length > 3">+{{ b.skins.length-3 }}</div>
                </div>
                <div class="bet-coef">{{ b.status=='ingame'?"В ИГРЕ":b.status=="lose"?"КРАШ":b.number.toFixed(2)+"x" }}</div>
                <template v-if="b.win">
                    <div class="bet-win">$ {{ b.win.price.toFixed(2) }}</div>
                    <div class="bet-win-item">
                        <img class="bet-win-image" :class="'r-'+b.win.rarity" :src="b.win.image">
                        <div class="tooltip">{{ getFull(b.win) }}</div>
                    </div>
                </template>
                <template v-else>
                    <div class="bet-win"></div>
                    <div class="bet-win-item">
                        <img class="bet-win-image">
                    </div>
                </template>
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
            timeLeft: 10,
            status: 0,
            bet: null,
            winSkin: null,
        }),
        mounted() {
            $(window).resize(() => {
                if(this.game.status == "current")
                    paintGraph(0, this.number);
            });
            socket.on('timeLeft', (data) =>  {
                this.timeLeft = data.toFixed(2);
            });

            socket.on('newGame', (data) => {
                clearGraph();
                store.commit('addGame', this.game);
                store.commit('setGame', data);
                this.number = 1;
                this.bet = null;
                this.betReSum();
                store.commit('setBets', []);
            });

            socket.on('startGame', (data) => {
                var g = this.game;
                g.status = 'current';
                store.commit('setGame', g);
                if(!this.bet) this.betSum = 0;
            });

            socket.on('endGame', (data) => {
                var g = this.game;
                g.salt = data.salt;
                g.number = data.number;
                g.status = 'finished';
                this.number = data.number;
                if(this.bet){
                    if(this.bet.status == 'win')
                        this.betSum = this.bet.win.price;
                    else
                        this.betSum = 0;
                    store.commit('delSkins', this.bet.skins);
                }
                for(var i in data.winSkins)
                    if(data.winSkins[i].user_id == this.user.id){
                        var skin = this.bet.win;
                        skin.id = data.winSkins[i].skin_id;
                        store.commit('addSkins', [skin]);
                    }
                store.commit('setGame', g);
                store.commit('loseBets');
            });

            socket.on('newBet', (data) => {
                store.commit('addBet', data);
            });

            socket.on('stopBet', (data) => {
                store.commit('changeBet', data);
                this.winSkin = this.bet.win;
            });

            socket.on('cancelBet', (data) => {
                store.commit('delBet', data.id);
            });

            socket.on('crashGraph', (x) => {
                x = parseFloat(x);
                this.number = x;
                if(this.bet && this.bet.status == 'ingame')
                    this.betSum = this.bet.sum * x;
                if(this.bet && this.bet.status == 'win')
                    this.betSum = this.bet.win.price;
                store.commit('winBets', this.number);
            });
        },
        watch: {
            bets(nv, ov){
                this.sum = 0;
                if(this.bets.length && this.user.auth && this.bets[0].user.id == this.user.id)
                    this.bet = this.bets[0];
                for(var i of nv)
                    this.sum += i.sum;
            },
            checked(nv, ov){
                if(this.game.status == 'created'){
                    this.betReSum();
                    this.getWinSkin();
                }
            },
            number(){
                paintGraph(0, this.number);
            },
            auto(){
                this.getWinSkin();
            },
            game(){
                if(this.game.status != 'current') clearGraph();
            },
            bet(){
                if(this.bet == null){
                    this.betReSum();
                    this.getWinSkin();
                }else{
                    this.betSum = this.bet.sum;
                }
            },
            betSum(){
                this.betSum = parseFloat(this.betSum).toFixed(2);
            }
        },
        computed: {
            ...mapState(['user', 'skins', 'bets', 'games', 'game', 'checked']),
        },
        methods: {
            getWinSkin: function(){
                if(this.game.status != 'created' || this.bet != null) return;
                if(this.betSum == 0 || this.auto <= 1 || this.auto > 10000){
                    this.winSkin = null;
                    return;
                }
                axios.get(APP_URL+'/api/crash/getWin', {
                    params: {
                        number: this.auto,
                        sum: this.betSum,
                    },
                })
                .then((req) => {
                    if(req.config.params.number != this.auto || req.config.params.sum != this.betSum) return;
                    this.winSkin = req.data;
                })
                .catch(() => {
                    notifyError('API error');
                });
            },
            getButtonText: function(){
                if(this.getButtonDisabled()) return "ОЖИДАНИЕ";
                else if(this.game.status=='current' && this.bet && (this.bet.number >= this.number || this.bet.number == 0)) return "ОСТАНОВИТЬ";
                else if(this.game.status == 'created' && this.bet) return "ОТМЕНИТЬ"
                else return "НАЧАТЬ";
            },
            getButtonDisabled: function(){
                if(this.game.status == 'finished' || this.betSum == 0 || this.bet && this.bet.status != 'ingame') return true;
                else return false;
            },
            putAuto: function(auto){
                if(this.game.status != 'created') return;
                this.auto = auto;
            },
            betReSum: function(){
                if(this.bet) return;
                this.betSum = 0;
                for(var i of this.checked)
                    this.betSum += this.skins[i].price;
            },
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
                for(var i of this.checked)
                    tBet.push(this.skins[i].id);
                axios.get(APP_URL+'/api/crash/bet', {
                    params: {
                        skins: JSON.stringify(tBet),
                        number: this.auto,
                    },
                })
                .then((req) => {
                    store.commit('setChecked', []);
                    var skins = this.skins;
                    for(var i in skins)
                        if(tBet.includes(skins[i].id))
                            skins[i].ingame = true;
                    store.commit('setSkins', skins);
                    if(req.data.type == 'error')
                        notifyError(req.data.text);
                })
                .catch(() => {
                    notifyError('API error');
                });
            },
            stopBet: function(){
                axios.get(APP_URL+'/api/crash/stop', {
                    params: {
                        number: this.number,
                    }
                })
                .then((req) => {
                    if(req.data.type == 'error')
                        notifyError(req.data.text);
                })
                .catch(() => {
                    notifyError('API error');
                });
            },
            cancelBet: function(){
                axios.get(APP_URL+'/api/crash/cancel', {
                })
                .then((req) => {
                    var skins = this.skins;
                    for(var i in skins)
                        skins[i].ingame = false;
                    store.commit('setSkins', skins);
                    this.bet = null;
                    if(req.data.type == 'error')
                        notifyError(req.data.text);
                })
                .catch(() => {
                    notifyError('API error');
                });
            },
            press: function(){
                if(this.game.status == 'created' && !this.bet) this.newBet();
                else if(this.game.status == 'created') this.cancelBet();
                else if(this.game.status == 'current') this.stopBet();
            }
        }
    }
</script>