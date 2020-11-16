<template>
    <div class="chat">
        <div class="chat-header">
            <i class="fas fa-comments chat-header-icon"></i>
            <div class="chat-header-text">ОНЛАЙН ЧАТ</div>
            <router-link :to="{ path: '/chatrules' }" class="chat-header-rules">правила</router-link>
        </div>
        <div class="chat-body">
            <div class="chat-inner" id="chat">
                <div class="chat-message" v-for="message in messages" :key="message.id" :class="{ reversed:user.id == message.user.id }">
                    <router-link :to="{ name: 'user', params: { id: message.user.id } }"><img class="chat-image" :src="message.user.image"></router-link>
                    <div class="chat-block">
                        <div class="chat-nameblock">
                            <div class="chat-name" @click="reply(message.user.name)">{{ message.user.name }}</div>
                            <div class="chat-time">{{ message.time }}</div>
                        </div>
                        <div class="chat-text">{{ message.text }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="chat-send">
            <input type="text" name="message" class="chat-send-input" placeholder="Введите сообщение" maxlength="100" autocomplete="off" v-model="message">
            <div class="button chat-send-button" @click="send" :class="{ 'button-disabled':message == '' }"><i class="fas fa-arrow-right chat-send-icon"></i></div>
        </div>
    </div>
</template>

<script>

    import { mapState } from 'vuex';
    export default {
        data() {
            return {
                message: '',
                scroll: true,
            };
        },
        computed: {
            ...mapState(['user', 'messages']),
        },
        mounted() {
            $(window).resize(() => {
                $('#chat').scrollTop($('#chat')[0].scrollHeight);
            });
            socket.on('message', (data) => {
                store.commit('addMessage', data);
            });
            $('#chat').scroll(() => {
                this.scroll = ($('#chat').scrollTop() == $('#chat')[0].scrollHeight- $('#chat')[0].clientHeight);
            });
            document.addEventListener('keydown', (event) => {
                if (event.code == 'Enter') {
                    this.send();
                }
            });
        },
        updated() {
            if(this.scroll)
                this.$nextTick(() => $('#chat').scrollTop($('#chat')[0].scrollHeight));
        },
        watch: {
        },
        methods: {
            reply: function(name){
                this.message = name+', ';
            },
            send: function(){
                if(this.message == '') return;
                if(!this.user.auth) {
                    notifyError('need login');
                    return;
                }
                axios.get(APP_URL+'/api/chat/send', {
                    params: {
                        text: this.message,
                    },
                })
                .then((req) => {
                    this.message = '';
                    if(req.data.type == 'error')
                        notifyError(req.data.text);
                })
                .catch(() => {
                    notifyError('API error');
                });
            },
        },
    }
</script>