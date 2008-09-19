<?php
include('../../../iframe/common.php');

$tasks = $sql->getAll("SELECT Task.id, Task.name, Task.status, Duration.id AS duration_id, Duration.to_time "
	. " FROM Task INNER JOIN Duration ON Duration.task_id = Task.id "
	. " WHERE status = 'working' "
	. " AND Duration.id=(SELECT MAX(id) FROM Duration WHERE task_id=Task.id) "
	. " GROUP BY Task.id ORDER BY Task.type");

//Get the active tasks
$active_tasks = array();
foreach($tasks as $task) {
	if($task['to_time'] == '0000-00-00 00:00:00') {
		$active_tasks[] = array(
			'id'	=>	$task['id'],
			'name'	=>	$task['name']
		);
	}
}

$html = new HTML;
$template->addResource('tasks/clock.js','js');
$template->addResource('libraries/shortcut.js','js');
render();
