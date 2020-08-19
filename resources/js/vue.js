import Vue from 'vue'
import VueRouter from 'vue-router'
import Vuex from 'vuex'

Vue.use(VueRouter);
Vue.use(Vuex);

let files = require.context('../components/pages', true, /\.vue$/i);
files.keys().map(key => window['vue_'+key.split('/').pop().split('.')[0]] = files(key).default);

files = require.context('../components', false, /\.vue$/i);
files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default));

window.store = new Vuex.Store({
    state: {
        skins: null,
        money: 0,
        games: null,
        gameid: null,
        bets: null,
        isAuth: false,
        checked: null,
    },
        mutations: {
        setSkins(state, skins){
            state.skins = skins;
        },
        setChecked(state, checked){
            state.checked = checked;
        },
        setGames(state, games){
            state.games = games;
        },
        setBets(state, bets){
            state.bets = bets;
        },
        setMoney(state, money){
            state.money = money;
        },
        setGameid(state, gameid){
            state.gameid = gameid;
        },
        setAuth(state){
            state.isAuth = true;
        },
        addSkins(state, skins){
            state.skins = state.skins.concat(skins);
        },
        delSkins(state, skins){
            for(var i in skins){
                var index = state.skins.findIndex((el) => {
                    return (el.id == skins[i]);
                });
                if(index != -1)
                    state.skins.splice(index, 1);
            }
        },
        addBet(state, bet){
            state.bet.push(bet);
        },
    }
})

const router = new VueRouter({
    base: window.APP_REL_URL,
    mode: 'history',
    routes: [
        {
            path: '/',
            component: vue_main,
        },
        {
            path: '/game',
            component: vue_game,
        },
        {
            path: '/user',
            component: vue_user,
        },
        {
            path: '/support',
            component: vue_support,
        },
        {
            path: '/faq',
            component: vue_faq,
        },
        {
            path: '/fair',
            component: vue_fair,
        },
        {
            path: '/agreement',
            component: vue_agreement,
        },
        {
            path: '*',
            component: vue_404,
        },
    ],
});

import { mapState } from 'vuex';

window.vm = new Vue({
    el: '#app',
    router,
    store,
    watch: {
        '$store.state.money': function(nv, ov) {
            $("#money").text(nv);
        },
    },
});