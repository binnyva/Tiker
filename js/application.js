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
function siteInit() {
	if(window.init) init(); //If there is a init() anywhere, call it on 'onload'
}
JSL.dom(window).load(siteInit);
