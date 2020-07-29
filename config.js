var config = {
    crashServerPort: 7777,
    chatServerPort: 2020,
    domain: 'http://localhost/csgocrash',
    secretKey: 'test',
    mathfunc: function(x){
    	return Math.pow(1.06, x).toFixed(2);
    },
}

module.exports = config;