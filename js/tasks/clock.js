clock = {
	'timer':false,
	'seconds':0,
	'minutes':0,
	'hours':0,
	'estimate':0,
	'time_left_in_mins': 0,
	'total':{
		'minutes':0,
		'hours':0
	},

	'start':function() {
		this.timer = window.setInterval("clock.tick()",1000);
		if(this.estimate) this.updateProgress();
		fixTaskNameWidth();
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
		this.estimate = 0;
		this.time_left_in_mins = 0;

		this.updateView();
		fixTaskNameWidth();
		$("#progress").css({"width":"0%"});
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
		$("#timer-hours").html(padNumber(this.hours,2));
		$("#timer-mins").html(padNumber(this.minutes,2));
		$("#timer-secs").html(padNumber(this.seconds,2));
		
		$("#timer-total-hours").html(padNumber(this.total.hours,2));
		$("#timer-total-mins").html(padNumber(this.total.minutes,2));

		// Update progress indicator if task has a estimated time...
		if(this.minutes && !this.seconds && this.estimate) {
			this.updateProgress();
		}
	},
	'updateProgress' : function() {
		if(this.time_left_in_mins <= 0) {
			$("body").addClass("timeout");
			// var sound = new Howl({  urls: ['images/sounds/bell.mp3']}).play();
			return;
		}

		var percent_complete = 0;
		if(this.estimate != this.time_left_in_mins && this.time_left_in_mins < this.estimate) {
			percent_complete = Math.floor((this.estimate - this.time_left_in_mins) / this.estimate * 100);
			$("#progress").css({"width":percent_complete+"%"});
		}

		this.time_left_in_mins--;
	},
	'setEstimate' : function(estimate) {
		this.estimate = estimate;
		this.time_left_in_mins = this.estimate - ((this.total.hours * 60) + this.total.minutes);
		return this.estimate;
	}
}

