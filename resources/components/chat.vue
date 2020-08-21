<template>
    <div class="chat">
        <div class="chat-header">
            <i class="fas fa-comments chat-header-icon"></i>
            <div class="chat-header-text">ОНЛАЙН ЧАТ</div>
            <div class="chat-header-rules">правила</div>
        </div>
        <div class="chat-body">
            <div class="chat-inner">
                <div class="chat-message">
                    <a href="#"><img class="chat-image" src="img/ava.jpg"></a>
                    <div class="chat-block">
                        <div class="chat-nameblock">
                            <a class="chat-name" href="#">vanchenkin</a>
                            <div class="chat-time">17:27</div>
                        </div>
                        <div class="chat-text">Всем привет!</div>
                    </div>
                </div>
                <div class="chat-message reversed">
                    <img class="chat-image" src="img/ava.jpg">
                    <div class="chat-block">
                        <div class="chat-nameblock">
                            <div class="chat-name">vanchenkin</div>
                            <div class="chat-time">17:27</div>
                        </div>
                        <div class="chat-text">Всем привет!</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="chat-send">
            <input type="text" name="message" class="chat-send-input" placeholder="Введите сообщение" autocomplete="off">
            <div class="button chat-send-button"><i class="fas fa-arrow-right chat-send-icon"></i></div>
        </div>
    </div>
</template>

<script>
    import { mapState } from 'vuex';
    export default {
        data() {
            return {
                
            };
        },
        computed: {
            ...mapState(['role']),
        },
        created() {
            
        },
        watch: {
            
        },
        methods: {
            
        },
    }
</script>