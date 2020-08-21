var currentX = 0;
$(window).resize(function() {
	paintGraph(currentX);
});
var step = 0.4;
window.paintGraph = function (x) {
	currentX = x;
	var xes = [];
	var i = 0;
	while(i <= x){
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
			scaleMinSpace: 30,
			offset: 45,
		},
	};
	new Chartist.Line('.ct-chart', data, options);
}

function getY(x) {
	return Math.pow(1.06, x);
}