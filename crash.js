var config  = require('./config.js'),
    io = require('socket.io')(config.port),
    redisClient   = require('redis').createClient({
        host: '127.0.0.1',
        //password: '',
        port: 6379,
    }),
    req = require('requestify');

console.log('CSGOCRASH STARTED AT :' + config.port);

redisClient.psubscribe('*');
redisClient.setMaxListeners(0);
redisClient.on("pmessage", function(pattern, channel, message) {
    message = JSON.parse(message);
    if(channel == 'crash.bet'){
        io.sockets.emit('newBet', message);
    }
    if(channel == 'crash.stop'){
        io.sockets.emit('stopBet', message);
    }
    if(channel == 'crash.cancel'){
        io.sockets.emit('cancelBet', message);
    }
    if(channel == 'chat.message'){
        io.sockets.emit('message', message);
    }
    if(channel == 'draw.count'){
        io.sockets.emit('drawCount', message);
    }
});

var timer, ngtimer, game, online = 0;
io.on('connection', function(socket) {
    online+=1;
    io.sockets.emit('online', online);
    socket.on('disconnect', function() {
        online -=1;
        io.sockets.emit('online', online);
    });
});

function startTimer() {
    var x = 1;
    clearInterval(timer);
    timer = setInterval(function () {
        x = x + 0.1;
        var num  = config.crashFunction(x);
        if(num >= game.number){
            io.sockets.emit('crashGraph', game.number);
            console.log('x = '+game.number);
            clearInterval(timer);
    		finishGame();
            return;
    	}
        console.log('x = '+config.crashFunction(x));
        io.sockets.emit('crashGraph', config.crashFunction(x));
    }, 100);
}

function startNGTimer(){
    var time = config.timeLeft;
    clearInterval(ngtimer);
    ngtimer = setInterval(function(){
        io.sockets.emit('timeLeft', time);
        if(Math.trunc(time*100) === Math.trunc(time)*100)
		    console.log('New game in '+time.toFixed(2));
        time -= 0.1;
        if(time <= 0){
            clearInterval(ngtimer);
            startGame();
        }
    }, 100);
}

function getCurrentGame(){
    req.post(config.domain+'/api/crash/getCurrentGame', {
        secretKey: config.secretKey
    }).then(function(response) {
        game = JSON.parse(response.body);
        console.log('Current game! #' + game.id);
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
        console.log('New game! #' + game.id);
		io.sockets.emit('newGame', {
            id: game.id,
            hash: game.hash,
            status: 'created',
        });
		startNGTimer();
    },function(response){
        console.error('Что-то пошло не так при создании новой игры');
        setTimeout(newGame, 1000);
    });
}

function finishGame(){
    req.post(config.domain+'/api/crash/finishGame', {
        secretKey: config.secretKey
    })
    .then(function(response) {
		io.sockets.emit('endGame', {
            "number": game.number,
            "salt": game.salt,
            "winSkins" : JSON.parse(response.body),
        });
        console.log('game finished');
		setTimeout(newGame, config.endTime);
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
        io.sockets.emit('startGame');
        startTimer();
    },function(response){
        console.error('Что-то пошло не так при старте игры');
        setTimeout(startGame, 1000);
    });
}

if(config.game)
    getCurrentGame();