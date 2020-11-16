var config = {
    port: 7777,
    domain: 'http://localhost/csgocrash',
    secretKey: 'test',
    func: (x) => Math.pow(1.06, x).toFixed(2),
    endTime: 5000,
    timeLeft: 10,
    game: false,
}

module.exports = config;