<template>
    <div class="skins-block">
        <div class="skins-top">
            <div class="skins-m">
                <div class="skins-bal">
                    <div class="skins-bal-text">БАЛАНС</div>
                    <div class="skins-bal-value">$0.00</div>
                </div>
                <div class="skins-bal">
                    <div class="skins-bal-text">ВЫБРАНО</div>
                    <div class="skins-bal-value">$0.00</div>
                </div>
            </div>
            <div class="skins-checkall">ВЫБРАТЬ ВСЕ</div>
        </div>
        <div class="skins-item" v-for="(skin,id) in skins" :key="id">
            <input  type="checkbox" :id="skin.id" v-model="checked" :value="id" class="hidden" @change="compSum" :disabled="remain < skin.price && checked.indexOf(id) != -1 || skin.status == 'bet'">
            <label  :for="skin.id" class="skins-label"  :class="{ disabled : remain < skin.price && checked.indexOf(id) != -1 || skin.status == 'bet'}">
                <div class="skin-name">{{ skin.name }}</div>
                <div class="skin-price">{{ skin.price }}</div>
            </label>
        </div>
        <button class="skins-button" @click='changeBuy'>Обменять</button>
        <button class="skins-button" @click=''>Получить</button>
        <div class="skins-money" :class="{ hidden: isBuy }">Баланс: {{ money }} $</div>
        <div class="skins-modal" :class="{ hidden: !isBuy }">
            <div class="skins-modal-content">
                <div class="skins-ost">Остаток: {{ remain }}</div>
                <div class="skins-modal-body">
                    <div class="skins-item" v-for="(skin,id) in loadedSkins" :key="id">
                        <input  type="checkbox" :id="'ls.'+skin.id" :value="id" class="hidden"  v-model="buyChecked" @change="compRemain" :disabled="remain < skin.price && buyChecked.indexOf(id) == -1">
                        <label :for="'ls.'+skin.id" class="skins-label" :class="{ disabled : remain < skin.price && buyChecked.indexOf(id) == -1 }">
                            <div class="skin-name">{{ skin.name }}</div>
                            <div class="skin-price">{{ skin.price }}</div>
                        </label>
                    </div>
                </div>
                <div class="skins-modal-footer">
                    <div class="" @click='buySkins' :disabled="remain == sum">Обменять</div>
                    <div class="" @click='changeBuy'>Закрыть</div>
                </div>
            </div>
        </div>
    </div>
</template>

<style>
    .modal_body{
        height:  300px;
        overflow: auto;
    }
    .modal_content{
        width:  500px;
    }
    .skins_item{
        display: inline-block;
    }
    .skins_label{
        background-color: lightblue;
        padding: 10px;
        margin: 5px;
    }
    .skins_input:checked ~ .skins_label{
        background-color: green;
    }
    .hidden{
        display: none;
    }
    .disabled{
        background-color: grey;
    }
</style>

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
            };
        },
        computed: {
            ...mapState(['isAuth', 'skins', 'money']),
        },
        created() {
            $(() => {
                let el = document.querySelector('#modal_body');
                if(el)
                el.addEventListener('scroll', () => {
                    if (el.scrollTop + el.clientHeight >= el.scrollHeight) {
                        this.loadMore();
                    }
                });
            });
        },
        mounted() {
            this.sum = parseInt(this.money);
        },
        watch: {
            money(newValue, oldValue) {
                this.sum = parseInt(this.money);
                this.checked = [];
                this.isBuy = false;
            },
            sum(newValue, oldValue){
                this.remain += newValue - oldValue;
                if(this.remain < 0){
                    this.buyChecked = [];
                    this.remain = this.sum;
                }
            },
            checked(newValue, oldValue){
                this.$store.commit('setChecked', newValue);
            }
        },
        methods: {
            compSum: function(event){
                if(!event.target.checked) this.sum-=this.skins[event.target.value].price;
                else this.sum+=this.skins[event.target.value].price;
            },
            compRemain: function(event){
                var id = event.target.value;
                if(!event.target.checked) this.remain+=this.loadedSkins[id].price;
                else this.remain-=this.loadedSkins[id].price;
            },
            changeBuy: function(){
                this.loadedSkins = [];
                this.buyChecked = [];
                if(this.isBuy == false){
                    this.page = 1;
                    this.loadMore();
                    this.remain = this.sum;
                }
                this.isBuy = !this.isBuy;
            },
            loadMore: function(){
                axios.get(APP_URL+'/api/skins/get?page='+this.page)
                .then((req) => {
                    this.loadedSkins = this.loadedSkins.concat(req.data.data);
                    this.page++;
                })
                .catch(() => {
                    $.notify('error with api');
                });
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
                        $.notify('error with data');
                    }else if(req.data.type == 'success'){
                        $.notify('OK');
                        this.$store.commit('delSkins', req.data.del);
                        this.$store.commit('addSkins', req.data.add);
                        this.$store.commit('setMoney', req.data.money);
                    }
                    this.changeBuy();
                    this.checked = [];
                })
                .catch(() => {
                    $.notify('error with api');
                });
            }
        },
    }
</script>