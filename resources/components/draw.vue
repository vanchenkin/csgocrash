<template>
    <div class="draw">
        <div class="draw-header">
            <i class="fas fa-trophy draw-header-icon"></i>
            <div class="draw-header-text">БЕСПЛАТНЫЙ РОЗЫГРЫШ</div>
            <div class="draw-header-time" v-if="draw && draw.time > 0">
                {{ ('00'+(time/60/60>>0)).slice(-2) }}:{{ ('00'+(time/60>>0)%60).slice(-2) }}:{{ ('00'+(time>>0)%60).slice(-2) }}
            </div>
        </div>
        <div class="draw-body" v-if="draw && draw.time > 0">
            <img class="draw-body-image" :class="'r-'+draw.skin.rarity" :src="draw.skin.image">
            <div class="draw-body-r">
                <div class="draw-body-text">{{ draw.skin.weapon }}</div>
                <div class="draw-body-stext">{{ draw.skin.name.toUpperCase() }}</div>
                <div class="draw-body-ttext">{{ draw.skin.quality }}</div>
                <div class="button draw-body-button" @click="take" :class="{ disabled: draw.take }">{{ draw.take?"УЧАСТВУЕМ":"УЧАСТВОВАТЬ" }}</div>
            </div>
        </div>
        <div class="draw-no" v-else>
            <i class="fas fa-hand-paper draw-no-icon"></i>
            <div class="draw-no-text">
                В ДАННЫЙ МОМЕНТ</br> КОНКУРС НЕ ПРОВОДИТСЯ
            </div>
        </div>
        <div class="draw-count">
            <div class="draw-count-text">УЧАСТНИКОВ</div>
            <div class="draw-count-c">{{ draw && draw.time > 0?count:"НЕТ" }}</div>
        </div>
        <div class="draw-last">
            <div class="draw-last-text">ПОСЛЕДНИЙ ПОБЕДИТЕЛЬ</div>
            <div class="draw-last-c">
                <router-link :to="{ name: 'user', params: { id: draw.last_winner.id } }" v-if="draw && draw.last_winner">
                    <img class="draw-last-image"  :src="draw.last_winner.image">
                </router-link>
                <template v-else>НЕТ</template>
            </div>
        </div>
    </div>
</template>

<script>
    import { mapState } from 'vuex';
    export default {
        data() {
            return {
                count: 0,
                time: 0,
            };
        },
        computed: {
            ...mapState(['draw', 'user']),
        },
        watch:{
            draw(){
                if(this.draw){
                    this.count = this.draw.count;
                    this.time = this.draw.time;
                }
            },
        },
        mounted() {
            socket.on('drawCount', (data) => {
                this.count = data;
            });
            setInterval(() => {this.time--}, 1000);
        },
        methods: {
            take: function(){
                axios.get(APP_URL+'/api/draw/take')
                .then((req) => {
                    if(req.data.type = "success"){
                        store.commit('takeDraw');
                    }
                })
                .catch(() => {
                    notifyError('API error');
                });
            },
        },
    }
</script>