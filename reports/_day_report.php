<?php
 
if(isset($QUERY['day'])) $day = $QUERY['day'];
else $day = date('Y-m-d');

$day_tasks = $sql->getAll("SELECT Task.id,Task.name,Duration.id AS duration_id, Duration.from_time,Duration.to_time "
		. " FROM Task INNER JOIN Duration ON Duration.task_id=Task.id "
		. " WHERE DATE(Duration.to_time)='$day' OR DATE(Duration.from_time)='$day' ORDER BY Duration.from_time");

$tasks_aggregate = array();
$todays_tasks = array();
foreach($day_tasks as $task) {
	$class = '';
	
	$from = strtotime($task['from_time']);
	$to = strtotime($task['to_time']);
	
	if(date('Y-m-d', $from) != $day) { //Some tasks may start the day before.
		$from = strtotime("$day 00:00:00");//Set the starting point as today midnight.
	}
	
	if($task['to_time'] == '0000-00-00 00:00:00') { //Active Task.
		$class .= 'active ';
		$to = time();
	}
	
	$left = getTimePercentage($from);
	$width = time2percent(0, ($to-$from)/60);
	
	list($hour_difference, $minute_difference) = getTimeDifference($from, $to);
	if($minute_difference < 3 and $hour_difference == 0) continue; //Too small as task to list(or its a accidental click)
	
	$from_time = date('h:i a', $from);
	$to_time = date('h:i a', $to);
	
	if($width < 4) $class .= 'small ';
	
	$todays_tasks[] = array(
		'class'	=>	$class,
		'left'	=>	$left,
		'width'	=>	$width,
		'name'	=>	$task['name'],
		'from_time'	=>	$from_time,
		'to_time'	=>	$to_time,
		'hour_difference'	=>	$hour_difference,
		'minute_difference'	=>	$minute_difference,
	);
	
	// Find the total time taken for each task.
	$task_id = $task['id'];
	if(!isset($tasks_aggregate[$task_id])) { //Initalize the array if its not created before
		$tasks_aggregate[$task_id] = array(
			'task_id'		=> $task_id,
			'name'			=> $task['name'],
			'total_hour'	=> 0,
			'total_minute'	=> 0,
			'duration_id'	=> $task['duration_id'],
		);
	}
	$total_minute = $tasks_aggregate[$task_id]['total_minute'] + $minute_difference;
	$total_hour = $tasks_aggregate[$task_id]['total_hour'] + $hour_difference + intval($total_minute / 60);
	$total_minute = $total_minute % 60;
	$tasks_aggregate[$task_id]['total_minute'] = $total_minute;
	$tasks_aggregate[$task_id]['total_hour'] = $total_hour;
}

// Every 15 min is 1% width on the screen - so calculate the width and the location of the task using its from and to time.
function getTimePercentage($time) {
	$hour = date('G', $time);
	$min = intval(date('i', $time));
	
	return time2percent($hour, $min);
}
function time2percent($hour, $min) {
	$min_rounded = round($min, -1); //Round to the nearest 10 mins
	$percent_points = $hour * 4; // 1 hour is 6% width
	$percent_points+= $min_rounded/15; // each 15 minutes is 1% width
	return intval($percent_points);
}

