<template>
  <span>{{(displayNumber/Math.pow(10, tof)).toFixed(tof)}}</span>
</template>

<script>
    export default {
        data() {
            return {
                displayNumber: 0,
                interval: false,
            }
        },
        props:{
            'number':{default:0},
            'tof':{default:0},
            'back':{default:true},
        },
        ready:function(){
            this.displayNumber = this.number ? this.number : 0;
        },
        watch:{
            number: function(){
                clearInterval(this.interval);
                var a = parseFloat(this.number).toFixed(this.tof), b = parseFloat(this.displayNumber).toFixed(this.tof);
                if(a == b){
                    return;
                }
                this.interval = window.setInterval(() => {
                    if(!this.back && a < b){
                        this.displayNumber = this.number;
                        clearInterval(this.interval);
                    }
                    if(a != b){
                        var change = (this.number - this.displayNumber) / 10;
                        change = change >= 0 ? Math.ceil(change) : Math.floor(change);
                        this.displayNumber = this.displayNumber + change;
                    }
                }, 20);
            },
        },
    }
</script>