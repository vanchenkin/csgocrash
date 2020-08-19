require('./bootstrap');

window.socket = io.connect(':7777', {secure: true, 'force new connection': true});

require('./vue.js');
//require('./crash_graph.js');