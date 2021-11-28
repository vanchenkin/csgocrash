<template>
    <div class="skins-block" id="skins">
        <div class="skins-top">
            <div class="skins-m">
                <div class="skins-bal">
                    <div class="skins-bal-text">БАЛАНС</div>
                    <div class="skins-bal-value">$ {{ money.toFixed(2) }}</div>
                </div>
                <div class="skins-bal">
                    <div class="skins-bal-text">ВЫБРАНО</div>
                    <div class="skins-bal-value skins-bal-check">$ {{ sum.toFixed(2) }}</div>
                </div>
            </div>
            <div class="skins-checkall" @click="checkAll">ВЫБРАТЬ ВСЕ</div>
        </div>
        <div class="skins-items-wrapper">
            <div class="skins-items">
                <div class="skins-item" v-for="(skin,id) in skins" :key="id">
                    <input  type="checkbox" :id="id" v-model="checked" :value="id" class="skins-input hidden" :disabled="(isBuy && remain < skin.price && checked.indexOf(id) != -1) || skin.status == 'bet'">
                    <label  :for="id" class="skins-label"  :class="{ disabled : (isBuy && remain < skin.price && checked.indexOf(id) != -1) || skin.ingame}">
                        <div class="skins-price">$ {{ skin.price.toFixed(2) }}</div>
                        <span class="skins-image">
                            <img :class="'r-'+skin.rarity" :src="skin.image">
                        </span>
                        <div class="skins-weapon">{{ skin.weapon }}</div>
                        <div class="skins-name">{{ skin.name }}</div>
                        <div class="skins-quality">{{ skin.quality }}</div>
                    </label>
                </div>
            </div>
        </div>
        <div class="skins-buttons">
            <div class="button skins-button skins-gray" @click='withdraw'>ВЫВЕСТИ</div>
            <div class="button skins-button" @click='changeBuy'>ОБМЕНЯТЬ</div>
        </div>
        <div class="skins-modal blur" :class="{ hidden: !isBuy }">
            <div class="skins-modal-content">
                <div class="skins-modal-top">
                    <div class="skins-ost">
                        <div class="skins-ost-text">ОСТАТОК</div>
                        <div class="skins-ost-value">$ {{ remain.toFixed(2) }}</div>
                    </div>
                    <div class="skins-sort">
                        <input class="skins-sort-name skins-sort-input" type="text" placeholder="Введите название" v-model="query" @input="reload()">
                        <div class="skins-sort-price">
                            <input class="skins-sort-min skins-sort-input" type="text" placeholder="Мин.цена" v-model="min" @input="reload()">
                            <input class="skins-sort-max skins-sort-input" type="text" placeholder="Макс.цена" v-model="max" @input="reload()">
                        </div>
                    </div>
                </div>
                <div class="skins-items-wrapper" id="modal-items">
                    <template v-if="loading">
                        <div class="skins-loading"><div class="skins-loader"></div></div>
                    </template>
                    <template v-else-if="loadedSkins.length">
                        <div class="skins-items">
                            <div class="skins-item" v-for="(skin,id) in loadedSkins" :key="id">
                                <input  type="checkbox" :id="'ls.'+skin.id" :value="id" class="skins-input hidden" v-model="buyChecked" :disabled="remain < skin.price && buyChecked.indexOf(id) == -1">
                                <label :for="'ls.'+skin.id" class="skins-label" :class="{ disabled : remain < skin.price && buyChecked.indexOf(id) == -1 }">
                                    <div class="skins-price">$ {{ skin.price.toFixed(2) }}</div>
                                    <img class="skins-image" :class="'r-'+skin.rarity" :src="skin.image">
                                    <div class="skins-weapon">{{ skin.weapon }}</div>
                                    <div class="skins-name">{{ skin.name }}</div>
                                    <div class="skins-quality">{{ skin.quality }}</div>
                                </label>
                            </div>
                        </div>
                    </template>
                    <template v-else>
                        <div class="skins-notfound">СКИНОВ НЕ НАЙДЕНО</div>
                    </template>
                </div>
                <div class="skins-buttons">
                    <div class="button skins-button skins-gray" @click='changeBuy'>ЗАКРЫТЬ</div>
                    <div class="button skins-button" @click='buySkins' :class="{ 'button-disabled': remain == money }">ОБМЕНЯТЬ</div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import { mapState } from 'vuex';
    export default {
        data() {
            return {
                checked: [],
                sum: 0,
                remain: 0,
                isBuy: false,
                loadedSkins: [],
                buyChecked: [],
                page: 1,
                min: null,
                max: null,
                query: '',
                loading: true,
            };
        },
        computed: {
            ...mapState(['user', 'skins', 'money']),
        },
        created() {
            $(() => {
                let el = document.querySelector('#modal-items');
                if(el)
                    el.addEventListener('scroll', () => {
                        if (el.scrollTop + el.clientHeight >= el.scrollHeight - 5) {
                            this.loadMore();
                        }
                    });
                $(document).mouseup((e) => {
                    var div = $("#skins");
                    if (!div.is(e.target) && div.has(e.target).length === 0 && this.isBuy) {
                        this.changeBuy();
                    }
                });
            });
        },
        watch: {
            money(newValue, oldValue) {
                this.sum = 0;
                this.checked = [];
                this.isBuy = false;
            },
            sum(newValue, oldValue){
                this.remain += newValue - oldValue;
            },
            checked(newValue, oldValue){
                this.$store.commit('setChecked', newValue);
                var s = 0;
                for(var i in newValue)
                    s += this.skins[newValue[i]].price;
                this.sum = s;
            },
            buyChecked(newValue, oldValue){
                var s = 0;
                for(var i in newValue)
                    s += this.loadedSkins[newValue[i]].price;
                this.remain = this.sum + this.money - s;
            },
            '$store.state.checked'(nv){
                this.checked = nv;
            }
        },
        methods: {
            reload: function(){
                this.loading = true;
                this.loadedSkins = [];
                this.buyChecked = [];
                this.page = 1;
                this.remain = this.sum + this.money;
                this.loadMore();
            },
            checkAll: function(){
                var sum = 0;
                for(var i in this.skins)
                    if(!this.skins[i].ingame)
                        sum++;
                if(this.checked.length == sum)
                    this.checked = [];
                else{
                    var a = [];
                    for(var i in this.skins)
                        if(!this.skins[i].ingame)
                            a.push(i)
                    this.checked = a;
                }
            },
            changeBuy: function(){
                if(this.isBuy == false){
                    this.max = (this.sum + this.money);
                    this.reload();
                }
                this.isBuy = !this.isBuy;
            },
            loadMore: function(){
                axios.get(APP_URL+'/api/skins/get?page='+this.page, {
                    params: {
                        min: this.min,
                        max: this.max?this.max:1000000000,
                        query: this.query,
                    }
                })
                .then((req) => {
                    var pas = req.config.params;
                    if(pas.max != this.max || pas.min != this.min || pas.query != this.query || req.data.current_page != this.page) return;
                    this.loadedSkins = this.loadedSkins.concat(req.data.data);
                    this.page++;
                    this.loading = false;
                }).catch(() => {
                    notifyError('API error');
                });
            },
            withdraw: function(){
                // if(req.data.type == 'success')
                //     notifySuccess(req.data.text);
                // else
                //     notifyError(req.data.text);
            },
            buySkins: function(){
                var tBuy = [], tSell = [];
                for(var i in this.buyChecked)
                    tBuy.push({id: this.loadedSkins[this.buyChecked[i]].id, price: this.loadedSkins[this.buyChecked[i]].price});
                for(var i in this.checked)
                    tSell.push(this.skins[this.checked[i]].id);
                axios.get(APP_URL+'/api/skins/buy', {
                    params: {
                        skins: JSON.stringify(tBuy),
                        sell: JSON.stringify(tSell),
                    }
                })
                .then((req) => {
                    if(req.data.type == 'error'){
                        notifyError(req.data.text);
                    }else if(req.data.type == 'success'){
                        this.$store.commit('delSkins', req.data.del);
                        this.$store.commit('addSkins', req.data.add);
                        this.$store.commit('setMoney', req.data.money);
                    }
                    this.changeBuy();
                    this.checked = [];
                })
                .catch(() => {
                    notifyError('API error');
                });
            }
        },
    }
</script>