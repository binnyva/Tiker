function init() {
	if(document.getElementById("edit-task-link")) $("#edit-task-link").click(showEditForm);
}

function showEditForm(e) {
	e.stopPropogation();
	
	$("#edit-task-form").show();
	$("#edit-task-link").html("Edit Task...");
}