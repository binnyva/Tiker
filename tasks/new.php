<?php
include('../../../iframe/common.php');

if(isRequest('action','Add') and isRequest('name')) {
	$task_id = newTask($QUERY['name']);
	$duration_id = 0;
	if(isset($QUERY['task_start'])) $duration_id = startTask($task_id);

	$data = array('task_id'=>$task_id,'duration_id'=>$duration_id);
	showMessage("Task '$PARAM[name]' created successfully",'index.php','success',$data);
}
