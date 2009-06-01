<?php
include('../common.php');
include('../includes/cli_login.php');

if(i($QUERY,'action') == 'Add' and i($QUERY,'name')) {
	$task_id = $Task->create($QUERY['name']);
	$duration_id = 0;
	if(isset($QUERY['task_start'])) $duration_id = $Task->startTask($task_id);

	$data = array('task_id'=>$task_id,'duration_id'=>$duration_id);
	showMessage("Task '$PARAM[name]' created successfully",'index.php','success',$data);
}
