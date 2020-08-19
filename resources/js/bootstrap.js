try {
    window.$ = window.jQuery = require('jquery');
} catch (e) {}

window.notify = require('./notify.min.js');

window.axios = require('axios');
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

window.io = require('socket.io-client');