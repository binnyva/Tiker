<?php
function isRequest($key, $value = false) {
	if($value === false) {
		if(isset($_REQUEST[$key]) and $_REQUEST[$key]) return true;
	}
	if(isset($_REQUEST[$key]) and $_REQUEST[$key] == $value) return true;
	
	return false;
}
 
function newTask($name) {
	global $sql;

	$fields = array(
		'name'		=> $name,
		'added_on'	=> date('Y-m-d H:i:s'),
		'type'		=> 'once',
		'status'	=> 'scheduled'
	);

	return $sql->insert('Task',$fields);
}

function editTask($id,$name,$type,$status) {
	global $sql;
	$fields = array(
		'name'		=> $name,
		'type'		=> $type,
		'status'	=> $status
	);

	return $sql->update('Task',$fields,"WHERE id='$id'");
}

function startTask($task_id) {
	global $sql;

	$sql->update('Task',array('status'=>'working'),"WHERE id='$task_id'");
	$duration_id = startTimer($task_id);
	return $duration_id;
}

/// Stops tasks - but if the given task is recurring, this will return false - it will not be stopped(not even paused).
function stopTask($task_id) {
	global $sql;

	$stopped = $sql->update('Task',array('status'=>'done', 'completed_on'=>'NOW()'),"WHERE id='$task_id' AND type!='recurring'");
	if($stopped) {
		pauseTimer($task_id);
		return true;
	}
	
	// Guess it was recurring task
	return false;
}

/// Stops all tasks - even recurring onces
function forceStopTask($task_id) {
	global $sql;

	$stopped = $sql->update('Task',array('status'=>'done', 'completed_on'=>'NOW()'),"WHERE id='$task_id'");
	if($stopped) pauseTimer($task_id);
}

function startTimer($task_id) { 
	global $sql;
	$sql->insert('Duration',array(
			'task_id'=>$task_id,
			'from_time'=>date('Y-m-d H:i:s')
		));
	return $sql->fetchInsertId();
}
function pauseTimer($task_id) {
	global $sql;
	$current_duration_id = getCurrentDuration($task_id);
	if($current_duration_id) {
		$sql->Update('Duration',array(
				'task_id'=>$task_id,
				'to_time'=>date('Y-m-d H:i:s')
			),"WHERE id=$current_duration_id");
	}
	return $current_duration_id;
}

function continueTimer($task_id) {
	global $sql;
	$duration_details = $sql->getAssoc("SELECT id, UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(from_time) AS time_taken "
									. "  FROM Duration WHERE task_id=$task_id "
									. " AND to_time='0000-00-00 00:00:00' ORDER BY from_time DESC LIMIT 0,1");
	return $duration_details;
}

/** Gets the total time spent on a given task */
function getTotalTime($task_id) {
	global $sql;
	$total_time = $sql->getOne("SELECT SUM(UNIX_TIMESTAMP(to_time)-UNIX_TIMESTAMP(from_time)) FROM Duration "
								. "WHERE task_id=$task_id AND to_time!='0000-00-00 00:00:00' GROUP BY task_id");
	return $total_time;
}

/**
 * Gets the ID of the most recent duration for the given task.
 * Basically, it finds the duration with the highest 'from_time' field of this task.
 */
function getCurrentDuration($task_id) {
	global $sql;
	$duration_id = $sql->getOne("SELECT id FROM Duration WHERE task_id=$task_id AND to_time='0000-00-00 00:00:00' "
										. " ORDER BY from_time DESC LIMIT 0,1");
	return $duration_id;
}

/// Get the time difference between the two given time and returns it as an hour, minute array
function getTimeDifference($from, $to, $return_type='hour_min') {
	// The argument can be a timestamp or a mysql date string - both are parsed correctly
	if(!is_numeric($from)) $from = strtotime($from);
	if(!is_numeric($to)) $to = strtotime($to);
	
	$diff = $to - $from;
	if($return_type == 'seconds') return $diff;
	return seconds2hourmin($diff);
}

/// Converts the seconds given as the argument to a hour, minute array.
function seconds2hourmin($seconds) {
	$minute_difference = ($seconds/60) % 60;
	$hour_difference = floor(($seconds/60)/60);
	
	return array($hour_difference, $minute_difference);
}

