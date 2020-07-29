var config  = require('./config.js'),
    io = require('socket.io')(config.chatServerPort),
    redisClient   = require('redis').createClient({
        host: '127.0.0.1',
        //password: '',
        port: 6379,
    }),
    req = require('requestify');

console.log('CHAT STARTED AT ' + config.domain + ':' + config.crashServerPort);

redisClient.psubscribe('crash.*');
redisClient.setMaxListeners(0);
redisClient.on("message", function(channel, message) {
    if(channel == 'crash.bet'){
        io.sockets.emit('newBet', message);
		message = JSON.parse(message);
		console.log('bet from '+message.username+' amount '+message.sum);
    }
});