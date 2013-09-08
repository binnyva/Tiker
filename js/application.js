function padNumber(number,points) {
	var number_string = number;
	if(!number) number = 1;//0 Protection
	var upper_limit = Math.pow(10,points-1);
	while(number < upper_limit) {
		number_string = "0" + number_string;
		number = number * 10;
	}
	return number_string;
}

//Framework Specific
function showMessage(data) {
	if(data.success) $("success-message").innerHTML = stripSlashes(data.success);
	if(data.error) $("error-message").innerHTML = stripSlashes(data.error);
}
function stripSlashes(text) {
	if(!text) return "";
	return text.replace(/\\([\'\"])/,"$1");
}

function makeCalender() {
	console.log("Make");
	calendar.opt['display_element'] = this.id;
	calendar.opt['input'] = "day";
	calendar.showCalendar();
}

function setDate(year, month, day) {
	document.getElementById(calendar.opt["input"]).value = year + "-" + month + "-" + day;
	calendar.hideCalendar();
	document.getElementById("change-day-form").submit();
}

function siteInit() {
	$("a.confirm").click(function(e) { //If a link has a confirm class, confrm the action
		var action = (this.title) ? this.title : "do this";
		action = action.substr(0,1).toLowerCase() + action.substr(1); //Lowercase the first char.

		if(!confirm("Are you sure you want to " + action + "?")) JSL.event(e).stop();
	});

	if(document.getElementById("change-day")) calendar.set("change-day", {"onclick": makeCalender, "onDateSelect":setDate});


	if(window.init) init(); //If there is a init() anywhere, call it on 'onload'
	if(window.main) main(); //Same for main()
}
JSL.dom(window).load(siteInit);
