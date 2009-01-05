<h1>Task '<?=$data['name']?>'</h1>

<h2>Details</h2>

<table>
<tr><th>From</th><th>To</th><th>Duration</th><th colspan="2">Actions</th></tr>
<?php
$counter = 0;
foreach($task_details as $work) {
	$difference_sec = getTimeDifference($work['from_time'], $work['to_time'], 'seconds');
	
	$diff = seconds2hourmin($difference_sec, 'string');
?>
<tr class="<?=($counter++ % 2) ? 'odd' : 'even'?>"><td><?=date('d M Y, h:i a', $work['from_time'])?></td>
<td><?=date('h:i a', $work['to_time'])?></td>
<td><?=$diff?></td>
<td><a href="duration_edit.php?duration=<?=$work['id']?>" class="with-icon edit">Edit</a></td>
<td><a href="duration_delete.php?duration=<?=$work['id']?>&amp;task=<?=$QUERY['task']?>" class="with-icon delete confirm">Delete</a></td></tr>

<?php } ?>
</table>

<?php printPager(); ?>

<strong>Total Time</strong>: <?=seconds2hourmin($total_time, 'string')?>


<?php if(i($QUERY,'action') != 'show_form') { ?>
<h2 class="with-icon edit"><a href="#" id="edit-task-link">Edit Task?</a></h2>

<div id="edit-task-form">
<?php } else { ?>
<h2>Edit Task</h2>

<div>
<?php } ?>


<?php
$action = 'Edit';
require("../templates/tasks/_form.php");
?>
</div>