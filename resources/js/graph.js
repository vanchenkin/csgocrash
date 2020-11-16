var currentX = 0;
var step = 0.1;
window.paintGraph = function (x, y) {
	if(!$('#graph').length) return;
	if(y) x = getX(y);
	currentX = x;
	var xes = [];
	var i = 0;
	while(i <= currentX){
		xes.push(getY(i));
		i += step;
	}
	var data = {
		series: [xes],
	};
	var options = {
	  	height: $('#graph').height(),
	  	width: $('#graph').width(),
	  	axisX: {
			showGrid: false,
			offset: 5
		},
		fullWidth: true,
		showPoint: false,
		axisY: {
			labelInterpolationFnc: function(value) {
				return value.toFixed(2);
			},
			type: Chartist.AutoScaleAxis,
			scaleMinSpace: 25,
			offset: 45,
		},
	};
	new Chartist.Line('.ct-chart', data, options);
}
window.clearGraph = function(){
	$('#graph').empty();
}

function getY(x) {
	return Math.pow(1.06, x);
}

function getX(y) {
	return Math.log(y) / Math.log(1.06);
}