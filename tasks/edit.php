<?php
include('../common.php');
include('_form.php');

if(isset($QUERY['action']) and $QUERY['action']=='Edit') {
	if($Task->edit($QUERY['id'], $QUERY['name'], $QUERY['description'],$QUERY['status'], $QUERY['type'], $QUERY['completed_on'], $QUERY['project_id'])) {
		showMessage("Task updated successfully",'tasks/list.php');
	}
}

$task_details = $sql->getAll("SELECT UNIX_TIMESTAMP(from_time) AS from_time, UNIX_TIMESTAMP(to_time) AS to_time FROM Duration WHERE task_id=$QUERY[id]");

$data = $Task->find($QUERY['id']);
if(!$data) showMessage("Invalid task specified",'tasks/list.php', 'error');
render();
