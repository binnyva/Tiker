function openPopup(e) {
	if(!e) var e = window.event;
	var url = this.href;
	window.open(url, "popup_id", "scrollbars,resizable,width=300,height=450");
	
	//e.cancelBubble is supported by IE - this will kill the bubbling process.
	e.cancelBubble = true;
	e.returnValue = false;

	//e.stopPropagation works only in Firefox.
	if (e.stopPropagation) {
		e.stopPropagation();
		e.preventDefault();
	}
}

function indexInit() {
	$("remote-popup").click(openPopup);
}
$(window).load(indexInit);

