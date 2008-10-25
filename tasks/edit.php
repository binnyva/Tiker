<?php
include('../common.php');
include('_form.php');

if(isset($QUERY['action']) and $QUERY['action']=='Edit') {
	if($Task->edit($QUERY['id'], $QUERY['name'], $QUERY['description'],$QUERY['status'], $QUERY['type'], $QUERY['completed_on'], $QUERY['project_id'])) {
		showMessage("Task updated successfully",'tasks/index.php');
	}
}

$task_details = $sql->getAll("SELECT id, UNIX_TIMESTAMP(from_time) AS from_time, 
	IF(to_time='0000-00-00 00:00:00', UNIX_TIMESTAMP(), UNIX_TIMESTAMP(to_time)) AS to_time FROM Duration WHERE task_id=$QUERY[id]");

$data = $Task->find($QUERY['id']);
if(!$data) showMessage("Invalid task specified",'tasks/list.php', 'error');
render();
