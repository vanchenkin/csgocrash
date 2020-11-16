<template>
    <div class="content support">
    	<div class="page">
    		<div class="page-header">Техническая поддержка</div>
	   		<div class="page-body">
	   			<template v-if="tickets.length">
	   				<div class="tickets">
			   			<div class="ticket" v-for="ticket in tickets" :key="ticket.id">
		                    <div class="ticket-left">
		                        <div class="ticket-name">{{ ticket.name }}</div>
		                        <div class="ticket-text">{{ ticket.text }}</div>
		                    </div>
		                    <div class="ticket-right">
		                        <div class="ticket-status">{{ (ticket.status=="opened"?"Открыт":"Закрыт") }}</div>
		                        <div class="ticket-time">{{ ticket.created_at }}</div>
		                    </div>
		                </div>
		    		</div>
		    		<div class="ticket-new">
			            <input type="text" name="name" class="ticket-new-input" placeholder="Введите имя" maxlength="100" autocomplete="off" v-model="ticketName">
			            <input type="text" name="text" class="ticket-new-input" placeholder="Введите текст" maxlength="500" autocomplete="off" v-model="ticketText">
			            <div class="button ticket-new-button" @click="send" :class="{ 'button-disabled':message == '' }"><i class="fas fa-arrow-right ticket-new-icon"></i></div>
		    		</div>
		    		</template>
	    		<template v-else>
	    			<div class="support-guest">Для использования технической поддержки необходимо войти</div>
	    		</template>
	   		</div>
    	</div>
	    <div class="footer">
	        <div class="footer-right">
	            <router-link class="agreement" :to="{ path: '/agreement' }">Пользовательское соглашение</router-link>
	        </div>
	    </div>
    </div>
</template>

<script>
	import { mapState } from 'vuex';
    export default {
        data() {
            return {
            	ticketMessage: '',
            	ticketName: '',
            };
        },
        computed: {
            ...mapState(['tickets']),
        },
        watch: {
            
        },
        methods: {
            send: function(){
                if(this.ticketMessage == '' || this.ticketName == '') return;
                axios.get(APP_URL+'/api/ticket/new', {
                    params: {
                    	name: this.ticketName,
                        text: this.ticketMessage,
                    },
                })
                .then((req) => {
                    this.ticketName = '';
                    this.ticketMessage = '';
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