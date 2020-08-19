var config = {
    port: 7777,
    domain: 'http://localhost/csgocrash',
    secretKey: 'test',
    mathfunc: function(x){
    	return Math.pow(1.06, x).toFixed(2);
    },
}

module.exports = config;