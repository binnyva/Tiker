<?php
include('../common.php');
checkUser();

$tasks = $sql->getAll("SELECT Task.id, Task.name, Task.status, Duration.id AS duration_id, Duration.to_time "
	. " FROM Task INNER JOIN Duration ON Duration.task_id = Task.id "
	. " WHERE (status = 'paused' OR status = 'working') AND Task.type='once' AND Task.user_id=$_SESSION[user_id] "
	. " AND Duration.id=(SELECT MAX(id) FROM Duration WHERE task_id=Task.id) "
	. " GROUP BY Task.id ORDER BY Task.type");

$recurring_tasks = $sql->getAll("SELECT Task.id, Task.name, Task.status, Duration.id AS duration_id, Duration.to_time "
	. " FROM Task INNER JOIN Duration ON Duration.task_id = Task.id "
	. " WHERE (status = 'paused' OR status = 'working') AND Task.type='recurring' AND Task.user_id=$_SESSION[user_id] "
	. " AND Duration.id=(SELECT MAX(id) FROM Duration WHERE task_id=Task.id) "
	. " GROUP BY Task.id ORDER BY Task.type");


//Get the active tasks
$active_tasks = array();
foreach(array_merge($recurring_tasks,$tasks) as $task) {
	if($task['to_time'] == '0000-00-00 00:00:00') {
		$active_tasks[] = array(
			'id'	=>	$task['id'],
			'name'	=>	$task['name']
		);
	}
}

$html = new HTML;
$template->addResource('tasks/clock.js','js');
$template->addResource('library/shortcut.js','js');
render('tasks/remote.php',false);
