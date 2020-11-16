<template>
    <div class="content">
        
    </div>
</template>

<script>
    import { mapState } from 'vuex';
    export default {
        data() {
            return {
                
            };
        },
        mounted() {
            
        },
        watch: {
        	$route(to, from) {
        		this.reload(to.id);
			}
        },
        methods: {
            reload: function(id){
                
            },
            send: function(){
                axios.get(APP_URL+'/api/game/get', {
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