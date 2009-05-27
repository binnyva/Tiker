<?php
include('../common.php');

//Used by the remote.php file
if(!isRequest('task_id')) showMessage("Task ID not given!",'index.php','error');
checkTaskOwnership($QUERY['task_id']);

if(isRequest('action','start')) {
	$total_time = $Task->getTotalTime($QUERY['task_id']); //Find how long this task was going on
	$duration_id = $Task->startTask($QUERY['task_id']);
	
	$times = formatTimeFromSeconds($total_time);

	$data = array(
		'duration_id'=>$duration_id,
		'total_time_minutes'=>$times[1],
		'total_time_hours'=>$times[0]
	);
	showMessage("Task Started",'index.php','success',$data);

} else if(isRequest('action','pause')) {
	$Task->pauseTimer($QUERY['task_id']);
	showMessage("Task Paused");

} else if(isRequest('action','done')) {
	if($Task->stopTask($QUERY['task_id']))	showMessage("Task Compleated");
	else {// Someone tried to end a recurring task. Bad, bad.
		showMessage("Recurring task - cannot be finished",'index.php','error');
	}

} else if(isRequest('action','continue')) {
	$total_time = $Task->getTotalTime($QUERY['task_id']); //Find how long this task was going on
	$durations = $Task->continueTimer($QUERY['task_id']);
	
	$times = formatTimeFromSeconds($total_time);
	$time_taken_so_far = formatTimeFromSeconds($durations['time_taken']);
	
	$times[0] = $times[0] + $time_taken_so_far[0];
	$times[1] = $times[1] + $time_taken_so_far[1];

	$data = array(
		'duration_id'		=>	$durations['id'],
		'total_time_minutes'=>	$times[1],
		'total_time_hours'	=>	$times[0],
		'time_taken_hours'	=>	$time_taken_so_far[0],
		'time_taken_mins'	=>	$time_taken_so_far[1],
		'time_taken_secs'	=>	$time_taken_so_far[2]
	);
	showMessage("Task Continuing",'index.php','success',$data);
}


///Get the duration in Hours and Minutes
function formatTimeFromSeconds($total_time) {
	$total_time_minutes = intVal($total_time/60);
	$total_time_secs = $total_time % 60;
	if(($total_time_minutes % 60) > 30) $total_time_minutes++; //A rough rounder : 232 secs = 4 mins instead of 3
	$total_time_hours = intVal($total_time_minutes/60);
	$total_time_minutes = $total_time_minutes % 60;
	
	return array($total_time_hours,$total_time_minutes,$total_time_secs);
}