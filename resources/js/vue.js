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
        skins: [],
        money: 0,
        games: [],
        game: [],
        bets: [],
        role: 'no',
        checked: [],
        user: [],
        messages: [],
        draw: [],
        tickets: [],
    },
    mutations: {
        setSkins(state, skins){
            state.skins = skins
            state.skins.sort((a,b) => {
                if(a.price < b.price) return 1;
                else if(a.price > b.price) return -1;
                else return (a.name < b.name) ? 1 : -1;
            });
        },
        setMessages(state, messages){
            state.messages = messages;
        },
        addMessage(state, message){
            state.messages.push(message);
            state.messages = state.messages.splice(0, 80);
        },
        setChecked(state, checked){
            state.checked = checked;
        },
        setGames(state, games){
            state.games = games;
            state.games = state.games.splice(0, 20);
        },
        addGame(state, game){
            state.games.unshift(game);
            state.games = state.games.splice(0, 20);
        },
        setBets(state, bets){
            state.bets = bets;
            state.bets.sort((a,b) => {
                if(a.user.id == 4) return -1;
                else if(b.user.id == 4) return 1;
                else return (a.sum < b.sum ? 1 : -1);
            });
        },
        setMoney(state, money){
            state.money = money;
        },
        setGame(state, game){
            state.game = game;
        },
        setUser(state, user){
            state.user = user;
        },
        setTickets(state, tickets){
            state.tickets = tickets;
        },
        addSkins(state, skins){
            state.skins = state.skins.concat(skins);
            state.skins.sort((a,b) => {
                if(a.price < b.price) return 1;
                else if(a.price > b.price) return -1;
                else return (a.name < b.name) ? 1 : -1;
            });
        },
        addBet(state, bet){
            state.bets.push(bet);
        },
        delSkins(state, skins){
            for(var i in skins){
                var index = state.skins.findIndex((el) => {
                    return (el.id == skins[i].id);
                });
                if(index != -1)
                    state.skins.splice(index, 1);
            }
            state.skins.sort((a,b) => {
                if(a.price < b.price) return 1;
                else if(a.price > b.price) return -1;
                else return (a.name < b.name) ? 1 : -1;
            });
        },
        delBet(state, id){
            var index = state.bets.findIndex((el) => {
                return (el.id == id);
            });
            if(index != -1)
                state.bets.splice(index, 1);
            state.bets.sort((a,b) => {
                if(a.user.id == 4) return -1;
                else if(b.user.id == 4) return 1;
                else return (a.sum < b.sum ? 1 : -1);
            });
        },
        changeBet(state, bet){
            var index = state.bets.findIndex((el) => {
                return (el.id == bet.id);
            });
            if(index != -1){
                Vue.set(state.bets[index], 'win', bet.win);
                Vue.set(state.bets[index], 'number', bet.number);
            }
        },
        winBets(state, number){
            for(var i in state.bets){
                if(state.bets[i].number <= number && state.bets[i].number != 0)
                    Vue.set(state.bets[i], 'status', 'win');
            }
        },
        loseBets(state, number){
            for(var i in state.bets){
                if(state.bets[i].status != 'win')
                    Vue.set(state.bets[i], 'status', 'lose');
            }
        },
        setDraw(state, draw){
            state.draw = draw;
        },
        takeDraw(state){
            state.draw.take = true;;
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
            path: '/game/:id',
            component: vue_game,
            name: 'game',
        },
        {
            path: '/user/:id',
            component: vue_user,
            name: 'user',
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
            path: '/chatrules',
            component: vue_chatrules,
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
    data: {
        bSound: true,
    },
    watch: {
        '$store.state.money': function(nv, ov) {
            $("#money").text(nv.toFixed(2));
        },
    },
});