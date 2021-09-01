require('./bootstrap');

window.socket = io.connect(process.env.MIX_CRASH_HOST+':'+process.env.MIX_CRASH_PORT, {secure: true, 'force new connection': true});
require('./graph.js');
require('./vue.js');

$.notify.addStyle('success', {
	html: "<div><div class='notify notify-success'><span data-notify-text/></div></div>",
});

$.notify.addStyle('error', {
	html: "<div><div class='notify notify-error'><span data-notify-text/></div></div>",
});

notifySuccess = (s) => {
	$.notify(s, {
		style: 'success',
	});
}

notifyError = (s) => {
	$.notify(s, {
		style: 'error',
	});
}

socket.on('online', (data) => {
    $('#online').html(data);
});

window.turnSound = function() {
	vm.bSound = !vm.bSound;
}