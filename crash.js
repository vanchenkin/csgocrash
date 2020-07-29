var config  = require('./config.js'),
    io = require('socket.io')(config.crashServerPort),
    redisClient   = require('redis').createClient({
        host: '127.0.0.1',
        //password: '',
        port: 6379,
    }),
    req = require('requestify');

console.log('CRASH STARTED AT ' + config.domain + ':' + config.crashServerPort);

redisClient.psubscribe('crash.*');
redisClient.setMaxListeners(0);
redisClient.on("pmessage", function(channel, message) {
    if(channel == 'crash.bet'){
        io.sockets.emit('newBet', message);
		message = JSON.parse(message);
		console.log('bet from '+message.username+' amount '+message.sum);
    }
    if(channel == 'crash.stop'){
        io.sockets.emit('stopBet', message);
    }
});

var timer, ngtimer;
function startTimer() {
    var x = 1;
    clearInterval(timer);
    timer = setInterval(function () {
        x = x + 0.1;
        var num  = config.mathfunc(x);
        if(num >= game.number){
    		finishGame();
    		clearInterval(timer);
    	}
        console.log('X равен '+num);
        io.sockets.emit('crashGraph', x);
    }, 100);
}
function startNGTimer(){
    var time = 25;
    clearInterval(ngtimer);
    ngtimer = setInterval(function(){
        io.sockets.emit('timeLeft', time);
		console.log('Новая игра через '+time);
        time--;
        if(time <= 0){
            //clearInterval(ngtimer);
            //startGame();
        }
    }, 1000);
}
function getCurrentGame(){
    req.post(config.domain+'/api/crash/getCurrentGame', {
        secretKey: config.secretKey
    }).then(function(response) {
        game = JSON.parse(response.body);
        console.log('Текущая игра! #' + game.id);
		if(game.status == 'created') startNGTimer();
        if(game.status == 'current') startTimer();
        if(game.status == 'finished') newGame();
    },function(response){
        console.error('Что-то пошло не так при получении текущей игры');
        setTimeout(getCurrentGame, 1000);
    });
}
function newGame(){
    req.post(config.domain+'/api/crash/newGame', {
        secretKey: config.secretKey
    }).then(function(response) {
        game = JSON.parse(response.body);
        console.log('Новая игра! #' + game.id);
		io.sockets.emit('startGame', {id: game.id});
		setTimeout(startNGTimer, 1000);
    },function(response){
        console.error('Что-то пошло не так при создании новой игры');
        setTimeout(newGame, 1000);
    });
}
function finishGame(x){
    req.post(config.domain+'/api/crash/finishGame', {
        secretKey: config.secretKey
    })
    .then(function(response) {
		io.sockets.emit('endGame', {
            "id": game.id,
            "number": game.number,
            "x": x,
            "salt": game.salt,
        });
		setTimeout(newGame, 4000);
    },function(response){
        console.error('Что-то пошло не так при завершении игры');
        setTimeout(finishGame, 1000);
    });
}
function startGame(status){
    req.post(config.domain+'/api/crash/startGame', {
        secretKey: config.secretKey
    }).then(function(response) {
        game = JSON.parse(response.body);
        startTimer();
    },function(response){
        console.error('Что-то пошло не так при старте игры');
        setTimeout(startGame, 1000);
    });
}
getCurrentGame();