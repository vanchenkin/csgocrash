var redisClient   = require('redis').createClient({
        host: '127.0.0.1',
        password: 123,
        port: 6379,
    }),
    req = require('requestify');

redisClient.psubscribe('*');
redisClient.on("pmessage", function(channel, message) {
    console.log(1);
});