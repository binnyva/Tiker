function init() {
	$("edit-task-link").click(showEditForm);

}


function showEditForm(e) {
	JSL.event(e).stop();
	
	$("edit-task-form").show();
	$("edit-task-link").innerHTML = "Edit Task...";
}