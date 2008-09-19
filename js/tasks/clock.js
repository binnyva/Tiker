clock = {
	'timer':false,
	'seconds':0,
	'minutes':0,
	'hours':0,
	'total':{
		'minutes':0,
		'hours':0
	},

	'start':function() {
		this.timer = window.setInterval("clock.tick()",1000);
	},
	'pause':function () {
		if(this.timer) window.clearInterval(this.timer);
	},
	'stop':function () {
		this.pause();
		this.timer = false;
		this.seconds = 0;
		this.minutes = 0;
		this.hours = 0;
		this.updateView();
	},
	'tick':function() {
		this.seconds++;
		if(this.seconds == 60) {
			this.minutes++;
			this.total.minutes++;
			this.seconds = 0;
	
			if(this.minutes == 60) {
				this.hours++;
				this.total.hours++;
				this.minutes = 0;
				this.total.minutes = 0;
			}
		}
		this.updateView();
	},
	'updateView' : function() {
		$("timer-hours").innerHTML= padNumber(this.hours,2);
		$("timer-mins").innerHTML = padNumber(this.minutes,2);
		$("timer-secs").innerHTML = padNumber(this.seconds,2);
		
		$("timer-total-hours").innerHTML= padNumber(this.total.hours,2);
		$("timer-total-mins").innerHTML = padNumber(this.total.minutes,2);
	}
}

