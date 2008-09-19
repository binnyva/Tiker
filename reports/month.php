<?php
require('../common.php');

$calendar = new Calendar("day");
$calendar->limit = array(
	'from'	=> array('year'=>date('Y')-2,	'month'=>1),
	'to'	=> array('year'=>date('Y'),		'month'=>date('n'))
);
$calendar->setDateField("Duration.from_time");
$calendar->setQuery("SELECT COUNT(*) AS total_items, Duration.from_time, DATE_FORMAT(Duration.from_time,'%d') AS day, "
		. " SUM(IF(Duration.to_time='0000-00-00 00:00:00', UNIX_TIMESTAMP(),UNIX_TIMESTAMP(Duration.to_time))-UNIX_TIMESTAMP(Duration.from_time)) AS time_tracked"
		. " FROM Task INNER JOIN Duration ON Duration.task_id=Task.id ", "",/*WHERE*/ " GROUP BY day");

function day($year, $month, $day) {
	global $calendar;
	$all_tasks = $calendar->getData($day);
 	if(!$all_tasks) return;
 	$tasks = $all_tasks[0];

 	print "<a href='day.php?day=$year-$month-$day'>" . $tasks['total_items'] . " items</a><br />";
 	list($hour, $min) = seconds2hourmin($tasks['time_tracked']);
 	print "$hour hours, $min minutes tracked";
	
}

render();
