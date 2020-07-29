$(function() {
	var graphStep = 0.1;
	var startX = 0;
	var canvas = $('#crashGraphic')[0];
	var context = canvas.getContext('2d'); 
	var canvasWidth;
	var canvasHeight;
	var scaleX = 30;
	var scaleY = 200;
	var border = 5;
	var drawX, drawY;

	function paintCrashGraphic(curentX, randomNumber) {	
		canvasWidth = canvas.width;
		canvasHeight = canvas.height;
		context.lineWidth = 2;
		if ((border * 2 + curentX * scaleX) > canvasWidth) {
			scaleX = (canvasWidth - border * 2) / curentX;
		}
		if ((border * 2 + getCrashGraphicY(curentX) * scaleY) > canvasHeight) {
			scaleY = (canvasHeight - border * 2) / getCrashGraphicY(curentX);
		}
		context.strokeStyle = '#fff';	
		context.clearRect(0, 0, canvasWidth, canvasHeight);	
		context.beginPath();
		context.moveTo(border, canvasHeight - border );
		context.lineTo(border, border);	
		context.moveTo(border, canvasHeight - border);
		context.lineTo(canvasWidth - border, canvasHeight - border);
		
		drawX = startX;
		var isFirst = true;
		context.stroke();
		context.beginPath();				
		context.lineWidth = 3;
		
		context.strokeStyle = '#a0a1a7';		
		while (drawX <= curentX) {
			drawY = getCrashGraphicY(drawX);
			drawX += graphStep;
			if (isFirst) {
				isFirst = false;
				context.moveTo(border + drawX * scaleX, canvasHeight - border - (drawY - 1) * scaleY);
			} else {
				context.lineTo(border + drawX * scaleX, canvasHeight - border - (drawY - 1) * scaleY);
			}
		}
		
		if (!randomNumber) {
			context.stroke();	
			context.font = "50px Tahoma";
			context.textAlign = "center";
			context.fillStyle = "#ffcd05";
			context.fillText(drawY.toFixed(2) + 'x', canvas.width/2, canvas.height/2); 
		} else {
			context.stroke();
			context.font = "50px Tahoma";
			context.textAlign = "center";
			context.fillStyle = "red";
			context.fillText(randomNumber + 'x', canvas.width/2, canvas.height/2); 
		}
	}
		
	function getCrashGraphicY(x) {
		return Math.pow(1.06, x);
	}
				
	function getCrashGraphicX(y) {
		return Math.log(y) / Math.log(1.06);
	}
	
	paintCrashGraphic(startX);

	function cgBetStop() {
		$('.cg_user_bet_panel .cg_bet_button').addClass('disable');
		$('.cg_user_bet_panel input').attr('disabled', 'disabled');
	}
	
	function cgBetStart() {
		$('.cg_user_bet_panel .cg_bet_button').removeClass('disable');
		$('.cg_user_bet_panel input').removeAttr('disabled');
	}
});