<?php
include('../common.php');
include('_form.php');

checkTaskOwnership($QUERY['task']);

if(isset($QUERY['action']) and $QUERY['action']=='Edit') {
	if($Task->edit($QUERY['task'], $QUERY['name'], $QUERY['description'],$QUERY['status'], $QUERY['type'], $QUERY['completed_on'], $QUERY['project_id'])) {
		showMessage("Task updated successfully",'tasks/index.php');
	}
}

$pager = new SqlPager("SELECT id, UNIX_TIMESTAMP(from_time) AS from_time, 
	IF(to_time='0000-00-00 00:00:00', UNIX_TIMESTAMP(), UNIX_TIMESTAMP(to_time)) AS to_time FROM Duration WHERE task_id=$QUERY[task] ORDER BY from_time DESC", 20);
$task_details = $pager->getPage();
$total_time = $sql->getOne("SELECT SUM(IF(to_time='0000-00-00 00:00:00', UNIX_TIMESTAMP(), UNIX_TIMESTAMP(to_time)) - UNIX_TIMESTAMP(from_time)) FROM Duration WHERE task_id=$QUERY[task]");

$data = $Task->find($QUERY['task']);

if(!$data) showMessage("Invalid task specified",'tasks/index.php', 'error');
render();
