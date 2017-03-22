<?php 
require('common.php');

checkUser();

//Get the final 10 tasks.
$last_10_tasks = $sql->getAll("SELECT Task.name, Task.id as task_id, Task.status, Task.type, Duration.id AS duration_id, Duration.from_time, Duration.to_time
	FROM Duration INNER JOIN Task ON Task.id=Duration.task_id
	WHERE Task.user_id=$_SESSION[user_id]
	ORDER BY Duration.id DESC
	LIMIT 0,10");
$last_tasks = array();

foreach($last_10_tasks as $this_task) {
	// Find the duration of the task
	if($this_task['to_time'] == '0000-00-00 00:00:00') $this_task['to_time'] = date('Y-m-d H:i:s');
	$from = strtotime($this_task['from_time']);
	$to = strtotime($this_task['to_time']);
	list($hour_difference, $minute_difference) = getTimeDifference($from, $to);
	if($minute_difference < 3 and $hour_difference == 0) continue; //Too small as task to list(or its a accidental click)
	
	$task = array(
		'task_id'		=> $this_task['task_id'],
		'name'			=> $this_task['name'],
		'total_hour'	=> $hour_difference,
		'total_minute'	=> $minute_difference,
		'duration_id'	=> $this_task['duration_id'],
		'from_to'		=> date('h:i a', $from) . ' to ' . date('h:i a', $to),
	);
	
	array_push($last_tasks, $task);
}

include('reports/_day_report.php');

$html = new HTML;
$template->addResource('tasks/clock.js','js');
$template->addResource('library/shortcut.js','js');
$template->addResource('reports/day.css','css');
// $template->addResource('tasks/remote.css','css');
// $template->addResource('tasks/remote.js','js');
render();
