function openPopup(e) {
	var url = this.href;
	window.open(url, "popup_id", "scrollbars,resizable,width=300,height=450");

	e.stopPropagation();
}

function indexInit() {
	$("#remote-popup").click(openPopup);
}
$(indexInit);

