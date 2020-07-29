<template>
	<div style='color:black'>
        <div class="skins_auth" v-if="isAuth">
            <div class="skins_item" v-for="(skin,id) in skins" :key="id">
                <input  type="checkbox" :id="skin.id" v-model="checked" :value="id" class="skins_input hidden" @change="compSum" :disabled="remain < skin.price && checked.indexOf(id) != -1 || skin.status == 'bet'">
                <label  :for="skin.id" class="skins_label"  :class="{ disabled : remain < skin.price && checked.indexOf(id) != -1 || skin.status == 'bet'}">
                    <div class="skin_name">{{ skin.name }}</div>
                    <div class="skin_price">{{ skin.price }}</div>
                </label>
            </div>
            <button type="button" class="btn btn-primary" @click='changeBuy'>
              Buy
            </button>
            <div :class="{ hidden: isBuy }">Баланс: {{ money }}</div>
            <div :class="{ hidden: !isBuy }">
                <div class="modal_content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">Обмен</h5>
                    </div>
                    <div class="buy_money">Остаток: {{ remain }}</div>
                    <div class="modal_body" id="modal_body">
                        <div class="skins_item" v-for="(skin,id) in loadedSkins" :key="id">
                            <input  type="checkbox" :id="'ls.'+skin.id" :value="id" class="skins_input hidden"  v-model="buyChecked" @change="compRemain" :disabled="remain < skin.price && buyChecked.indexOf(id) == -1">
                            <label :for="'ls.'+skin.id" class="skins_label" :class="{ disabled : remain < skin.price && buyChecked.indexOf(id) == -1 }">
                                <div class="skin_name">{{ skin.name }}</div>
                                <div class="skin_price">{{ skin.price }}</div>
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-dismiss="modal" @click='buySkins' :disabled="remain == sum">Change</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal" @click='changeBuy'>Close</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="skins_guest" v-else>
            вы не вошли в аккаунт
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
                this.$emit('checkedEvent', this.checked);
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