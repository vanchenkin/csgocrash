var config = {
    port: 7777,
    domain: 'http://localhost/csgocrash',
    secretKey: 'test',
    crashFunction: (x) => Math.pow(1.06, x).toFixed(2),
    endTime: 5000,
    timeLeft: 10,
    game: true,
}

module.exports = config;