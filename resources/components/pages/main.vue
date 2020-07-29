<template>
	<div class="container">
        <skins @checkedEvent="setChecked"></skins>
	    <div class="row justify-content-center">
            <div class="total-game">
                <div class="page cg_block">
                    <div class="game-info" style="height:86px;">
                        <div class="game-info-title">
                            <div class="game-price">
                                <div class="game-price-inner">
                                    <div class="game-price-content">
                                        <p class="box">
                                            <span class="game-id">ИГРА <span>#<span id="gameid">{{ gameid }}</span></span>
                                            </span>
                                            <span class="game-bank">БАНК<span class="space">:</span><span id="roundBank4"></span><span style="font-size: 14px; padding-left: 3px; color: #ccc;">РУБ</span></span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="content_block_body page_block_body cg_body page_block whiteBg clearfix">
                        <div class="cg_graph_block">
                            <canvas id="crashGraphic" width="450" height="350"></canvas>

                            <div class="time_left disable">Следующий раунд через: <span>0</span></div>
                        </div>

                        <div class="cg_user_bet_panel">
                            <div class="cg_input_place">
                                <label for="cg_number" style="color:orange" >Число для вывода</label>
                                <input type="text" name="cg_number" id="cg_number" value="" placeholder="Введите число для вывода" v-model="number">
                            </div>
                            <button class="cg_bet_button" @click="newBet">Поставить</button>
                            <button class="cg_bet_button" @click="cancelBet">Отменить ставку</button>
                            <div class="cg_hash_secret">
                                <div class="cg_hash">
                                    <span class="param">Хеш:</span> <span class="hash"></span>
                                </div>
                                <div class="cg_secret">
                                    <span class="param">Секрет:</span> <span class="secret">Скрыто</span>
                                </div>
                                <div class="hash_label">(Хеш = md5 (Секрет Итоговое число))</div>
                            </div>
                        </div>

                        <div class="cg_users_and_history clearfix">
                            <div class="slimScrollDiv" style="position: relative; overflow: hidden; width: 450px; height: 310px;">
                                <div class="cg_history_block">
                                	<div class="cg_bet_title">История</div>
	                                <div class="game1 clearfix">
	                                    <div class="num">#</div>
	                                    <div class="secret">секрет</div>
	                                    <div class="number">x</div>
	                                    <div class="hash">хэш</div>
	                                </div>
	                                <div class="gameshist">
                                        <div class="game1 clearfix" v-for="(game,id) in games" :key="id" >
                                            <div class="num">{{ game.id }}</div>
                                            <div class="secret">секрет</div>
                                            <div class="number">x</div>
                                            <div class="hash">хэш</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="cg_users_block">
                                <div class="slimScrollDiv mCSB_container" id="usersList" style="position: relative; overflow: hidden; width: 450px; height: 310px;">
                                    <div class="cg_bet_place">
	                                    <div class="cg_bet_title">Участники</div>
	                                    <div class="cg_bet1 clearfix">
		                                    <div class="img"><img src=""></div>
		                                    <div class="name">игрок</div>
		                                    <div class="bet_num">x</div>
		                                    <div class="bet_sum">ставка</div>
	                                    </div>
	                                    <div class="betshist">
		                                    <div class="cg_bet clearfix" v-for="(bet,id) in bets" :key="id" >
			                                    <div class="img"><img :src="bet.image"></div>
			                                    <div class="name">{{ bet.username }}</div>
			                                    <div class="bet_num">{{ (bet.openNumber?bet.openNumber:'??') }}</div>
			                                    <div class="bet_sum">{{ bet.sum }}</div>
		                                    </div>
	                                    </div>
                                    </div>
                            	</div>
                            </div>
                        </div>
                    </div>
                </div>
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
            var socketCG = io.connect(':7777', {
                secure: true,
                'force new connection': true
            });

            socketCG.on('timeLeft', (data) =>  {
                $('.cg_graph_block .time_left').removeClass('disable');
                $('.cg_graph_block .time_left span').html(data);
            });

            socketCG.on('startGame', (data) => { 
                startX = 0;
                scaleX = 20;
                scaleY = 150;
                cgBetStop();
                $('#gameid').html(data.id);
                $('.cg_hash_secret .secret').html('Скрыто');
                $('.cg_hash_secret .hash').html(data.hash);
            });

            socketCG.on('endGame', (data) => { 
                paintCrashGraphic(data.x, data.number);
            });

            socketCG.on('newBet', (data) => {
                store.commit('addBet', data);
                console.log(data);
            });

            socketCG.on('crashGraph', (x) => {
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