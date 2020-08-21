<template>
    <div class="draw">
        <div class="draw-header">
            <i class="fas fa-trophy draw-header-icon"></i>
            <div class="draw-header-text">БЕСПЛАТНЫЙ РОЗЫГРЫШ</div>
            <div class="draw-header-time">12:23</div>
        </div>
        <div class="draw-body">
            <img class="draw-body-image" src="img/draw.png">
            <div class="draw-body-r">
                <div class="draw-body-text">AWP</div>
                <div class="draw-body-stext">ИСТОРИЯ О ДРАКОНЕ</div>
                <div class="draw-body-ttext">(WELL-WORN)</div>
                <div class="button draw-body-button">УЧАСТВОВАТЬ</div>
            </div>
        </div>
        <div class="draw-count">
            <div class="draw-count-text">УЧАСТНИКОВ</div>
            <div class="draw-count-c">12</div>
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
            ...mapState(['']),
        },
        created() {
            
        },
        watch: {
            
        },
        methods: {
            
        },
    }
</script>