<h1><?=date('l, dS F, Y', strtotime($day)); ?></h1>

<ul id="day-navigation">
<li><a href="day.php?day=<?=date('Y-m-d', strtotime($day) - 60*60*24); ?>">&lt; Previous Day</a></li>
<li><?=date('F d', strtotime($day)); ?></li>
<li><a href="day.php?day=<?=date('Y-m-d', strtotime($day) + 60*60*24); ?>">Next Day &gt;</a></li>
</ul><br />

<ul id="day-hour-scale">
<li>&nbsp;12 AM</li><li>&nbsp;1 AM</li><li>&nbsp;2 AM</li><li>&nbsp;3 AM</li><li>&nbsp;4 AM</li><li>&nbsp;5 AM</li>
<li>&nbsp;6 AM</li><li>&nbsp;7 AM</li><li>&nbsp;8 AM</li><li>&nbsp;9 AM</li><li>&nbsp;10 AM</li><li>&nbsp;11 AM</li>
<li>&nbsp;12 Noon</li><li>&nbsp;1 PM</li><li>&nbsp;2 PM</li><li>&nbsp;3 PM</li><li>&nbsp;4 PM</li><li>&nbsp;5 PM</li>
<li>&nbsp;6 PM</li><li>&nbsp;7 PM</li><li>&nbsp;8 PM</li><li>&nbsp;9 PM</li><li>&nbsp;10 PM</li><li>&nbsp;11 PM</li>
</ul><br /><br />

<div id="tasks-area">
<?php
foreach($todays_tasks as $task) {
	extract($task);
	print "<div class='daily-task $class' style='left:$left%;width:$width%;'>\n";
	print "<span class='task-name'>$name</span><br />\n";
	print "<span class='task-details'>From $from_time to $to_time (";
	if($hour_difference) print "$hour_difference hours, "; //Don't show the hour difference if its 0
	print "$minute_difference minutes)</span>";
	print "</div>\n";
}
?>
</div>
<br />

<h3>Total Time Taken</h3>
<dl id="tasks-aggregate">
<?php
foreach($tasks_aggregate as $task) {
	print "<dt>$task[name]</dt><dd>";
	if($task['total_hour']) print "$task[total_hour] hours, "; //Don't show the hour difference if its 0
	print "$task[total_minute] minutes</dd>";
}
?>
</dl>
